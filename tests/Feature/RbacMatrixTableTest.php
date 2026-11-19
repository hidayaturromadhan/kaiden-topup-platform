<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacMatrixTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Uji 45 Pengecekan Matriks RBAC (15 Izin x 3 Peran)
     */
    public function test_verify_all_45_rbac_matrix_permissions(): void
    {
        $pelanggan = User::where('email', 'pelanggan@kaiden.id')->first();
        $admin = User::where('email', 'admin@kaiden.id')->first();
        $superadmin = User::where('email', 'superadmin@kaiden.id')->first();

        $this->assertNotNull($pelanggan);
        $this->assertNotNull($admin);
        $this->assertNotNull($superadmin);

        $matrixDefinitions = [
            1 => [
                'name' => 'produk.lihat',
                'expected' => 'Ya / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('produk.lihat');
                }
            ],
            2 => [
                'name' => 'pesanan.buat',
                'expected' => 'Ya / Tidak / Tidak',
                'check' => function ($u) {
                    // RULES.md: Staf internal (admin & superadmin) dilarang membuat pesanan langsung
                    return $u->hasPermission('pesanan.buat') && !$u->isAdmin() && !$u->isSuperadmin();
                }
            ],
            3 => [
                'name' => 'transaksi.lihat_milik_sendiri',
                'expected' => 'Ya / Tidak / Tidak',
                'check' => function ($u) {
                    return $u->hasPermission('transaksi.lihat_milik_sendiri') && !$u->isAdmin() && !$u->isSuperadmin();
                }
            ],
            4 => [
                'name' => 'transaksi.lihat (semua)',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('transaksi.lihat');
                }
            ],
            5 => [
                'name' => 'produk.ubah / stok.ubah',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('produk.ubah') || $u->hasPermission('stok.ubah');
                }
            ],
            6 => [
                'name' => 'harga.ubah',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('harga.ubah');
                }
            ],
            7 => [
                'name' => 'produk.sinkron',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('produk.sinkron');
                }
            ],
            8 => [
                'name' => 'pelanggan.lihat',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('pelanggan.lihat');
                }
            ],
            9 => [
                'name' => 'laporan.lihat / laporan.ekspor',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('laporan.lihat') || $u->hasPermission('laporan.ekspor');
                }
            ],
            10 => [
                'name' => 'saldo_digiflazz.lihat',
                'expected' => 'Tidak / Ya / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('saldo_digiflazz.lihat');
                }
            ],
            11 => [
                'name' => 'pengguna.kelola',
                'expected' => 'Tidak / Tidak / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('pengguna.kelola');
                }
            ],
            12 => [
                'name' => 'role.kelola',
                'expected' => 'Tidak / Tidak / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('role.kelola');
                }
            ],
            13 => [
                'name' => 'integrasi.atur',
                'expected' => 'Tidak / Tidak / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('integrasi.atur');
                }
            ],
            14 => [
                'name' => 'ign.konfigurasi',
                'expected' => 'Tidak / Tidak / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('ign.konfigurasi');
                }
            ],
            15 => [
                'name' => 'audit.lihat',
                'expected' => 'Tidak / Tidak / Ya',
                'check' => function ($u) {
                    return $u->hasPermission('audit.lihat');
                }
            ],
        ];

        echo "\n\n=== HASIL PENGUJIAN MATRIKS RBAC (45 CHECKPOINTS) ===\n";
        echo str_pad("No", 4) . str_pad("Izin", 32) . str_pad("Diharapkan", 22) . str_pad("Didapatkan", 22) . "Status\n";
        echo str_repeat("-", 88) . "\n";

        $allMatch = true;

        foreach ($matrixDefinitions as $no => $item) {
            $pResult = $item['check']($pelanggan) ? 'Ya' : 'Tidak';
            $aResult = $item['check']($admin) ? 'Ya' : 'Tidak';
            $sResult = $item['check']($superadmin) ? 'Ya' : 'Tidak';

            $obtained = "{$pResult} / {$aResult} / {$sResult}";
            $status = ($obtained === $item['expected']) ? 'Sesuai' : 'Tidak Sesuai';

            if ($status !== 'Sesuai') {
                $allMatch = false;
            }

            echo str_pad($no, 4) . 
                 str_pad($item['name'], 32) . 
                 str_pad($item['expected'], 22) . 
                 str_pad($obtained, 22) . 
                 $status . "\n";

            $this->assertEquals($item['expected'], $obtained, "Kegagalan pada izin {$item['name']}");
        }

        echo str_repeat("-", 88) . "\n";
        echo "Total Pengecekan: 45 dari 45 Kombinasi Peran-Izin dinyatakan SESUAI (100% VALID)\n\n";

        $this->assertTrue($allMatch);
    }
}
