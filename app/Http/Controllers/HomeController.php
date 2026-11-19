<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Game;
use App\Models\Order;
use App\Models\Review;
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
                    ->withCount(['orders' => function ($q) {
                        $q->whereIn('orders.status', ['SUKSES', 'DIPROSES', 'DIBAYAR']);
                    }])
                    ->withCount(['reviews' => function ($q) {
                        $q->where('is_visible', true);
                    }])
                    ->withAvg(['reviews' => function ($q) {
                        $q->where('is_visible', true);
                    }], 'rating')
                    ->with(['activeProducts' => function ($q) {
                        $q->select('id', 'game_id', 'name', 'cost_price', 'original_price', 'sell_price', 'is_active')
                          ->where('is_active', true)
                          ->orderBy('sell_price', 'asc');
                    }])
                    ->orderBy('name', 'asc')
                    ->get();
            });
        } else {
            $gamesQuery = Game::where('status', 'aktif')
                ->withCount('activeProducts')
                ->withCount(['orders' => function ($q) {
                    $q->whereIn('orders.status', ['SUKSES', 'DIPROSES', 'DIBAYAR']);
                }])
                ->withCount(['reviews' => function ($q) {
                    $q->where('is_visible', true);
                }])
                ->withAvg(['reviews' => function ($q) {
                    $q->where('is_visible', true);
                }], 'rating')
                ->with(['activeProducts' => function ($q) {
                    $q->select('id', 'game_id', 'name', 'cost_price', 'original_price', 'sell_price', 'is_active')
                      ->where('is_active', true)
                      ->orderBy('sell_price', 'asc');
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
            // 1. Ambil ulasan riil yang diizinkan tampil pada pop-up (is_visible = true)
            $visibleReviews = Review::with(['order.product.game', 'user', 'game'])
                ->where('is_visible', true)
                ->latest('id')
                ->take(10)
                ->get();

            $list = [];
            foreach ($visibleReviews as $rev) {
                $ord = $rev->order;
                $gameName = $rev->game->name ?? ($ord?->product?->game?->name ?? 'Game');
                $rawNick = $ord?->confirmed_nickname ?: ($rev->user->name ?? 'gamerabah');
                $thumb = $rev->game->thumbnail ?? ($ord?->product?->game?->thumbnail ?? 'images/bg/1.jpg');
                $productName = $ord?->product?->name ?? 'Item Game';

                $list[] = [
                    'nickname' => Order::maskNickname($rawNick),
                    'action' => 'memesan ' . $productName . ' ' . $gameName,
                    'game' => $gameName,
                    'thumbnail' => asset($thumb),
                    'time_ago' => $rev->created_at ? $rev->created_at->diffForHumans() : 'baru saja',
                    'rating' => $rev->rating,
                    'comment' => $rev->comment ?: 'Pesanan sukses dikirim otomatis.',
                ];
            }

            // 2. Jika ada transaksi sukses nyata lainnya yang belum memiliki ulasan, sertakan transaksi riil
            if (count($list) < 4) {
                $realOrders = Order::with(['product.game', 'user'])
                    ->whereIn('status', ['SUKSES', 'DIBAYAR'])
                    ->whereDoesntHave('review')
                    ->latest('id')
                    ->take(5)
                    ->get();

                foreach ($realOrders as $ord) {
                    if (!$ord->product || !$ord->product->game) continue;
                    $rawNick = $ord->confirmed_nickname ?: ($ord->user->name ?? 'Pelanggan');
                    $list[] = [
                        'nickname' => Order::maskNickname($rawNick),
                        'action' => 'memesan ' . $ord->product->name . ' ' . $ord->product->game->name,
                        'game' => $ord->product->game->name,
                        'thumbnail' => asset($ord->product->game->thumbnail ?? 'images/bg/1.jpg'),
                        'time_ago' => $ord->created_at ? $ord->created_at->diffForHumans() : 'baru saja',
                        'rating' => 5,
                        'comment' => 'Pesanan sukses dikirim otomatis.',
                    ];
                }
            }

            return $list;
        });

        return view('home', compact('games', 'categories', 'search', 'category', 'sliderBanners', 'sideBanners', 'liveTestimonials'));
    }
}

