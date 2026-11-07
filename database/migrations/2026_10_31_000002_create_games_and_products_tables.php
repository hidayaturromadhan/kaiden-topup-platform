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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('checker_code')->nullable();
            $table->boolean('needs_zone')->default(false);
            $table->string('status')->default('aktif'); // 'aktif', 'nonaktif'
            $table->string('publisher')->nullable();
            $table->string('category')->default('Mobile Game');
            $table->string('thumbnail')->nullable();
            $table->string('banner_image')->nullable();
            $table->text('description')->nullable();
            $table->text('guide_text')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('digiflazz_sku')->index();
            $table->string('name');
            $table->unsignedBigInteger('cost_price')->default(0); // in Rupiah
            $table->unsignedBigInteger('sell_price')->default(0); // in Rupiah
            $table->boolean('is_active')->default(true);
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('games');
    }
};

