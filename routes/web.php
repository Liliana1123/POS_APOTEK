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

// ===== Data Master =====

Route::middleware(['auth', 'permission:master.kategori'])->group(function () {
    Route::resource('kategori', KategoriController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

Route::middleware(['auth', 'permission:master.satuan'])->group(function () {
    Route::resource('satuan', SatuanController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

Route::middleware(['auth', 'permission:master.pabrik'])->group(function () {
    Route::resource('pabrik', PabrikController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

Route::middleware(['auth', 'permission:master.supplier'])->group(function () {
    Route::resource('supplier', SupplierController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

Route::middleware(['auth', 'permission:master.pelanggan'])->group(function () {
    Route::resource('pelanggan', PelangganController::class);
});

Route::middleware(['auth', 'permission:master.barang'])->group(function () {
    Route::resource('barang', BarangController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('barang/import-template', [BarangController::class, 'importTemplate'])->name('barang.import-template');
});

Route::middleware(['auth', 'permission:master.barang:kelola'])->group(function () {
    Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
});

Route::middleware(['auth', 'permission:master.custom-discount'])->group(function () {
    Route::resource('custom-discount', CustomDiscountController::class)->except('show');
});

// ===== Transaksi =====

Route::middleware(['auth', 'permission:transaksi.penjualan'])->group(function () {
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('penjualan/{penjualan}/detail', [PenjualanController::class, 'detail'])->name('penjualan.detail');
    Route::get('penjualan/{penjualan}/piutang/payment-form', [PembayaranPiutangController::class, 'form'])->name('penjualan.piutang.payments.form');
    Route::post('penjualan/{penjualan}/piutang/payments', [PembayaranPiutangController::class, 'store'])->name('penjualan.piutang.payments.store');
});

Route::middleware(['auth', 'permission:transaksi.penerimaan'])->group(function () {
    Route::resource('penerimaan', PenerimaanController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('penerimaan/{penerimaan}/print', [PenerimaanController::class, 'print'])->name('penerimaan.print');
    Route::get('penerimaan/{penerimaan}/payment-form', [PenerimaanController::class, 'paymentForm'])->name('penerimaan.payments.form');
    Route::post('penerimaan/{penerimaan}/payments', [PenerimaanController::class, 'paymentStore'])->name('penerimaan.payments.store');
    Route::get('penerimaan/{penerimaan}/susulan-form', [PenerimaanController::class, 'susulanForm'])->name('penerimaan.susulan.form');
    Route::post('penerimaan/{penerimaan}/susulan', [PenerimaanController::class, 'susulanStore'])->name('penerimaan.susulan.store');
});

Route::middleware(['auth', 'permission:transaksi.rusak'])->group(function () {
    Route::resource('rusak', RusakController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('rusak/{rusak}/print', [RusakController::class, 'print'])->name('rusak.print');
});

// ===== Laporan =====

Route::middleware(['auth', 'permission:laporan.stok'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
});

Route::middleware(['auth', 'permission:laporan.penerimaan'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/penerimaan', [LaporanController::class, 'penerimaan'])->name('penerimaan');
});

Route::middleware(['auth', 'permission:laporan.penjualan'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
});

Route::middleware(['auth', 'permission:laporan.rusak'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/rusak', [LaporanController::class, 'rusak'])->name('rusak');
});

Route::middleware(['auth', 'permission:laporan.laba-rugi'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
});

Route::middleware(['auth', 'permission:laporan.diskon'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/diskon', [LaporanController::class, 'diskon'])->name('diskon');
});

// ===== Sistem =====

Route::middleware(['auth', 'permission:sistem.kelola-user'])->group(function () {
    Route::resource('user', UserController::class)->except('show');
    Route::patch('user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle');
});

Route::middleware(['auth', 'permission:sistem.pengaturan:kelola'])->group(function () {
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});

Route::middleware(['auth', 'permission:sistem.activity-log'])->group(function () {
    Route::get('/activity-log', [DashboardController::class, 'activityLog'])->name('activity-log');
});

Route::get(
    'pelanggan/{pelanggan}/piutang',
    [PembayaranPiutangController::class, 'pelanggan']
)->middleware(['auth', 'permission:master.pelanggan'])
    ->name('pelanggan.piutang');

Route::post('pelanggan/register-member', [PelangganController::class, 'registerMember'])
    ->middleware(['auth', 'permission:master.pelanggan:kelola'])
    ->name('pelanggan.register-member');