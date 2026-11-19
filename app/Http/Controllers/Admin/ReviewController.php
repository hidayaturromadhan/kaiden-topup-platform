<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Tampilkan daftar seluruh feedback & ulasan pelanggan
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $ratingFilter = $request->query('rating');
        $visibilityFilter = $request->query('visibility');
        $gameFilter = $request->query('game_id');

        $query = Review::with(['order.product.game', 'user', 'game'])
            ->latest('id');

        // Filter Pencarian (nama pelanggan, email, kode pesanan, komentar)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_code', 'LIKE', "%{$search}%")
                         ->orWhere('confirmed_nickname', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter Rating Bintang (1 - 5)
        if (!empty($ratingFilter) && in_array((int)$ratingFilter, [1, 2, 3, 4, 5])) {
            $query->where('rating', (int)$ratingFilter);
        }

        // Filter Status Tampilan Pop-up
        if ($visibilityFilter === 'visible') {
            $query->where('is_visible', true);
        } elseif ($visibilityFilter === 'hidden') {
            $query->where('is_visible', false);
        }

        // Filter Game
        if (!empty($gameFilter)) {
            $query->where('game_id', (int)$gameFilter);
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Data Statistik Rekapitulasi
        $totalCount = Review::count();
        $avgRating = Review::avg('rating') ?: 0;
        $visibleCount = Review::where('is_visible', true)->count();
        $hiddenCount = Review::where('is_visible', false)->count();

        $starCounts = [
            5 => Review::where('rating', 5)->count(),
            4 => Review::where('rating', 4)->count(),
            3 => Review::where('rating', 3)->count(),
            2 => Review::where('rating', 2)->count(),
            1 => Review::where('rating', 1)->count(),
        ];

        $games = Game::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact(
            'reviews',
            'totalCount',
            'avgRating',
            'visibleCount',
            'hiddenCount',
            'starCounts',
            'search',
            'ratingFilter',
            'visibilityFilter',
            'gameFilter',
            'games'
        ));
    }

    /**
     * Beralih status tampil/sembunyi feedback pada pop-up beranda
     */
    public function toggleVisibility(Request $request, Review $review)
    {
        $oldState = (bool)$review->is_visible;
        $newState = !$oldState;

        $review->is_visible = $newState;
        $review->save();

        // Hapus cache pop-up beranda agar perubahan langsung aktif seketika
        Cache::forget('home_live_testimonials');

        // Catat pada Audit Log
        AuditLog::record(
            action: 'feedback.toggle_popup',
            targetType: 'Review',
            targetId: (string)$review->id,
            oldValues: ['is_visible' => $oldState],
            newValues: [
                'is_visible' => $newState,
                'order_code' => $review->order?->order_code,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ],
            actorId: auth()->id()
        );

        $statusLabel = $newState ? 'Ditampilkan pada pop-up beranda' : 'Disembunyikan dari pop-up beranda';

        return back()->with('success', "Status feedback berhasil diubah: {$statusLabel}.");
    }

    /**
     * Simpan / perbarui balasan admin untuk ulasan pelanggan
     */
    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'nullable|string|max:500',
        ], [
            'admin_reply.max' => 'Balasan admin maksimal 500 karakter.',
        ]);

        $oldReply = $review->admin_reply;
        $newReply = $validated['admin_reply'] ?? null;

        $review->admin_reply = $newReply;
        $review->save();

        AuditLog::record(
            action: 'feedback.balas',
            targetType: 'Review',
            targetId: (string)$review->id,
            oldValues: ['admin_reply' => $oldReply],
            newValues: ['admin_reply' => $newReply, 'order_code' => $review->order?->order_code],
            actorId: auth()->id()
        );

        return back()->with('success', 'Balasan resmi untuk ulasan pelanggan berhasil diperbarui.');
    }
}
