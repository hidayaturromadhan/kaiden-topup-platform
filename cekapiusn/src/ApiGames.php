<?php

namespace Aditdev;

use Aditdev\Http\HttpClient;
use Aditdev\Providers\CodashopProvider;
use Aditdev\Providers\CodashopDynamicProvider;
use Aditdev\Providers\DancingIdolProvider;
use Aditdev\Providers\GopayProvider;
use Aditdev\Providers\ProviderInterface;
use Aditdev\Support\Result;

/*
 * PHP library for checking a game nickname based on an account ID.
 *
 * Usage (modern):
 *   $api = new ApiGames();
 *   $result = $api->check('MOBILE_LEGENDS', '604210151', '8425'); // array
 *
 * Usage (legacy, still supported):
 *   echo $api->MOBILE_LEGENDS('604210151', '8425'); // JSON string
 *
 * Add or remove games by editing src/games.php.
 */

// Allow direct `require 'src/ApiGames.php'` without Composer's autoloader.
require_once __DIR__ . '/Support/Result.php';
require_once __DIR__ . '/Http/HttpClient.php';
require_once __DIR__ . '/Providers/ProviderInterface.php';
require_once __DIR__ . '/Providers/CodashopProvider.php';
require_once __DIR__ . '/Providers/CodashopDynamicProvider.php';
require_once __DIR__ . '/Providers/DancingIdolProvider.php';
require_once __DIR__ . '/Providers/GopayProvider.php';

class ApiGames
{
    /** @var array<string,array<string,mixed>> */
    private $games;

    /** @var array<string,ProviderInterface> */
    private $providers;

    public function __construct()
    {
        $this->games = require __DIR__ . '/games.php';

        $http = new HttpClient();
        $this->providers = [
            'gopay'            => new GopayProvider($http),
            'codashop'         => new CodashopProvider($http),
            'codashop_dynamic' => new CodashopDynamicProvider($http),
            'dancingidol'      => new DancingIdolProvider($http),
        ];
    }

    /**
     * Check a nickname. Returns a normalized array:
     *   ['status' => 200, 'nickname' => '...']  on success
     *   ['status' => 4xx|5xx, 'msg' => '...']    otherwise
     *
     * @return array{status:int, nickname?:string, msg?:string, game?:string, provider?:string}
     */
    public function check($game, $id, $otherId = '')
    {
        return $this->resolve($game, $id, $otherId)->toArray();
    }

    /**
     * Whether the given game code is supported.
     */
    public function supports($game)
    {
        return isset($this->games[strtoupper(trim((string) $game))]);
    }

    /**
     * List of supported game codes.
     *
     * @return array<int,string>
     */
    public function games()
    {
        return array_keys($this->games);
    }

    /**
     * Get detailed list of all games for UI and documentation.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getGameList(): array
    {
        $list = [];
        foreach ($this->games as $code => $cfg) {
            $list[] = [
                'code'         => $code,
                'name'         => $cfg['name'] ?? $code,
                'requiresZone' => isset($cfg['zone']) && $cfg['zone'] === 'user',
                'zoneLabel'    => ($cfg['zone'] ?? '') === 'user' ? 'Zone / Server ID' : null,
                'provider'     => $cfg['provider'] ?? 'codashop',
                'sampleId'     => $cfg['sampleId'] ?? '',
                'sampleZone'   => $cfg['sampleZone'] ?? '',
                'sampleIgn'    => $cfg['sampleIgn'] ?? '',
            ];
        }
        return $list;
    }

    /**
     * Get single game configuration.
     */
    public function getGame(string $game): ?array
    {
        $code = strtoupper(trim($game));
        return $this->games[$code] ?? null;
    }

    /**
     * Run a lookup and return a Result object with auto-fallback support.
     */
    private function resolve($game, $id, $otherId): Result
    {
        $code = strtoupper(trim((string) $game));

        if (!isset($this->games[$code])) {
            return Result::notFound("Game not found ($code)");
        }

        if (trim((string) $id) === '') {
            return Result::invalid('Account ID is required');
        }

        $config = $this->games[$code] + [
            'provider'        => 'codashop',
            'voucherTypeName' => $code,
        ];

        $primaryProviderName = $config['provider'];
        $provider = $this->providers[$primaryProviderName] ?? null;
        if ($provider === null) {
            return Result::error("Unknown provider ({$primaryProviderName})");
        }

        $result = $provider->lookup($config, trim((string) $id), trim((string) $otherId));

        // If primary provider failed and a fallback is configured, try fallback
        if ($result->status !== 200 && !empty($config['fallback'])) {
            $fallbackName = $config['fallback'];
            $fallbackProvider = $this->providers[$fallbackName] ?? null;
            if ($fallbackProvider !== null) {
                $fallbackResult = $fallbackProvider->lookup($config, trim((string) $id), trim((string) $otherId));
                if ($fallbackResult->status === 200) {
                    return $fallbackResult;
                }
            }
        }

        return $result;
    }

    /**
     * Legacy support: `$api->MOBILE_LEGENDS($id, $zone)` returns a JSON string,
     * matching the original library's behaviour.
     */
    public function __call($name, $arguments)
    {
        $id      = $arguments[0] ?? '';
        $otherId = $arguments[1] ?? '';

        return $this->resolve($name, $id, $otherId)->toJson();
    }
}
