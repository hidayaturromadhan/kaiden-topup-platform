<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true)->latest();
    }

    /**
     * Rating rata-rata dihitung dinamis dari ulasan pelanggan
     */
    public function getAverageRatingAttribute(): float
    {
        return \Illuminate\Support\Facades\Cache::remember("game_rating_{$this->id}", 3600, function () {
            $avg = $this->reviews()->where('is_visible', true)->avg('rating');
            return $avg ? round((float)$avg, 2) : 4.95;
        });
    }

    /**
     * Jumlah ulasan yang masuk
     */
    public function getTotalReviewsCountAttribute(): int
    {
        return $this->reviews()->where('is_visible', true)->count();
    }

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('active_game_categories');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('active_game_categories');
        });
    }
}

