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
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
        });
    }
}

