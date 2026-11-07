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
        $response->assertSee('Panduan Pengisian ID Game');
        $response->assertSee('Mobile Legends');
        $response->assertSee('Free Fire');
    }
}
