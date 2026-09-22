<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PabrikController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\RusakController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembayaranPiutangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\CustomDiscountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengaturanController;

use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Redirect halaman utama ke dashboard (atau ke login kalau belum login)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('kategori', KategoriController::class)->except('show');
    Route::resource('satuan', SatuanController::class)->except('show');
    Route::resource('pabrik', PabrikController::class)->except('show');
    Route::resource('supplier', SupplierController::class)->except('show');
    Route::resource('pelanggan', PelangganController::class);
    Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
    Route::get('barang/import-template', [BarangController::class, 'importTemplate'])->name('barang.import-template');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::resource('barang', BarangController::class);
    Route::resource('custom-discount', CustomDiscountController::class)->except('show');
    Route::post('custom-discount/{custom_discount}/toggle', [CustomDiscountController::class, 'toggle'])->name('custom-discount.toggle');
    Route::resource('user', UserController::class)->except('show');
    Route::patch('user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle');
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('/activity-log', [DashboardController::class, 'activityLog'])->name('activity-log');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('penerimaan', PenerimaanController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('penerimaan/{penerimaan}/print', [PenerimaanController::class, 'print'])->name('penerimaan.print');
    Route::get('penerimaan/{penerimaan}/payment-form', [PenerimaanController::class, 'paymentForm'])->name('penerimaan.payments.form');
    Route::post('penerimaan/{penerimaan}/payments', [PenerimaanController::class, 'paymentStore'])->name('penerimaan.payments.store');
    Route::get('penerimaan/{penerimaan}/susulan-form',[PenerimaanController::class, 'susulanForm'])->name('penerimaan.susulan.form');
    Route::post('penerimaan/{penerimaan}/susulan',[PenerimaanController::class, 'susulanStore'])->name('penerimaan.susulan.store');
    Route::resource('rusak', RusakController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('rusak/{rusak}/print', [RusakController::class, 'print']) ->name('rusak.print');
});

Route::middleware(['auth'])->group(function () {
    // Route Cetak Laporan Riwayat Penjualan
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('pelanggan/register-member', [PelangganController::class, 'registerMember'])->name('pelanggan.register-member');
});

Route::middleware(['auth', 'role:admin'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
    Route::get('/penerimaan', [LaporanController::class, 'penerimaan'])->name('penerimaan');
    Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
    Route::get('/rusak', [LaporanController::class, 'rusak'])->name('rusak');
    Route::get('/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
    Route::get('/diskon', [LaporanController::class, 'diskon'])->name('diskon');
});

Route::get(
    'pelanggan/{pelanggan}/piutang',
    [PembayaranPiutangController::class, 'pelanggan']
)->name('pelanggan.piutang');

Route::get(
    'penjualan/{penjualan}/detail',
    [PenjualanController::class, 'detail']
)->name('penjualan.detail');

Route::get(
    'penjualan/{penjualan}/piutang/payment-form',
    [PembayaranPiutangController::class, 'form']
)->name('penjualan.piutang.payments.form');

Route::post(
    'penjualan/{penjualan}/piutang/payments',
    [PembayaranPiutangController::class, 'store']
)->name('penjualan.piutang.payments.store');





// ==== CONTOH pemakaian middleware role untuk modul lain nanti ====
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::resource('kategori', KategoriController::class);
// });