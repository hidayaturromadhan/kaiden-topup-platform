<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    public function __construct(
        protected DigiflazzService $digiflazzService
    ) {}

    /**
     * Menampilkan Halaman Katalog Khusus Produk & Game KAIDEN
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $category = $request->query('category');
        $publisher = $request->query('publisher');
        $sort = $request->query('sort', 'populer');
        $tab = $request->query('tab', 'games'); // 'games' atau 'products'

        // Daftar kategori game aktif
        $categories = Cache::remember('active_game_categories', 3600, function () {
            return Game::where('status', 'aktif')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');
        });

        // Daftar publisher aktif
        $publishers = Cache::remember('active_game_publishers', 3600, function () {
            return Game::where('status', 'aktif')
                ->whereNotNull('publisher')
                ->distinct()
                ->pluck('publisher');
        });

        // 1. Query untuk Katalog Berdasarkan Game
        $gamesQuery = Game::where('status', 'aktif')
            ->withCount('activeProducts')
            ->with(['activeProducts' => function ($q) {
                $q->select('id', 'game_id', 'name', 'sell_price', 'category', 'is_active');
            }]);

        if ($search) {
            $gamesQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('publisher', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('activeProducts', function ($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($category && $category !== 'Semua') {
            $gamesQuery->where('category', $category);
        }

        if ($publisher && $publisher !== 'Semua') {
            $gamesQuery->where('publisher', $publisher);
        }

        // Sorting Game
        switch ($sort) {
            case 'nama_asc':
                $gamesQuery->orderBy('name', 'asc');
                break;
            case 'nama_desc':
                $gamesQuery->orderBy('name', 'desc');
                break;
            case 'harga_asc':
                // Diurutkan berdasarkan harga produk terendah di memori/collection setelah query
                break;
            case 'harga_desc':
                break;
            default: // populer
                $gamesQuery->orderBy('id', 'asc');
                break;
        }

        $games = $gamesQuery->get();

        if ($sort === 'harga_asc') {
            $games = $games->sortBy(function ($game) {
                return $game->activeProducts->min('sell_price') ?? PHP_INT_MAX;
            })->values();
        } elseif ($sort === 'harga_desc') {
            $games = $games->sortByDesc(function ($game) {
                return $game->activeProducts->min('sell_price') ?? 0;
            })->values();
        }

        // 2. Query untuk Katalog Berdasarkan Item / Denominasi Produk Individu
        $productsQuery = Product::where('is_active', true)
            ->with(['game' => function ($q) {
                $q->select('id', 'name', 'slug', 'category', 'publisher', 'thumbnail');
            }])
            ->whereHas('game', function ($gq) use ($category, $publisher) {
                $gq->where('status', 'aktif');
                if ($category && $category !== 'Semua') {
                    $gq->where('category', $category);
                }
                if ($publisher && $publisher !== 'Semua') {
                    $gq->where('publisher', $publisher);
                }
            });

        if ($search) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('digiflazz_sku', 'LIKE', "%{$search}%")
                  ->orWhereHas('game', function ($gq) use ($search) {
                      $gq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('publisher', 'LIKE', "%{$search}%");
                  });
            });
        }

        switch ($sort) {
            case 'nama_asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'nama_desc':
                $productsQuery->orderBy('name', 'desc');
                break;
            case 'harga_asc':
                $productsQuery->orderBy('sell_price', 'asc');
                break;
            case 'harga_desc':
                $productsQuery->orderBy('sell_price', 'desc');
                break;
            default:
                $productsQuery->orderBy('sell_price', 'asc');
                break;
        }

        $products = $productsQuery->get();
        $currentBalance = $this->digiflazzService->getCurrentBalance();
        $products->each(function ($prod) use ($currentBalance) {
            $prod->is_stock_available = ($currentBalance >= $prod->cost_price);
        });

        $totalGamesCount = Game::where('status', 'aktif')->count();
        $totalProductsCount = Product::where('is_active', true)->whereHas('game', fn($q) => $q->where('status', 'aktif'))->count();

        // Respon AJAX dinamis untuk filter tanpa reload halaman dengan skeleton web render
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $html = view('catalog.partials.content', compact(
                'games',
                'products',
                'tab',
                'search',
                'category',
                'publisher',
                'sort'
            ))->render();

            return response()->json([
                'status' => 'success',
                'html' => $html,
                'tab' => $tab,
                'category' => $category,
                'publisher' => $publisher,
                'sort' => $sort,
                'search' => $search,
                'games_count' => $games->count(),
                'products_count' => $products->count(),
                'total_games_count' => $totalGamesCount,
                'total_products_count' => $totalProductsCount,
            ]);
        }

        return view('catalog.index', compact(
            'games',
            'products',
            'categories',
            'publishers',
            'search',
            'category',
            'publisher',
            'sort',
            'tab',
            'totalGamesCount',
            'totalProductsCount'
        ));
    }
}

