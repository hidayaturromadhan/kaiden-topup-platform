<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Fulfillment;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DigiflazzService
{
    public function isProduction(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }
        $mode = env('DIGIFLAZZ_MODE', config('services.digiflazz.mode', 'development'));
        return strtolower($mode) === 'production';
    }

    public function getUsername(): string
    {
        $settingUser = Setting::get('digiflazz_username');
        if (!empty($settingUser) && $settingUser !== 'kaiden_dev') {
            return $settingUser;
        }
        $envUser = env('DIGIFLAZZ_USERNAME');
        if (!empty($envUser) && $envUser !== 'kaiden_dev') {
            return $envUser;
        }
        return config('services.digiflazz.username', 'kaiden_dev');
    }

    public function getApiKey(): string
    {
        $settingKey = Setting::get('digiflazz_api_key');
        if (!empty($settingKey) && $settingKey !== 'dev-digiflazz-key') {
            return $settingKey;
        }
        $envKey = env('DIGIFLAZZ_API_KEY');
        if (!empty($envKey) && $envKey !== 'dev-digiflazz-key') {
            return $envKey;
        }
        return config('services.digiflazz.api_key', 'dev-digiflazz-key');
    }

    public function getBaseUrl(): string
    {
        return rtrim(Setting::get('digiflazz_base_url', config('services.digiflazz.base_url', env('DIGIFLAZZ_BASE_URL', 'https://api.digiflazz.com/v1'))), '/');
    }

    /**
     * Ambang batas saldo aman (default Rp 500.000)
     */
    public function getLowBalanceThreshold(): int
    {
        return (int)Setting::get('digiflazz_low_balance_threshold', config('services.digiflazz.low_balance_threshold', 500000));
    }

    /**
     * Cek Saldo Deposit Digiflazz (FR-FUL-4 & permission saldo_digiflazz.lihat)
     */
    public function checkBalance(): array
    {
        $username = $this->getUsername();
        $apiKey = $this->getApiKey();
        $sign = md5($username . $apiKey . 'depo');

        // Jika dalam testing atau menggunakan kunci dev lokal
        if ($apiKey === 'dev-digiflazz-key' || app()->environment('testing')) {
            try {
                $response = Http::timeout(2)->post($this->getBaseUrl() . '/cek-saldo', [
                    'cmd' => 'deposit',
                    'username' => $username,
                    'sign' => $sign,
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    if (isset($data['deposit'])) {
                        $deposit = (int)$data['deposit'];
                        $threshold = $this->getLowBalanceThreshold();

                        return [
                            'success' => true,
                            'deposit' => $deposit,
                            'threshold' => $threshold,
                            'is_low' => $deposit < $threshold,
                            'message' => 'Saldo berhasil diperbarui.',
                        ];
                    }
                }
            } catch (\Throwable $e) {}

            $mockDeposit = (int)Setting::get('mock_digiflazz_deposit', 2750000);
            $threshold = $this->getLowBalanceThreshold();

            return [
                'success' => true,
                'is_mock' => true,
                'deposit' => $mockDeposit,
                'threshold' => $threshold,
                'is_low' => $mockDeposit < $threshold,
                'message' => 'Saldo menggunakan simulasi pengujian lokal.',
            ];
        }

        try {
            $response = Http::timeout(10)->post($this->getBaseUrl() . '/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $username,
                'sign' => $sign,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $deposit = (int)($data['deposit'] ?? 0);
                $threshold = $this->getLowBalanceThreshold();

                return [
                    'success' => true,
                    'deposit' => $deposit,
                    'threshold' => $threshold,
                    'is_low' => $deposit < $threshold,
                    'message' => 'Saldo berhasil diperbarui.',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Digiflazz checkBalance exception: ' . $e->getMessage());
        }

        // Fallback saldo simulasi untuk development/pengujian
        $mockDeposit = (int)Setting::get('mock_digiflazz_deposit', 2750000);
        $threshold = $this->getLowBalanceThreshold();

        return [
            'success' => true,
            'is_mock' => true,
            'deposit' => $mockDeposit,
            'threshold' => $threshold,
            'is_low' => $mockDeposit < $threshold,
            'message' => 'Saldo menggunakan simulasi lokal (kunci API live belum terhubung).',
        ];
    }

    /**
     * Dapatkan saldo deposit Digiflazz saat ini dengan caching (30 detik).
     * Mencegah spam request ke endpoint /cek-saldo pada traffic kunjungan tinggi.
     */
    public function getCurrentBalance(): int
    {
        return Cache::remember('digiflazz_live_balance', 30, function () {
            $result = $this->checkBalance();
            return (int)($result['deposit'] ?? 0);
        });
    }

    /**
     * Periksa apakah suatu produk dapat dipenuhi oleh sistem supplier (modal saldo mencukupi).
     */
    public function isProductAvailable(Product $product): bool
    {
        if (!$product->is_active) {
            return false;
        }

        $balance = $this->getCurrentBalance();
        return $balance >= $product->cost_price;
    }

    /**
     * Pemenuhan pesanan otomatis ke Digiflazz
     * RULES.md: Hanya dipanggil setelah status DIBAYAR. ref_id unik disimpan di fulfillments.
     */
    public function fulfill(Order $order): Fulfillment
    {
        $order->load(['product.game', 'user']);

        // Keamanan Eksekusi: Tolak pemenuhan jika pesanan berstatus batal atau kedaluwarsa
        if (in_array($order->status, ['DIBATALKAN', 'KEDALUWARSA'])) {
            Log::warning("Pencegahan Eksekusi: Pesanan {$order->order_code} berstatus {$order->status} dicegah dari pemenuhan Digiflazz.");
            $existing = Fulfillment::where('order_id', $order->id)->latest('id')->first();
            if ($existing) {
                return $existing;
            }
            return Fulfillment::create([
                'order_id' => $order->id,
                'digiflazz_ref_id' => 'DF-PREVENTED-' . $order->order_code . '-' . time(),
                'status' => 'gagal',
                'response_summary' => 'Pesanan dibatalkan/kedaluwarsa, pemenuhan dicegah oleh sistem.',
            ]);
        }

        // Set status pesanan menjadi DIPROSES
        $order->status = 'DIPROSES';
        $order->save();

        // Buat ref_id unik
        $refId = 'DF-' . $order->order_code . '-' . strtoupper(bin2hex(random_bytes(3)));

        $customerNo = $order->user_id_game;
        if (!empty($order->zone_id)) {
            $customerNo .= $order->zone_id;
        }

        $username = $this->getUsername();
        $apiKey = $this->getApiKey();
        $sku = $order->product->digiflazz_sku;
        $sign = md5($username . $apiKey . $refId);

        $fulfillmentStatus = 'pending';
        $summary = '';

        try {
            $response = Http::timeout(15)->post($this->getBaseUrl() . '/transaction', [
                'username' => $username,
                'buyer_sku_code' => $sku,
                'customer_no' => $customerNo,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $statusStr = strtolower($data['status'] ?? '');
                $summary = $data['message'] ?? ($data['sn'] ?? 'Diproses oleh Digiflazz');

                if ($statusStr === 'sukses') {
                    $fulfillmentStatus = 'sukses';
                    $order->status = 'SUKSES';
                    $order->save();
                } elseif ($statusStr === 'gagal') {
                    $fulfillmentStatus = 'gagal';
                    $order->status = 'GAGAL';
                    $order->notes = ($order->notes ? $order->notes . "\n" : '') . "[DIGIFLAZZ GAGAL]: {$summary}";
                    $order->save();
                } else {
                    $fulfillmentStatus = 'pending';
                    $order->status = 'DIPROSES';
                    $order->save();
                }
            } else {
                if ($this->isProduction()) {
                    // MODE PRODUCTION: Jangan membuat sukses palsu jika Digiflazz menolak transaksi
                    $errData = $response->json()['data'] ?? [];
                    $errMessage = $errData['message'] ?? ($response->json()['message'] ?? 'Provider Digiflazz menolak permintaan (HTTP ' . $response->status() . ')');
                    $fulfillmentStatus = 'gagal';
                    $summary = $errMessage;
                    $order->status = 'GAGAL';
                    $order->notes = ($order->notes ? $order->notes . "\n" : '') . "[DIGIFLAZZ GAGAL]: {$errMessage}";
                    $order->save();
                } else {
                    // Mode Pengujian / Sandbox Lokal
                    $fulfillmentStatus = 'sukses';
                    $summary = 'Simulasi pengiriman otomatis sukses (SN: KDN' . rand(10000000, 99999999) . ')';
                    $order->status = 'SUKSES';
                    $order->save();
                }
            }
        } catch (\Throwable $e) {
            Log::error("Digiflazz fulfillment exception for Order {$order->order_code}: " . $e->getMessage());
            if ($this->isProduction()) {
                // MODE PRODUCTION: Tandai pending untuk antrean follow-up admin (jangan palsukan sukses)
                $fulfillmentStatus = 'pending';
                $summary = 'Timeout saat menghubungkan ke Digiflazz: ' . $e->getMessage();
                $order->status = 'DIPROSES';
                $order->notes = ($order->notes ? $order->notes . "\n" : '') . "[DIGIFLAZZ TIMEOUT]: Menunggu callback webhook atau retry admin.";
                $order->save();
            } else {
                $fulfillmentStatus = 'sukses';
                $summary = 'Simulasi pengiriman otomatis berhasil dijalankan (Local Dev)';
                $order->status = 'SUKSES';
                $order->save();
            }
        }

        $fulfillment = Fulfillment::create([
            'order_id' => $order->id,
            'digiflazz_ref_id' => $refId,
            'status' => $fulfillmentStatus,
            'response_summary' => $summary,
        ]);

        // Catat di Audit Log
        AuditLog::record(
            action: 'pesanan.pemenuhan_digiflazz',
            targetType: 'Order',
            targetId: $order->id,
            oldValues: ['status' => 'DIBAYAR'],
            newValues: [
                'status' => $order->status,
                'fulfillment_id' => $fulfillment->id,
                'ref_id' => $refId,
                'digiflazz_status' => $fulfillmentStatus,
            ],
            actorId: $order->user_id
        );

        return $fulfillment;
    }

    /**
     * Sinkronisasi Katalog Produk dari Digiflazz (FR-PRD-1 & permission produk.sinkron)
     * Dilengkapi Proteksi Anti-Rugi, Caching Pricelist, Penanganan Rate Limit RC 83,
     * dan Pemetaan Brand Resmi Digiflazz ke Katalog KAIDEN.
     */
    public function syncProducts(): array
    {
        $username = $this->getUsername();
        $apiKey = $this->getApiKey();
        $sign = md5($username . $apiKey . 'pricelist');
        $cachePath = storage_path('app/digiflazz_pricelist_raw.json');

        $syncedCount = 0;
        $updatedCount = 0;
        $items = [];
        $isFromCache = false;
        $rateLimitNote = '';

        try {
            $response = Http::timeout(30)->post($this->getBaseUrl() . '/price-list', [
                'cmd' => 'prepaid',
                'username' => $username,
                'sign' => $sign,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                // Cek penanganan limitasi RC 83 (maksimal 1x per 5 menit)
                if (isset($data['rc']) && $data['rc'] === '83') {
                    if (file_exists($cachePath)) {
                        $items = json_decode(file_get_contents($cachePath), true) ?: [];
                        $isFromCache = true;
                        $rateLimitNote = ' (Menggunakan cache lokal karena API Digiflazz membatasi request 1x per 5 menit)';
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Digiflazz membatasi pengecekan harga maksimal 1 kali per 5 menit (RC 83). Silakan tunggu beberapa saat lagi.',
                        ];
                    }
                } elseif (is_array($data) && count($data) > 0 && !isset($data['rc'])) {
                    $items = $data;
                    file_put_contents($cachePath, json_encode($items, JSON_PRETTY_PRINT));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Digiflazz syncProducts exception: ' . $e->getMessage());
            if (file_exists($cachePath)) {
                $items = json_decode(file_get_contents($cachePath), true) ?: [];
                $isFromCache = true;
                $rateLimitNote = ' (Menggunakan cache lokal karena kendala koneksi ke Digiflazz)';
            }
        }

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data katalog dari Digiflazz.',
            ];
        }

        // Peta Brand Digiflazz ke Game Slug di KAIDEN
        $brandMap = [
            'MOBILE LEGENDS' => 'mobile-legends',
            'MOBILE LEGEND' => 'mobile-legends',
            'MLBB' => 'mobile-legends',
            'FREE FIRE' => 'free-fire',
            'FREEFIRE' => 'free-fire',
            'VALORANT' => 'valorant',
            'CALL OF DUTY' => 'call-of-duty-mobile',
            'CALL OF DUTY MOBILE' => 'call-of-duty-mobile',
            'CODM' => 'call-of-duty-mobile',
            'ARENA OF VALOR' => 'arena-of-valor',
            'AOV' => 'arena-of-valor',
            'PUBG MOBILE' => 'pubg-mobile',
            'PUBG' => 'pubg-mobile',
            'GENSHIN IMPACT' => 'genshin-impact',
            'GENSHIN' => 'genshin-impact',
            'HONOR OF KINGS' => 'honor-of-kings',
            'HOK' => 'honor-of-kings',
            'POINT BLANK' => 'point-blank',
            'STEAM WALLET' => 'steam-wallet',
            'STEAM' => 'steam-wallet',
            'ROBLOX' => 'roblox',
            'HONKAI STAR RAIL' => 'honkai-star-rail',
            'HONKAI: STAR RAIL' => 'honkai-star-rail',
            'RAGNAROK' => 'ragnarok',
            'BLOOD STRIKE' => 'blood-strike',
            'EA SPORTS FC MOBILE' => 'fc-mobile',
            'FC MOBILE' => 'fc-mobile',
            'FIFA MOBILE' => 'fc-mobile',
        ];

        $allGames = Game::all()->keyBy('slug');
        $affectedGameIds = [];
        $newGamesCount = 0;

        foreach ($items as $item) {
            $brandRaw = strtoupper(trim($item['brand'] ?? ''));
            $sku = trim($item['buyer_sku_code'] ?? '');
            if (empty($sku)) {
                continue;
            }

            // Filter Ketat Kategori: Hanya izinkan kategori Games / Voucher Game
            $categoryRaw = strtolower(trim($item['category'] ?? ''));
            $isGameCategory = str_contains($categoryRaw, 'game') || str_contains($categoryRaw, 'voucher');
            
            // Kecualikan eksplisit provider telko dan PPOB non-game
            $nonGameKeywords = ['pulsa', 'data', 'telkomsel', 'indosat', 'xl', 'tri', 'smartfren', 'axis', 'pln', 'listrik', 'gas', 'pertamina', 'bpjs', 'pdam', 'emoney', 'e-money', 'ovo', 'gopay', 'dana', 'shopeepay', 'linkaja', 'k-vision'];
            foreach ($nonGameKeywords as $kw) {
                if (str_contains(strtolower($brandRaw), $kw) || str_contains($categoryRaw, $kw)) {
                    $isGameCategory = false;
                    break;
                }
            }

            if (!$isGameCategory && !isset($brandMap[$brandRaw])) {
                continue;
            }

            // Temukan Game yang cocok
            $game = null;
            if (isset($brandMap[$brandRaw])) {
                $game = $allGames->get($brandMap[$brandRaw]);
            }

            if (!$game) {
                $game = Game::where('checker_code', $brandRaw)
                    ->orWhere('name', 'LIKE', "%{$brandRaw}%")
                    ->first();
            }

            // MEKANISME DINAMIS: Jika game belum terdaftar di sistem KAIDEN,
            // buatkan entri Game baru secara otomatis agar produk langsung masuk ke katalog!
            if (!$game && !empty($brandRaw)) {
                $targetSlug = $brandMap[$brandRaw] ?? Str::slug($brandRaw);
                $game = Game::where('slug', $targetSlug)->first();

                if (!$game) {
                    $formattedName = match($brandRaw) {
                        'PUBG', 'PUBG MOBILE' => 'PUBG Mobile',
                        'CODM', 'CALL OF DUTY' => 'Call of Duty: Mobile',
                        'GENSHIN IMPACT', 'GENSHIN' => 'Genshin Impact',
                        'HONOR OF KINGS', 'HOK' => 'Honor of Kings',
                        'POINT BLANK' => 'Point Blank',
                        'STEAM', 'STEAM WALLET' => 'Steam Wallet',
                        'ROBLOX' => 'Roblox',
                        'HONKAI STAR RAIL', 'HONKAI: STAR RAIL' => 'Honkai: Star Rail',
                        'FC MOBILE', 'FIFA MOBILE', 'EA SPORTS FC MOBILE' => 'EA SPORTS FC Mobile',
                        'BLOOD STRIKE' => 'Blood Strike',
                        'GROWTOPIA' => 'Growtopia',
                        default => ucwords(strtolower($brandRaw)),
                    };

                    $gameCategory = match(true) {
                        str_contains($brandRaw, 'PUBG') || str_contains($brandRaw, 'FREE FIRE') || str_contains($brandRaw, 'BLOOD') => 'Battle Royale',
                        str_contains($brandRaw, 'VALORANT') || str_contains($brandRaw, 'DUTY') || str_contains($brandRaw, 'POINT BLANK') => 'FPS',
                        str_contains($brandRaw, 'LEGEND') || str_contains($brandRaw, 'AOV') || str_contains($brandRaw, 'HONOR') => 'MOBA',
                        str_contains($brandRaw, 'STEAM') || str_contains($brandRaw, 'GOOGLE') || str_contains($brandRaw, 'VOUCHER') => 'Voucher',
                        default => 'Online Game',
                    };

                    $needsZone = (
                        str_contains($brandRaw, 'MOBILE LEGENDS') || 
                        str_contains($brandRaw, 'GENSHIN') || 
                        str_contains($brandRaw, 'LIFEAFTER') ||
                        str_contains($brandRaw, 'RAGNAROK') ||
                        str_contains(strtolower($item['product_name'] ?? ''), 'zone') ||
                        str_contains(strtolower($item['desc'] ?? ''), 'server')
                    );

                    $publisher = match(true) {
                        str_contains($brandRaw, 'PUBG') => 'Tencent Games',
                        str_contains($brandRaw, 'GENSHIN') || str_contains($brandRaw, 'HONKAI') => 'HoYoverse',
                        str_contains($brandRaw, 'HONOR') => 'Level Infinite',
                        str_contains($brandRaw, 'STEAM') => 'Valve',
                        str_contains($brandRaw, 'ROBLOX') => 'Roblox Corp',
                        str_contains($brandRaw, 'POINT BLANK') => 'Zepetto',
                        str_contains($brandRaw, 'FC MOBILE') || str_contains($brandRaw, 'FIFA') => 'EA Sports',
                        str_contains($brandRaw, 'GROWTOPIA') => 'Ubisoft',
                        default => 'Official Publisher',
                    };

                    $game = Game::create([
                        'name' => $formattedName,
                        'slug' => $targetSlug,
                        'checker_code' => $brandRaw,
                        'needs_zone' => $needsZone,
                        'status' => 'aktif',
                        'publisher' => $publisher,
                        'category' => $gameCategory,
                        'thumbnail' => 'images/bg/1.jpg',
                        'description' => "Top up {$formattedName} termurah, resmi, dan instan 24 jam.",
                        'guide_text' => $needsZone 
                            ? "Masukkan User ID dan Zone/Server ID akun {$formattedName} Anda dengan benar."
                            : "Masukkan User ID akun {$formattedName} Anda dengan benar.",
                    ]);

                    $allGames->put($targetSlug, $game);
                    $newGamesCount++;
                }
            }

            if ($game) {
                $affectedGameIds[$game->id] = true;
                $costPrice = (int)($item['price'] ?? 0);

                // Margin default: minimal 6% atau Rp 1.000 untuk kepastian laba aman
                $defaultMargin = max(1000, (int)round($costPrice * 0.06));
                $sellPrice = $costPrice + $defaultMargin;

                // Status keaktifan dari sisi Buyer dan Seller Digiflazz
                $isBuyerActive = (bool)($item['buyer_product_status'] ?? true);
                $isSellerActive = (bool)($item['seller_product_status'] ?? true);
                $isDigiflazzActive = $isBuyerActive && $isSellerActive;

                $product = Product::where('digiflazz_sku', $sku)->first();

                if ($product) {
                    $product->cost_price = $costPrice;

                    // PROTEKSI ANTI-RUGI OTOMATIS:
                    // Jika harga modal Digiflazz naik dan mendekati atau melampaui harga jual saat ini,
                    // sistem otomatis menaikkan harga jual agar toko tidak pernah menjual rugi.
                    if ($costPrice >= $product->sell_price) {
                        $minSafeMargin = max(1000, (int)round($costPrice * 0.05));
                        $newSellPrice = $costPrice + $minSafeMargin;
                        $product->sell_price = $newSellPrice;

                        if ($product->original_price && $product->original_price < $newSellPrice) {
                            $product->original_price = (int)round($newSellPrice * 1.08);
                        }
                    }

                    // Sinkronkan status keaktifan supplier
                    $product->is_active = $isDigiflazzActive;
                    $product->save();
                    $updatedCount++;
                } else {
                    Product::create([
                        'game_id' => $game->id,
                        'digiflazz_sku' => $sku,
                        'name' => $item['product_name'] ?? $sku,
                        'cost_price' => $costPrice,
                        'original_price' => (int)round($sellPrice * 1.08),
                        'sell_price' => $sellPrice,
                        'is_active' => $isDigiflazzActive,
                        'category' => $item['type'] ?? ($item['category'] ?? 'Top Up'),
                    ]);
                    $syncedCount++;
                }
            }
        }

        // Nonaktifkan SKU mock/dummy untuk game yang telah memiliki produk riil dari Digiflazz
        foreach (array_keys($affectedGameIds) as $gameId) {
            $hasRealProducts = Product::where('game_id', $gameId)
                ->where('digiflazz_sku', 'NOT LIKE', 'MLBB\_%')
                ->where('digiflazz_sku', 'NOT LIKE', 'FF\_%')
                ->where('digiflazz_sku', 'NOT LIKE', 'VALO\_%')
                ->where('digiflazz_sku', 'NOT LIKE', 'CODM\_%')
                ->where('digiflazz_sku', 'NOT LIKE', 'AOV\_%')
                ->where('is_active', true)
                ->exists();

            if ($hasRealProducts) {
                Product::where('game_id', $gameId)
                    ->where(function ($q) {
                        $q->where('digiflazz_sku', 'LIKE', 'MLBB\_%')
                          ->orWhere('digiflazz_sku', 'LIKE', 'FF\_%');
                    })
                    ->update(['is_active' => false]);
            }
        }

        // Nonaktifkan game non-game yang tidak sengaja terdaftar dari sinkronisasi sebelumnya
        $nonGameSlugs = ['indosat', 'telkomsel', 'xl', 'axis', 'smartfren', 'tri', 'pertamina-gas', 'k-vision-dan-gol', 'aulx'];
        Game::whereIn('slug', $nonGameSlugs)->update(['status' => 'nonaktif']);

        // Reset cache katalog & beranda agar game dan produk baru langsung tampil secara instan
        Cache::forget('active_game_categories');
        Cache::forget('active_game_publishers');
        Cache::forget('home_default_games');
        Cache::forget('catalog_default_games');
        Cache::forget('catalog_default_products');
        Cache::forget('active_games_total_count');
        Cache::forget('active_products_total_count');

        // Catat Audit Log
        AuditLog::record(
            action: 'produk.sinkron',
            targetType: 'Digiflazz',
            targetId: null,
            oldValues: null,
            newValues: [
                'new_products' => $syncedCount,
                'updated_products' => $updatedCount,
                'new_games' => $newGamesCount,
                'is_cached' => $isFromCache,
            ],
            actorId: auth()->id()
        );

        $gameNote = $newGamesCount > 0 ? " ({$newGamesCount} game baru otomatis didaftarkan)" : "";

        return [
            'success' => true,
            'new_products' => $syncedCount,
            'updated_products' => $updatedCount,
            'new_games' => $newGamesCount,
            'message' => "Sinkronisasi berhasil{$rateLimitNote}. {$syncedCount} produk baru ditambahkan{$gameNote}, {$updatedCount} produk diperbarui sesuai API Production Digiflazz.",
        ];
    }
}

