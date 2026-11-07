<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'product_id',
        'user_id_game',
        'zone_id',
        'confirmed_nickname',
        'locked_price',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'locked_price' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function fulfillments(): HasMany
    {
        return $this->hasMany(Fulfillment::class);
    }

    public function latestFulfillment(): HasOne
    {
        return $this->hasOne(Fulfillment::class)->latestOfMany();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Nama/Nickname yang disamarkan persis format: gamerabah -> ga******bah
     */
    public function getMaskedNicknameAttribute(): string
    {
        $raw = $this->confirmed_nickname ?: ($this->user->name ?? 'gamerabah');
        return self::maskNickname($raw);
    }

    /**
     * Helper masking nama/nickname:
     * Contoh: "gamerabah" -> "ga******bah"
     */
    public static function maskNickname(?string $name): string
    {
        if (empty($name)) {
            return 'ga******mer';
        }

        $name = trim($name);
        $len = mb_strlen($name);

        if ($len <= 3) {
            return mb_substr($name, 0, 1) . '******' . ($len > 1 ? mb_substr($name, -1) : '');
        }

        if ($len <= 5) {
            return mb_substr($name, 0, 2) . '******' . mb_substr($name, -1);
        }

        $prefix = mb_substr($name, 0, 2);
        $suffix = mb_substr($name, -3);

        return $prefix . '******' . $suffix;
    }
}

