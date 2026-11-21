<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function __construct(
        protected DigiflazzService $digiflazzService
    ) {}

    public function index(Request $request)
    {
        $status = $request->query('status');
        $gameId = $request->query('game_id');
        $search = $request->query('q');

        $query = Order::with(['product.game', 'user', 'payment', 'latestFulfillment']);

        if ($status && $status !== 'SEMUA') {
            $query->where('status', $status);
        }

        if ($gameId) {
            $query->whereHas('product', function ($q) use ($gameId) {
                $q->where('game_id', $gameId);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'LIKE', "%{$search}%")
                  ->orWhere('user_id_game', 'LIKE', "%{$search}%")
                  ->orWhere('confirmed_nickname', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $games = Cache::remember('admin_games_dropdown', 3600, function () {
            return Game::orderBy('name')->get();
        });

        return view('admin.orders.index', compact('orders', 'games', 'status', 'gameId', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['product.game', 'user', 'payment', 'fulfillments']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Retry Pemenuhan Digiflazz secara manual jika sempat pending / gagal / cut-off
     * Memungkinkan admin memproses ulang pesanan yang gagal setelah pihak seller/supplier normal.
     */
    public function retryFulfillment(Order $order)
    {
        // Cegah pemrosesan ganda jika pesanan sudah berstatus SUKSES (item sudah diterima)
        if ($order->status === 'SUKSES') {
            return back()->with('error', 'Pesanan ini sudah berstatus SUKSES dan item telah berhasil terkirim.');
        }

        // Syarat kelayakan:
        // 1. Pembayaran Midtrans sukses (settlement, capture, success), ATAU
        // 2. Status pesanan berada di DIBAYAR, GAGAL, DIPROSES, atau DIBATALKAN (dengan settlement)
        $isPaymentSettled = $order->payment && in_array(strtolower($order->payment->status), ['settlement', 'capture', 'success']);
        $isStatusEligible = in_array($order->status, ['DIBAYAR', 'GAGAL', 'DIPROSES']);

        if (!$isPaymentSettled && !$isStatusEligible) {
            return back()->with('error', 'Pesanan belum terbayar atau tidak memenuhi syarat untuk diproses ulang ke Digiflazz.');
        }

        $oldStatus = $order->status;

        // Jika pesanan sebelumnya sempat berstatus GAGAL atau DIBATALKAN, reset status menjadi DIBAYAR
        // agar proses pemenuhan di DigiflazzService dapat dieksekusi tanpa terhalang guard pembatalan
        if (in_array($order->status, ['GAGAL', 'DIBATALKAN'])) {
            $order->status = 'DIBAYAR';
            $order->save();
        }

        // Eksekusi pemenuhan ke Digiflazz
        $fulfillment = $this->digiflazzService->fulfill($order);

        // RULES.md Section 5.C.3: Wajib catat tindakan retry manual ini di audit_logs
        AuditLog::record(
            action: 'transaksi.retry_fulfillment',
            targetType: 'Order',
            targetId: $order->id,
            oldValues: ['status' => $oldStatus],
            newValues: [
                'status' => $order->status,
                'fulfillment_id' => $fulfillment->id,
                'ref_id' => $fulfillment->digiflazz_ref_id,
                'digiflazz_status' => $fulfillment->status,
                'summary' => $fulfillment->response_summary,
            ],
            actorId: Auth::id()
        );

        if ($fulfillment->status === 'sukses') {
            return back()->with('success', 'Pemenuhan Digiflazz BERHASIL! ' . ($fulfillment->response_summary ?: 'Item telah berhasil terkirim ke akun game pelanggan.'));
        } elseif ($fulfillment->status === 'gagal') {
            return back()->with('error', 'Pemenuhan Digiflazz GAGAL: ' . ($fulfillment->response_summary ?: 'Ditolak oleh provider seller.'));
        } else {
            return back()->with('success', 'Permintaan pemenuhan sedang DIPROSES oleh Digiflazz (Pending). Nomor referensi: ' . $fulfillment->digiflazz_ref_id);
        }
    }

    /**
     * Tindak Lanjut Refund Manual via Midtrans (PRD FR-FUL-3 & RULES.md Section 5.C.3)
     */
    public function markRefund(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $oldStatus = $order->status;
        $order->status = 'DIBATALKAN';
        $order->notes = ($order->notes ? $order->notes . "\n" : '') . '[REFUND MANUAL]: ' . $validated['reason'] . ' (Diproses oleh ' . Auth::user()->name . ')';
        $order->save();

        // RULES.md Section 5.C.3: Wajib dicatat di audit_logs
        AuditLog::record(
            action: 'transaksi.refund_manual',
            targetType: 'Order',
            targetId: $order->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'DIBATALKAN', 'reason' => $validated['reason']],
            actorId: Auth::id()
        );

        return back()->with('success', "Pesanan #{$order->order_code} berhasil ditandai sebagai Refund Manual.");
    }
}

