<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\DigiflazzService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $customer;
    protected Game $game;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->superAdmin = User::where('email', 'superadmin@kaiden.id')->first();
        $this->customer = User::where('email', 'pelanggan@kaiden.id')->first();
        $this->game = Game::where('slug', 'mobile-legends')->first();
        $this->product = Product::where('digiflazz_sku', 'MLBB_PASS')->first();
    }

    /**
     * Admin dapat melihat daftar produk beserta metrik margin dan proteksi anti-rugi
     */
    public function test_admin_can_view_products_and_margin_stats(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Proteksi Margin Harga');
        $response->assertSee('Harga Modal (DF)');
        $response->assertSee('Harga Normal (Coret)');
        $response->assertSee('Diskon Promo');
        $response->assertSee('Margin Untung Bersih');
    }

    /**
     * Proteksi Anti-Rugi: Admin DITOLAK saat mencoba menyetel harga jual di bawah modal Digiflazz
     */
    public function test_admin_cannot_set_sell_price_lower_than_cost_price(): void
    {
        // Modal saat ini: 27.500
        $this->assertEquals(27500, $this->product->cost_price);

        // Coba set harga jual rugi Rp 20.000 (di bawah modal 27.500)
        $response = $this->actingAs($this->superAdmin)->patch(
            route('admin.products.update_price', $this->product->id),
            [
                'cost_price' => 27500,
                'sell_price' => 20000,
                'original_price' => 30000,
            ]
        );

        $response->assertSessionHasErrors(['sell_price']);
        $this->product->refresh();
        $this->assertNotEquals(20000, $this->product->sell_price);
    }

    /**
     * Validasi Logika Diskon: Harga normal coret tidak boleh lebih murah dari harga promo jual
     */
    public function test_admin_cannot_set_original_price_lower_than_sell_price(): void
    {
        // Coba set original_price 25.000 tapi sell_price 29.900 (tidak masuk akal)
        $response = $this->actingAs($this->superAdmin)->patch(
            route('admin.products.update_price', $this->product->id),
            [
                'sell_price' => 29900,
                'original_price' => 25000,
            ]
        );

        $response->assertSessionHasErrors(['original_price']);
        $this->product->refresh();
        $this->assertNotEquals(25000, $this->product->original_price);
    }

    /**
     * Sukses Mengubah Harga Promo & Diskon dengan Margin Sehat Serta Tercatat di Audit Log
     */
    public function test_admin_can_update_price_and_discount_with_safe_margin(): void
    {
        // Modal: 27.500. Set sell_price 31.000, original_price 36.000.
        // Diskon: (36.000 - 31.000)/36.000 = 13.88% -> 14%
        // Margin: 31.000 - 27.500 = +3.500 (+12.7%)
        $response = $this->actingAs($this->superAdmin)->patch(
            route('admin.products.update_price', $this->product->id),
            [
                'sell_price' => 31000,
                'original_price' => 36000,
            ]
        );

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->product->refresh();
        $this->assertEquals(31000, $this->product->sell_price);
        $this->assertEquals(36000, $this->product->original_price);
        $this->assertTrue($this->product->has_discount);
        $this->assertEquals(14, $this->product->discount_percent);
        $this->assertEquals(3500, $this->product->margin);
        $this->assertEquals(12.7, $this->product->margin_percent);
        $this->assertEquals('healthy', $this->product->margin_status['level']);

        // Pastikan Audit Log tercatat (RULES.md Section 4.C.2)
        $audit = AuditLog::where('action', 'harga.ubah')
            ->where('target_id', $this->product->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals(31000, $audit->new_values['sell_price']);
    }

    /**
     * Pelanggan melihat harga diskon dan checkout dengan harga promo yang terkunci
     */
    public function test_customer_sees_discount_and_locks_promo_price_on_checkout(): void
    {
        // Set produk promo
        $this->product->update([
            'cost_price' => 20000,
            'original_price' => 30000,
            'sell_price' => 25000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('order.game', $this->game->slug));
        $response->assertStatus(200);
        $response->assertSee('-17%'); // (30000 - 25000) / 30000 = 16.6% -> 17%

        // Mock Midtrans API Snap
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'dummy-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-snap-token-12345',
            ], 201),
        ]);

        // Checkout
        $checkoutResponse = $this->actingAs($this->customer)->post(route('order.checkout'), [
            'product_id' => $this->product->id,
            'user_id_game' => '12345678',
            'zone_id' => '2001',
            'manual_confirmation' => 1,
        ]);

        $order = Order::latest('id')->first();
        $this->assertNotNull($order);
        // Terkunci pada harga promo 25.000
        $this->assertEquals(25000, $order->locked_price);
    }

    /**
     * Proteksi Otomatis Sinkronisasi Digiflazz: Jika supplier menaikkan modal melebihi harga jual,
     * sistem secara otomatis menaikkan harga jual dengan margin aman agar tidak boncos.
     */
    public function test_sync_digiflazz_auto_raises_sell_price_when_cost_increases(): void
    {
        // Kondisi awal: cost 27.500, sell 29.900
        $this->product->update([
            'cost_price' => 27500,
            'sell_price' => 29900,
            'original_price' => 35000,
        ]);

        // Mock Digiflazz price-list API di mana modal naik drastis menjadi 35.000 (melebihi sell_price 29.900)
        Http::fake([
            '*/price-list' => Http::response([
                'data' => [
                    [
                        'buyer_sku_code' => 'MLBB_PASS',
                        'product_name' => 'Weekly Diamond Pass',
                        'category' => 'Games',
                        'brand' => 'MOBILE LEGENDS',
                        'type' => 'Umum',
                        'seller_name' => 'Supplier A',
                        'price' => 35000, // MODAL NAIK MENJADI 35.000!
                        'buyer_product_status' => true,
                        'seller_product_status' => true,
                        'unlimited_stock' => true,
                        'stock' => 9999,
                        'multi' => true,
                        'start_cut_off' => '23:50',
                        'end_cut_off' => '00:10',
                        'desc' => 'Weekly Diamond Pass',
                    ]
                ]
            ], 200),
        ]);

        $service = app(DigiflazzService::class);
        $result = $service->syncProducts();

        $this->assertTrue($result['success']);
        $this->product->refresh();

        // Modal baru harus 35.000
        $this->assertEquals(35000, $this->product->cost_price);

        // Harga jual HARUS otomatis dinaikkan di atas modal baru (tidak boleh rugi)
        $this->assertGreaterThanOrEqual(35000, $this->product->sell_price);
        $this->assertGreaterThan(0, $this->product->margin);
        $this->assertGreaterThanOrEqual(35000, $this->product->original_price);
    }
}

