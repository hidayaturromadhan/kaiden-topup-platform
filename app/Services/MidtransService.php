<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Dapatkan Server Key dari DB Setting atau .env
     */
    public function getServerKey(): string
    {
        return Setting::get('midtrans_server_key', config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'your-midtrans-server-key')));
    }

    /**
     * Dapatkan Client Key
     */
    public function getClientKey(): string
    {
        return Setting::get('midtrans_client_key', config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', 'your-midtrans-client-key')));
    }

    /**
     * Cek environment produksi atau sandbox
     */
    public function isProduction(): bool
    {
        $isProd = Setting::get('midtrans_is_production', config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)));
        return filter_var($isProd, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Snap API URL
     */
    public function getSnapUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Snap JS CDN Script URL
     */
    public function getSnapJsUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Membuat transaksi Snap Midtrans untuk pesanan
     */
    public function createSnapTransaction(Order $order): Payment
    {
        $order->load(['user', 'product.game']);

        // Format Midtrans Order ID: KDN-{order_id}-{timestamp}
        $midtransOrderId = $order->order_code . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int)$order->locked_price,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'item_details' => [
                [
                    'id' => $order->product->digiflazz_sku,
                    'price' => (int)$order->locked_price,
                    'quantity' => 1,
                    'name' => mb_substr($order->product->game->name . ' - ' . $order->product->name, 0, 50),
                ]
            ],
            'custom_field1' => $order->order_code,
            'custom_field2' => (string)$order->id,
            'callbacks' => [
                'finish' => route('order.show', $order->order_code),
            ],
        ];

        $serverKey = $this->getServerKey();
        $snapToken = null;
        $snapRedirectUrl = null;

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post($this->getSnapUrl(), $params);

            if ($response->successful()) {
                $data = $response->json();
                $snapToken = $data['token'] ?? null;
                $snapRedirectUrl = $data['redirect_url'] ?? null;
            } else {
                Log::warning('Midtrans Snap API Warning: ' . $response->body());
                // Fallback simulation token jika server key sandbox dummy
                $snapToken = 'SANDBOX_SNAP_' . md5($midtransOrderId . microtime(true));
                $snapRedirectUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken;
            }
        } catch (\Throwable $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            $snapToken = 'DEV_TOKEN_' . md5($midtransOrderId);
            $snapRedirectUrl = '#';
        }

        // Simpan ke tabel payments (terpisah dari orders sesuai RULES.md)
        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'midtrans_order_id' => $midtransOrderId,
                'status' => 'pending',
                'snap_token' => $snapToken,
                'snap_redirect_url' => $snapRedirectUrl,
            ]
        );

        return $payment;
    }

    /**
     * Verifikasi signature notifikasi webhook Midtrans
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $serverKey = $this->getServerKey();

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Memproses notifikasi Midtrans secara idempotent dengan Database Transaction
     */
    public function handleNotification(array $payload): array
    {
        $midtransOrderId = $payload['order_id'] ?? null;
        if (!$midtransOrderId) {
            return ['status' => 'error', 'message' => 'order_id missing'];
        }

        $payment = Payment::where('midtrans_order_id', $midtransOrderId)->first();
        if (!$payment) {
            // Coba cari dari custom_field2 (order_id)
            if (!empty($payload['custom_field2'])) {
                $payment = Payment::where('order_id', $payload['custom_field2'])->first();
            }

            if (!$payment) {
                return ['status' => 'not_found', 'message' => 'Payment record not found'];
            }
        }

        return DB::transaction(function () use ($payment, $payload) {
            // Lock record order untuk mencegah race condition (Atomic Lock)
            $order = Order::where('id', $payment->order_id)->lockForUpdate()->first();

            if (!$order) {
                return ['status' => 'error', 'message' => 'Associated order not found'];
            }

            // Aturan Idempotency (RULES.md Section 5.B.3):
            // Jika pesanan sudah DIBAYAR, DIPROSES, atau SUKSES, abaikan callback ganda
            if (in_array($order->status, ['DIBAYAR', 'DIPROSES', 'SUKSES'])) {
                Log::info("Idempotent check: Order {$order->order_code} already processed with status {$order->status}. Ignoring duplicate callback.");
                return ['status' => 'ignored', 'message' => 'Order already processed'];
            }

            $transactionStatus = $payload['transaction_status'] ?? '';
            $fraudStatus = $payload['fraud_status'] ?? '';
            $paymentType = $payload['payment_type'] ?? $payment->payment_method;

            $isSuccess = false;

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $isSuccess = true;
                }
            } elseif ($transactionStatus === 'settlement') {
                $isSuccess = true;
            }

            // Update payment record
            $payment->payment_method = $paymentType;
            $payment->status = $transactionStatus;
            $payment->notification_payload = $payload;

            if ($isSuccess) {
                $payment->paid_at = now();
                $payment->save();

                $oldStatus = $order->status;
                $order->status = 'DIBAYAR';
                $order->save();

                // Catat log status pembayaran
                AuditLog::record(
                    action: 'pembayaran.settlement',
                    targetType: 'Order',
                    targetId: $order->id,
                    oldValues: ['status' => $oldStatus],
                    newValues: ['status' => 'DIBAYAR', 'payment_id' => $payment->id, 'method' => $paymentType],
                    actorId: $order->user_id
                );

                // Langsung trigger pemenuhan pesanan Digiflazz
                app(DigiflazzService::class)->fulfill($order);

                return ['status' => 'success', 'order_status' => 'DIBAYAR'];
            } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
                $payment->save();
                $order->status = 'DIBATALKAN';
                $order->save();
                return ['status' => 'cancelled', 'order_status' => 'DIBATALKAN'];
            } elseif ($transactionStatus === 'expire') {
                $payment->save();
                $order->status = 'KEDALUWARSA';
                $order->save();
                return ['status' => 'expired', 'order_status' => 'KEDALUWARSA'];
            }

            $payment->save();
            return ['status' => 'pending', 'order_status' => $order->status];
        });
    }

    /**
     * Cek status transaksi langsung ke Midtrans API (berguna untuk sinkronisasi lokal/sandbox tanpa ngrok)
     */
    public function checkStatus(string $midtransOrderId): ?array
    {
        $serverKey = $this->getServerKey();
        $baseUrl = $this->isProduction()
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->get($baseUrl . $midtransOrderId . '/status');

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('Midtrans checkStatus exception: ' . $e->getMessage());
        }

        return null;
    }
}

