<?php

namespace Aditdev\Providers;

use Aditdev\Http\HttpClient;
use Aditdev\Support\Result;

/**
 * GoPay Games nickname validation provider.
 *
 * Fast, reliable public API for checking in-game names:
 * Endpoint: https://gopay.co.id/games/v1/order/user-account
 * Payload: {"code": "GAME_CODE", "data": {"userId": "...", "zoneId": "..."}}
 */
class GopayProvider implements ProviderInterface
{
    public const ENDPOINT = 'https://gopay.co.id/games/v1/order/user-account';

    /** @var HttpClient */
    private $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function lookup(array $game, $id, $otherId = ''): Result
    {
        $code = $game['gopayCode'] ?? $game['code'] ?? '';
        if (empty($code)) {
            return Result::invalid('GoPay game code is missing');
        }

        $body = [
            'code' => $code,
            'data' => [
                'userId' => (string) $id,
                'zoneId' => (string) $otherId,
            ],
        ];

        $response = $this->http->postJson(self::ENDPOINT, $body, $this->headers());
        if ($response->failed()) {
            return Result::error('Gagal menghubungi server GoPay Games');
        }

        $data = $response->json();
        if ($data === null) {
            return Result::error('Respon tidak valid dari GoPay Games');
        }

        // Success response usually comes with status 200 or 201 and data.username
        if (($response->status === 200 || $response->status === 201) && !empty($data['data']['username'])) {
            $username = urldecode((string) $data['data']['username']);
            return Result::success($username);
        }

        // If error message returned
        $errorMessage = $data['message'] ?? $game['errorMessage'] ?? 'User ID atau Server tidak valid';
        if (is_array($errorMessage)) {
            $errorMessage = implode(', ', $errorMessage);
        }

        return Result::invalid($errorMessage);
    }

    /**
     * @return array<string,string>
     */
    private function headers(): array
    {
        return [
            'Origin'     => 'https://gopay.co.id',
            'Referer'    => 'https://gopay.co.id/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept'     => 'application/json, text/plain, */*',
        ];
    }
}
