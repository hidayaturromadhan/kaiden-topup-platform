<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_catalog_games_page_renders_successfully()
    {
        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Produk &amp; Top Up Game', false);
        $response->assertSee('Koleksi Game', false);
        $response->assertSee('Mobile Legends');
    }

    public function test_catalog_products_tab_renders_successfully()
    {
        $response = $this->get(route('catalog.index', ['tab' => 'products']));

        $response->assertStatus(200);
        $response->assertSee('Semua Paket Item', false);
        $response->assertSee('Beli', false);
    }

    public function test_catalog_search_filters_results()
    {
        $response = $this->get(route('catalog.index', ['q' => 'Valorant']));

        $response->assertStatus(200);
        $response->assertSee('Valorant');
    }

    public function test_order_page_accepts_product_id_parameter()
    {
        $game = Game::where('slug', 'mobile-legends')->first();
        $this->assertNotNull($game);
        $product = $game->products->first();
        $this->assertNotNull($product);

        $response = $this->get(route('order.game', [
            'slug' => $game->slug,
            'product_id' => $product->id
        ]));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('QRIS');
    }

    public function test_guide_page_renders_successfully()
    {
        $response = $this->get(route('guide.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Pengisian ID');
        $response->assertSee('Mobile Legends');
        $response->assertSee('Free Fire');
    }

    public function test_guide_page_excludes_games_without_active_products()
    {
        // 1. Buat game tanpa produk aktif
        Game::create([
            'name' => 'Demo Game Kosong',
            'slug' => 'demo-game-kosong',
            'category' => 'Game',
            'publisher' => 'Demo',
            'status' => 'aktif',
            'guide_text' => 'Masukkan ID Anda.',
        ]);

        // 2. Buat produk non-game (seperti Indosat / Pertamina Gas) meskipun memiliki produk aktif
        $telcoGame = Game::create([
            'name' => 'Indosat Ooredoo',
            'slug' => 'indosat',
            'category' => 'Pulsa & Data',
            'publisher' => 'Indosat',
            'status' => 'aktif',
            'guide_text' => 'Masukkan nomor telepon.',
        ]);
        Product::create([
            'game_id' => $telcoGame->id,
            'name' => 'Pulsa 10.000',
            'digiflazz_sku' => 'IND10K',
            'cost_price' => 10000,
            'sell_price' => 11000,
            'original_price' => 11000,
            'is_active' => true,
        ]);

        \Illuminate\Support\Facades\Cache::forget('guide_active_games_v2');

        $response = $this->get(route('guide.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Demo Game Kosong');
        $response->assertDontSee('Indosat Ooredoo');
    }

    public function test_homepage_and_catalog_render_real_sales_and_feedback_without_fake_data()
    {
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);

        // Pastikan TIDAK ADA data fake hardcode yang dulu muncul
        $homeResponse->assertDontSee('4.8k terjual');
        $homeResponse->assertDontSee('3.5k terjual');
        $homeResponse->assertDontSee('2.1k terjual');
        $homeResponse->assertDontSee('1.2k terjual');
        $homeResponse->assertDontSee('840 terjual');
        $homeResponse->assertDontSee('10500.00');
        $homeResponse->assertDontSee('2648.00');

        // Pastikan format harga menggunakan standar Rupiah
        $homeResponse->assertSee('Rp');

        // Pastikan di katalog juga tidak ada data penjualan fake
        $catalogResponse = $this->get(route('catalog.index'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertDontSee('4.8k terjual');
        $catalogResponse->assertDontSee('3.5k terjual');
    }
}
