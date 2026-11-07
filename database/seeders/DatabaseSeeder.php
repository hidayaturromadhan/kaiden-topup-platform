<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Banner;
use App\Models\Game;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions Seed
        $permissions = [
            // Hak Akses Pelanggan
            ['name' => 'produk.lihat', 'display_name' => 'Lihat Katalog Produk', 'description' => 'Melihat daftar game dan nominal produk'],
            ['name' => 'pesanan.buat', 'display_name' => 'Buat Pesanan Top Up', 'description' => 'Membuat pesanan baru untuk akun game'],
            ['name' => 'transaksi.lihat_milik_sendiri', 'display_name' => 'Lihat Transaksi Sendiri', 'description' => 'Melihat riwayat transaksi milik akun sendiri'],

            // Hak Akses Operasional Admin
            ['name' => 'transaksi.lihat', 'display_name' => 'Lihat Semua Transaksi', 'description' => 'Melihat seluruh transaksi dari semua pelanggan'],
            ['name' => 'produk.ubah', 'display_name' => 'Kelola Produk', 'description' => 'Menambah atau memperbarui metadata produk'],
            ['name' => 'stok.ubah', 'display_name' => 'Kelola Status/Stok Produk', 'description' => 'Mengaktifkan atau menonaktifkan status produk'],
            ['name' => 'harga.ubah', 'display_name' => 'Kelola Harga Jual', 'description' => 'Mengubah harga jual produk'],
            ['name' => 'produk.sinkron', 'display_name' => 'Sinkronisasi Produk Digiflazz', 'description' => 'Menarik katalog dan harga modal terbaru dari Digiflazz'],
            ['name' => 'pelanggan.lihat', 'display_name' => 'Lihat Data Pelanggan', 'description' => 'Melihat data akun pelanggan'],
            ['name' => 'laporan.lihat', 'display_name' => 'Lihat Laporan Penjualan', 'description' => 'Melihat ringkasan laporan transaksi penjualan'],
            ['name' => 'laporan.ekspor', 'display_name' => 'Ekspor Laporan CSV', 'description' => 'Mengekspor data transaksi ke file CSV'],
            ['name' => 'saldo_digiflazz.lihat', 'display_name' => 'Lihat Saldo Deposit Digiflazz', 'description' => 'Melihat saldo deposit dan status peringatan saldo rendah'],
            ['name' => 'banner.kelola', 'display_name' => 'Kelola Banner & Promo', 'description' => 'Menambah, mengubah, dan mengaktifkan banner promo di halaman utama'],

            // Hak Akses Superadmin
            ['name' => 'pengguna.kelola', 'display_name' => 'Kelola Pengguna Internal', 'description' => 'Membuat dan mengelola akun admin atau pengguna lain'],
            ['name' => 'role.kelola', 'display_name' => 'Kelola Peran & Izin', 'description' => 'Mengatur penetapan peran dan matriks izin RBAC'],
            ['name' => 'integrasi.atur', 'display_name' => 'Konfigurasi Integrasi API', 'description' => 'Mengatur kredensial API Midtrans dan Digiflazz'],
            ['name' => 'ign.konfigurasi', 'display_name' => 'Pengaturan IGN Checker', 'description' => 'Mengatur feature flag dan konfigurasi pengecekan ID game'],
            ['name' => 'audit.lihat', 'display_name' => 'Lihat Audit Log', 'description' => 'Melihat rekaman riwayat audit perubahan sistem'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        $rolePelanggan = Role::firstOrCreate(
            ['name' => 'pelanggan'],
            ['display_name' => 'Pelanggan', 'description' => 'Pengguna umum pembeli voucher dan top up']
        );

        $roleAdmin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Staf operasional harian, katalog, dan transaksi']
        );

        $roleSuperadmin = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['display_name' => 'Super Administrator', 'description' => 'Pemegang hak akses tertinggi sistem dan tata kelola keamanan']
        );

        // Assign Permissions ke Roles
        $pelangganPerms = Permission::whereIn('name', [
            'produk.lihat',
            'pesanan.buat',
            'transaksi.lihat_milik_sendiri',
        ])->pluck('id');
        $rolePelanggan->permissions()->sync($pelangganPerms);

        $adminPerms = Permission::whereIn('name', [
            'produk.lihat',
            'transaksi.lihat',
            'produk.ubah',
            'stok.ubah',
            'harga.ubah',
            'produk.sinkron',
            'pelanggan.lihat',
            'laporan.lihat',
            'laporan.ekspor',
            'saldo_digiflazz.lihat',
            'banner.kelola',
        ])->pluck('id');
        $roleAdmin->permissions()->sync($adminPerms);

        $superadminPerms = Permission::whereIn('name', [
            'produk.lihat',
            'transaksi.lihat',
            'produk.ubah',
            'stok.ubah',
            'harga.ubah',
            'produk.sinkron',
            'pelanggan.lihat',
            'laporan.lihat',
            'laporan.ekspor',
            'saldo_digiflazz.lihat',
            'banner.kelola',
            'pengguna.kelola',
            'role.kelola',
            'integrasi.atur',
            'ign.konfigurasi',
            'audit.lihat',
        ])->pluck('id');
        $roleSuperadmin->permissions()->sync($superadminPerms);

        // 2. Akun Pengguna Awal
        $superadminUser = User::firstOrCreate(
            ['email' => 'superadmin@kaiden.id'],
            [
                'name' => 'Super Administrator KAIDEN',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $superadminUser->roles()->sync([$roleSuperadmin->id]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@kaiden.id'],
            [
                'name' => 'Administrator Operasional',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->roles()->sync([$roleAdmin->id]);

        $pelangganUser = User::firstOrCreate(
            ['email' => 'pelanggan@kaiden.id'],
            [
                'name' => 'Rifki Pelanggan Setia',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $pelangganUser->roles()->sync([$rolePelanggan->id]);

        // 3. Game Catalog Seed
        $gamesData = [
            [
                'name' => 'Mobile Legends: Bang Bang',
                'slug' => 'mobile-legends',
                'checker_code' => 'MOBILE_LEGENDS',
                'needs_zone' => true,
                'publisher' => 'Moonton',
                'category' => 'MOBA',
                'thumbnail' => 'images/bg/aspirant.jpg',
                'description' => 'Top up diamond Mobile Legends resmi dan instan langsung masuk ke akun Anda.',
                'guide_text' => 'Buka game Mobile Legends, ketuk avatar profil di sudut kiri atas. User ID tertera di bawah avatar berupa angka 8-10 digit, dan Zone ID berada di dalam tanda kurung (contoh: ID 12345678, Zone 2001).',
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'checker_code' => 'FREEFIRE',
                'needs_zone' => false,
                'publisher' => 'Garena',
                'category' => 'Battle Royale',
                'thumbnail' => 'images/bg/freefire.jpg',
                'description' => 'Beli diamond Free Fire termurah dengan proses pengiriman kilat 24 jam.',
                'guide_text' => 'Buka game Free Fire, masuk ke profil pemain di pojok kiri atas. Temukan UID akun Anda (contoh: 87654321) di samping avatar.',
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'checker_code' => 'VALORANT',
                'needs_zone' => false,
                'publisher' => 'Riot Games',
                'category' => 'FPS',
                'thumbnail' => 'images/bg/valorant.jpg',
                'description' => 'Isi Valorant Points (VP) resmi untuk membeli skin dan battle pass favorit Anda.',
                'guide_text' => 'Masukkan Riot ID lengkap beserta Tagline akun Riot Anda (contoh: PlayerName#ID1).',
            ],
            [
                'name' => 'Call of Duty: Mobile',
                'slug' => 'call-of-duty-mobile',
                'checker_code' => 'CALL_OF_DUTY',
                'needs_zone' => false,
                'publisher' => 'Garena / Activision',
                'category' => 'FPS',
                'thumbnail' => 'images/bg/codm.jpg',
                'description' => 'Top up CP Call of Duty Mobile resmi dan aman untuk senjata legendaris.',
                'guide_text' => 'Buka menu Pengaturan CODM, pilih tab Lainnya, lalu salin OpenID Anda di pojok kanan bawah.',
            ],
            [
                'name' => 'Arena of Valor',
                'slug' => 'arena-of-valor',
                'checker_code' => 'AOV',
                'needs_zone' => false,
                'publisher' => 'Garena',
                'category' => 'MOBA',
                'thumbnail' => 'images/bg/aov.jpg',
                'description' => 'Top up Voucher Arena of Valor cepat dan terpercaya.',
                'guide_text' => 'Buka AOV, masuk ke profil, dan temukan OpenID Anda pada bagian info akun.',
            ],
        ];

        foreach ($gamesData as $gData) {
            $game = Game::firstOrCreate(['slug' => $gData['slug']], $gData);

            // Seed Produk untuk tiap game
            if ($game->slug === 'mobile-legends') {
                $mlbbProducts = [
                    ['digiflazz_sku' => 'MLBB_86', 'name' => '86 Diamonds', 'cost' => 19500, 'sell' => 21500, 'original' => null],
                    ['digiflazz_sku' => 'MLBB_172', 'name' => '172 Diamonds', 'cost' => 38000, 'sell' => 42000, 'original' => null],
                    ['digiflazz_sku' => 'MLBB_257', 'name' => '257 Diamonds', 'cost' => 57500, 'sell' => 63000, 'original' => 68000],
                    ['digiflazz_sku' => 'MLBB_344', 'name' => '344 Diamonds', 'cost' => 76000, 'sell' => 84000, 'original' => null],
                    ['digiflazz_sku' => 'MLBB_429', 'name' => '429 Diamonds', 'cost' => 95000, 'sell' => 105000, 'original' => null],
                    ['digiflazz_sku' => 'MLBB_706', 'name' => '706 Diamonds', 'cost' => 152000, 'sell' => 168000, 'original' => 185000],
                    ['digiflazz_sku' => 'MLBB_PASS', 'name' => 'Weekly Diamond Pass', 'cost' => 27500, 'sell' => 29900, 'original' => 35000],
                    ['digiflazz_sku' => 'MLBB_TWILIGHT', 'name' => 'Twilight Pass', 'cost' => 140000, 'sell' => 155000, 'original' => 175000],
                ];
                foreach ($mlbbProducts as $p) {
                    Product::updateOrCreate(
                        ['digiflazz_sku' => $p['digiflazz_sku']],
                        [
                            'game_id' => $game->id,
                            'name' => $p['name'],
                            'cost_price' => $p['cost'],
                            'original_price' => $p['original'],
                            'sell_price' => $p['sell'],
                            'is_active' => true,
                            'category' => 'Diamonds',
                        ]
                    );
                }
            } elseif ($game->slug === 'free-fire') {
                $ffProducts = [
                    ['digiflazz_sku' => 'FF_70', 'name' => '70 Diamonds', 'cost' => 9500, 'sell' => 10500, 'original' => null],
                    ['digiflazz_sku' => 'FF_140', 'name' => '140 Diamonds', 'cost' => 18500, 'sell' => 20500, 'original' => null],
                    ['digiflazz_sku' => 'FF_355', 'name' => '355 Diamonds', 'cost' => 46000, 'sell' => 51000, 'original' => 58000],
                    ['digiflazz_sku' => 'FF_720', 'name' => '720 Diamonds', 'cost' => 91000, 'sell' => 100000, 'original' => null],
                    ['digiflazz_sku' => 'FF_1450', 'name' => '1450 Diamonds', 'cost' => 182000, 'sell' => 200000, 'original' => 225000],
                    ['digiflazz_sku' => 'FF_MEMBER_W', 'name' => 'Membership Mingguan', 'cost' => 28000, 'sell' => 31000, 'original' => 36000],
                ];
                foreach ($ffProducts as $p) {
                    Product::updateOrCreate(
                        ['digiflazz_sku' => $p['digiflazz_sku']],
                        [
                            'game_id' => $game->id,
                            'name' => $p['name'],
                            'cost_price' => $p['cost'],
                            'original_price' => $p['original'],
                            'sell_price' => $p['sell'],
                            'is_active' => true,
                            'category' => 'Diamonds',
                        ]
                    );
                }
            } elseif ($game->slug === 'valorant') {
                $valoProducts = [
                    ['digiflazz_sku' => 'VALO_475', 'name' => '475 Points', 'cost' => 52000, 'sell' => 57000, 'original' => null],
                    ['digiflazz_sku' => 'VALO_1000', 'name' => '1000 Points', 'cost' => 105000, 'sell' => 115000, 'original' => null],
                    ['digiflazz_sku' => 'VALO_2050', 'name' => '2050 Points', 'cost' => 210000, 'sell' => 230000, 'original' => 255000],
                    ['digiflazz_sku' => 'VALO_3650', 'name' => '3650 Points', 'cost' => 370000, 'sell' => 400000, 'original' => null],
                ];
                foreach ($valoProducts as $p) {
                    Product::updateOrCreate(
                        ['digiflazz_sku' => $p['digiflazz_sku']],
                        [
                            'game_id' => $game->id,
                            'name' => $p['name'],
                            'cost_price' => $p['cost'],
                            'original_price' => $p['original'],
                            'sell_price' => $p['sell'],
                            'is_active' => true,
                            'category' => 'Points',
                        ]
                    );
                }
            } elseif ($game->slug === 'call-of-duty-mobile') {
                $codmProducts = [
                    ['digiflazz_sku' => 'CODM_31', 'name' => '31 CP', 'cost' => 4800, 'sell' => 5500, 'original' => null],
                    ['digiflazz_sku' => 'CODM_63', 'name' => '63 CP', 'cost' => 9500, 'sell' => 11000, 'original' => null],
                    ['digiflazz_sku' => 'CODM_128', 'name' => '128 CP', 'cost' => 19000, 'sell' => 21500, 'original' => null],
                    ['digiflazz_sku' => 'CODM_321', 'name' => '321 CP', 'cost' => 47000, 'sell' => 52000, 'original' => 60000],
                    ['digiflazz_sku' => 'CODM_645', 'name' => '645 CP', 'cost' => 94000, 'sell' => 104000, 'original' => null],
                ];
                foreach ($codmProducts as $p) {
                    Product::updateOrCreate(
                        ['digiflazz_sku' => $p['digiflazz_sku']],
                        [
                            'game_id' => $game->id,
                            'name' => $p['name'],
                            'cost_price' => $p['cost'],
                            'original_price' => $p['original'],
                            'sell_price' => $p['sell'],
                            'is_active' => true,
                            'category' => 'CP',
                        ]
                    );
                }
            } elseif ($game->slug === 'arena-of-valor') {
                $aovProducts = [
                    ['digiflazz_sku' => 'AOV_40', 'name' => '40 Vouchers', 'cost' => 9500, 'sell' => 10500, 'original' => null],
                    ['digiflazz_sku' => 'AOV_90', 'name' => '90 Vouchers', 'cost' => 19000, 'sell' => 21000, 'original' => null],
                    ['digiflazz_sku' => 'AOV_230', 'name' => '230 Vouchers', 'cost' => 47000, 'sell' => 52000, 'original' => 58000],
                    ['digiflazz_sku' => 'AOV_470', 'name' => '470 Vouchers', 'cost' => 94000, 'sell' => 104000, 'original' => null],
                ];
                foreach ($aovProducts as $p) {
                    Product::updateOrCreate(
                        ['digiflazz_sku' => $p['digiflazz_sku']],
                        [
                            'game_id' => $game->id,
                            'name' => $p['name'],
                            'cost_price' => $p['cost'],
                            'original_price' => $p['original'],
                            'sell_price' => $p['sell'],
                            'is_active' => true,
                            'category' => 'Vouchers',
                        ]
                    );
                }
            }
        }

        // 4. Default Settings Seed
        Setting::set('ign_checker_enabled', 'true', false, 'Feature flag pengecekan nickname otomatis via GameCheckService');
        Setting::set('digiflazz_low_balance_threshold', '500000', false, 'Ambang batas saldo minimum Digiflazz untuk memicu warning badge');
        Setting::set('midtrans_server_key', env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-3PP7h2ArGhy9IvVCiaMfS1cZ'), true, 'Midtrans Sandbox Server Key');
        Setting::set('midtrans_client_key', env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-JFtPNzgFuR3e_w4m'), false, 'Midtrans Sandbox Client Key');
        Setting::set('midtrans_is_production', 'false', false, 'Mode integrasi Midtrans (false = Sandbox, true = Production)');
        Setting::set('mock_digiflazz_deposit', '2750000', false, 'Saldo deposit simulasi untuk pengujian lokal');
        Setting::set('whatsapp_number', '6281234567890', false, 'Nomor WhatsApp resmi Customer Support untuk wa.me');

        // 4. Banners & Promos Seed (Bisa Dikelola via Admin & Tampilan Natural Bernilai Jual)
        $defaultBanners = [
            [
                'title' => 'Top Up Diamond & Voucher Instan 24 Jam Nonstop',
                'subtitle' => 'Didukung Midtrans Payment Gateway resmi. Bebas ribet tanpa konfirmasi manual.',
                'badge_text' => 'PROMO WEEKEND GAMING KAIDEN',
                'discount_tag' => 'DISKON!',
                'discount_value' => '20 RB',
                'discount_subtext' => 'Min. Trx Rp 45.000',
                'button_text' => 'Klaim Promo',
                'button_url' => '#katalog-game',
                'image' => 'images/banners/banner_valorant.jpg',
                'banner_type' => 'slider',
                'gradient_theme' => 'crimson',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Event Booyah Pass & Diamond Termurah 2026',
                'subtitle' => 'Stok resmi server Indonesia dengan pengiriman kilat langsung masuk akun.',
                'badge_text' => 'SPECIAL EVENT BOOYAH',
                'discount_tag' => 'BONUS!',
                'discount_value' => '+15% DM',
                'discount_subtext' => 'Pengiriman Kilat < 60 Detik',
                'button_text' => 'Top Up Sekarang',
                'button_url' => '/game/free-fire',
                'image' => 'images/banners/banner_freefire.jpg',
                'banner_type' => 'slider',
                'gradient_theme' => 'slate',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Cek Nickname Akun Otomatis Sebelum Pembayaran',
                'subtitle' => 'Garansi 100% uang kembali jika transaksi terkendala atau salah kirim.',
                'badge_text' => 'ANTI SALAH KIRIM DIAMOND',
                'discount_tag' => 'BIAYA ADMIN:',
                'discount_value' => 'Rp 0',
                'discount_subtext' => 'Bebas Admin via QRIS & E-Wallet',
                'button_text' => 'Lihat Katalog',
                'button_url' => '/katalog',
                'image' => 'images/banners/banner_codm.jpg',
                'banner_type' => 'slider',
                'gradient_theme' => 'emerald',
                'sort_order' => 3,
                'is_active' => true,
            ],
            // 2 Side Banners (Promo Rekomendasi Game Resmi)
            [
                'title' => 'Flash Promo Mobile Legends',
                'subtitle' => 'Diskon s/d 15% + Bonus Diamond Langsung Masuk',
                'badge_text' => 'PROMO SPESIAL HARI INI',
                'discount_tag' => 'DISKON',
                'discount_value' => '15%',
                'discount_subtext' => 'Tanpa Antre',
                'button_text' => 'Top Up MLBB',
                'button_url' => '/game/mobile-legends',
                'image' => 'images/bg/aspirant.jpg',
                'banner_type' => 'side',
                'gradient_theme' => 'crimson',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Super Deal Free Fire Diamond',
                'subtitle' => 'Mulai Rp 2.869 Bebas Biaya Admin',
                'badge_text' => 'FLASH SALE POPULER',
                'discount_tag' => 'TERMURAH',
                'discount_value' => 'Rp 2.869',
                'discount_subtext' => 'Stok Resmi',
                'button_text' => 'Top Up Free Fire',
                'button_url' => '/game/free-fire',
                'image' => 'images/banners/banner_freefire.jpg',
                'banner_type' => 'side',
                'gradient_theme' => 'amber',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($defaultBanners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], $b);
        }

        // 4.B Seed Transaksi Selesai & Ulasan/Testimoni Pelanggan (Termasuk contoh gamerabah)
        $pelangganUser = User::where('email', 'pelanggan@kaiden.id')->first();
        $ffProduct = Product::where('digiflazz_sku', 'FF_70_DM')->first() ?? Product::first();
        $mlProduct = Product::where('digiflazz_sku', 'MLBB_PASS')->first() ?? Product::skip(1)->first();

        if ($pelangganUser && $ffProduct) {
            $order1 = Order::firstOrCreate(
                ['order_code' => 'KDN-SEED-001'],
                [
                    'user_id' => $pelangganUser->id,
                    'product_id' => $ffProduct->id,
                    'user_id_game' => '87654321',
                    'zone_id' => null,
                    'confirmed_nickname' => 'gamerabah',
                    'locked_price' => $ffProduct->sell_price,
                    'status' => 'SUKSES',
                    'created_at' => now()->subMinutes(3),
                ]
            );

            Review::firstOrCreate(
                ['order_id' => $order1->id],
                [
                    'user_id' => $pelangganUser->id,
                    'game_id' => $ffProduct->game_id,
                    'rating' => 5,
                    'comment' => 'Top up diamond Free Fire kilat banget, baru bayar langsung masuk!',
                    'is_anonymous' => false,
                    'is_visible' => true,
                ]
            );
        }

        if ($pelangganUser && $mlProduct) {
            $order2 = Order::firstOrCreate(
                ['order_code' => 'KDN-SEED-002'],
                [
                    'user_id' => $pelangganUser->id,
                    'product_id' => $mlProduct->id,
                    'user_id_game' => '12345678',
                    'zone_id' => '2001',
                    'confirmed_nickname' => 'sultan_alucard',
                    'locked_price' => $mlProduct->sell_price,
                    'status' => 'SUKSES',
                    'created_at' => now()->subMinutes(7),
                ]
            );

            Review::firstOrCreate(
                ['order_id' => $order2->id],
                [
                    'user_id' => $pelangganUser->id,
                    'game_id' => $mlProduct->game_id,
                    'rating' => 5,
                    'comment' => 'Weekly pass Mobile Legends langsung aktif otomatis kurang dari 30 detik. Recommended!',
                    'is_anonymous' => false,
                    'is_visible' => true,
                ]
            );
        }

        // 5. Catat log inisialisasi sistem
        AuditLog::record(
            action: 'sistem.inisialisasi',
            targetType: 'System',
            targetId: null,
            oldValues: null,
            newValues: ['status' => 'Sistem RBAC dan katalog KAIDEN berhasil diinisialisasi'],
            actorId: $superadminUser->id
        );
    }
}
