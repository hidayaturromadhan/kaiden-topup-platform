<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'checker_code',
        'needs_zone',
        'status',
        'publisher',
        'category',
        'thumbnail',
        'banner_image',
        'description',
        'guide_text',
    ];

    protected function casts(): array
    {
        return [
            'needs_zone' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_active', true)->orderBy('sell_price', 'asc');
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Product::class, 'game_id', 'product_id');
    }

    public function successfulOrders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Product::class, 'game_id', 'product_id')
            ->whereIn('orders.status', ['SUKSES', 'DIPROSES', 'DIBAYAR']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true)->latest();
    }

    /**
     * Total pesanan riil berhasil
     */
    public function getSalesCountAttribute(): int
    {
        if (isset($this->attributes['orders_count'])) {
            return (int) $this->attributes['orders_count'];
        }

        return (int) \Illuminate\Support\Facades\Cache::remember("game_sales_count_{$this->id}", 300, function () {
            return $this->successfulOrders()->count();
        });
    }

    /**
     * Format tampilan terjual (misal: "4", "1.2k")
     */
    public function getSalesCountFormattedAttribute(): string
    {
        $count = $this->sales_count;
        if ($count >= 1000) {
            return number_format($count / 1000, 1) . 'k';
        }
        return (string) $count;
    }

    /**
     * Rating rata-rata dihitung dinamis dari ulasan pelanggan riil
     */
    public function getAverageRatingAttribute(): float
    {
        if (isset($this->attributes['reviews_avg_rating']) && $this->attributes['reviews_avg_rating'] !== null) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        if (array_key_exists('reviews_avg_rating', $this->attributes) && $this->attributes['reviews_avg_rating'] === null) {
            return 0.0;
        }

        return (float) \Illuminate\Support\Facades\Cache::remember("game_rating_{$this->id}", 300, function () {
            $avg = $this->reviews()->where('is_visible', true)->avg('rating');
            return $avg ? round((float) $avg, 1) : 0.0;
        });
    }

    /**
     * Jumlah ulasan / feedback riil yang masuk
     */
    public function getReviewsCountAttribute(): int
    {
        if (isset($this->attributes['reviews_count'])) {
            return (int) $this->attributes['reviews_count'];
        }

        return (int) \Illuminate\Support\Facades\Cache::remember("game_reviews_count_{$this->id}", 300, function () {
            return $this->reviews()->where('is_visible', true)->count();
        });
    }

    public function getTotalReviewsCountAttribute(): int
    {
        return $this->reviews_count;
    }

    /**
     * Produk aktif dengan harga terendah
     */
    public function getLowestProductAttribute(): ?Product
    {
        return $this->activeProducts->sortBy('sell_price')->first();
    }

    /**
     * Persentase diskon tertinggi aktif di antara produk game ini
     */
    public function getMaxDiscountPercentAttribute(): int
    {
        return (int) ($this->activeProducts->where('has_discount', true)->max('discount_percent') ?? 0);
    }

    protected static function booted(): void
    {
        static::saved(function (Game $game) {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_games');
            \Illuminate\Support\Facades\Cache::forget('active_game_categories');
            \Illuminate\Support\Facades\Cache::forget('active_game_publishers');
            \Illuminate\Support\Facades\Cache::forget('admin_games_dropdown');
            \Illuminate\Support\Facades\Cache::forget('guide_active_games');
            \Illuminate\Support\Facades\Cache::forget('guide_active_games_v2');
            \Illuminate\Support\Facades\Cache::forget('active_games_total_count');
            \Illuminate\Support\Facades\Cache::forget("order_game_{$game->slug}");
        });

        static::deleted(function (Game $game) {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_games');
            \Illuminate\Support\Facades\Cache::forget('active_game_categories');
            \Illuminate\Support\Facades\Cache::forget('active_game_publishers');
            \Illuminate\Support\Facades\Cache::forget('admin_games_dropdown');
            \Illuminate\Support\Facades\Cache::forget('guide_active_games');
            \Illuminate\Support\Facades\Cache::forget('guide_active_games_v2');
            \Illuminate\Support\Facades\Cache::forget('active_games_total_count');
            \Illuminate\Support\Facades\Cache::forget("order_game_{$game->slug}");
        });
    }
}

