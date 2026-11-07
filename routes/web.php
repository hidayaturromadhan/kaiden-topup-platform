<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DigiflazzWebhookController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Superadmin\AuditLogController as SuperadminAuditLogController;
use App\Http\Controllers\Superadmin\RoleController as SuperadminRoleController;
use App\Http\Controllers\Superadmin\SettingController as SuperadminSettingController;
use App\Http\Controllers\Superadmin\UserController as SuperadminUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Customer Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/panduan', [GuideController::class, 'index'])->name('guide.index');
Route::get('/game/{slug}', [OrderController::class, 'showGame'])->name('order.game');
Route::post('/api/check-ign', [OrderController::class, 'checkIgn'])->name('order.check_ign');

// Webhook Eksternal (Midtrans & Digiflazz)
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle'])->name('midtrans.notification');
Route::post('/digiflazz/webhook', [DigiflazzWebhookController::class, 'handle'])->name('digiflazz.webhook');

/*
|--------------------------------------------------------------------------
| Autentikasi Pengguna
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Area Pelanggan (Memerlukan Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Checkout Pemesanan (Hanya pelanggan dengan permission pesanan.buat)
    Route::post('/order/checkout', [OrderController::class, 'checkout'])
        ->middleware('permission:pesanan.buat')
        ->name('order.checkout');

    // Detail Order & Snap Pay (Anti-IDOR Ownership Enforced via OrderPolicy)
    Route::get('/order/{order_code}', [OrderController::class, 'show'])->name('order.show');

    // Riwayat Transaksi Pelanggan
    Route::get('/my-orders', [OrderController::class, 'myOrders'])
        ->middleware('permission:transaksi.lihat_milik_sendiri')
        ->name('orders.my');

    // Kirim Feedback / Ulasan Pelanggan Setelah Pesanan Selesai
    Route::post('/order/{order_code}/review', [ReviewController::class, 'store'])
        ->name('orders.review.store');
});

/*
|--------------------------------------------------------------------------
| Dashboard Operasional Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    // Dashboard Ringkasan
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:transaksi.lihat')
        ->name('dashboard');

    // Manajemen Produk & Harga
    Route::get('/products', [AdminProductController::class, 'index'])
        ->middleware('permission:produk.lihat')
        ->name('products.index');

    Route::patch('/products/{product}/price', [AdminProductController::class, 'updatePrice'])
        ->middleware('permission:harga.ubah')
        ->name('products.update_price');

    Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleStatus'])
        ->middleware('permission:stok.ubah')
        ->name('products.toggle');

    Route::post('/products/sync', [AdminProductController::class, 'sync'])
        ->middleware('permission:produk.sinkron')
        ->name('products.sync');

    // Manajemen Banner & Promo Beranda
    Route::resource('banners', AdminBannerController::class)
        ->except(['show'])
        ->middleware('permission:banner.kelola');

    Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggleStatus'])
        ->middleware('permission:banner.kelola')
        ->name('banners.toggle');

    // Manajemen Seluruh Transaksi
    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->middleware('permission:transaksi.lihat')
        ->name('orders.index');

    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
        ->middleware('permission:transaksi.lihat')
        ->name('orders.show');

    Route::post('/orders/{order}/retry', [AdminOrderController::class, 'retryFulfillment'])
        ->middleware('permission:transaksi.lihat')
        ->name('orders.retry');

    Route::post('/orders/{order}/refund', [AdminOrderController::class, 'markRefund'])
        ->middleware('permission:transaksi.lihat')
        ->name('orders.refund');

    // Data Pelanggan
    Route::get('/customers', [AdminCustomerController::class, 'index'])
        ->middleware('permission:pelanggan.lihat')
        ->name('customers.index');

    // Laporan & Ekspor CSV
    Route::get('/reports', [AdminReportController::class, 'index'])
        ->middleware('permission:laporan.lihat')
        ->name('reports.index');

    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])
        ->middleware('permission:laporan.ekspor')
        ->name('reports.export');
});

/*
|--------------------------------------------------------------------------
| Pengelolaan Keamanan Superadmin
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->as('superadmin.')->middleware('auth')->group(function () {
    // Manajemen Pengguna Internal
    Route::get('/users', [SuperadminUserController::class, 'index'])
        ->middleware('permission:pengguna.kelola')
        ->name('users.index');

    Route::post('/users', [SuperadminUserController::class, 'store'])
        ->middleware('permission:pengguna.kelola')
        ->name('users.store');

    Route::put('/users/{user}', [SuperadminUserController::class, 'update'])
        ->middleware('permission:pengguna.kelola')
        ->name('users.update');

    Route::post('/users/{user}/toggle', [SuperadminUserController::class, 'toggleStatus'])
        ->middleware('permission:pengguna.kelola')
        ->name('users.toggle');

    // Manajemen Peran & Matriks Izin
    Route::get('/roles', [SuperadminRoleController::class, 'index'])
        ->middleware('permission:role.kelola')
        ->name('roles.index');

    Route::put('/roles/{role}', [SuperadminRoleController::class, 'update'])
        ->middleware('permission:role.kelola')
        ->name('roles.update');

    // Pengaturan Integrasi & Feature Flag IGN
    Route::get('/settings', [SuperadminSettingController::class, 'index'])
        ->middleware('permission:integrasi.atur,ign.konfigurasi')
        ->name('settings.index');

    Route::post('/settings', [SuperadminSettingController::class, 'update'])
        ->middleware('permission:integrasi.atur,ign.konfigurasi')
        ->name('settings.update');

    // Audit Log (Append-Only Viewer)
    Route::get('/audit-logs', [SuperadminAuditLogController::class, 'index'])
        ->middleware('permission:audit.lihat')
        ->name('audit_logs.index');

    Route::get('/audit-logs/{auditLog}', [SuperadminAuditLogController::class, 'show'])
        ->middleware('permission:audit.lihat')
        ->name('audit_logs.show');
});
