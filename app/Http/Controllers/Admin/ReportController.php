<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $gameId = $request->query('game_id');
        $status = $request->query('status');

        $query = Order::with(['product.game', 'user', 'payment'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($gameId) {
            $query->whereHas('product', function ($q) use ($gameId) {
                $q->where('game_id', $gameId);
            });
        }

        if ($status && $status !== 'SEMUA') {
            $query->where('status', $status);
        }

        $summary = [
            'total_transactions' => (clone $query)->count(),
            'total_revenue' => (clone $query)->whereIn('status', ['DIBAYAR', 'DIPROSES', 'SUKSES'])->sum('locked_price'),
            'total_success' => (clone $query)->where('status', 'SUKSES')->count(),
            'total_failed' => (clone $query)->whereIn('status', ['GAGAL', 'DIBATALKAN', 'KEDALUWARSA'])->count(),
        ];

        $orders = $query->latest()->paginate(20)->withQueryString();
        $games = Game::orderBy('name')->get();

        return view('admin.reports.index', compact('orders', 'games', 'startDate', 'endDate', 'gameId', 'status', 'summary'));
    }

    /**
     * Ekspor Laporan ke CSV (permission: laporan.ekspor & audit log)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if (!$user->hasPermission('laporan.ekspor')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin laporan.ekspor.');
        }

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $gameId = $request->query('game_id');
        $status = $request->query('status');

        $query = Order::with(['product.game', 'user', 'payment', 'latestFulfillment'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($gameId) {
            $query->whereHas('product', function ($q) use ($gameId) {
                $q->where('game_id', $gameId);
            });
        }

        if ($status && $status !== 'SEMUA') {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        // RULES.md Section 4.C.2: Wajib mencatat audit log saat ekspor laporan CSV
        AuditLog::record(
            action: 'laporan.ekspor',
            targetType: 'Report',
            targetId: null,
            oldValues: null,
            newValues: [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'game_id' => $gameId,
                'status_filter' => $status,
                'total_rows' => $orders->count(),
            ],
            actorId: $user->id
        );

        $filename = 'laporan_transaksi_kaiden_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $file = fopen('php://output', 'w');
            // Menulis BOM untuk kompatibilitas Excel Windows
            fputs($file, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($file, [
                'Kode Transaksi',
                'Tanggal',
                'Nama Game',
                'Item / Produk',
                'User ID Game',
                'Zone ID',
                'Nickname Terkonfirmasi',
                'Pelanggan',
                'Email',
                'Harga (Rp)',
                'Status Transaksi',
                'Metode Pembayaran',
                'Ref ID Digiflazz',
            ]);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->order_code,
                    $o->created_at->format('Y-m-d H:i:s'),
                    $o->product->game->name ?? '-',
                    $o->product->name ?? '-',
                    $o->user_id_game,
                    $o->zone_id ?? '-',
                    $o->confirmed_nickname ?? '-',
                    $o->user->name ?? '-',
                    $o->user->email ?? '-',
                    $o->locked_price,
                    $o->status,
                    $o->payment->payment_method ?? '-',
                    $o->latestFulfillment->digiflazz_ref_id ?? '-',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}

