<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // contoh: KDN-20261031-XXXXX
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('user_id_game');
            $table->string('zone_id')->nullable();
            $table->string('confirmed_nickname')->nullable();
            $table->unsignedBigInteger('locked_price'); // snapshot harga saat pesanan dibuat
            $table->enum('status', [
                'MENUNGGU_BAYAR',
                'DIBAYAR',
                'DIPROSES',
                'SUKSES',
                'GAGAL',
                'KEDALUWARSA',
                'DIBATALKAN'
            ])->default('MENUNGGU_BAYAR');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique()->index();
            $table->string('payment_method')->nullable(); // e.g. gopay, qris, bca_va
            $table->string('status')->default('pending'); // pending, settlement, expire, cancel, deny, failure
            $table->string('snap_token')->nullable();
            $table->string('snap_redirect_url')->nullable();
            $table->json('notification_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('digiflazz_ref_id')->unique()->index();
            $table->enum('status', ['pending', 'sukses', 'gagal'])->default('pending');
            $table->text('response_summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fulfillments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
    }
};

