<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $games = Cache::remember('guide_active_games', 3600, function () {
            return Game::where('status', 'aktif')->orderBy('name')->get();
        });

        $selectedSlug = $request->query('game');
        $selectedGame = $selectedSlug ? Game::where('slug', $selectedSlug)->first() : null;

        return view('guide.index', compact('games', 'selectedGame'));
    }
}
