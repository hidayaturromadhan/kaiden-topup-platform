<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Menentukan apakah user dapat melihat detail order tertentu (Anti-IDOR Ownership Check)
     */
    public function view(User $user, Order $order): bool
    {
        // Admin / Superadmin dengan permission 'transaksi.lihat' boleh melihat semua transaksi
        if ($user->hasPermission('transaksi.lihat')) {
            return true;
        }

        // Pelanggan dengan permission 'transaksi.lihat_milik_sendiri' HANYA boleh melihat transaksinya sendiri
        if ($user->hasPermission('transaksi.lihat_milik_sendiri') && (int)$order->user_id === (int)$user->id) {
            return true;
        }

        return false;
    }

    /**
     * Menentukan apakah user dapat membuat pesanan
     */
    public function create(User $user): bool
    {
        // Admin & Superadmin dilarang membuat pesanan sesuai aturan PRD Section 7 & RULES.md
        return $user->hasPermission('pesanan.buat');
    }
}

