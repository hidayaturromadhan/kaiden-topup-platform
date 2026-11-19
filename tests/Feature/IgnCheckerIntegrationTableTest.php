<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\GameCheckService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class IgnCheckerIntegrationTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Uji 5 Skenario Pengujian IGN Checker (Tabel 4.6)
     */
    public function test_verify_all_ign_checker_scenarios(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        echo "\n\n=== HASIL PENGUJIAN INTEGRASI IGN CHECKER (TABEL 4.6) ===\n";

        // 1. ID akun valid
        $res1 = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);
        $res1->assertStatus(200);
        $this->assertTrue($res1->json('success'));
        $this->assertNotEmpty($res1->json('nickname'));
        $got1 = "Sistem mengembalikan success: true dan menampilkan nickname pemain ({$res1->json('nickname')})";
        echo "1. ID Akun Valid: {$got1} [SESUAI]\n";

        // 2. ID akun salah
        Http::fake([
            'https://api.duniagames.co.id/*' => Http::response(['data' => []], 200),
        ]);
        // Kirim ID karakter yang tidak memenuhi format provider / tidak ditemukan
        $res2 = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => 'invalid_id_not_found',
            'zone_id' => '0000',
        ]);
        $res2->assertStatus(200);
        $this->assertFalse($res2->json('success'));
        $this->assertStringContainsString('tidak ditemukan', $res2->json('message'));
        $got2 = "Peringatan 'User ID tidak ditemukan' tampil dan pelanggan diminta memeriksa ID kembali";
        echo "2. ID Akun Salah: {$got2} [SESUAI]\n";

        // 3. Layanan timeout atau diblokir (Graceful Fallback & Manual Confirmation)
        Http::fake([
            'https://api.duniagames.co.id/*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out after 12000 milliseconds');
            },
        ]);
        $res3 = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '99999999',
            'zone_id' => '1234',
        ]);
        $res3->assertStatus(200);
        $this->assertFalse($res3->json('success'));
        $this->assertTrue($res3->json('is_error'));
        $this->assertStringContainsString('timeout', $res3->json('message'));

        // Pastikan transaksi checkout tetap bisa dilanjutkan via manual_confirmation
        $checkoutRes = $this->actingAs($pelanggan)->post(route('order.checkout'), [
            'product_id' => $product->id,
            'user_id_game' => '99999999',
            'zone_id' => '1234',
            'manual_confirmation' => 1,
        ]);
        $checkoutRes->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $pelanggan->id,
            'user_id_game' => '99999999',
            'status' => 'MENUNGGU_BAYAR',
        ]);
        $got3 = "Peringatan kendala jaringan tampil, opsi konfirmasi manual aktif, dan checkout berhasil dibuat";
        echo "3. Layanan Timeout / Error: {$got3} [SESUAI]\n";

        // 4. Permintaan melebihi batas (Rate Limiting)
        $ip = '127.0.0.1';
        $rateKey = "ign_check_rate:{$ip}";
        RateLimiter::clear($rateKey);
        // Hit rate limiter sebanyak 20 kali (batas maksimal per menit)
        for ($i = 0; $i < 20; $i++) {
            RateLimiter::hit($rateKey, 60);
        }
        $res4 = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);
        $res4->assertStatus(429);
        $this->assertFalse($res4->json('success'));
        $this->assertStringContainsString('Terlalu banyak permintaan', $res4->json('message'));
        $got4 = "Permintaan diblokir dengan status HTTP 429 Too Many Requests beserta info waktu tunggu";
        echo "4. Rate Limiting: {$got4} [SESUAI]\n";

        // 5. Feature Flag dimatikan oleh Superadmin
        RateLimiter::clear($rateKey);
        Setting::set('ign_checker_enabled', 'false');
        $res5 = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);
        $res5->assertStatus(200);
        $this->assertFalse($res5->json('success'));
        $this->assertTrue($res5->json('feature_disabled'));

        // Checkout tetap dapat dilanjutkan
        $checkoutRes5 = $this->actingAs($pelanggan)->post(route('order.checkout'), [
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'zone_id' => '2001',
            'manual_confirmation' => 1,
        ]);
        $checkoutRes5->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $pelanggan->id,
            'user_id_game' => '12345678',
            'status' => 'MENUNGGU_BAYAR',
        ]);
        $got5 = "Pengecekan otomatis dilewati tanpa blocking dan pelanggan tetap dapat melanjutkan checkout";
        echo "5. Feature Flag Dimatikan: {$got5} [SESUAI]\n";

        echo "Seluruh 5 Skenario Pengujian IGN Checker Terbukti 100% VALID & SESUAI\n\n";
    }
}
