<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Jalankan seeder lengkap agar seluruh matriks RBAC dan produk terpasang
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * UJI 1: Pelanggan hanya bisa melihat transaksinya sendiri (Anti-IDOR)
     */
    public function test_customer_can_view_own_order_but_forbidden_from_other_customer_order(): void
    {
        $pelanggan1 = User::where('email', 'pelanggan@kaiden.id')->first();

        // Buat Pelanggan 2
        $rolePelanggan = Role::where('name', 'pelanggan')->first();
        $pelanggan2 = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'pelanggan2@kaiden.id',
            'password' => bcrypt('Password123!'),
            'status' => 'aktif',
        ]);
        $pelanggan2->roles()->sync([$rolePelanggan->id]);

        $product = Product::first();

        // Order milik Pelanggan 1
        $order1 = Order::create([
            'order_code' => 'KDN-TEST-001',
            'user_id' => $pelanggan1->id,
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        // Order milik Pelanggan 2
        $order2 = Order::create([
            'order_code' => 'KDN-TEST-002',
            'user_id' => $pelanggan2->id,
            'product_id' => $product->id,
            'user_id_game' => '87654321',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        // Pelanggan 1 mengakses order miliknya sendiri => SUKSES (200)
        $this->actingAs($pelanggan1)
            ->get(route('order.show', $order1->order_code))
            ->assertStatus(200)
            ->assertSee($order1->order_code);

        // Pelanggan 1 mencoba mengakses order milik Pelanggan 2 (IDOR Attack) => DITOLAK (403)
        $this->actingAs($pelanggan1)
            ->get(route('order.show', $order2->order_code))
            ->assertStatus(403)
            ->assertSee('Akses Ditolak');
    }

    /**
     * UJI 2: Admin dapat melihat order semua pelanggan
     */
    public function test_admin_can_view_any_order(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $product = Product::first();

        $order = Order::create([
            'order_code' => 'KDN-TEST-003',
            'user_id' => $pelanggan->id,
            'product_id' => $product->id,
            'user_id_game' => '99999999',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        $this->actingAs($admin)
            ->get(route('order.show', $order->order_code))
            ->assertStatus(200);
    }

    /**
     * UJI 3: Pencegahan Forced Browsing ke Halaman Admin oleh Pelanggan
     */
    public function test_customer_cannot_access_admin_dashboard_or_settings(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();

        // Coba akses Dashboard Admin
        $this->actingAs($pelanggan)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);

        // Coba akses Kelola Pengguna Superadmin
        $this->actingAs($pelanggan)
            ->get(route('superadmin.users.index'))
            ->assertStatus(403);

        // Coba akses Kredensial API
        $this->actingAs($pelanggan)
            ->get(route('superadmin.settings.index'))
            ->assertStatus(403);
    }

    /**
     * UJI 4: Admin Tidak Memiliki Hak Akses Fitur Superadmin (Least Privilege)
     */
    public function test_admin_cannot_access_superadmin_features(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();

        // Admin dilarang mengelola pengguna internal
        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertStatus(403);

        // Admin dilarang mengubah konfigurasi API dan kunci
        $this->actingAs($admin)
            ->get(route('superadmin.settings.index'))
            ->assertStatus(403);

        // Admin dilarang melihat Audit Log
        $this->actingAs($admin)
            ->get(route('superadmin.audit_logs.index'))
            ->assertStatus(403);
    }

    /**
     * UJI 5: Admin & Superadmin DILARANG membuat pesanan langsung (RULES.md Section 4.A)
     */
    public function test_admin_and_superadmin_cannot_create_orders(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $superadmin = User::where('email', 'superadmin@kaiden.id')->first();
        $product = Product::first();

        $payload = [
            'product_id' => $product->id,
            'user_id_game' => '12345678',
            'manual_confirmation' => 1,
        ];

        // Admin order => Ditolak 403
        $this->actingAs($admin)
            ->post(route('order.checkout'), $payload)
            ->assertStatus(403);

        // Superadmin order => Ditolak 403
        $this->actingAs($superadmin)
            ->post(route('order.checkout'), $payload)
            ->assertStatus(403);
    }

    /**
     * UJI 6: Pencegahan Eskalasi Peran / Mass Assignment pada Registrasi Publik
     */
    public function test_public_registration_cannot_escalate_role_to_admin_or_superadmin(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Hacker Wannabe',
            'email' => 'hacker@kaiden.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'superadmin',
            'roles' => ['superadmin', 'admin'],
        ]);

        $response->assertRedirect(route('home'));

        $user = User::where('email', 'hacker@kaiden.id')->first();
        $this->assertNotNull($user);

        // Pastikan hanya memiliki role 'pelanggan'
        $this->assertTrue($user->hasRole('pelanggan'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('superadmin'));
    }

    /**
     * UJI 7: Model AuditLog Bersifat Append-Only & Melarang Update/Delete
     */
    public function test_audit_log_is_immutable_and_append_only(): void
    {
        $log = AuditLog::create([
            'action' => 'test.aksi',
            'target_type' => 'Test',
            'target_id' => '1',
            'ip_address' => '127.0.0.1',
        ]);

        // Coba Update => Wajib melempar RuntimeException
        try {
            $log->update(['action' => 'modifikasi_ilegal']);
            $this->fail('AuditLog seharusnya tidak dapat diubah (update).');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('append-only', $e->getMessage());
        }

        // Coba Delete => Wajib melempar RuntimeException
        try {
            $log->delete();
            $this->fail('AuditLog seharusnya tidak dapat dihapus (delete).');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('append-only', $e->getMessage());
        }
    }

    /**
     * UJI 8: Perubahan Harga Jual Wajib Mencatat Audit Log dengan Old dan New Values
     */
    public function test_price_change_records_audit_log_with_values(): void
    {
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $product = Product::first();

        $oldPrice = $product->sell_price;
        $newPrice = $oldPrice + 5000;

        $this->actingAs($admin)
            ->patch(route('admin.products.update_price', $product->id), [
                'sell_price' => $newPrice,
            ])
            ->assertRedirect();

        $product->refresh();
        $this->assertEquals($newPrice, $product->sell_price);

        // Verifikasi entri AuditLog tercatat
        $audit = AuditLog::where('action', 'harga.ubah')
            ->where('target_id', (string)$product->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals($admin->id, $audit->actor_id);
        $this->assertEquals($oldPrice, $audit->old_values['sell_price']);
        $this->assertEquals($newPrice, $audit->new_values['sell_price']);
    }
}

