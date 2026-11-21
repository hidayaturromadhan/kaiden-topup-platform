<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prods = App\Models\Product::where('name', 'LIKE', '%Blood%')
    ->orWhere('digiflazz_sku', 'LIKE', '%bs%')
    ->get(['id', 'name', 'digiflazz_sku', 'cost_price', 'sell_price', 'is_active']);

echo json_encode($prods, JSON_PRETTY_PRINT);
