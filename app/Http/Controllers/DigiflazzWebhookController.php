<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DigiflazzWebhookController extends Controller
{
    /**
     * Menangani callback status pemenuhan dari Digiflazz (FR-FUL-2 & RULES.md Section 5.C)
     * Payload Digiflazz berupa event update status dengan data: ref_id, status, message, sn.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Digiflazz Webhook Received', [
            'headers' => [
                'event' => $request->header('X-Digiflazz-Event'),
                'delivery' => $request->header('X-Digiflazz-Delivery'),
            ],
            'payload' => $payload,
        ]);

        $data = $payload['data'] ?? $payload;
        $refId = $data['ref_id'] ?? null;

        if (!$refId) {
            return response()->json(['success' => false, 'message' => 'ref_id tidak ditemukan dalam payload.'], 400);
        }

        // Cari record fulfillment berdasarkan ref_id unik Digiflazz
        $fulfillment = Fulfillment::where('digiflazz_ref_id', $refId)->first();

        if (!$fulfillment) {
            Log::warning("Digiflazz Webhook: Fulfillment dengan ref_id {$refId} tidak ditemukan.");
            return response()->json(['success' => false, 'message' => 'Fulfillment record tidak ditemukan.'], 404);
        }

        $rawStatus = strtolower(trim($data['status'] ?? ''));
        $message = $data['message'] ?? ($data['sn'] ?? 'Status diperbarui via webhook Digiflazz');
        $sn = $data['sn'] ?? null;

        return DB::transaction(function () use ($fulfillment, $rawStatus, $message, $sn, $data) {
            $order = Order::where('id', $fulfillment->order_id)->lockForUpdate()->first();

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Pesanan terkait tidak ditemukan.'], 404);
            }

            $oldOrderStatus = $order->status;
            $oldFulfillmentStatus = $fulfillment->status;

            if ($rawStatus === 'sukses') {
                $fulfillment->status = 'sukses';
                $fulfillment->response_summary = $sn ? "SN: {$sn} - {$message}" : $message;
                $fulfillment->save();

                $order->status = 'SUKSES';
                $order->save();

                AuditLog::record(
                    action: 'digiflazz.webhook_sukses',
                    targetType: 'Order',
                    targetId: $order->id,
                    oldValues: ['order_status' => $oldOrderStatus, 'fulfillment_status' => $oldFulfillmentStatus],
                    newValues: [
                        'order_status' => 'SUKSES',
                        'fulfillment_status' => 'sukses',
                        'sn' => $sn,
                        'message' => $message,
                    ],
                    actorId: $order->user_id
                );
            } elseif ($rawStatus === 'gagal') {
                $fulfillment->status = 'gagal';
                $fulfillment->response_summary = $message;
                $fulfillment->save();

                // Tandai pesanan sebagai GAGAL agar masuk antrean tindak lanjut admin (PRD FR-FUL-3)
                $order->status = 'GAGAL';
                $order->notes = ($order->notes ? $order->notes . "\n" : '') . "[DIGIFLAZZ GAGAL]: {$message} (Waktu: " . now()->format('d/m/Y H:i') . ')';
                $order->save();

                AuditLog::record(
                    action: 'digiflazz.webhook_gagal',
                    targetType: 'Order',
                    targetId: $order->id,
                    oldValues: ['order_status' => $oldOrderStatus, 'fulfillment_status' => $oldFulfillmentStatus],
                    newValues: [
                        'order_status' => 'GAGAL',
                        'fulfillment_status' => 'gagal',
                        'message' => $message,
                    ],
                    actorId: $order->user_id
                );
            } else {
                // Status pending / proses
                $fulfillment->status = 'pending';
                $fulfillment->response_summary = $message;
                $fulfillment->save();
            }

            return response()->json([
                'success' => true,
                'message' => "Webhook berhasil diproses. Status: {$fulfillment->status}",
                'order_status' => $order->status,
            ]);
        });
    }
}

