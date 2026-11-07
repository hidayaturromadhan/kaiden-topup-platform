<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::where('status', 'aktif')->orderBy('name')->get();
        $selectedSlug = $request->query('game');
        $selectedGame = $selectedSlug ? Game::where('slug', $selectedSlug)->first() : null;

        return view('guide.index', compact('games', 'selectedGame'));
    }
}
