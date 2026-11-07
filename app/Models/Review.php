<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'game_id',
        'rating',
        'comment',
        'is_anonymous',
        'is_visible',
        'admin_reply',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_anonymous' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Nama pengguna yang disamarkan: gamerabah -> ga******bah
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->order->confirmed_nickname ?: ($this->user->name ?? 'gamerabah');
        return Order::maskNickname($name);
    }

    public function getMaskedNicknameAttribute(): string
    {
        return $this->getDisplayNameAttribute();
    }

    /**
     * Clear game rating cache when review changes
     */
    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            \Illuminate\Support\Facades\Cache::forget("game_rating_{$review->game_id}");
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
        });

        static::deleted(function (Review $review) {
            \Illuminate\Support\Facades\Cache::forget("game_rating_{$review->game_id}");
            \Illuminate\Support\Facades\Cache::forget('home_default_games');
        });
    }
}
