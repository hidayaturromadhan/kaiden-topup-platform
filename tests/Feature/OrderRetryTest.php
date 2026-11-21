<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderRetryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_retry_failed_digiflazz_order_manually(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        // 1. Buat pesanan yang gagal pemenuhan
        $order = Order::create([
            'order_code' => 'KDN-TEST-RETRY-01',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '312222594949',
            'locked_price' => $product->sell_price,
            'status' => 'GAGAL',
            'notes' => '[DIGIFLAZZ GAGAL]: Cut Off (Perbaikan Sistem Seller)',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'KDN-TEST-RETRY-01-PAY',
            'amount' => $product->sell_price,
            'status' => 'settlement',
            'payment_method' => 'qris',
            'paid_at' => now(),
        ]);

        Fulfillment::create([
            'order_id' => $order->id,
            'digiflazz_ref_id' => 'DF-KDN-TEST-01',
            'status' => 'gagal',
            'response_summary' => 'Cut Off (Perbaikan Sistem Seller)',
        ]);

        // Mock respon Digiflazz sukses saat di-retry
        Http::fake([
            '*/transaction' => Http::response([
                'data' => [
                    'ref_id' => 'DF-KDN-TEST-RETRY-NEW',
                    'customer_no' => '312222594949',
                    'buyer_sku_code' => $product->digiflazz_sku,
                    'status' => 'Sukses',
                    'message' => 'Transaksi Sukses',
                    'sn' => 'SN9988776655',
                    'rc' => '00',
                ]
            ], 200),
        ]);

        // Eksekusi retry oleh admin
        $response = $this->actingAs($admin)->post(route('admin.orders.retry', $order->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'SUKSES',
        ]);

        $this->assertDatabaseHas('fulfillments', [
            'order_id' => $order->id,
            'status' => 'sukses',
        ]);

        // Verifikasi tercatat di audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'transaksi.retry_fulfillment',
            'target_type' => 'Order',
            'target_id' => $order->id,
            'actor_id' => $admin->id,
        ]);
    }

    public function test_cannot_retry_order_that_is_already_sukses(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-RETRY-02',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '312222594949',
            'locked_price' => $product->sell_price,
            'status' => 'SUKSES',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.retry', $order->id));

        $response->assertSessionHas('error');
    }

    public function test_customer_cannot_call_retry_route(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-RETRY-03',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '312222594949',
            'locked_price' => $product->sell_price,
            'status' => 'GAGAL',
        ]);

        $response = $this->actingAs($pelanggan)->post(route('admin.orders.retry', $order->id));

        $response->assertStatus(403);
    }
}
