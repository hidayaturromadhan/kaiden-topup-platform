<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    // Audit log bersifat append-only, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_id',
        'action',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Mencegah modifikasi atau penghapusan record AuditLog (Append-Only)
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('AuditLog bersifat append-only dan dilarang dimodifikasi.');
        });

        static::deleting(function () {
            throw new \RuntimeException('AuditLog bersifat append-only dan dilarang dihapus dari aplikasi.');
        });
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Helper untuk mencatat log aktivitas
     */
    public static function record(
        string $action,
        ?string $targetType = null,
        mixed $targetId = null,
        mixed $oldValues = null,
        mixed $newValues = null,
        ?int $actorId = null
    ): static {
        return static::create([
            'actor_id' => $actorId ?? Auth::id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId ? (string)$targetId : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
        ]);
    }
}

