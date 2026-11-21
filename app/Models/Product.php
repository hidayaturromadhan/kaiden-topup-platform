<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'digiflazz_sku',
        'name',
        'cost_price',
        'original_price',
        'sell_price',
        'is_active',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'integer',
            'original_price' => 'integer',
            'sell_price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Hitung total transaksi berhasil untuk produk ini
     */
    public function getSalesCountAttribute(): int
    {
        if (isset($this->attributes['orders_count'])) {
            return (int) $this->attributes['orders_count'];
        }

        return (int) \Illuminate\Support\Facades\Cache::remember("product_sales_count_{$this->id}", 300, function () {
            return $this->orders()->whereIn('status', ['SUKSES', 'DIPROSES', 'DIBAYAR'])->count();
        });
    }

    /**
     * Format tampilan terjual (misal: "0", "12", "1.2k")
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
     * Rating rata-rata produk riil (berdasarkan review pesanan produk ini, atau rating game terkait)
     */
    public function getAverageRatingAttribute(): float
    {
        if (isset($this->attributes['reviews_avg_rating']) && $this->attributes['reviews_avg_rating'] !== null) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        return (float) \Illuminate\Support\Facades\Cache::remember("product_rating_{$this->id}", 300, function () {
            $avg = Review::whereHas('order', fn($q) => $q->where('product_id', $this->id))
                         ->where('is_visible', true)
                         ->avg('rating');

            if ($avg !== null && $avg > 0) {
                return round((float) $avg, 1);
            }

            return $this->game ? $this->game->average_rating : 0.0;
        });
    }

    /**
     * Jumlah ulasan riil produk ini (atau review game induk)
     */
    public function getReviewsCountAttribute(): int
    {
        if (isset($this->attributes['reviews_count'])) {
            return (int) $this->attributes['reviews_count'];
        }

        return (int) \Illuminate\Support\Facades\Cache::remember("product_reviews_count_{$this->id}", 300, function () {
            $count = Review::whereHas('order', fn($q) => $q->where('product_id', $this->id))
                           ->where('is_visible', true)
                           ->count();

            if ($count > 0) {
                return $count;
            }

            return $this->game ? $this->game->reviews_count : 0;
        });
    }

    /**
     * Hitung margin keuntungan dalam nominal Rupiah
     */
    public function getMarginAttribute(): int
    {
        return max(0, $this->sell_price - $this->cost_price);
    }

    /**
     * Hitung persentase margin keuntungan terhadap harga modal Digiflazz
     */
    public function getMarginPercentAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0.0;
        }

        return round((($this->sell_price - $this->cost_price) / $this->cost_price) * 100, 1);
    }

    /**
     * Status apakah produk sedang memiliki diskon aktif dari harga normal
     */
    public function getHasDiscountAttribute(): bool
    {
        return !empty($this->original_price) && $this->original_price > $this->sell_price;
    }

    /**
     * Nominal potongan harga dari harga normal (Rupiah)
     */
    public function getDiscountAmountAttribute(): int
    {
        return $this->has_discount ? ($this->original_price - $this->sell_price) : 0;
    }

    /**
     * Persentase potongan harga dari harga normal (%)
     */
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->has_discount || $this->original_price <= 0) {
            return 0;
        }

        return (int)round((($this->original_price - $this->sell_price) / $this->original_price) * 100);
    }

    /**
     * Penilaian kesehatan margin keuntungan untuk mencegah risiko boncos/rugi modal
     */
    public function getMarginStatusAttribute(): array
    {
        if ($this->sell_price < $this->cost_price) {
            return [
                'level' => 'danger',
                'label' => 'Rugi Modal',
                'badge_class' => 'bg-red-50 text-red-700 border-red-200',
                'is_loss' => true,
            ];
        }

        if ($this->margin_percent < 3.0) {
            return [
                'level' => 'warning',
                'label' => 'Margin Tipis (<3%)',
                'badge_class' => 'bg-amber-50 text-amber-800 border-amber-200',
                'is_loss' => false,
            ];
        }

        return [
            'level' => 'healthy',
            'label' => 'Margin Sehat',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'is_loss' => false,
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_products');
            \Illuminate\Support\Facades\Cache::forget('active_products_total_count');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_metrics');
            if ($product->game_id) {
                $slug = $product->game?->slug;
                if ($slug) {
                    \Illuminate\Support\Facades\Cache::forget("order_game_{$slug}");
                    \Illuminate\Support\Facades\Cache::forget("order_game_products_{$slug}");
                }
            }
        });

        static::deleted(function (Product $product) {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_games');
            \Illuminate\Support\Facades\Cache::forget('catalog_default_products');
            \Illuminate\Support\Facades\Cache::forget('active_products_total_count');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_metrics');
            if ($product->game_id) {
                $slug = $product->game?->slug;
                if ($slug) {
                    \Illuminate\Support\Facades\Cache::forget("order_game_{$slug}");
                    \Illuminate\Support\Facades\Cache::forget("order_game_products_{$slug}");
                }
            }
        });
    }
}

