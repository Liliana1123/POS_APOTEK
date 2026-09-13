# Plan: Manajemen User + Identitas Apotek

## Keputusan yang sudah diambil user
- Dua modul dikerjakan sekarang
- User handling: hapus bila tanpa transaksi, nonaktif bila berrelasi
- Logo: ya, upload file
- Role tertinggi: admin saja (tanpa superadmin)

## Modul 1 — Manajemen User

| Item | Detail |
|------|--------|
| Model | `User` sudah ada, tanpa perubahan |
| Migration | Tidak ada (skema `users` sudah punya name/email/password/role/aktif) |
| Controller | `app/Http/Controllers/UserController.php` — resource (index, create, store, edit, update, destroy) + `toggleAktif` |
| Views | `resources/views/user/index.blade.php`, `create.blade.php`, `edit.blade.php` — ikuti pola KategoriController/views |
| Routes | `Route::resource('user', UserController::class)->except('show')` + `Route::patch('user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle')` dalam grup `role:admin` |
| Sidebar | Tambah item "Kelola User" di grup Data Master (admin only) di `layouts/app.blade.php` |
| Logic hapus | `destroy()`: cek apakah user punya `penerimaan`/`penjualan` via `hasMany`. Bila punya relasi → `abort`/redirect error "Hapus tidak bisa, akun punya riwayat transaksi. Nonaktifkan saja." Bila tidak → delete. Cegah hapus dirinya sendiri (`$user->id === auth()->id()` → error) |
| Toggle aktif | `aktif = !aktif`; guard sama: jangan biarkan nonaktifkan diri sendiri |

## Modul 2 — Identitas Apotek (Info Apotek)

| Item | Detail |
|------|--------|
| Migration | `database/migrations/2026_09_11_000001_create_info_apoteks_table.php` — tabel `info_apoteks` (id, nama_apotek, alamat, telepon, email, no_izin_sia, nama_apoteker_pj, no_sipa, logo, timestamps) |
| Model | `app/Models/InfoApotek.php` (fillable semua, `morphMap` tidak perlu; helper `singleton()`) |
| Controller | `app/Http/Controllers/PengaturanController.php` — `index()` (form edit, GET first-or-create) + `update()` (PUT, upload logo) |
| Views | `resources/views/pengaturan/index.blade.php` — single form edit |
| Routes | `Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index')` + `Route::put('pengaturan', ...)->name('pengaturan.update')` dalam grup `role:admin` |
| Sidebar | Tambah item "Pengaturan Apotek" di grup Data Master (admin only) |
| Logo upload | `Storage::disk('public')->putFile('info-apotek', $file)`; validasi `image|max:2048`; hapus file lama bila ada; simpan path relatif di kolom `logo`. Jalankan `php artisan storage:link` bila belum |
| Akses umbrella | Helper/`View::share` atau langsung `InfoApotek::first()` di views yang butuh identitas (print/layout). Sederhana: `@php $apotek = App\Models\InfoApotek::first(); @endphp` di tempat yang butuh, atau `View::composer` di sidebar. Pilih yang paling minim: baca di layout app + print |

## File yang dibuat/diubah
1. `database/migrations/2026_09_11_000001_create_info_apoteks_table.php` (baru)
2. `app/Models/InfoApotek.php` (baru)
3. `app/Http/Controllers/UserController.php` (baru)
4. `app/Http/Controllers/PengaturanController.php` (baru)
5. `resources/views/user/index.blade.php` (baru)
6. `resources/views/user/create.blade.php` (baru)
7. `resources/views/user/edit.blade.php` (baru)
8. `resources/views/pengaturan/index.blade.php` (baru)
9. `routes/web.php` (tambah routes)
10. `resources/views/layouts/app.blade.php` (tambah 2 item sidebar + ganti "APOTEK KITA" hardcoded baris 39 memakai nama_pengaturan bila ada)
11. `resources/views/penerimaan/print.blade.php` (opsional: ambil identitas dari `info_apoteks` bila header masih hardcode)

## Verifikasi
- `php artisan migrate` sukses
- `php artisan route:list` — route user.* dan pengaturan.* muncul
- Login admin → menu "Kelola User" dan "Pengaturan Apotek" tampil di sidebar
- Buat user baru, nonaktifkan, aktifkan kembali
- Edit identitas apotek + upload logo, cek muncul di sidebar & print
- Delete user tanpa transaksi sukses; delete user berrelasi ditolak; nonaktifkan diri sendiri ditolak

## Catatan
- Admin = role tertinggi, tanpa superadmin
- Hapus user hanya bila tanpa relasi transaksi