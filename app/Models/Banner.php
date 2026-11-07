<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'discount_tag',
        'discount_value',
        'discount_subtext',
        'button_text',
        'button_url',
        'image',
        'banner_type',
        'gradient_theme',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope banner aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope banner tipe slider utama
     */
    public function scopeSlider(Builder $query): Builder
    {
        return $query->where('banner_type', 'slider');
    }

    /**
     * Scope banner tipe samping (side banner)
     */
    public function scopeSide(Builder $query): Builder
    {
        return $query->where('banner_type', 'side');
    }

    /**
     * Helper URL gambar banner
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        // Default fallback image sesuai theme
        return match($this->gradient_theme) {
            'slate' => asset('images/bg/freefire.jpg'),
            'emerald' => asset('images/bg/valorant.jpg'),
            'amber' => asset('images/bg/codm.jpg'),
            default => asset('images/bg/valorant.jpg'),
        };
    }

    /**
     * Helper kelas CSS gradient latar belakang
     */
    public function getGradientClassAttribute(): string
    {
        return match($this->gradient_theme) {
            'slate' => 'from-[#1E293B] via-[#0F172A] to-[#111827]',
            'emerald' => 'from-[#064E3B] via-[#022C22] to-[#111827]',
            'amber' => 'from-[#78350F] via-[#451A03] to-[#111827]',
            'blue' => 'from-[#1E3A8A] via-[#172554] to-[#111827]',
            default => 'from-[#6D0808] via-[#3B0707] to-[#18181B]', // Crimson khas KAIDEN
        };
    }
}

