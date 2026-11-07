<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Services\DigiflazzService;
use App\Services\GameCheckService;
use App\Services\MidtransService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected GameCheckService $gameCheckService,
        protected MidtransService $midtransService,
        protected DigiflazzService $digiflazzService
    ) {}

    /**
     * Halaman Detail Game & Pemesanan (Layout 2 Kolom Asimetris sesuai RULES.md)
     */
    public function showGame(string $slug)
    {
        $game = Game::where('slug', $slug)->where('status', 'aktif')->firstOrFail();
        $products = $game->activeProducts()->orderBy('sell_price')->get();
        $currentBalance = $this->digiflazzService->getCurrentBalance();

        // Tandai ketersediaan tiap produk terhadap saldo modal operasional supplier
        $products->each(function ($prod) use ($currentBalance) {
            $prod->is_stock_available = ($currentBalance >= $prod->cost_price);
        });

        return view('orders.create', compact('game', 'products', 'currentBalance'));
    }

    /**
     * Pengecekan IGN Server-side (FR-IGN-1 & RULES.md Section 5.A)
     */
    public function checkIgn(Request $request)
    {
        $request->validate([
            'game_code' => ['required', 'string'],
            'user_id' => ['required', 'string', 'max:50'],
            'zone_id' => ['nullable', 'string', 'max:50'],
        ]);

        $ip = $request->ip();
        $rateKey = "ign_check_rate:{$ip}";

        // Rate limiter pada route pengecekan (RULES.md Section 5.A.2)
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak permintaan pengecekan ID. Harap tunggu {$seconds} detik.",
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        $result = $this->gameCheckService->check(
            $request->input('game_code'),
            $request->input('user_id'),
            $request->input('zone_id')
        );

        return response()->json($result);
    }

    /**
     * Checkout Pesanan (FR-ORD-1, FR-PAY-1, FR-RBAC-2)
     */
    public function checkout(Request $request)
    {
        // Pastikan hanya role pelanggan dengan permission 'pesanan.buat' yang bisa order (RULES.md Section 4.A)
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan pemesanan.');
        }

        if (!$user->hasPermission('pesanan.buat')) {
            abort(403, 'Akses ditolak: Akun staf internal dilarang membuat pesanan langsung.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'user_id_game' => ['required', 'string', 'max:50'],
            'zone_id' => ['nullable', 'string', 'max:50'],
            'confirmed_nickname' => ['nullable', 'string', 'max:100'],
            'manual_confirmation' => ['nullable', 'boolean'],
        ]);

        $product = Product::with('game')->findOrFail($validated['product_id']);

        if (!$product->is_active || $product->game->status !== 'aktif') {
            return back()->withInput()->withErrors(['product_id' => 'Produk atau game ini sedang tidak tersedia.']);
        }

        // VALIDASI SEBELUM PEMBAYARAN: Cek kesiapan stok supplier (tanpa membocorkan istilah modal/saldo ke pelanggan)
        $currentBalance = $this->digiflazzService->getCurrentBalance();
        if ($product->cost_price > $currentBalance) {
            return back()
                ->withInput()
                ->withErrors([
                    'product_id' => 'Mohon maaf, nominal produk ini sedang dalam pemeliharaan sistem atau pembaruan stok. Silakan pilih nominal lainnya atau coba beberapa saat lagi.',
                ]);
        }

        if ($product->game->needs_zone && empty($validated['zone_id'])) {
            return back()->withInput()->withErrors(['zone_id' => 'Zone/Server ID wajib diisi untuk game ini.']);
        }

        return DB::transaction(function () use ($user, $product, $validated) {
            // Generate Kode Unik: KDN-YYYYMMDD-XXXXX
            $orderCode = 'KDN-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Kunci harga produk ke orders.locked_price (RULES.md Section 5.B.1)
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $user->id,
                'product_id' => $product->id,
                'user_id_game' => $validated['user_id_game'],
                'zone_id' => $validated['zone_id'] ?? null,
                'confirmed_nickname' => $validated['confirmed_nickname'] ?? null,
                'locked_price' => $product->sell_price,
                'status' => 'MENUNGGU_BAYAR',
            ]);

            // Buat transaksi Midtrans Snap
            $payment = $this->midtransService->createSnapTransaction($order);

            return redirect()->route('order.show', $order->order_code);
        });
    }

    /**
     * Halaman Detail & Pembayaran Pesanan (Anti-IDOR Ownership Enforced via OrderPolicy)
     */
    public function show(string $orderCode)
    {
        $order = Order::with(['product.game', 'payment', 'fulfillments', 'user', 'review'])
            ->where('order_code', $orderCode)
            ->firstOrFail();

        // Otorisasi Anti-IDOR (RULES.md Section 4.B.2)
        $this->authorize('view', $order);

        // Sinkronisasi status instan dari Midtrans jika masih MENUNGGU_BAYAR (sangat berguna untuk uji coba lokal/sandbox)
        if ($order->status === 'MENUNGGU_BAYAR' && $order->payment && $order->payment->midtrans_order_id) {
            $statusData = $this->midtransService->checkStatus($order->payment->midtrans_order_id);
            if ($statusData && !empty($statusData['transaction_status']) && in_array($statusData['transaction_status'], ['settlement', 'capture', 'cancel', 'deny', 'expire'])) {
                $this->midtransService->handleNotification($statusData);
                $order->refresh();
                $order->load(['product.game', 'payment', 'fulfillments', 'user', 'review']);
            }
        }

        $clientKey = $this->midtransService->getClientKey();
        $snapJsUrl = $this->midtransService->getSnapJsUrl();

        return view('orders.show', compact('order', 'clientKey', 'snapJsUrl'));
    }

    /**
     * Riwayat Pesanan Pelanggan (permission: transaksi.lihat_milik_sendiri)
     */
    public function myOrders(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('transaksi.lihat_milik_sendiri')) {
            abort(403, 'Akses ditolak.');
        }

        $status = $request->query('status');

        $query = Order::with(['product.game', 'payment'])
            ->where('user_id', $user->id);

        if ($status && $status !== 'SEMUA') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'status'));
    }
}

