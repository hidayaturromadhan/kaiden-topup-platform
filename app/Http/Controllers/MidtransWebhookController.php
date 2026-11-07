<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Verifikasi Signature Midtrans (RULES.md Section 5.B.2)
        if (!$this->midtransService->verifySignature($payload)) {
            Log::warning('Midtrans Webhook: Invalid signature detected', [
                'ip' => $request->ip(),
                'payload' => $payload,
            ]);

            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // 2. Proses notifikasi idempotent
        $result = $this->midtransService->handleNotification($payload);

        return response()->json($result);
    }
}

