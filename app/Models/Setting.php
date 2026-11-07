<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    /**
     * Mengambil nilai setting dengan dekripsi otomatis jika encrypted
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->is_encrypted && !empty($setting->value)) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (DecryptException $e) {
                return $default;
            }
        }

        return $setting->value ?? $default;
    }

    /**
     * Menyimpan nilai setting dengan enkripsi jika is_encrypted = true
     */
    public static function set(string $key, mixed $value, bool $isEncrypted = false, ?string $description = null): static
    {
        $rawValue = $value;
        if ($isEncrypted && !empty($value)) {
            $rawValue = Crypt::encryptString((string)$value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $rawValue,
                'is_encrypted' => $isEncrypted,
                'description' => $description,
            ]
        );
    }
}

