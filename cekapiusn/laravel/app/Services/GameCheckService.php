<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Service untuk memeriksa in-game nickname (IGN) berbagai game
 * menggunakan mekanisme GoPay Games & Codashop.
 */
class GameCheckService
{
    private const GOPAY_ENDPOINT = 'https://gopay.co.id/games/v1/order/user-account';

    /**
     * Periksa Nickname berdasarkan Game Code, User ID, dan Zone/Server ID
     *
     * @param string $gameCode Contoh: 'MOBILE_LEGENDS', 'FREEFIRE', 'VALORANT', 'CALL_OF_DUTY', 'AOV', 'HOK'
     * @param string $userId   User ID Akun Game Pemain
     * @param string|null $zoneId Zone ID / Server ID (khusus game seperti MLBB dll)
     * @return array
     */
    public function checkNickname(string $gameCode, string $userId, ?string $zoneId = null): array
    {
        $code = strtoupper(trim($gameCode));

        try {
            $response = Http::withHeaders([
                'Origin'     => 'https://gopay.co.id',
                'Referer'    => 'https://gopay.co.id/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json, text/plain, */*',
            ])->timeout(12)->post(self::GOPAY_ENDPOINT, [
                'code' => $code,
                'data' => [
                    'userId' => (string) trim($userId),
                    'zoneId' => (string) trim($zoneId ?? ''),
                ],
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data['data']['username'])) {
                $nickname = urldecode((string) $data['data']['username']);
                return [
                    'success'  => true,
                    'status'   => 200,
                    'game'     => $code,
                    'user_id'  => $userId,
                    'zone_id'  => $zoneId,
                    'nickname' => $nickname,
                    'message'  => 'User ID valid',
                ];
            }

            $errorMessage = $data['message'] ?? 'ID atau Server Game tidak ditemukan / tidak valid.';
            if (is_array($errorMessage)) {
                $errorMessage = implode(', ', $errorMessage);
            }

            return [
                'success' => false,
                'status'  => 400,
                'game'    => $code,
                'user_id' => $userId,
                'message' => $errorMessage,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'status'  => 500,
                'game'    => $code,
                'user_id' => $userId,
                'message' => 'Gagal menghubungi server game: ' . $e->getMessage(),
            ];
        }
    }
}
