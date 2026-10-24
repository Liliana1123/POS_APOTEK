# Dokumen Audit & Rencana Pembersihan Dead Code (deadcode-removal.md)

Dokumen ini berisi panduan komprehensif, metodologi audit, serta matriks periksa (*checklist*) menyeluruh untuk mengidentifikasi dan menghapus kode mati (*dead code*), file yatim (*orphaned files*), rute/metode tak terpakai, fungsi JavaScript usang, serta artefak sisa dari seluruh ekosistem proyek **POS Apotek** (mencakup 5 domain bisnis, 20 controller, 25 model, dan 21 modul view).

---

## 🎯 Tujuan Pembersihan (Goals)

1. **Kebersihan Total Repositori (Full Code Hygiene)**: Menyingkirkan file sampah stray, file default framework yang tidak terpakai (seperti `welcome.blade.php`), serta folder kosong tak bertuan.
2. **Eliminasi Logika Usang & Bertumpuk**: Memastikan tidak ada fungsi, event listener, atau modal lama yang masih hidup berdampingan dengan komponen reusable baru.
3. **Ramping & Cepat (Optimized Build & Cache)**: Mengurangi beban parsing Blade Compiler, meringankan bundle Vite, dan mempercepat autoloading Composer.
4. **Keamanan Eksekusi (Zero Regression)**: Menjamin 100% fungsionalitas apotek tetap berjalan normal dengan verifikasi test suite otomatis.

---

## 🗺️ Peta Cakupan Proyek (Project Scope Coverage)

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

## 🛡️ 4 Prinsip Keamanan Pembersihan (Safety First)

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

## 🔍 Temuan Audit Kode Mati Proyek (Project-Wide Dead Code Findings)

### Kategori A: File Sampah & Direktori Kosong di Repositori
File-file yatim atau sisa eksekusi yang tidak memiliki keterkaitan dengan alur sistem.

| No | Target File / Direktori | Ukuran | Status | Tindakan Pembersihan |
|---|---|---|---|---|
| A.1 | `how --stat 2ece851` (di root) | 1.8 KB | 🔴 File Sampah | File sisa typo output terminal `git show`. **Wajib dihapus**. |
| A.2 | `resources/views/welcome.blade.php` | 82.5 KB | 🔴 Yatim (Orphaned) | Template bawaan Laravel. Tidak ada rute di `routes/web.php` dan tidak ada controller yang memanggil `view('welcome')`. **Wajib dihapus**. |
| A.3 | `resources/views/discount-events/` | 0 B | 🟡 Direktori Kosong | Folder kosong tanpa file Blade apapun. **Wajib dihapus**. |
| A.4 | File backup `.bak`, `.old`, `.tmp` | 0 B | 🟢 Bersih | Tidak ditemukan file backup liar di dalam `resources/` maupun `app/`. |

---

### Kategori B: Blade Views & Partials (Per Domain Proyek)

#### B.1. Domain Master Data
* [x] **Modal Konfirmasi Hapus**: Sudah distandardisasi menggunakan `<x-modal-confirm-delete>` global di layout. Modal hapus individual lama di view telah dibersihkan.
* [x] **Aksi Tabel**: Tombol aksi pada `barang`, `supplier`, `satuan`, `pabrik`, `kategori`, `user`, dan `custom_discount` sudah menggunakan `<x-table-action>`.
* [ ] **Modal Detail Barang**: Audit `barang/index.blade.php` untuk memastikan tidak ada markup modal lama yang tidak terpakai.

