<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        protected DigiflazzService $digiflazzService
    ) {}

    public function index(Request $request)
    {
        $gameId = $request->query('game_id');
        $status = $request->query('status');
        $search = $request->query('q');

        $query = Product::with('game');

        if ($gameId) {
            $query->where('game_id', $gameId);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('digiflazz_sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('game_id')->orderBy('sell_price')->paginate(15)->withQueryString();
        $games = Cache::remember('admin_games_dropdown', 3600, function () {
            return Game::orderBy('name')->get();
        });

        return view('admin.products.index', compact('products', 'games', 'gameId', 'status', 'search'));
    }

    /**
     * Mengubah harga jual & harga promo produk (permission: harga.ubah & audit log)
     * Dilengkapi Proteksi Anti-Rugi Modal Digiflazz & Transparansi Diskon
     */
    public function updatePrice(Request $request, Product $product)
    {
        $user = Auth::user();
        if (!$user->hasPermission('harga.ubah')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin harga.ubah.');
        }

        $validated = $request->validate([
            'sell_price' => ['required', 'integer', 'min:0'],
            'original_price' => ['nullable', 'integer', 'min:0'],
        ], [
            'sell_price.required' => 'Harga jual wajib diisi.',
            'sell_price.integer' => 'Harga jual harus berupa nominal angka valid.',
            'sell_price.min' => 'Harga jual tidak boleh bernilai negatif.',
            'original_price.integer' => 'Harga normal harus berupa nominal angka valid.',
            'original_price.min' => 'Harga normal tidak boleh bernilai negatif.',
        ]);

        $newSellPrice = (int)$validated['sell_price'];
        $newOriginalPrice = !empty($validated['original_price']) ? (int)$validated['original_price'] : null;

        // 1. PROTEKSI ANTI-RUGI: sell_price TIDAK BOLEH di bawah modal Digiflazz
        if ($newSellPrice < $product->cost_price) {
            $lossAmount = $product->cost_price - $newSellPrice;
            return back()
                ->withInput()
                ->withErrors([
                    'sell_price' => "Harga jual (Rp " . number_format($newSellPrice, 0, ',', '.') . ") tidak boleh lebih rendah dari harga modal Digiflazz (Rp " . number_format($product->cost_price, 0, ',', '.') . "). Margin rugi sebesar -Rp " . number_format($lossAmount, 0, ',', '.') . " dicegah oleh sistem untuk melindungi keuntungan bisnis."
                ]);
        }

        // 2. LOGIKA DISKON: Jika harga normal (original_price) diisi, nilainya harus >= sell_price
        if ($newOriginalPrice !== null && $newOriginalPrice < $newSellPrice) {
            return back()
                ->withInput()
                ->withErrors([
                    'original_price' => "Harga normal/coret (Rp " . number_format($newOriginalPrice, 0, ',', '.') . ") tidak boleh lebih rendah dari harga promo/jual (Rp " . number_format($newSellPrice, 0, ',', '.') . "). Kosongkan jika tidak ada promo diskon."
                ]);
        }

        $oldSellPrice = $product->sell_price;
        $oldOriginalPrice = $product->original_price;

        $product->sell_price = $newSellPrice;
        $product->original_price = $newOriginalPrice;
        $product->save();

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan harga jual
        AuditLog::record(
            action: 'harga.ubah',
            targetType: 'Product',
            targetId: $product->id,
            oldValues: [
                'sell_price' => $oldSellPrice,
                'original_price' => $oldOriginalPrice,
                'cost_price' => $product->cost_price,
                'product_name' => $product->name,
                'sku' => $product->digiflazz_sku,
            ],
            newValues: [
                'sell_price' => $product->sell_price,
                'original_price' => $product->original_price,
                'margin' => $product->margin,
                'margin_percent' => $product->margin_percent . '%',
                'discount_percent' => $product->has_discount ? ($product->discount_percent . '%') : '0%',
            ],
            actorId: $user->id
        );

        // Reset cache katalog & beranda seketika
        Cache::forget('home_default_games');
        Cache::forget('catalog_default_games');
        Cache::forget('catalog_default_products');
        Cache::forget('active_game_categories');
        Cache::forget('active_game_publishers');
        Cache::forget('active_games_total_count');
        Cache::forget('active_products_total_count');
        if ($product->game) {
            Cache::forget("order_game_{$product->game->slug}");
            Cache::forget("order_game_products_{$product->game->slug}");
        }

        $successMsg = "Harga produk '{$product->name}' berhasil diperbarui. Margin untung: +Rp " . number_format($product->margin, 0, ',', '.') . " (" . $product->margin_percent . "%)";
        if ($product->has_discount) {
            $successMsg .= " | Promo Diskon {$product->discount_percent}% aktif (Hemat Rp " . number_format($product->discount_amount, 0, ',', '.') . ")";
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Mengubah stok / status aktif produk (permission: stok.ubah & audit log)
     */
    public function toggleStatus(Product $product)
    {
        $user = Auth::user();
        if (!$user->hasPermission('stok.ubah')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin stok.ubah.');
        }

        $oldStatus = $product->is_active;
        $product->is_active = !$product->is_active;
        $product->save();

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan stok/status
        AuditLog::record(
            action: 'stok.ubah',
            targetType: 'Product',
            targetId: $product->id,
            oldValues: ['is_active' => $oldStatus, 'product_name' => $product->name, 'sku' => $product->digiflazz_sku],
            newValues: ['is_active' => $product->is_active],
            actorId: $user->id
        );

        $statusText = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        // Reset cache katalog & beranda seketika
        Cache::forget('home_default_games');
        Cache::forget('catalog_default_games');
        Cache::forget('catalog_default_products');
        Cache::forget('active_game_categories');
        Cache::forget('active_game_publishers');
        Cache::forget('active_games_total_count');
        Cache::forget('active_products_total_count');
        if ($product->game) {
            Cache::forget("order_game_{$product->game->slug}");
            Cache::forget("order_game_products_{$product->game->slug}");
        }

        return back()->with('success', "Status produk '{$product->name}' berhasil {$statusText}.");
    }

    /**
     * Sinkronisasi katalog produk dari Digiflazz (permission: produk.sinkron & audit log)
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('produk.sinkron')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin produk.sinkron.');
        }

        $result = $this->digiflazzService->syncProducts();

        if (empty($result['success'])) {
            return back()->with('error', $result['message'] ?? 'Gagal melakukan sinkronisasi dengan Digiflazz.');
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Memperbarui gambar poster, banner header, dan metadata Game (permission: produk.ubah & audit log)
     */
    public function updateGameImage(Request $request, Game $game)
    {
        $user = Auth::user();
        if (!$user->hasPermission('produk.ubah')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin produk.ubah.');
        }

        $validated = $request->validate([
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'banner_file'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'preset_image'   => ['nullable', 'string', 'max:255'],
            'publisher'      => ['nullable', 'string', 'max:100'],
            'category'       => ['nullable', 'string', 'max:100'],
        ], [
            'thumbnail_file.image' => 'File thumbnail poster harus berupa file gambar valid.',
            'thumbnail_file.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, WEBP.',
            'thumbnail_file.max'   => 'Ukuran file gambar poster maksimal 4 MB.',
            'banner_file.image'    => 'File banner header harus berupa file gambar valid.',
            'banner_file.mimes'    => 'Format banner yang diperbolehkan: JPEG, PNG, JPG, WEBP.',
            'banner_file.max'      => 'Ukuran file banner header maksimal 4 MB.',
        ]);

        $oldThumbnail = $game->thumbnail;
        $oldBanner = $game->banner_image;

        $targetDir = public_path('images/games');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        // 1. Upload Thumbnail Poster Baru
        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $filename = 'game_' . $game->slug . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($targetDir, $filename);

            // Hapus file custom lama jika tersimpan di images/games
            if ($oldThumbnail && str_starts_with($oldThumbnail, 'images/games/') && file_exists(public_path($oldThumbnail))) {
                @unlink(public_path($oldThumbnail));
            }

            $game->thumbnail = 'images/games/' . $filename;
        } elseif (!empty($validated['preset_image'])) {
            $game->thumbnail = $validated['preset_image'];
        }

        // 2. Upload Banner Header Baru (Opsional)
        if ($request->hasFile('banner_file')) {
            $bFile = $request->file('banner_file');
            $bFilename = 'banner_' . $game->slug . '_' . time() . '_' . Str::random(6) . '.' . $bFile->getClientOriginalExtension();
            $bFile->move($targetDir, $bFilename);

            if ($oldBanner && str_starts_with($oldBanner, 'images/games/') && file_exists(public_path($oldBanner))) {
                @unlink(public_path($oldBanner));
            }

            $game->banner_image = 'images/games/' . $bFilename;
        }

        // 3. Metadata Tambahan
        if ($request->filled('publisher')) {
            $game->publisher = trim($validated['publisher']);
        }
        if ($request->filled('category')) {
            $game->category = trim($validated['category']);
        }

        $game->save();

        // 4. Catat Audit Log sesuai RULES.md Section 4.C
        AuditLog::record(
            action: 'produk.ubah',
            targetType: 'Game',
            targetId: $game->id,
            oldValues: [
                'game_name'    => $game->name,
                'thumbnail'    => $oldThumbnail,
                'banner_image' => $oldBanner,
            ],
            newValues: [
                'game_name'    => $game->name,
                'thumbnail'    => $game->thumbnail,
                'banner_image' => $game->banner_image,
                'publisher'    => $game->publisher,
                'category'     => $game->category,
            ],
            actorId: $user->id
        );

        // 5. Invalidate Seluruh Cache Terkait Katalog, Beranda, & Detail Game
        Cache::forget('home_default_games');
        Cache::forget('catalog_default_games');
        Cache::forget('catalog_default_products');
        Cache::forget('active_game_categories');
        Cache::forget('active_game_publishers');
        Cache::forget('admin_games_dropdown');
        Cache::forget('active_games_total_count');
        Cache::forget("order_game_{$game->slug}");
        Cache::forget("order_game_products_{$game->slug}");

        return back()->with('success', "Gambar poster & data game '{$game->name}' berhasil diperbarui dan diterapkan ke seluruh katalog.");
    }
}

