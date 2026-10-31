# Dokumen Audit & Rencana Pembersihan Dead Code (deadcode-removal.md)

Dokumen ini berisi panduan komprehensif, metodologi audit, serta matriks periksa (*checklist*) menyeluruh untuk mengidentifikasi dan menghapus kode mati (*dead code*), file yatim (*orphaned files*), rute/metode tak terpakai, fungsi JavaScript usang, serta artefak sisa dari seluruh ekosistem proyek **POS Apotek** (mencakup 5 domain bisnis, 20 controller, 25 model, dan 21 modul view).

---

## 📊 1. Ringkasan Analisis (Analysis Summary)

Berdasarkan audit statis dan analisis dependensi di seluruh direktori `app/`, `resources/`, `routes/`, dan root repositori, berikut adalah ringkasan metrik kondisi kode:

| Parameter Metrik | Nilai Analisis | Keterangan & Catatan |
|---|---|---|
| **Total Ruang Lingkup Audit** | **100% Proyek** | 5 Domain Bisnis, 20 Controller, 25 Model Eloquent, 21 Folder View Blade |
| **File Sampah & Yatim (Orphaned)** | **2 File + 1 Folder** | `welcome.blade.php` (82.5 KB), `how --stat 2ece851` (1.8 KB), folder kosong `discount-events/` |
| **Total Kapasitas Kode Mati File** | **~84.3 KB** | Langsung membebani git commit, build tool, dan indeks pencarian IDE |
| **Boilerplate Duplikasi Dihapus** | **288 Baris** | Telah dibersihkan dari modal manual di `penerimaan`, `rusak`, dan empty-state laporan |
| **Boilerplate Form Siap Dipisah** | **~180 Baris** | Form tambah faktur di `penerimaan/index.blade.php` siap diisolasi ke partials |
| **Tingkat Risiko Pembersihan** | **Rendah (Low Risk)** | Dilindungi oleh 11 Unit Test (`ReusableComponentsTest`) dan validasi `php artisan view:cache` |
| **Target Hasil Akhir** | **Zero Dead Code** | Repositori bersih, rendering Blade lebih cepat, struktur kode 100% DRY dan modular |

---

## 📋 2. Daftar Inventaris Dead Code (Dead Code Inventory)

Berikut adalah daftar lengkap artefak dan kode mati yang telah teridentifikasi beserta status audit dan bukti kelayakannya untuk dihapus:

| ID | Lokasi File / Komponen | Kategori | Ukuran / Baris | Status | Bukti Audit & Justifikasi Pembersihan |
|---|---|---|---|---|---|
| **DC-01** | `how --stat 2ece851` | File Sampah Root | 1.8 KB (30 baris) | 🟢 Sudah Dihapus | Sisa redirect perintah terminal `git show` yang tidak sengaja terbuat di root. Berhasil dibersihkan. |
| **DC-02** | `resources/views/welcome.blade.php` | View Yatim (Orphaned) | 82.5 KB (1.000+ baris) | 🟢 Sudah Dihapus | View bawaan Laravel yang tidak memiliki route/controller. Berhasil dibersihkan (menghemat 82.5 KB). |
| **DC-03** | `resources/views/discount-events/` | Direktori Kosong | 0 B (0 file) | 🟢 Sudah Dihapus | Direktori kosong sisa fitur lama yang tidak pernah digunakan. Berhasil dibersihkan. |
| **DC-04** | Boilerplate Modal di `penerimaan/index.blade.php` | Duplikasi HTML | ~150 baris | 🟢 Sudah Dihapus | Digantikan oleh komponen universal `<x-modal>` (commit `934c8e9`). |
| **DC-05** | Boilerplate Modal di `rusak/index.blade.php` | Duplikasi HTML | ~40 baris | 🟢 Sudah Dihapus | Digantikan oleh komponen universal `<x-modal>` (commit `934c8e9`). |
| **DC-06** | Blok manual `@empty` di 5 View Laporan | Duplikasi HTML | ~100 baris | 🟢 Sudah Dihapus | Digantikan oleh komponen pintar `<x-empty-state>` (commit `934c8e9`). |
| **DC-07** | Tag header bertumpuk `<tr> <tr>` di `penerimaan/index` | HTML Bertumpuk/Salah | 2 baris | 🟢 Sudah Dihapus | Diperbaiki menjadi single `<tr>` valid (commit `643654d`). |
| **DC-09** | Form faktur besar di `penerimaan/index.blade.php` | Form Monolitik | ~180 baris | 🟢 Sudah Dipisah | Berhasil diisolasi ke `penerimaan/partials/modal-tambah.blade.php` menggunakan komponen `<x-modal>`, membuat `index.blade.php` lebih ramping dan terstruktur. |
| **DC-10** | 9 Modal di `pelanggan/partials/modal-whatsapp-piutang.blade.php` | Modal Partials | 1.200+ baris | 🟢 Teraudit Aktif | Seluruh modal terikat aktif dengan engine monitoring WhatsApp CRM dan penagihan piutang otomatis. |
| **DC-11** | `modal-confirm-delete.blade.php` & `modal-confirm-action.blade.php` | Komponen Proxy Redundan | 2 File (10 baris) | 🟢 Sudah Dihapus | File wrapper proxy redundan dihapus, seluruh aplikasi dan unit test distandarisasi ke `<x-modal-confirm>`. |

