<?php

use App\Models\Pelanggan;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statusPiutang = 'semua';
$query = Pelanggan::query()->where('is_member', true);

$pelanggans = $query
    ->withCount('penjualan')
    ->withSum('penjualan as total_belanja', 'total')
    ->withSum('discountUsages as total_diskon', 'nominal')
    ->with(['penjualan' => function ($q) {
        $q->where('metode_pembayaran', 'piutang')
          ->with('pembayaranPiutang');
    }])
    ->orderByRaw('member_id IS NULL, member_id asc')
    ->paginate(15)
    ->withQueryString();

echo 'Rows: ' . $pelanggans->count() . PHP_EOL;

$status = 'semua';
$view = view('pelanggan.index', compact('pelanggans', 'status', 'statusPiutang'))->render();
echo 'View OK, length ' . strlen($view) . PHP_EOL;