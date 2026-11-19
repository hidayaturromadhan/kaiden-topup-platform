<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthorizedAccessTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Uji 6 Skenario Pengujian Akses Tidak Sah (Tabel 4.3)
     */
    public function test_verify_all_unauthorized_access_scenarios(): void
    {
        $pelanggan1 = User::where('email', 'pelanggan@kaiden.id')->first();
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $superadmin = User::where('email', 'superadmin@kaiden.id')->first();
        $product = Product::first();

        // Buat Pelanggan 2 untuk uji IDOR
        $rolePelanggan = Role::where('name', 'pelanggan')->first();
        $pelanggan2 = User::create([
            'name' => 'Pelanggan Korban',
            'email' => 'korban@kaiden.id',
            'password' => bcrypt('Password123!'),
            'status' => 'aktif',
        ]);
        $pelanggan2->roles()->sync([$rolePelanggan->id]);

        $orderPelanggan2 = Order::create([
            'order_code' => 'KDN-VICTIM-001',
            'user_id' => $pelanggan2->id,
            'product_id' => $product->id,
            'user_id_game' => '11223344',
            'locked_price' => $product->sell_price,
            'status' => 'MENUNGGU_BAYAR',
        ]);

        echo "\n\n=== HASIL PENGUJIAN AKSES TIDAK SAH (TABEL 4.3) ===\n";

        // 1. Pelanggan membuka transaksi pelanggan lain melalui URL (IDOR)
        $res1 = $this->actingAs($pelanggan1)->get(route('order.show', $orderPelanggan2->order_code));
        $this->assertEquals(403, $res1->status());
        $got1 = "Ditolak (HTTP 403 Akses Ditolak)";
        echo "1. IDOR Pelanggan: {$got1} [SESUAI]\n";

        // 2. Pelanggan membuka halaman admin (Forced Browsing)
        $res2 = $this->actingAs($pelanggan1)->get(route('admin.dashboard'));
        $this->assertEquals(403, $res2->status());
        $got2 = "Ditolak (HTTP 403 Akses Ditolak)";
        echo "2. Forced Browsing Admin: {$got2} [SESUAI]\n";

        // 3. Admin membuka halaman pengelolaan pengguna milik superadmin
        $res3 = $this->actingAs($admin)->get(route('superadmin.users.index'));
        $this->assertEquals(403, $res3->status());
        $got3 = "Ditolak (HTTP 403 Akses Ditolak)";
        echo "3. Privilege Escalation Admin: {$got3} [SESUAI]\n";

        // Logout untuk mengembalikan status guest sebelum menguji registrasi
        auth()->logout();

        // 4. Menaikkan peran melalui input pada registrasi (Mass Assignment / Role Tampering)
        $res4 = $this->post(route('register.post'), [
            'name' => 'Attacker',
            'email' => 'attacker@kaiden.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'superadmin',
            'roles' => ['superadmin', 'admin'],
        ]);
        $attacker = User::where('email', 'attacker@kaiden.id')->first();
        $this->assertNotNull($attacker);
        $this->assertTrue($attacker->hasRole('pelanggan'));
        $this->assertFalse($attacker->hasRole('admin'));
        $this->assertFalse($attacker->hasRole('superadmin'));
        $got4 = "Parameter diabaikan, peran tetap 'pelanggan'";
        echo "4. Mass Assignment Role: {$got4} [SESUAI]\n";

        // 5. Memanipulasi parameter ID pesanan pada permintaan (Fake / Manipulated ID)
        $res5 = $this->actingAs($pelanggan1)->get('/orders/KDN-MANIPULATED-999999');
        $this->assertEquals(404, $res5->status());
        $got5 = "Ditolak (HTTP 404 Tidak Ditemukan)";
        echo "5. Parameter Tampering: {$got5} [SESUAI]\n";

        // Logout kembali untuk menguji akses halaman terproteksi sebagai guest
        auth()->logout();

        // 6. Mengakses halaman terproteksi tanpa login (Guest Access)
        $res6 = $this->get(route('admin.dashboard'));
        $this->assertTrue($res6->isRedirect(route('login')));
        $got6 = "Diarahkan (HTTP 302 Redirect) ke halaman login";
        echo "6. Unauthenticated Protected Access: {$got6} [SESUAI]\n";

        echo "Seluruh 6 Skenario Pengujian Akses Tidak Sah Terbukti 100% AMAN & SESUAI\n\n";
    }
}
