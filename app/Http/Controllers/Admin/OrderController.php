<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $games = Game::orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'games', 'status', 'gameId', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['product.game', 'user', 'payment', 'fulfillments']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Retry Pemenuhan Digiflazz secara manual jika sempat pending / gagal
     */
    public function retryFulfillment(Order $order)
    {
        if (!in_array($order->status, ['DIBAYAR', 'GAGAL', 'DIPROSES'])) {
            return back()->withErrors(['error' => 'Pesanan tidak memenuhi syarat untuk diproses ulang.']);
        }

        $fulfillment = $this->digiflazzService->fulfill($order);

        return back()->with('success', 'Perintah pemenuhan ke Digiflazz berhasil dikirim ulang.');
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

