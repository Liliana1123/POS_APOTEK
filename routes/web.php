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
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\CustomDiscountController;

use Illuminate\Support\Facades\Route;

// ==== TEMPEL BLOK INI KE routes/web.php ====
// Boleh hapus/ganti route "welcome" bawaan Laravel kalau sudah tidak dipakai.

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
    Route::resource('barang', BarangController::class)->except('show');
    Route::resource('custom-discount', CustomDiscountController::class)->except('show');
    Route::post('custom-discount/{custom_discount}/toggle', [CustomDiscountController::class, 'toggle'])->name('custom-discount.toggle');
    Route::get('/activity-log', [DashboardController::class, 'activityLog'])->name('activity-log');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('penerimaan', PenerimaanController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('penerimaan/{penerimaan}/print', [PenerimaanController::class, 'print'])->name('penerimaan.print');
    Route::get('penerimaan/{penerimaan}/payment-form', [PenerimaanController::class, 'paymentForm'])->name('penerimaan.payments.form');
    Route::post('penerimaan/{penerimaan}/payments', [PenerimaanController::class, 'paymentStore'])->name('penerimaan.payments.store');
    Route::resource('rusak', RusakController::class)->only(['index', 'create', 'store']);
});

Route::middleware(['auth'])->group(function () {
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


// ==== CONTOH pemakaian middleware role untuk modul lain nanti ====
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::resource('kategori', KategoriController::class);
// });