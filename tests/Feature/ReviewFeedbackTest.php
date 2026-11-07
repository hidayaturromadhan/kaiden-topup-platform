<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_nickname_masking_helper_matches_gamerabah_format()
    {
        $this->assertEquals('ga******bah', Order::maskNickname('gamerabah'));
        $this->assertEquals('su******ard', Order::maskNickname('sultan_alucard'));
        $this->assertEquals('al******ard', Order::maskNickname('alucard'));
        $this->assertEquals('da******t', Order::maskNickname('dayat'));
        $this->assertEquals('bu******i', Order::maskNickname('budi'));
    }

    public function test_customer_can_submit_review_after_order_is_completed()
    {
        $customer = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-REVIEW-1',
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'confirmed_nickname' => 'gamerabah',
            'locked_price' => $product->sell_price,
            'status' => 'SUKSES',
        ]);

        $response = $this->actingAs($customer)->post(route('orders.review.store', $order->order_code), [
            'rating' => 5,
            'comment' => 'Proses cepat banget, diamond langsung masuk < 1 menit!',
            'is_anonymous' => 0,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Proses cepat banget, diamond langsung masuk < 1 menit!',
        ]);

        // Customer viewing order detail sees their review
        $detailResponse = $this->actingAs($customer)->get(route('order.show', $order->order_code));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Sudah Diulas');
        $detailResponse->assertSee('Proses cepat banget, diamond langsung masuk');
    }

    public function test_customer_cannot_submit_review_for_unpaid_order()
    {
        $customer = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-REVIEW-2',
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'confirmed_nickname' => 'gamerabah',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        $response = $this->actingAs($customer)->post(route('orders.review.store', $order->order_code), [
            'rating' => 5,
            'comment' => 'Belum bayar tapi mau review',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', ['order_id' => $order->id]);
    }

    public function test_other_customer_cannot_review_someone_else_order()
    {
        $owner = User::where('email', 'pelanggan@kaiden.id')->first();
        $otherCustomer = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'other_customer@kaiden.id',
            'password' => bcrypt('password'),
            'status' => 'aktif',
        ]);
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-REVIEW-3',
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'confirmed_nickname' => 'gamerabah',
            'locked_price' => $product->sell_price,
            'status' => 'SUKSES',
        ]);

        $response = $this->actingAs($otherCustomer)->post(route('orders.review.store', $order->order_code), [
            'rating' => 1,
            'comment' => 'Review bajakan',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', ['order_id' => $order->id]);
    }

    public function test_homepage_renders_live_testimonial_popup_with_masked_nickname()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('live-testimonial-popup');
        $response->assertSee('ga******bah');
    }
}
