<?php

use App\Models\AuditLog;
use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Mekanisme Terjadwal 1: Sinkronisasi Produk & Harga Modal dari Digiflazz (PRD FR-PRD-1)
 */
Artisan::command('kaiden:sync-products', function (DigiflazzService $digiflazzService) {
    $this->info('Memulai sinkronisasi katalog produk & harga modal dari Digiflazz...');
    $result = $digiflazzService->syncProducts();
    $this->info($result['message']);
})->purpose('Sinkronisasi katalog produk dan harga modal dari Digiflazz secara terjadwal');

/**
 * Mekanisme Terjadwal 2: Pengecekan & Pembersihan Pesanan Pending (PRD FR-FUL-2 & FR-PAY-4)
 */
Artisan::command('kaiden:check-pending-orders', function () {
    $this->info('Memeriksa pesanan kedaluwarsa & pending...');

    // 1. Kedaluwarsakan pesanan yang belum dibayar lebih dari 24 jam
    $expiredOrders = Order::where('status', 'MENUNGGU_BAYAR')
        ->where('created_at', '<', now()->subHours(24))
        ->get();

    $expiredCount = 0;
    foreach ($expiredOrders as $order) {
        $order->status = 'KEDALUWARSA';
        $order->save();

        if ($order->payment) {
            $order->payment->status = 'expire';
            $order->payment->save();
        }

        AuditLog::record(
            action: 'transaksi.auto_kedaluwarsa',
            targetType: 'Order',
            targetId: $order->id,
            oldValues: ['status' => 'MENUNGGU_BAYAR'],
            newValues: ['status' => 'KEDALUWARSA'],
            actorId: null
        );

        $expiredCount++;
    }

    $this->info("Pengecekan selesai. {$expiredCount} pesanan diubah ke status KEDALUWARSA.");
})->purpose('Memeriksa pesanan menggantung dan mengedaluwarsakan transaksi melebihi batas waktu');

// Penjadwalan Otomatis Sesuai PRD
Schedule::command('kaiden:check-pending-orders')->everyTenMinutes();
Schedule::command('kaiden:sync-products')->dailyAt('04:00');

