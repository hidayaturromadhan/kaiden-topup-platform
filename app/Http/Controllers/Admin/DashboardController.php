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

class DashboardController extends Controller
{
    public function __construct(
        protected DigiflazzService $digiflazzService
    ) {}

    public function index()
    {
        $today = Carbon::today();

        // Metrik Ringkasan Transaksi
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->whereIn('status', ['DIBAYAR', 'DIPROSES', 'SUKSES'])
            ->sum('locked_price');

        $pendingCount = Order::whereIn('status', ['MENUNGGU_BAYAR', 'DIPROSES'])->count();
        $successCount = Order::where('status', 'SUKSES')->count();
        $failedCount = Order::whereIn('status', ['GAGAL', 'DIBATALKAN', 'KEDALUWARSA'])->count();

        // Saldo Deposit Digiflazz (FR-FUL-4 & permission: saldo_digiflazz.lihat)
        $digiflazzBalance = null;
        if (Auth::user()->hasPermission('saldo_digiflazz.lihat')) {
            $digiflazzBalance = $this->digiflazzService->checkBalance();
        }

        // Transaksi Terbaru
        $recentOrders = Order::with(['product.game', 'user', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrdersCount',
            'todayRevenue',
            'pendingCount',
            'successCount',
            'failedCount',
            'digiflazzBalance',
            'recentOrders'
        ));
    }
}

