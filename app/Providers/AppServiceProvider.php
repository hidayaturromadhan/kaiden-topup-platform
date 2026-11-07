<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set zona waktu & locale default ke Asia/Jakarta (WIB) secara konsisten
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        Gate::policy(Order::class, OrderPolicy::class);

        // Dynamic Gate check untuk setiap permission
        Gate::before(function (User $user, string $ability) {
            // Jika ability cocok dengan format permission (e.g. produk.ubah), cek lewat hasPermission
            if (str_contains($ability, '.')) {
                return $user->hasPermission($ability) ? true : null;
            }
            return null;
        });
    }
}
