<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameCheckService;
use Illuminate\Http\JsonResponse;

class GameCheckController extends Controller
{
    protected GameCheckService $gameCheckService;

    public function __construct(GameCheckService $gameCheckService)
    {
        $this->gameCheckService = $gameCheckService;
    }

    /**
     * Endpoint API untuk cek IGN
     * POST /api/game/check-ign
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game'    => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
        ], [
            'game.required'    => 'Parameter game wajib diisi (contoh: MOBILE_LEGENDS, FREEFIRE).',
            'user_id.required' => 'Parameter user_id wajib diisi.',
        ]);

        $result = $this->gameCheckService->checkNickname(
            $validated['game'],
            $validated['user_id'],
            $validated['zone_id'] ?? null
        );

        $httpStatus = $result['status'] ?? ($result['success'] ? 200 : 400);

        return response()->json($result, $httpStatus);
    }
}
