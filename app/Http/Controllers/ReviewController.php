<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan rating & ulasan dari pelanggan setelah pesanan berstatus sukses/dibayar
     */
    public function store(Request $request, string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // 1. Verifikasi kepemilikan pesanan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memberikan ulasan untuk pesanan ini.');
        }

        // 2. Verifikasi status pesanan harus selesai (SUKSES atau DIBAYAR)
        if (!in_array($order->status, ['SUKSES', 'DIBAYAR'])) {
            return back()->with('error', 'Ulasan hanya dapat dikirim setelah pesanan Anda berhasil diproses.');
        }

        // 3. Verifikasi belum pernah memberikan ulasan
        if ($order->review()->exists()) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
        ], [
            'rating.required' => 'Silakan pilih rating bintang 1 sampai 5.',
            'rating.between' => 'Rating harus bernilai antara 1 sampai 5 bintang.',
            'comment.max' => 'Panjang ulasan maksimal 500 karakter.',
        ]);

        Review::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'game_id' => $order->product->game_id,
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_visible' => true,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda! Penilaian Anda sangat berarti bagi kami.');
    }
}

