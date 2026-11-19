# Panduan Integrasi Mekanisme Cek IGN ke Aplikasi Laravel

Dokumen ini menjelaskan cara menggunakan mekanisme pengecekan **In-Game Name (IGN)** game (Mobile Legends, Free Fire, Valorant, Call of Duty, AOV, dll) di aplikasi Laravel Anda.

---

## 📌 Ringkasan Mekanisme

Library `check-ign` bekerja dengan cara mengirimkan data User ID (dan Zone/Server ID jika diperlukan) ke upstream payment gateway / game inquiry endpoint resmi:
1. **GoPay Games API (`https://gopay.co.id/games/v1/order/user-account`)**:
   - Metode paling stabil, cepat, dan tidak terhalang proteksi checksum.
   - Digunakan untuk: **Mobile Legends: Bang Bang, Free Fire, VALORANT, Call of Duty Mobile, Arena of Valor, Honor of Kings, FC Mobile, Magic Chess: Go Go, PUBG Mobile**.
2. **Codashop & DancingIdol Provider**:
   - Digunakan sebagai fallback atau untuk game khusus seperti **AU2 Mobile** dan **Point Blank**.

---

## 🚀 Cara 1: Menggunakan Service Class Native Laravel (Sangat Direkomendasikan)

Cara ini tidak membutuhkan dependensi Composer eksternal apa pun, hanya menggunakan `Illuminate\Support\Facades\Http` bawaan Laravel.

### 1. Salin Service ke `app/Services/GameCheckService.php`

Buat file baru di Laravel Anda: `app/Services/GameCheckService.php` (file sudah kami sediakan di folder [`laravel/app/Services/GameCheckService.php`](file:///c:/xampp/htdocs/cekapiusn/laravel/app/Services/GameCheckService.php)):

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class GameCheckService
{
    private const GOPAY_ENDPOINT = 'https://gopay.co.id/games/v1/order/user-account';

    /**
     * @param string $gameCode 'MOBILE_LEGENDS', 'FREEFIRE', 'VALORANT', 'AOV', 'CALL_OF_DUTY', dll
     * @param string $userId   User ID Akun
     * @param string|null $zoneId Zone ID / Server (wajib untuk MLBB)
     */
    public function checkNickname(string $gameCode, string $userId, ?string $zoneId = null): array
    {
        $code = strtoupper(trim($gameCode));

        try {
            $response = Http::withHeaders([
                'Origin'     => 'https://gopay.co.id',
                'Referer'    => 'https://gopay.co.id/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
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
                return [
                    'success'  => true,
                    'status'   => 200,
                    'game'     => $code,
                    'user_id'  => $userId,
                    'zone_id'  => $zoneId,
                    'nickname' => urldecode((string) $data['data']['username']),
                ];
            }

            return [
                'success' => false,
                'status'  => 400,
                'game'    => $code,
                'message' => $data['message'] ?? 'ID atau Server Game tidak ditemukan.',
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'status'  => 500,
                'message' => 'Gagal menghubungi server game: ' . $e->getMessage(),
            ];
        }
    }
}
```

### 2. Buat Controller di `app/Http/Controllers/GameCheckController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameCheckService;
use Illuminate\Http\JsonResponse;

class GameCheckController extends Controller
{
    protected GameCheckService $gameCheckService;

    public function __construct(GameCheckService $gameCheckService)
    {
        $this->gameCheckService = $gameCheckService;
    }

    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game'    => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
        ]);

        $result = $this->gameCheckService->checkNickname(
            $validated['game'],
            $validated['user_id'],
            $validated['zone_id'] ?? null
        );

        return response()->json($result, $result['status'] ?? 200);
    }
}
```

### 3. Tambahkan Route di `routes/api.php`

```php
use App\Http\Controllers\GameCheckController;

Route::post('/game/check-ign', [GameCheckController::class, 'check']);
```

---

## 💡 Tips Caching di Laravel (Untuk Menghemat Request & Mempercepat Respon)

Anda bisa menggunakan `Cache::remember` bawaan Laravel selama misalnya 1 hari:

```php
use Illuminate\Support\Facades\Cache;

$cacheKey = "ign_{$gameCode}_{$userId}_{$zoneId}";

$result = Cache::remember($cacheKey, now()->addDay(), function () use ($gameService, $gameCode, $userId, $zoneId) {
    return $gameService->checkNickname($gameCode, $userId, $zoneId);
});
```

---

## 📦 Cara 2: Menyalin Folder `src/` Library Ini Langsung ke Laravel

Jika Anda ingin mempertahankan struktur class asli `Aditdev\ApiGames`:
1. Salin seluruh folder `src/` dari project ini ke `app/Libraries/CheckIgn/` di Laravel Anda.
2. Di controller atau service Laravel Anda, panggil:
   ```php
   require_once app_path('Libraries/CheckIgn/ApiGames.php');

   $api = new \Aditdev\ApiGames();
   $result = $api->check('MOBILE_LEGENDS', '604210151', '8425');
   // Output: ['status' => 200, 'nickname' => 'Diezy x Ruuma Kopi']
   ```

---

## 🧪 Pengujian Langsung Lokal

Anda dapat menguji sistem ini secara langsung di browser lokal Anda sekarang juga di:
- **Web Tester Dashboard**: [http://localhost/cekapiusn/](http://localhost/cekapiusn/)
- **REST API Endpoint**: [http://localhost/cekapiusn/api.php?game=MOBILE_LEGENDS&user_id=604210151&zone_id=8425](http://localhost/cekapiusn/api.php?game=MOBILE_LEGENDS&user_id=604210151&zone_id=8425)
- **Daftar Game JSON**: [http://localhost/cekapiusn/api.php?action=games](http://localhost/cekapiusn/api.php?action=games)
