<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\DigiflazzService;
use App\Services\MidtransService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * UJI 1: Webhook Midtrans dengan Signature Valid & Idempotensi Callback Ganda
     */
    public function test_midtrans_webhook_signature_verification_and_idempotency(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        // Buat order berstatus MENUNGGU_BAYAR
        $order = Order::create([
            'order_code' => 'KDN-WEBHOOK-01',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        $midtransService = app(MidtransService::class);
        $payment = $midtransService->createSnapTransaction($order);

        $orderId = $payment->midtrans_order_id;
        $statusCode = '200';
        $grossAmount = (string)$order->locked_price;
        $serverKey = $midtransService->getServerKey();

        // 1. Signature Palsu / Salah => Wajib ditolak 403
        $fakePayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => 'INVALID_SIGNATURE_KEY',
            'transaction_status' => 'settlement',
        ];

        $this->postJson(route('midtrans.notification'), $fakePayload)
            ->assertStatus(403);

        $this->assertEquals('MENUNGGU_BAYAR', $order->fresh()->status);

        // 2. Signature Sah => Status Menjadi DIBAYAR / SUKSES
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $validPayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ];

        $response = $this->postJson(route('midtrans.notification'), $validPayload);
        $response->assertStatus(200);

        $order->refresh();
        $this->assertContains($order->status, ['DIBAYAR', 'DIPROSES', 'SUKSES']);
        $this->assertEquals('settlement', $order->payment->status);
        $this->assertNotNull($order->payment->paid_at);

        // 3. Callback Ganda (Idempotency Test Sesuai RULES.md Section 5.B.3)
        // Mengirim kembali payload yang sama tidak boleh menyebabkan error atau fulfillment ganda
        $duplicateResponse = $this->postJson(route('midtrans.notification'), $validPayload);
        $duplicateResponse->assertStatus(200);
        $this->assertEquals('ignored', $duplicateResponse->json('status'));
    }

    /**
     * UJI 2: Pengecekan IGN (GoPay Games Provider via GameCheckService) & Cache
     */
    public function test_game_check_service_and_cache(): void
    {
        $response = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('nickname'));

        // Cek kedua wajib mengambil dari Cache
        $cachedResponse = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);

        $cachedResponse->assertStatus(200);
        $this->assertTrue($cachedResponse->json('cached'));
    }

    /**
     * UJI 3: Feature Flag IGN Checker Dinonaktifkan (RULES.md Section 5.A.3)
     */
    public function test_ign_checker_feature_flag_disabled_fallback(): void
    {
        // Matikan feature flag
        Setting::set('ign_checker_enabled', 'false');

        $response = $this->postJson(route('order.check_ign'), [
            'game_code' => 'MOBILE_LEGENDS',
            'user_id' => '12345678',
            'zone_id' => '2001',
        ]);

        $response->assertStatus(200);
        $this->assertFalse($response->json('success'));
        $this->assertTrue($response->json('feature_disabled'));

        // Pastikan pelanggan tetap bisa checkout dengan mencentang konfirmasi manual
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $checkoutResponse = $this->actingAs($pelanggan)->post(route('order.checkout'), [
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'zone_id' => '2001',
            'manual_confirmation' => 1,
        ]);

        $checkoutResponse->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $pelanggan->id,
            'user_id_game' => '12345678',
            'status' => 'MENUNGGU_BAYAR',
        ]);
    }

    /**
     * UJI 4: Pemantauan Saldo Deposit Digiflazz & Ambang Peringatan Rendah
     */
    public function test_digiflazz_balance_monitoring_and_threshold(): void
    {
        $digiflazzService = app(DigiflazzService::class);

        // Mock endpoint cek-saldo agar pengujian terisolasi & deterministik
        \Illuminate\Support\Facades\Http::fake([
            '*/cek-saldo' => \Illuminate\Support\Facades\Http::sequence()
                ->push(['data' => ['deposit' => 2750000]], 200)
                ->push(['data' => ['deposit' => 150000]], 200),
        ]);

        Setting::set('digiflazz_low_balance_threshold', '500000');

        $resultSafe = $digiflazzService->checkBalance();
        $this->assertTrue($resultSafe['success']);
        $this->assertFalse($resultSafe['is_low']);

        // Uji kondisi saldo kritis (150.000 < threshold 500.000)
        $resultCritical = $digiflazzService->checkBalance();
        $this->assertTrue($resultCritical['is_low']);
    }

    /**
     * UJI 5: Ekspor Laporan CSV Mencatat Audit Log
     */
    public function test_report_csv_export_generates_file_and_records_audit_log(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.export', [
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));

        // Verifikasi entri AuditLog
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'laporan.ekspor',
        ]);
    }

    /**
     * UJI 6: Webhook Digiflazz untuk Pembaruan Status Pemenuhan (FR-FUL-2)
     */
    public function test_digiflazz_webhook_fulfillment_update(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        // 1. Buat pesanan dengan fulfillment pending
        $order = Order::create([
            'order_code' => 'KDN-DF-WH-01',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '99887766',
            'locked_price' => $product->sell_price,
            'status' => 'DIPROSES',
        ]);

        $refId = 'DF-TEST-REF-' . time();
        $fulfillment = \App\Models\Fulfillment::create([
            'order_id' => $order->id,
            'digiflazz_ref_id' => $refId,
            'status' => 'pending',
            'response_summary' => 'Menunggu proses Digiflazz',
        ]);

        // 2. Kirim Webhook Sukses dari Digiflazz
        $webhookPayload = [
            'data' => [
                'ref_id' => $refId,
                'status' => 'Sukses',
                'message' => 'Top up diamond berhasil dikirim',
                'sn' => 'SN-9988112233',
                'buyer_sku_code' => $product->digiflazz_sku,
                'customer_no' => '99887766',
            ]
        ];

        $response = $this->postJson(route('digiflazz.webhook'), $webhookPayload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $order->refresh();
        $fulfillment->refresh();

        $this->assertEquals('SUKSES', $order->status);
        $this->assertEquals('sukses', $fulfillment->status);
        $this->assertStringContainsString('SN-9988112233', $fulfillment->response_summary);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'digiflazz.webhook_sukses',
            'target_id' => $order->id,
        ]);
    }
}

