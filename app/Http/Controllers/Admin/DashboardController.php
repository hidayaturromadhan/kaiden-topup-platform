<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\DigiflazzService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(
        protected DigiflazzService $digiflazzService
    ) {}

    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $validStatuses = ['DIBAYAR', 'DIPROSES', 'SUKSES'];

        // Caching Ringkasan Metrik Finansial & Grafik Analitis (120 detik, auto-cleared pada order baru)
        $metrics = Cache::remember('admin_dashboard_metrics', 120, function () use ($today, $monthStart, $validStatuses) {
            // Metrik Finansial Hari Ini
            $todayStats = Order::join('products', 'orders.product_id', '=', 'products.id')
                ->whereDate('orders.created_at', $today)
                ->whereIn('orders.status', $validStatuses)
                ->selectRaw('
                    COALESCE(SUM(orders.locked_price), 0) as revenue,
                    COALESCE(SUM(products.cost_price), 0) as cost,
                    COUNT(orders.id) as count
                ')
                ->first();

            $todayOrdersCount = (int) ($todayStats->count ?? 0);
            $todayRevenue = (int) ($todayStats->revenue ?? 0);
            $todayCost = (int) ($todayStats->cost ?? 0);
            $todayProfit = max(0, $todayRevenue - $todayCost);

            // Metrik Finansial Bulan Ini
            $monthStats = Order::join('products', 'orders.product_id', '=', 'products.id')
                ->where('orders.created_at', '>=', $monthStart)
                ->whereIn('orders.status', $validStatuses)
                ->selectRaw('
                    COALESCE(SUM(orders.locked_price), 0) as revenue,
                    COALESCE(SUM(products.cost_price), 0) as cost,
                    COUNT(orders.id) as count
                ')
                ->first();

            $monthOrdersCount = (int) ($monthStats->count ?? 0);
            $monthRevenue = (int) ($monthStats->revenue ?? 0);
            $monthCost = (int) ($monthStats->cost ?? 0);
            $monthProfit = max(0, $monthRevenue - $monthCost);
            $monthMarginPercent = $monthRevenue > 0 ? round(($monthProfit / $monthRevenue) * 100, 1) : 0;

            // Metrik Finansial Sepanjang Waktu
            $allTimeStats = Order::join('products', 'orders.product_id', '=', 'products.id')
                ->whereIn('orders.status', $validStatuses)
                ->selectRaw('
                    COALESCE(SUM(orders.locked_price), 0) as revenue,
                    COALESCE(SUM(products.cost_price), 0) as cost,
                    COUNT(orders.id) as count
                ')
                ->first();

            $allTimeOrdersCount = (int) ($allTimeStats->count ?? 0);
            $allTimeRevenue = (int) ($allTimeStats->revenue ?? 0);
            $allTimeCost = (int) ($allTimeStats->cost ?? 0);
            $allTimeProfit = max(0, $allTimeRevenue - $allTimeCost);
            $allTimeMarginPercent = $allTimeRevenue > 0 ? round(($allTimeProfit / $allTimeRevenue) * 100, 1) : 0;

            $averageOrderValue = $monthOrdersCount > 0 ? round($monthRevenue / $monthOrdersCount) : 0;

            // Status Pesanan Operasional
            $pendingCount = Order::whereIn('status', ['MENUNGGU_BAYAR', 'DIPROSES'])->count();
            $successCount = Order::where('status', 'SUKSES')->count();
            $failedCount = Order::whereIn('status', ['GAGAL', 'DIBATALKAN', 'KEDALUWARSA'])->count();
            $totalOrdersCount = Order::count();
            $settledCount = $successCount + $failedCount;
            $successRate = $settledCount > 0 ? round(($successCount / $settledCount) * 100, 1) : 100;

            // Data Tren Harian untuk Grafik (7 Hari, 14 Hari, 30 Hari)
            $trends = [
                '7d'  => $this->getTrendData(7, $validStatuses),
                '14d' => $this->getTrendData(14, $validStatuses),
                '30d' => $this->getTrendData(30, $validStatuses),
            ];

            // Performa per Game
            $topGames = Order::join('products', 'orders.product_id', '=', 'products.id')
                ->join('games', 'products.game_id', '=', 'games.id')
                ->whereIn('orders.status', $validStatuses)
                ->groupBy('games.id', 'games.name')
                ->selectRaw('
                    games.id as game_id,
                    games.name as game_name,
                    COALESCE(SUM(orders.locked_price), 0) as total_revenue,
                    COALESCE(SUM(products.cost_price), 0) as total_cost,
                    COUNT(orders.id) as total_orders
                ')
                ->orderByDesc('total_revenue')
                ->get()
                ->map(function ($item) {
                    $item->profit = max(0, $item->total_revenue - $item->total_cost);
                    $item->margin_percent = $item->total_revenue > 0
                        ? round(($item->profit / $item->total_revenue) * 100, 1)
                        : 0;
                    return $item;
                });

            return [
                'todayOrdersCount' => $todayOrdersCount,
                'todayRevenue' => $todayRevenue,
                'todayCost' => $todayCost,
                'todayProfit' => $todayProfit,
                'monthOrdersCount' => $monthOrdersCount,
                'monthRevenue' => $monthRevenue,
                'monthCost' => $monthCost,
                'monthProfit' => $monthProfit,
                'monthMarginPercent' => $monthMarginPercent,
                'allTimeOrdersCount' => $allTimeOrdersCount,
                'allTimeRevenue' => $allTimeRevenue,
                'allTimeCost' => $allTimeCost,
                'allTimeProfit' => $allTimeProfit,
                'allTimeMarginPercent' => $allTimeMarginPercent,
                'averageOrderValue' => $averageOrderValue,
                'pendingCount' => $pendingCount,
                'successCount' => $successCount,
                'failedCount' => $failedCount,
                'totalOrdersCount' => $totalOrdersCount,
                'successRate' => $successRate,
                'trends' => $trends,
                'topGames' => $topGames,
            ];
        });

        // Saldo Deposit Digiflazz dengan caching (60 detik untuk mencegah blocking network call)
        $digiflazzBalance = null;
        if (Auth::user()->hasPermission('saldo_digiflazz.lihat')) {
            $digiflazzBalance = Cache::remember('admin_digiflazz_balance_info', 60, function () {
                return $this->digiflazzService->checkBalance();
            });
        }

        // Transaksi Terbaru (cache 30 detik)
        $recentOrders = Cache::remember('admin_dashboard_recent_orders', 30, function () {
            return Order::with(['product.game', 'user', 'payment'])
                ->latest()
                ->take(8)
                ->get();
        });

        return view('admin.dashboard', array_merge($metrics, [
            'digiflazzBalance' => $digiflazzBalance,
            'recentOrders' => $recentOrders,
        ]));
    }

    /**
     * Hitung dataset tren harian (Omset, Modal, Keuntungan, Orders)
     */
    protected function getTrendData(int $days, array $validStatuses): array
    {
        $startDate = Carbon::today()->subDays($days - 1)->startOfDay();

        $dailyData = Order::join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereIn('orders.status', $validStatuses)
            ->selectRaw('
                DATE(orders.created_at) as order_date,
                SUM(orders.locked_price) as revenue,
                SUM(products.cost_price) as cost,
                COUNT(orders.id) as count
            ')
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        $labels = [];
        $revenue = [];
        $cost = [];
        $profit = [];
        $orders = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->toDateString();
            $display = $date->translatedFormat('d M');

            $record = $dailyData->get($dateKey);
            $rev = (int) ($record->revenue ?? 0);
            $cst = (int) ($record->cost ?? 0);
            $prof = max(0, $rev - $cst);
            $ord = (int) ($record->count ?? 0);

            $labels[] = $display;
            $revenue[] = $rev;
            $cost[] = $cst;
            $profit[] = $prof;
            $orders[] = $ord;
        }

        return [
            'labels'  => $labels,
            'revenue' => $revenue,
            'cost'    => $cost,
            'profit'  => $profit,
            'orders'  => $orders,
        ];
    }
}
