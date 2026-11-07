<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Banner;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BannerAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_view_banner_management_page()
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.banners.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Banner &amp; Promo', false);
        $response->assertSee('Tambah Banner Baru');
    }

    public function test_customer_cannot_access_banner_management()
    {
        $customer = User::where('email', 'pelanggan@kaiden.id')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get(route('admin.banners.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_new_banner()
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Promo MLBB Super Deals 2026',
            'subtitle' => 'Diskon instan diamond Mobile Legends untuk semua server.',
            'badge_text' => 'SUPER DEALS MLBB',
            'discount_tag' => 'DISKON!',
            'discount_value' => '30%',
            'discount_subtext' => 'Khusus Minggu Ini',
            'button_text' => 'Beli Sekarang',
            'button_url' => '/game/mobile-legends',
            'preset_image' => 'images/banners/banner_valorant.jpg',
            'banner_type' => 'slider',
            'gradient_theme' => 'crimson',
            'sort_order' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'title' => 'Promo MLBB Super Deals 2026',
            'discount_value' => '30%',
            'gradient_theme' => 'crimson',
        ]);

        // Verifikasi AuditLog tercatat
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'banner.tambah',
            'target_type' => 'Banner',
        ]);
    }

    public function test_admin_can_update_banner()
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $banner = Banner::first();
        $this->assertNotNull($banner);

        $response = $this->actingAs($admin)->put(route('admin.banners.update', $banner), [
            'title' => 'Judul Promo Diperbarui',
            'subtitle' => $banner->subtitle,
            'badge_text' => $banner->badge_text,
            'discount_tag' => $banner->discount_tag,
            'discount_value' => '50 RB',
            'discount_subtext' => $banner->discount_subtext,
            'button_text' => 'Cek Promo',
            'button_url' => $banner->button_url,
            'preset_image' => $banner->image,
            'banner_type' => $banner->banner_type,
            'gradient_theme' => 'slate',
            'sort_order' => $banner->sort_order,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'title' => 'Judul Promo Diperbarui',
            'discount_value' => '50 RB',
            'gradient_theme' => 'slate',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'banner.ubah',
            'target_type' => 'Banner',
            'target_id' => (string)$banner->id,
        ]);
    }

    public function test_admin_can_toggle_banner_status()
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $banner = Banner::first();
        $initialStatus = $banner->is_active;

        $response = $this->actingAs($admin)->post(route('admin.banners.toggle', $banner));

        $response->assertRedirect();
        $banner->refresh();
        $this->assertEquals(!$initialStatus, $banner->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'banner.toggle_status',
            'target_type' => 'Banner',
            'target_id' => (string)$banner->id,
        ]);
    }

    public function test_admin_can_delete_banner()
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $banner = Banner::create([
            'title' => 'Banner Sementara Untuk Hapus',
            'button_text' => 'Klik',
            'button_url' => '#',
            'banner_type' => 'slider',
            'gradient_theme' => 'amber',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.banners.destroy', $banner));

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseMissing('banners', [
            'id' => $banner->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'banner.hapus',
            'target_type' => 'Banner',
        ]);
    }

    public function test_homepage_renders_active_banners_from_database()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Top Up Diamond &amp; Voucher Instan 24 Jam Nonstop', false);
        $response->assertSee('Flash Promo Mobile Legends');
    }
}