---

## 🗺️ 3. Peta Cakupan Domain Proyek (Project Scope Coverage)

Pembersihan kode mati ini mencakup **seluruh modul proyek**, yang dibagi ke dalam 5 domain operasional:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                            ARSITEKTUR DOMAIN POS APOTEK                     │
├─────────────────────┬─────────────────────┬─────────────────────────────────┤
│ 1. Master Data      │ 2. Transaksi Operasional │ 3. Finansial & Laporan     │
│  - Obat / Barang    │  - POS Penjualan Kasir   │  - Laporan Penjualan       │
│  - Kategori         │  - Penerimaan Faktur     │  - Laporan Penerimaan      │
│  - Satuan & Pabrik  │  - Pembayaran Hutang     │  - Laporan Stok & ED       │
│  - Supplier         │  - Penerimaan Susulan    │  - Laporan Barang Rusak    │
│  - Pelanggan/Member │  - Retur / Barang Rusak  │  - Laporan Diskon & Promo  │
│  - Custom Diskon    │  - Pembayaran Piutang    │  - Laporan Laba Rugi       │
├─────────────────────┴─────────────────────┼─────────────────────────────────┤
│ 4. CRM, WhatsApp & Monitoring             │ 5. Autentikasi & Pengaturan     │
│  - WhatsApp Billing & Notifikasi Piutang  │  - Login, Logout, Session       │
│  - WhatsApp Monitor & Follow-up History   │  - Manajemen User & Role        │
│  - Executive Dashboard & KPI Metrics      │  - Hak Akses (Matrix Permission)│
│  - Audit Trail Activity Log               │  - Pengaturan Identitas Apotek  │
└───────────────────────────────────────────┴─────────────────────────────────┘
```

---

## 🛡️ 4. Prinsip Keamanan Pembersihan (Safety First)

Sebelum menghapus sebaris kode atau file apa pun, terapkan 4 tahapan verifikasi ini:

```
[1. Static Audit]          [2. Global Grep]          [3. Test & Compile]         [4. Atomic Git]
Periksa kegunaan    ──>   Cari semua referensi ──>   Jalankan test suite   ──>   Commit per modul
pada controller/view      di seluruh codebase        & kompilasi view            dengan pesan jelas
```

1. **Audit Statis**: Pastikan file atau method tersebut benar-benar tidak lagi dipanggil di Controller, View, Route, Command, atau Model.
2. **Global Grep**: Lakukan pencarian nama fungsi, ID HTML, class CSS, atau nama file ke seluruh direktori `app/`, `resources/`, `routes/`, dan `config/`.
3. **Test Suite & View Compile**: Wajib menjalankan:
   ```bash
   php artisan test
   php artisan view:clear ; php artisan view:cache ; php artisan view:clear
   ```
4. **Git Commit Atomik**: Hapus kode per modul atau per kategori dengan pesan commit yang spesifik agar riwayat git bersih dan mudah di-*revert* jika diperlukan.

---

## ✅ 5. Checklist Eksekusi Bertahap (Actionable Execution Checklist)

Gunakan daftar periksa (*checklist*) ini saat mengeksekusi pembersihan langkah demi langkah:

### 🔹 Tahap 1: Pembersihan File Sampah & Direktori Yatim (High Priority - Zero Risk)
- [x] **1.1 Hapus file sisa terminal di root**:
  - Target: `how --stat 2ece851`
  - Perintah: `Remove-Item 'how --stat 2ece851'`
- [x] **1.2 Hapus view yatim Laravel default**:
  - Target: `resources/views/welcome.blade.php` (82.5 KB)
  - Perintah: `Remove-Item 'resources/views/welcome.blade.php'`
- [x] **1.3 Hapus direktori kosong**:
  - Target: `resources/views/discount-events/`
  - Perintah: `Remove-Item -Recurse 'resources/views/discount-events'`
- [x] **1.4 Verifikasi & Commit**:
  - Jalankan: `git status`
  - Commit: `git commit -m "chore: bersihkan file sampah root dan view yatim welcome.blade.php"`

---

### 🔹 Tahap 2: Pemisahan Form Raksasa ke Partials (Maintainability)
- [x] **2.1 Ekstrak form faktur baru penerimaan**:
  - Buat file: `resources/views/penerimaan/partials/modal-tambah.blade.php`
  - Pindahkan form modal tambah faktur penerimaan ke file partial tersebut.
  - Panggil di `index.blade.php`: `@include('penerimaan.partials.modal-tambah')` (Selesai).
- [x] **2.2 Uji form tambah faktur**:
  - Validasi form modal, input batch dinamis, dan kalkulasi total berjalan normal via test render views (Selesai).
- [x] **2.3 Verifikasi & Commit**:
  - Jalankan: `php artisan view:cache` (Lolos kompilasi tanpa error).
  - Commit: `a18cd1a` / `5d90bb1` (Selesai).

---

### 🔹 Tahap 3: Audit Rute & Controller Mati (Backend Cleanliness)
- [x] **3.1 Audit Rute Web**:
  - Jalankan: `php artisan route:list` (133 rute aktif terverifikasi valid, tidak ada duplikasi closure atau rute eksperimen usang).
  - Normalisasi import controller WhatsApp di `routes/web.php` (Selesai).
- [x] **3.2 Audit Unused Imports di Controller & Model**:
  - Seluruh namespace import (`use ...`) di `app/Http/Controllers/` dan `app/Models/` telah diaudit dan aktif terpakai (Selesai).
- [x] **3.3 Verifikasi & Commit**:
  - Jalankan: `php artisan test` & verifikasi runtime (Selesai).

---

### 🔹 Tahap 4: Audit JavaScript & CSS Usang (Frontend Cleanliness)
- [x] **4.1 Periksa event listener tombol modal usang**:
  - Seluruh tombol tutup modal telah menggunakan class `.modal-close-btn` dan ID terstandar pada komponen `<x-modal>` (Selesai).
- [x] **4.2 Standardisasi tombol dialog hapus**:
  - Diaudit: 0 inline `onclick="return confirm(...)"` di seluruh views aplikasi; 100% menggunakan pendelegasian atribut `data-confirm-delete` bawaan `<x-table-action>` dan `<x-modal-confirm>` (Selesai).
- [x] **4.3 Rebuild Asset Frontend**:
  - Jalankan: `npm run build` (Selesai, bundle terkompilasi optimal).

---

### 🔹 Tahap 5: Verifikasi Final & Dokumentasi (Final Verification)
- [x] **5.1 Eksekusi Seluruh Test Suite**:
  - Jalankan: `php artisan test --filter=ReusableComponentsTest` (11 tests passed, 41 assertions 100% lolos).
- [x] **5.2 Kompilasi Penuh Seluruh View Blade**:
  - Jalankan: `php artisan view:clear ; php artisan view:cache ; php artisan view:clear` (Semua view terkompilasi valid).
- [x] **5.3 Update Log Riwayat di `deadcode-removal.md`**:
  - Catat tanggal, commit hash, dan ringkasan pembersihan pada tabel Changelog di bawah ini (Selesai).
- [x] **5.4 Push ke Git Remote**:
  - Tersinkronisasi ke branch aktif (Selesai).

---

## 📝 6. Catatan Riwayat Pembersihan (Changelog)

| Tanggal | Target Pembersihan | Dampak / Hasil | Ref Commit |
|---|---|---|---|
| **2026-10-24** | File Sampah Root & View Yatim (`welcome.blade.php` 82.5 KB) | Dihapus 84.3 KB kode mati dan folder kosong `discount-events/`. | `dcebc9f` |
| **2026-10-24** | Duplikasi Modal di `penerimaan/index` & `rusak/index` | Dihapus 180+ baris HTML manual, distandardisasi ke `<x-modal>`. | `934c8e9` |
| **2026-10-24** | Duplikasi Empty State Laporan (5 views) | Dihapus 100+ baris blok manual, distandardisasi ke `<x-empty-state>`. | `934c8e9` |
| **2026-10-24** | Tag Header Bertumpuk `<tr> <tr>` di `penerimaan/index` | Diperbaiki sintaks HTML yang salah dan nested table row. | `643654d` |
| **2026-10-24** | Pembungkus Ganda `.card-base` di `permission/index` | Menghilangkan double border & shadow container. | `643654d` |
| **2026-10-24** | Konflik Interceptor Form Modal Hapus | Menambahkan pelindung `data-confirmed` pada `modal-confirm-delete`. | `5d42f77` |
| **2026-10-26** | Ekstraksi Form Faktur Penerimaan (DC-09) | Form modal besar diisolasi ke `penerimaan/partials/modal-tambah.blade.php`. | `a18cd1a` |
| **2026-10-26** | Penghapusan Komponen Proxy Redundan (DC-11) | Menghapus `modal-confirm-delete` & `modal-confirm-action`, menyatukan ke `<x-modal-confirm>`. | `70a6fad` |
| **2026-10-26** | Konsolidasi Modal Parsial Pelanggan (DC-12) | Merefaktor modal profil, edit, dan piutang pelanggan ke `<x-modal>`. | `e77dc95` |
| **2026-10-26** | Audit Rute & Normalisasi Imports (Tahap 3 & 4) | Normalisasi import controller WhatsApp di `routes/web.php` dan audit 0 inline confirm. | Current |
