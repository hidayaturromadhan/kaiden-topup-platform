<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'pelanggan');
        })->withCount('orders')->withSum([
            'orders as total_spent' => function ($q) {
                $q->whereIn('status', ['DIBAYAR', 'DIPROSES', 'SUKSES']);
            }
        ], 'locked_price');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }
}

