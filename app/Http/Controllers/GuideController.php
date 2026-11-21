<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        // Daftar kata kunci produk non-game (telekomunikasi/PPOB/PLN/TV/Gas) yang tidak ditampilkan sebagai game
        $nonGameKeywords = [
            'pulsa', 'data', 'telkomsel', 'indosat', 'xl', 'tri', 'smartfren', 
            'axis', 'pln', 'listrik', 'gas', 'pertamina', 'bpjs', 'pdam', 
            'emoney', 'e-money', 'ovo', 'gopay', 'dana', 'shopeepay', 'linkaja', 'k-vision'
        ];

        // Gunakan key cache baru 'guide_active_games_v2' agar seketika aktif tanpa perlu menunggu cache lama expire
        $games = Cache::remember('guide_active_games_v2', 3600, function () use ($nonGameKeywords) {
            return Game::where('status', 'aktif')
                ->whereHas('activeProducts')
                ->where(function ($q) use ($nonGameKeywords) {
                    foreach ($nonGameKeywords as $kw) {
                        $q->where('slug', 'NOT LIKE', "%{$kw}%")
                          ->where('name', 'NOT LIKE', "%{$kw}%");
                    }
                })
                ->withCount('activeProducts')
                ->with(['activeProducts' => function ($q) {
                    $q->select('id', 'game_id', 'name', 'sell_price', 'is_active')
                      ->where('is_active', true)
                      ->orderBy('sell_price', 'asc');
                }])
                ->orderBy('name', 'asc')
                ->get();
        });

        $selectedSlug = $request->query('game');
        $selectedGame = $selectedSlug ? $games->firstWhere('slug', $selectedSlug) : null;

        return view('guide.index', compact('games', 'selectedGame'));
    }
}
