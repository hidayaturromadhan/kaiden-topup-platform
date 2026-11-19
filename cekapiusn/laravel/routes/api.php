<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameCheckController;

/*
|--------------------------------------------------------------------------
| API Routes for Game Nickname Checker
|--------------------------------------------------------------------------
|
| Daftarkan route ini di file routes/api.php pada aplikasi Laravel Anda.
|
*/

Route::post('/game/check-ign', [GameCheckController::class, 'check']);
