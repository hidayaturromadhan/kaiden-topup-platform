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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('discount_tag')->nullable();
            $table->string('discount_value')->nullable();
            $table->string('discount_subtext')->nullable();
            $table->string('button_text')->default('Klaim Promo');
            $table->string('button_url')->default('#katalog-game');
            $table->string('image')->nullable();
            $table->string('banner_type', 30)->default('slider'); // 'slider' or 'side'
            $table->string('gradient_theme', 30)->default('crimson'); // 'crimson', 'slate', 'emerald', 'amber', 'blue'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['banner_type', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
