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
use App\Http\Controllers\PermissionController;
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

// Resource master dipisah dua level: halaman daftar (lihat) boleh dibuka role
// yang cuma punya akses baca, aksi tambah/ubah/hapus butuh kelola.
//
// Urutan group di bawah itu wajib: group "kelola" didaftarkan sebelum group
// "lihat" pada tiap resource. Kalau route parameterized lebih dulu, `barang/create`
// akan tertangkap `barang/{barang}` (show) dan hasilnya 404 dari model binding.

Route::middleware(['auth', 'permission:master.kategori:kelola'])->group(function () {
    Route::resource('kategori', KategoriController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'permission:master.kategori'])->group(function () {
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
});

Route::middleware(['auth', 'permission:master.satuan:kelola'])->group(function () {
    Route::resource('satuan', SatuanController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'permission:master.satuan'])->group(function () {
    Route::get('satuan', [SatuanController::class, 'index'])->name('satuan.index');
});

Route::middleware(['auth', 'permission:master.pabrik:kelola'])->group(function () {
    Route::resource('pabrik', PabrikController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'permission:master.pabrik'])->group(function () {
    Route::get('pabrik', [PabrikController::class, 'index'])->name('pabrik.index');
});

Route::middleware(['auth', 'permission:master.supplier:kelola'])->group(function () {
    Route::resource('supplier', SupplierController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'permission:master.supplier'])->group(function () {
    Route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index');
});

Route::middleware(['auth', 'permission:master.pelanggan:kelola'])->group(function () {
    Route::resource('pelanggan', PelangganController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('pelanggan/register-member', [PelangganController::class, 'registerMember'])->name('pelanggan.register-member');
});
Route::middleware(['auth', 'permission:master.pelanggan'])->group(function () {
    Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('pelanggan/{pelanggan}/piutang', [PembayaranPiutangController::class, 'pelanggan'])->name('pelanggan.piutang');
    Route::get('pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
});

Route::middleware(['auth', 'permission:master.barang:kelola'])->group(function () {
    Route::resource('barang', BarangController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
});
Route::middleware(['auth', 'permission:master.barang'])->group(function () {
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/import-template', [BarangController::class, 'importTemplate'])->name('barang.import-template');
    Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
});

Route::middleware(['auth', 'permission:master.custom-discount:kelola'])->group(function () {
    Route::resource('custom-discount', CustomDiscountController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('custom-discount/{custom_discount}/toggle', [CustomDiscountController::class, 'toggle'])->name('custom-discount.toggle');
});
Route::middleware(['auth', 'permission:master.custom-discount'])->group(function () {
    Route::get('custom-discount', [CustomDiscountController::class, 'index'])->name('custom-discount.index');
});

// ===== Transaksi =====

Route::middleware(['auth', 'permission:transaksi.penjualan:kelola'])->group(function () {
    Route::get('penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('penjualan/export', [PenjualanController::class, 'export'])->name('penjualan.export');
    Route::get('penjualan/{penjualan}/piutang/payment-form', [PembayaranPiutangController::class, 'form'])->name('penjualan.piutang.payments.form');
    Route::post('penjualan/{penjualan}/piutang/payments', [PembayaranPiutangController::class, 'store'])->name('penjualan.piutang.payments.store');
});
Route::middleware(['auth', 'permission:transaksi.penjualan'])->group(function () {
    Route::get('penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('penjualan/{penjualan}/detail', [PenjualanController::class, 'detail'])->name('penjualan.detail');
    Route::get('penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
});

Route::middleware(['auth', 'permission:transaksi.penerimaan:kelola'])->group(function () {
    Route::resource('penerimaan', PenerimaanController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('penerimaan/{penerimaan}/payment-form', [PenerimaanController::class, 'paymentForm'])->name('penerimaan.payments.form');
    Route::post('penerimaan/{penerimaan}/payments', [PenerimaanController::class, 'paymentStore'])->name('penerimaan.payments.store');
    Route::get('penerimaan/{penerimaan}/susulan-form', [PenerimaanController::class, 'susulanForm'])->name('penerimaan.susulan.form');
    Route::post('penerimaan/{penerimaan}/susulan', [PenerimaanController::class, 'susulanStore'])->name('penerimaan.susulan.store');
});
Route::middleware(['auth', 'permission:transaksi.penerimaan'])->group(function () {
    Route::get('penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
    Route::get('penerimaan/{penerimaan}/print', [PenerimaanController::class, 'print'])->name('penerimaan.print');
    Route::get('penerimaan/{penerimaan}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
});

Route::middleware(['auth', 'permission:transaksi.rusak:kelola'])->group(function () {
    Route::resource('rusak', RusakController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'permission:transaksi.rusak'])->group(function () {
    Route::get('rusak', [RusakController::class, 'index'])->name('rusak.index');
    Route::get('rusak/{rusak}/print', [RusakController::class, 'print'])->name('rusak.print');
    Route::get('rusak/{rusak}', [RusakController::class, 'show'])->name('rusak.show');
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

Route::middleware(['auth', 'permission:sistem.kelola-user:kelola'])->group(function () {
    Route::resource('user', UserController::class)->except(['index', 'show']);
    Route::patch('user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle');
});
Route::middleware(['auth', 'permission:sistem.kelola-user'])->group(function () {
    Route::get('user', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', 'permission:sistem.pengaturan:kelola'])->group(function () {
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});

Route::middleware(['auth', 'permission:sistem.activity-log'])->group(function () {
    Route::get('/activity-log', [DashboardController::class, 'activityLog'])->name('activity-log');
});

// Izin Akses tidak lewat permission, hanya superadmin. Kalau lewat permission,
// superadmin bisa mengunci dirinya sendiri dengan mematikan aksesnya.
Route::middleware(['auth', 'superadmin'])->prefix('izin-akses')->name('permission.')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::post('/', [PermissionController::class, 'update'])->name('update');
    Route::post('reset', [PermissionController::class, 'reset'])->name('reset');
});