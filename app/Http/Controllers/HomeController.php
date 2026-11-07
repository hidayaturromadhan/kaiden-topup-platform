<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Game;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $category = $request->query('category');

        // Cache daftar kategori aktif selama 1 jam (3600 detik)
        $categories = Cache::remember('active_game_categories', 3600, function () {
            return Game::where('status', 'aktif')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');
        });

        // Jika tidak ada pencarian atau filter kategori khusus, gunakan cache katalog 10 menit
        if (empty($search) && (empty($category) || $category === 'Semua')) {
            $games = Cache::remember('home_default_games', 600, function () {
                return Game::where('status', 'aktif')
                    ->withCount('activeProducts')
                    ->with(['activeProducts' => function ($q) {
                        $q->select('id', 'game_id', 'sell_price');
                    }])
                    ->orderBy('name', 'asc')
                    ->get();
            });
        } else {
            $gamesQuery = Game::where('status', 'aktif')
                ->withCount('activeProducts')
                ->with(['activeProducts' => function ($q) {
                    $q->select('id', 'game_id', 'sell_price');
                }]);

            if ($search) {
                $gamesQuery->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('publisher', 'LIKE', "%{$search}%");
                });
            }

            if ($category && $category !== 'Semua') {
                $gamesQuery->where('category', $category);
            }

            $games = $gamesQuery->orderBy('name', 'asc')->get();
        }

        // Respon AJAX dinamis untuk filter kategori game di beranda tanpa reload halaman
        if ($request->ajax() || $request->has('ajax_games')) {
            $html = view('home.partials.games_grid', compact('games'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'category' => $category,
                'count' => $games->count()
            ]);
        }

        // Cache banner slider utama beranda selama 1 jam (3600 detik)
        $sliderBanners = Cache::remember('active_slider_banners', 3600, function () {
            return Banner::where('banner_type', 'slider')
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'desc')
                ->get();
        });

        // Cache side banner samping beranda selama 1 jam (3600 detik)
        $sideBanners = Cache::remember('active_side_banners', 3600, function () {
            return Banner::where('banner_type', 'side')
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'desc')
                ->take(2)
                ->get();
        });

        // Data Ulasan & Transaksi Pelanggan untuk Pop-up Beranda (Nickname dimask: misal gamerabah -> ga******bah)
        $liveTestimonials = Cache::remember('home_live_testimonials', 300, function () {
            $realOrders = Order::with(['product.game', 'review'])
                ->whereIn('status', ['SUKSES', 'DIBAYAR'])
                ->latest()
                ->take(10)
                ->get();

            $list = [];
            foreach ($realOrders as $ord) {
                if (!$ord->product || !$ord->product->game) continue;
                $rawNick = $ord->confirmed_nickname ?: ($ord->user->name ?? 'gamerabah');
                $list[] = [
                    'nickname' => Order::maskNickname($rawNick),
                    'action' => 'memesan ' . $ord->product->name . ' ' . $ord->product->game->name,
                    'game' => $ord->product->game->name,
                    'thumbnail' => asset($ord->product->game->thumbnail ?? 'images/bg/1.jpg'),
                    'time_ago' => $ord->created_at ? $ord->created_at->diffForHumans() : 'baru saja',
                    'rating' => $ord->review ? $ord->review->rating : 5,
                    'comment' => $ord->review && $ord->review->comment ? $ord->review->comment : 'Pesanan sukses dikirim otomatis.',
                ];
            }

            // Fallback contoh realistis pelanggan jika data database masih baru
            if (count($list) < 4) {
                $fallbacks = [
                    [
                        'nickname' => Order::maskNickname('gamerabah'),
                        'action' => 'memesan 70 Diamonds Free Fire',
                        'game' => 'Free Fire',
                        'thumbnail' => asset('images/bg/freefire.jpg'),
                        'time_ago' => '2 menit yang lalu',
                        'rating' => 5,
                        'comment' => 'Top up diamond Free Fire kilat banget, baru bayar langsung masuk!',
                    ],
                    [
                        'nickname' => Order::maskNickname('sultan_alucard'),
                        'action' => 'memesan Weekly Diamond Pass Mobile Legends',
                        'game' => 'Mobile Legends',
                        'thumbnail' => asset('images/bg/aspirant.jpg'),
                        'time_ago' => '5 menit yang lalu',
                        'rating' => 5,
                        'comment' => 'Weekly pass langsung aktif otomatis kurang dari 30 detik. Recommended!',
                    ],
                    [
                        'nickname' => Order::maskNickname('rey_sniper'),
                        'action' => 'memesan 1375 Points Valorant',
                        'game' => 'Valorant',
                        'thumbnail' => asset('images/bg/valorant.jpg'),
                        'time_ago' => '8 menit yang lalu',
                        'rating' => 5,
                        'comment' => 'Bayar pakai QRIS langsung terverifikasi instan tanpa ribet.',
                    ],
                    [
                        'nickname' => Order::maskNickname('bintang_mythic'),
                        'action' => 'memesan 296 Diamonds Mobile Legends',
                        'game' => 'Mobile Legends',
                        'thumbnail' => asset('images/bg/aspirant.jpg'),
                        'time_ago' => '12 menit yang lalu',
                        'rating' => 5,
                        'comment' => 'Harga paling murah dan proses resmi anti minus diamond.',
                    ],
                ];

                foreach ($fallbacks as $fb) {
                    if (count($list) >= 6) break;
                    $list[] = $fb;
                }
            }

            return $list;
        });

        return view('home', compact('games', 'categories', 'search', 'category', 'sliderBanners', 'sideBanners', 'liveTestimonials'));
    }
}