#### B.2. Domain Transaksi Operasional
* [x] **Modal Penerimaan**: 4 modal AJAX di [penerimaan/index.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/penerimaan/index.blade.php) (`detail`, `payment`, `susulan`, `edit`) sudah distandardisasi menjadi `<x-modal>`.
* [x] **Modal Barang Rusak**: Modal detail dan edit di [rusak/index.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/rusak/index.blade.php) sudah distandardisasi menjadi `<x-modal>`.
* [ ] **Form Raksasa Tambah Penerimaan**: [penerimaan/index.blade.php:372-550](file:///c:/xampp/htdocs/pos-apotek/resources/views/penerimaan/index.blade.php#L372-L550) berisi 180 baris form faktur baru. Rencana: Pindahkan ke `penerimaan/partials/modal-tambah.blade.php` agar file index ramping.
* [ ] **Modal Transaksi Kasir POS**: Periksa `penjualan/create.blade.php` (`#modal-pilih-member`, `#modal-daftar-member`), pastikan tidak ada selektor modal statis yang sudah usang.

#### B.3. Domain Finansial & Laporan (7 Modul Laporan)
* [x] **Kop Surat Cetak**: 7 view laporan sudah 100% menggunakan `<x-print-kop>`.
* [x] **Tanda Tangan Dokumen**: 7 view laporan sudah 100% menggunakan `<x-print-signature>`.
* [x] **Tombol Aksi Cetak & Excel**: 7 view laporan sudah 100% menggunakan `<x-print-actions>`.
* [x] **Empty State Tabel Laporan**: Blok manual 20 baris `@empty` pada laporan penerimaan, rusak, diskon, stok, dan penjualan sudah digantikan oleh `<x-empty-state>`.

#### B.4. Domain CRM & WhatsApp (Pelanggan & Piutang)
* [ ] **Partials Raksasa WhatsApp**: File `resources/views/pelanggan/partials/modal-whatsapp-piutang.blade.php` berukuran **1.200+ baris** dan mendefinisikan 9 modal berbeda. Perlu diaudit apakah seluruh 9 modal tersebut benar-benar aktif atau ada modal prototype/eksperimen lama yang sudah mati.

---

### Kategori C: Controller Methods & Route yang Tidak Terpakai (20 Controller)

Audit terhadap seluruh rute di `routes/web.php` dan 20 Controller:

| Modul Controller | File | Audit Status | Temuan / Catatan |
|---|---|---|---|
| **Barang** | `BarangController.php` | 🟢 Aktif | Master obat, batch, riwayat stok, barcode. |
| **Kategori, Satuan, Pabrik, Supplier** | `*Controller.php` | 🟢 Aktif | CRUD master data ringan via modal. |
| **Pelanggan & Piutang** | `PelangganController.php`, `PembayaranPiutangController.php` | 🟡 Perlu Audit | Periksa method preview tagihan yang mungkin sudah digantikan oleh endpoint WhatsApp. |
| **Penerimaan & Rusak** | `PenerimaanController.php`, `RusakController.php` | 🟢 Aktif | Transaksi masuk, retur, pembayaran supplier. |
| **Penjualan (Kasir)** | `PenjualanController.php` | 🟢 Aktif | Transaksi kasir, cetak struk thermal, invoice. |
| **Laporan & Analytics** | `LaporanController.php` (50 KB) | 🟡 Perlu Audit | Periksa apakah ada method export/laporan lama yang rutenya sudah tidak dipakai di frontend. |
| **Dashboard** | `DashboardController.php` (34 KB) | 🟢 Aktif | Query widget ringkasan harian/bulanan. |
| **WhatsApp Monitoring** | `WhatsAppMonitorController.php`, `WhatsAppPiutangController.php` | 🟢 Aktif | Webhook dan dispatch pesan WhatsApp. |
| **Permission & User** | `PermissionController.php`, `UserController.php` | 🟢 Aktif | Reset hak akses default dan manajemen staf. |
| **Quick Search** | `QuickSearchController.php` | 🟢 Aktif | Global search shortcut (`Ctrl+K`). |
| **Pengaturan** | `PengaturanController.php` | 🟢 Aktif | Profil apotek, SIA, SIPA, logo cetak. |

---

### Kategori D: JavaScript, Event Listener & Selektor Frontend

Audit pada file script di `resources/js/`:

| File Script | Area Audit | Status / Tindakan |
|---|---|---|
| `resources/js/shared/layout.js` | Interceptor form penghapusan `DELETE` | [x] Ditambahkan guard `data-confirmed="true"` agar tidak memunculkan modal hapus bertumpuk. |
| `resources/js/pages/penerimaan-index.js` | Modal detail, pembayaran, susulan | [x] Sudah sinkron dengan ID pada `<x-modal>`. |
| `resources/js/pages/penjualan-create.js` (560 baris) | Logika kasir & keranjang POS | [ ] Audit fungsi lama / variabel global yang tidak pernah dipanggil. |
| Script modal inline di Blade | Event listener manual tombol tutup | [x] Digantikan oleh class standar `.modal-close-btn` bawaan `<x-modal>`. |

---

### Kategori E: CSS & Styling Redundan

| File / Class CSS | Area Audit | Status / Temuan |
|---|---|---|
| `resources/css/app.css` | `.modal-backdrop-custom`, `.modal-container-custom` | [x] Fondasi modal bersih, terhubung langsung ke `<x-modal>`. |
| `.card-base` di luar `.table-custom-container` | `permission/index.blade.php` | [x] Sudah dibersihkan dari double border / shadow bertumpuk. |
| Inline styles tombol aksi (`style="color: ..."`) | Seluruh file index | [ ] Standardisasi ke Tailwind color classes (misal `text-blue-600`, `text-amber-500`). |

---

### Kategori F: Model Eloquent & Unused PHP Imports (25 Model)

| File Model | Area Audit | Status |
|---|---|---|
| `app/Models/Penerimaan.php`, `DetailPenerimaan.php` | Helper kalkulasi subtotal & status lunas | [x] Bersih, dipakai di komponen status-badge. |
| `app/Models/Barang.php`, `Pelanggan.php` | Scope pencarian & filter | [x] Bersih, sinkron dengan trait `Filterable`. |
| `app/Models/*.php` (Semua model) | Unused namespace imports (`use ...`) | [ ] Jalankan audit import PHP yang tidak terpakai di baris 1–15 setiap model. |

---

## 📋 Urutan Eksekusi Pembersihan (Actionable Roadmap)

```
[Tahap 1: Root & Yatim] ──> [Tahap 2: Partials Form] ──> [Tahap 3: Route & Controller] ──> [Tahap 4: JS & CSS] ──> [Tahap 5: Verifikasi Final]
- Hapus file stray root    - Pisah form tambah 180 baris - Audit route:list           - Bersihkan listener usang  - php artisan test (11/11 pass)
- Hapus welcome.blade.php    di penerimaan/index         - Rapikan method mati        - Rapikan inline styles     - php artisan view:cache
- Hapus discount-events/                                                                                          - npm run build
```

---

## 📝 Catatan Riwayat Pembersihan (Changelog)

| Tanggal | Target Pembersihan | Dampak / Hasil | Ref Commit |
|---|---|---|---|
| **2026-10-24** | Duplikasi Modal di `penerimaan/index` & `rusak/index` | Dihapus 180+ baris HTML manual, distandardisasi ke `<x-modal>`. | `934c8e9` |
| **2026-10-24** | Duplikasi Empty State Laporan (5 views) | Dihapus 100+ baris blok manual, distandardisasi ke `<x-empty-state>`. | `934c8e9` |
| **2026-10-24** | Tag Header Bertumpuk `<tr> <tr>` di `penerimaan/index` | Diperbaiki sintaks HTML yang salah dan nested table row. | `643654d` |
| **2026-10-24** | Pembungkus Ganda `.card-base` di `permission/index` | Menghilangkan double border & shadow container. | `643654d` |
| **2026-10-24** | Konflik Interceptor Form Modal Hapus | Menambahkan pelindung `data-confirmed` pada `modal-confirm-delete`. | `5d42f77` |
