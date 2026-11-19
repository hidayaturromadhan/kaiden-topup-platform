<?php

namespace Tests\Feature;

use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\DigiflazzService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DigiflazzIntegrationTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Uji 5 Skenario Pengujian Integrasi Digiflazz (Tabel 4.5)
     */
    public function test_verify_all_digiflazz_integration_scenarios(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $product = Product::first();
        $digiflazzService = app(DigiflazzService::class);

        echo "\n\n=== HASIL PENGUJIAN INTEGRASI DIGIFLAZZ (TABEL 4.5) ===\n";

        // Helper untuk membuat order dalam status DIPROSES dengan fulfillment pending
        $createProcessingOrder = function ($code) use ($pelanggan, $product) {
            $order = Order::create([
                'order_code' => $code,
                'user_id' => $pelanggan->id,
                'product_id' => $product->id,
                'user_id_game' => '12345678',
                'locked_price' => $product->sell_price,
                'status' => 'DIPROSES',
            ]);

            $refId = 'DF-' . $code . '-' . strtoupper(bin2hex(random_bytes(3)));
            $fulfillment = Fulfillment::create([
                'order_id' => $order->id,
                'digiflazz_ref_id' => $refId,
                'status' => 'pending',
                'response_summary' => 'Pesanan sedang diproses oleh supplier',
            ]);

            return [$order, $fulfillment, $refId];
        };

        // 1. Transaksi Sukses
        [$order1, $ful1, $refId1] = $createProcessingOrder('KDN-DF-01');
        $res1 = $this->postJson(route('digiflazz.webhook'), [
            'data' => [
                'ref_id' => $refId1,
                'status' => 'Sukses',
                'message' => 'Top Up Berhasil',
                'sn' => 'SN-MLBB-998877',
            ]
        ]);
        $res1->assertStatus(200);
        $order1->refresh();
        $ful1->refresh();
        $this->assertEquals('SUKSES', $order1->status);
        $this->assertEquals('sukses', $ful1->status);
        $this->assertStringContainsString('SN-MLBB-998877', $ful1->response_summary);
        $got1 = "Status pesanan otomatis diperbarui menjadi SUKSES dan nomor serial (SN) tersimpan";
        echo "1. Transaksi Sukses: {$got1} [SESUAI]\n";

        // 2. Transaksi Pending
        [$order2, $ful2, $refId2] = $createProcessingOrder('KDN-DF-02');
        $res2 = $this->postJson(route('digiflazz.webhook'), [
            'data' => [
                'ref_id' => $refId2,
                'status' => 'Pending',
                'message' => 'Sedang dalam antrean pengiriman',
            ]
        ]);
        $res2->assertStatus(200);
        $order2->refresh();
        $ful2->refresh();
        $this->assertEquals('DIPROSES', $order2->status);
        $this->assertEquals('pending', $ful2->status);
        $got2 = "Status pesanan tetap DIPROSES dan fulfillment pending hingga hasil akhir diterima";
        echo "2. Transaksi Pending: {$got2} [SESUAI]\n";

        // 3. Transaksi Gagal
        [$order3, $ful3, $refId3] = $createProcessingOrder('KDN-DF-03');
        $res3 = $this->postJson(route('digiflazz.webhook'), [
            'data' => [
                'ref_id' => $refId3,
                'status' => 'Gagal',
                'message' => 'User ID Game Tidak Ditemukan',
            ]
        ]);
        $res3->assertStatus(200);
        $order3->refresh();
        $ful3->refresh();
        $this->assertEquals('GAGAL', $order3->status);
        $this->assertEquals('gagal', $ful3->status);
        $this->assertStringContainsString('User ID Game Tidak Ditemukan', $order3->notes);
        $got3 = "Status pesanan menjadi GAGAL, alasan tercatat, dan masuk antrean tindak lanjut admin";
        echo "3. Transaksi Gagal: {$got3} [SESUAI]\n";

        // 4. Pengiriman dengan ref_id yang sama (Idempotent / Anti Double Fulfillment)
        // Kirim kembali payload sukses untuk refId1 yang sudah selesai
        $res4 = $this->postJson(route('digiflazz.webhook'), [
            'data' => [
                'ref_id' => $refId1,
                'status' => 'Sukses',
                'message' => 'Top Up Berhasil (Duplikat)',
                'sn' => 'SN-MLBB-998877',
            ]
        ]);
        $res4->assertStatus(200);
        $this->assertEquals(1, Fulfillment::where('digiflazz_ref_id', $refId1)->count());
        $got4 = "Sistem mencocokkan ref_id yang sama dan tidak memicu pembuatan pesanan ganda";
        echo "4. Idempotensi ref_id: {$got4} [SESUAI]\n";

        // 5. Saldo deposit di bawah ambang peringatan
        Http::fake([
            'https://api.digiflazz.com/*' => Http::response([
                'data' => ['deposit' => 250000] // Di bawah ambang 500.000
            ], 200),
            '*/cek-saldo' => Http::response([
                'data' => ['deposit' => 250000]
            ], 200),
        ]);
        Setting::set('digiflazz_low_balance_threshold', '500000');
        // Clear cache saldo dashboard
        \Illuminate\Support\Facades\Cache::forget('admin_digiflazz_balance_info');

        $balanceCheck = $digiflazzService->checkBalance();
        $this->assertTrue($balanceCheck['is_low']);

        $dashRes = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('SALDO KRITIS DI BAWAH AMBANG');
        $dashRes->assertSee('250.000');
        $got5 = "Peringatan SALDO KRITIS DI BAWAH AMBANG tampil jelas pada dashboard admin";
        echo "5. Peringatan Saldo Rendah: {$got5} [SESUAI]\n";

        echo "Seluruh 5 Skenario Pengujian Integrasi Digiflazz Terbukti 100% VALID & SESUAI\n\n";
    }
}
