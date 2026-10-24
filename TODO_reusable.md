# Rencana Refaktorisasi & Komponen Reusable (TODO)

Dokumen ini berisi daftar periksa (*checklist*) dan perencanaan teknis untuk mengekstrak kode-kode yang berulang (*boilerplate / duplication*) menjadi komponen Blade, direktif, helper, dan trait yang dapat digunakan kembali (*reusable*) di seluruh proyek **POS Apotek**.

---

## 🎯 Tujuan Refaktorisasi
1. **DRY (Don't Repeat Yourself)**: Menghapus duplikasi ratusan baris kode HTML dan PHP.
2. **Konsistensi UI/UX**: Tampilan form, badge, kop cetak dokumen, dan format angka seragam di semua modul.
3. **Kemudahan Pemeliharaan (Maintainability)**: Perubahan aturan bisnis atau layout cukup dilakukan pada satu file komponen.
4. **Kecepatan Pengembangan**: Mempercepat pembuatan modul baru cukup dengan menyusun komponen yang sudah ada.

---

## 📌 Status Tahapan Implementasi

```
[Phase 1: Cetak Dokumen] ✅ SELESAI ──> [Phase 2: Form & UI] ✅ SELESAI ──> [Phase 3: Directives] ✅ SELESAI ──> [Phase 4: Eloquent Traits] ✅ SELESAI ──> [Phase 5: Notifikasi & Toast] ✅ SELESAI ──> [Phase 6: Status Badge Cerdas] ✅ SELESAI ──> [Phase 7: Pagination Wrapper] ✅ SELESAI ──> [Phase 8: Modal Konfirmasi Hapus] ✅ SELESAI ──> [Phase 9: Header Halaman] ✅ SELESAI
```

---

## Fase 1: Template Cetak Dokumen (High Impact)
*Modul cetak laporan dan surat jalan apotek memiliki duplikasi paling banyak (>7 file).*

### 1.1 Komponen Kop Surat Apotek (`<x-print-kop>`)
- [x] **Lokasi**: `resources/views/components/print-kop.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Menampilkan logo, nama apotek resmi, alamat, nomor telepon/email, serta nomor SIA & SIPA dari cache data `InfoApotek`.
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/laporan/penjualan.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/rusak.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/laba_rugi.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/stok.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/penerimaan.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/diskon.blade.php` (Di-refactor)
  - [x] `resources/views/activity_log/index.blade.php` (Di-refactor)
- **Contoh Penggunaan**:
  ```blade
  <x-print-kop :apotek="$apotek" title="LAPORAN PENJUALAN APOTEK">
      Periode: {{ $dari }} s/d {{ $sampai }}
  </x-print-kop>
  ```

### 1.2 Komponen Tanda Tangan Cetak (`<x-print-signature>`)
- [x] **Lokasi**: `resources/views/components/print-signature.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Blok tanda tangan 2 kolom standar dokumen farmasi (Kiri: *Penanggung Jawab / Apoteker Pengelola*, Kanan: *Petugas / Kasir Pembuat Dokumen*).
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/laporan/penjualan.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/rusak.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/laba_rugi.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/stok.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/penerimaan.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/diskon.blade.php` (Di-refactor)
  - [x] `resources/views/activity_log/index.blade.php` (Di-refactor)
- **Contoh Penggunaan**:
  ```blade
  <x-print-signature :apotek="$apotek" />
  ```

---

## Fase 2: Form & Input Primitives

### 2.1 Komponen Pencarian Standar (`<x-search-input>`)
- [x] **Lokasi**: `resources/views/components/search-input.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Mengabstraksi input search wrapper `relative`, padding kanan `pr-8`, dan ikon kaca pembesar Heroicon.
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/kategori/index.blade.php` (Di-refactor)
  - [x] `resources/views/barang/index.blade.php` (Di-refactor)
  - [x] `resources/views/satuan/index.blade.php` (Di-refactor)
  - [x] `resources/views/supplier/index.blade.php` (Di-refactor)
  - [x] `resources/views/pabrik/index.blade.php` (Di-refactor)
  - [x] `resources/views/pelanggan/index.blade.php` (Di-refactor)
- **Contoh Penggunaan**:
  ```blade
  <x-search-input name="cari" placeholder="Cari..." class="w-full sm:w-[360px]" />
  ```

### 2.2 Komponen Form Field Modal (`<x-form.*>`)
- [x] **Lokasi**: 
  - `resources/views/components/form/input.blade.php` (Sudah dibuat & diuji)
  - `resources/views/components/form/select.blade.php` (Sudah dibuat & diuji)
  - `resources/views/components/form/textarea.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Menyeragamkan label dengan tanda required merah (`*`), class `form-input`, dan placeholder error `<p class="modal-field-error ...">` untuk validasi AJAX.
- [x] **File Terdampak**:
  - [x] `resources/views/kategori/index.blade.php` (Di-refactor)
  - [x] `resources/views/barang/index.blade.php` (Di-refactor)
  - [x] `resources/views/satuan/index.blade.php` (Di-refactor)
  - [x] `resources/views/supplier/index.blade.php` (Di-refactor)
  - [x] `resources/views/pabrik/index.blade.php` (Di-refactor)
  - [x] `resources/views/pelanggan/index.blade.php` (Di-refactor)
- **Contoh Penggunaan**:
  ```blade
  <x-form.input name="nama" label="Nama Kategori" required placeholder="Masukkan nama..." />
  <x-form.select name="satuan_id" label="Satuan" :options="$satuans" required />
  <x-form.textarea name="alamat" label="Alamat Lengkap" rows="3" />
  ```

### 2.3 Komponen KPI Card / Metrik (`<x-kpi-card>`)
- [x] **Lokasi**: `resources/views/components/kpi-card.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Menampilkan kartu ringkasan angka statistik (judul, nominal, keterangan, ikon dengan warna background aksen).
- [x] **File Terdampak / Status Integrasi**:
  - [x] `resources/views/dashboard.blade.php` (Di-refactor: 6 kartu KPI utama)
  - [ ] `resources/views/laporan/penjualan.blade.php`
  - [ ] `resources/views/penjualan/index.blade.php`
- **Contoh Penggunaan**:
  ```blade
  <x-kpi-card 
      title="Omzet Bersih" 
      :value="'Rp ' . number_format($omzetBersih, 0, ',', '.')" 
      subtitle="Setelah diskon" 
      icon="heroicon-o-banknotes" 
      variant="bersih" 
  />
  ```

### 2.4 Komponen Filter Rentang Tanggal Preset (`<x-date-range-filter>`)
- [x] **Lokasi**: `resources/views/components/date-range-filter.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Tombol pill preset (*Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini, Custom*) beserta toggle input kalender `dari` & `sampai`.
- [x] **File Terdampak / Status Integrasi**:
  - [x] `resources/views/dashboard.blade.php` (Di-refactor)
  - [x] `resources/views/laporan/laba_rugi.blade.php` (Di-refactor)
- **Contoh Penggunaan**:
  ```blade
  <x-date-range-filter
      :action="route('dashboard')"
      :periode="$periode"
      :dari="$dari"
      :sampai="$sampai"
  />
  ```

### 2.5 Komponen Pagination Wrapper (`<x-pagination>`)
- [x] **Lokasi**: `resources/views/components/pagination.blade.php` (Sudah dibuat & diuji)
- [x] **Deskripsi**: Wrapper cerdas untuk tautan pagination Laravel yang mengenkapsulasi container `.mt-4.pagination-container`, template apotek (`vendor.pagination.apotek`), dan otomatis menyembunyikan kontainer kosong jika hanya ada 1 halaman.
- [x] **File Terdampak / Status Integrasi (12 Modul)**:
  - [x] `resources/views/kategori/index.blade.php`
  - [x] `resources/views/barang/index.blade.php`
  - [x] `resources/views/satuan/index.blade.php`
  - [x] `resources/views/supplier/index.blade.php`
  - [x] `resources/views/pabrik/index.blade.php`
  - [x] `resources/views/pelanggan/index.blade.php`
  - [x] `resources/views/user/index.blade.php`
  - [x] `resources/views/penjualan/index.blade.php`
  - [x] `resources/views/penerimaan/index.blade.php`
  - [x] `resources/views/rusak/index.blade.php`
  - [x] `resources/views/custom_discount/index.blade.php`
  - [x] `resources/views/whatsapp-monitor/index.blade.php`
- **Contoh Penggunaan**:
  ```blade
  <x-pagination :paginator="$barangs" />
  ```

### 2.6 Komponen Modal Konfirmasi Hapus Reusable (`<x-modal-confirm-delete>`)
- [x] **Lokasi**: `resources/views/components/modal-confirm-delete.blade.php` (Dibuat & terpasang global di `layouts/app.blade.php`)
- [x] **Deskripsi**: Dialog konfirmasi penghapusan data seragam yang menggantikan popup default browser `window.confirm()`. Menampilkan ikon trash merah ber-ring lembut, judul, pesan peringatan tindakan tidak dapat dibatalkan, kotak sorotan nama item yang akan dihapus, tombol batal, dan tombol eksekusi hapus merah. Mendukung API global `window.confirmDelete(actionUrl, itemName, message)` dan otomatisasi pemicu klik atribut `data-confirm-delete`.
- [x] **File Terdampak / Status Integrasi**:
  - [x] `resources/views/layouts/app.blade.php`: Terpasang global.
  - [x] `resources/views/components/table-action.blade.php`: Terintegrasi dengan prop `:delete-item` dan event delegation.
  - [x] `resources/views/kategori/index.blade.php`: Mengirimkan `:delete-item="$kategori->nama"`.
  - [x] `resources/views/barang/index.blade.php`: Mengirimkan `:delete-item="$barang->nama"`.
  - [x] `resources/views/satuan/index.blade.php`: Mengirimkan `:delete-item="$satuan->nama"`.
  - [x] `resources/views/supplier/index.blade.php`: Mengirimkan `:delete-item="$supplier->nama"`.
  - [x] `resources/views/pabrik/index.blade.php`: Mengirimkan `:delete-item="$pabrik->nama"`.
  - [x] `resources/views/user/index.blade.php`: Mengirimkan `:delete-item="$user->name"`.
  - [x] `resources/views/custom_discount/index.blade.php`: Mengirimkan `:delete-item="$discount->nama"`.
  - [x] `resources/views/pelanggan/index.blade.php`: Tombol hapus terintegrasi modal dengan nama pelanggan.
  - [x] `resources/views/penerimaan/index.blade.php`: Tombol hapus terintegrasi modal dengan nomor faktur penerimaan.
  - [x] `resources/views/rusak/index.blade.php`: Tombol hapus terintegrasi modal dengan nama obat & batch.
  - [x] `resources/views/laporan/laba_rugi.blade.php`: Tombol hapus beban terintegrasi modal dengan rincian biaya.
- **Contoh Penggunaan**:
  ```blade
  <button type="button"
      data-confirm-delete="{{ route('barang.destroy', $barang) }}"
      data-item-name="{{ $barang->nama }}">
      Hapus
  </button>
  ```

### 2.7 Komponen Header Halaman Standar (`<x-page-header>`)
- [x] **Lokasi**: `resources/views/components/page-header.blade.php` (Ditingkatkan & diuji)
- [x] **Deskripsi**: Menyeragamkan header judul halaman `h1`, subtitle/deskripsi, tombol panah kembali opsional (`:back-url`), merge attribute class/ID, dan slot tombol aksi (Tambah Data, Cetak Laporan, Ekspor Excel).
- [x] **File Terdampak / Status Integrasi (100% Seluruh View)**:
  - [x] Modul Master: `kategori`, `barang`, `satuan`, `supplier`, `pabrik`, `pelanggan`, `user`, `custom_discount`, `permission`, `pengaturan`.
  - [x] Modul Transaksi & Kasir: `penjualan/index`, `penjualan/create`, `penjualan/show`, `penerimaan/index`, `penerimaan/create`, `rusak/index`, `rusak/create`, `rusak/show`.
  - [x] Modul Laporan & Monitoring: `laporan/penjualan`, `laporan/penerimaan`, `laporan/rusak`, `laporan/stok`, `laporan/diskon`, `laporan/laba_rugi`, `whatsapp-monitor/index`, `activity_log/index`.
- **Contoh Penggunaan**:
  ```blade
  <x-page-header title="Daftar Penerimaan Barang" subtitle="Kelola data faktur obat masuk." :back-url="route('penerimaan.index')">
      <button class="btn-primary">Tambah</button>
  </x-page-header>
  ```

---

## Fase 3: Blade Directives & Helper Formatting (Rupiah & Tanggal)

### 3.1 Directive Mata Uang `@rupiah(...)`
- [x] **Lokasi**: `app/Providers/AppServiceProvider.php` (Sudah didaftarkan & diuji)
- [x] **Deskripsi**: Menggantikan lebih dari 150 pemanggilan `Rp {{ number_format($val, 0, ',', '.') }}` di seluruh view.
- **Pendaftaran**:
  ```php
  \Blade::directive('rupiah', function ($expression) {
      return "<?php echo 'Rp ' . number_format((float)($expression ?? 0), 0, ',', '.'); ?>";
  });
  ```
- **Contoh Penggunaan**:
  ```blade
  @rupiah($item->subtotal)
  ```

### 3.2 Directive Tanggal Indonesia (`@tanggalId` & `@tanggalWaktuId`)
- [x] **Lokasi**: `app/Providers/AppServiceProvider.php` (Sudah didaftarkan, mendukung opsi format dinamis, & diuji)
- [x] **Deskripsi**: Menyeragamkan parsing tanggal Carbon Indonesia (`d M Y`, `d F Y`, dan `d M Y, H:i`).
- [x] **File Terdampak / Status Integrasi (100% Selesai)**:
  - [x] `resources/views/penjualan/show.blade.php`
  - [x] `resources/views/penjualan/index.blade.php`
  - [x] `resources/views/pelanggan/show.blade.php`
  - [x] `resources/views/pelanggan/index.blade.php`
  - [x] `resources/views/custom_discount/index.blade.php`
  - [x] `resources/views/whatsapp-monitor/index.blade.php`
  - [x] `resources/views/activity_log/index.blade.php`
  - [x] `resources/views/rusak/show.blade.php`
  - [x] `resources/views/rusak/index.blade.php`
  - [x] `resources/views/rusak/print.blade.php`
  - [x] `resources/views/penerimaan/index.blade.php`
  - [x] `resources/views/penerimaan/payment.blade.php`
  - [x] `resources/views/penerimaan/show.blade.php`
  - [x] `resources/views/penerimaan/susulan.blade.php`
  - [x] `resources/views/laporan/penjualan.blade.php`
  - [x] `resources/views/laporan/penerimaan.blade.php`
  - [x] `resources/views/laporan/rusak.blade.php`
  - [x] `resources/views/laporan/stok.blade.php`
  - [x] `resources/views/laporan/diskon.blade.php`
  - [x] `resources/views/laporan/laba_rugi.blade.php`
- **Contoh Penggunaan**:
  ```blade
  @tanggalId($penjualan->created_at)
  @tanggalId($penerimaan->tanggal, 'd F Y')
  @tanggalWaktuId($activity->created_at)
  ```

---

## Fase 4: Eloquent Model Traits (Backend Logic)

### 4.1 Trait Generator Kode Otomatis (`GeneratesCode`)
- [x] **Lokasi**: `app/Traits/GeneratesCode.php` (Sudah dibuat & diintegrasikan)
- [x] **Deskripsi**: Logika terpusat untuk membuat kode berurut apotek dengan prefix (contoh: `P-0001`, `MBR-000001`, `TRX-20261022-0001`).
- [x] **Integrasi Model**: `app/Models/Pelanggan.php` (`generateMemberId`)

### 4.2 Trait Filter Query Eloquent (`Filterable`)
- [x] **Lokasi**: `app/Traits/Filterable.php` (Sudah dibuat & diintegrasikan)
- [x] **Deskripsi**: Query scopes yang sering dipakai controller:
  - `scopeSearch($query, $columns, $term)` (Mendukung pencarian multi-kolom dan relasi bertingkat / nested relations)
  - `scopeDateRange($query, $column, $from, $to)` (Inklusif 00:00:00 s/d 23:59:59)
  - `scopeFilterStatus($query, $column, $status)`
- [x] **Integrasi Model & Controller (100% Selesai)**:
  - [x] `Pelanggan` & `PelangganController` (search multi-kolom nama, telepon, member_id)
  - [x] `Barang` & `BarangController` (search obat, kode apotek, kfa, merk, barcode & filter status aktif)
  - [x] `Supplier` & `SupplierController` (search nama, telepon, alamat, pic)
  - [x] `Pabrik` & `PabrikController` (search nama, telepon)
  - [x] `Kategori` & `KategoriController` (search nama)
  - [x] `Satuan` & `SatuanController` (search nama)
  - [x] `User` & `UserController` (search nama, email)
  - [x] `Penjualan` & `PenjualanController` (search no_faktur & dateRange tanggal)
  - [x] `Penerimaan` & `PenerimaanController` (search no_faktur, supplier.nama)
  - [x] `Rusak` & `RusakController` (search relasi nested detailPenerimaan.barang.nama, no_batch)
  - [x] `ActivityLog` & `DashboardController` (dateRange created_at & search action, target, user_name, user.name)
  - [x] `WhatsAppLog` & `WhatsAppMonitorController` (search nama_member, nomor_whatsapp, isi_pesan)

---

## Fase 5: Komponen Notifikasi Reusable (Toast & Alert)
*Notifikasi umpan balik aksi user (CRUD, pesan sukses, peringatan validasi, dan feedback AJAX).*

### 5.1 Komponen Toast Notifikasi Floating Top-Center (`<x-toast>`)
- [x] **Lokasi**: `resources/views/components/toast.blade.php` (Dibuat & diuji)
- [x] **Deskripsi**:
  - Penempatan **Top-Center (Atas Tengah)** (`fixed top-6 left-1/2 -translate-x-1/2 z-[9999]`) tepat setinggi pandangan mata (*eye-level*) staf & kasir.
  - Tampilan pill modern bernuansa *dark glassmorphism* (`bg-slate-900/95 backdrop-blur-md`) dengan border glow & ikon Heroicons berwarna (`success`, `error`, `warning`, `info`).
  - Animasi transisi masuk (*slide-down*) dan keluar (*slide-up*) dengan CSS transform.
  - Fitur jeda timer otomatis saat kursor mouse di-hover di atas notifikasi (*pause on hover*).
  - Menyediakan API JavaScript global seragam: `window.showToast(message, type, duration)` untuk request asynchronous/AJAX.
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/layouts/app.blade.php`: Menggantikan markup notifikasi session toast statis hardcoded dengan tag `<x-toast />`.
  - [x] `resources/views/whatsapp-monitor/index.blade.php`: Menghapus DOM duplikat `#fuToastNotification` dan mendelegasikan fungsi lokal `showToast` ke `window.showToast(...)`.

### 5.2 Komponen Inline Banner Alert (`<x-alert>`)
- [x] **Lokasi**: `resources/views/components/alert.blade.php` (Dibuat & diuji)
- [x] **Deskripsi**: Banner notifikasi kontekstual inline yang disematkan langsung di dalam form atau tabel, mendukung 4 varian warna (`success`, `danger/error`, `warning`, `info`), judul tebal opsional, slot konten fleksibel, dan tombol dismiss silang (`x-mark`).
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/laporan/laba_rugi.blade.php`: Menggantikan banner alert error manual `$errors->any()` dengan `<x-alert type="danger" ...>`.

---

## Fase 6: Komponen Status Badge Cerdas (`<x-status-badge>`)
*Badge status bisnis apotek terpusat (Pembayaran, Penerimaan, Stok, Kadaluarsa, Resep, Aktif/Nonaktif, Role, Member, Promo).*

### 6.1 Komponen Status Badge Cerdas (`<x-status-badge>`)
- [x] **Lokasi**: `resources/views/components/status-badge.blade.php` (Dibuat & diuji)
- [x] **Deskripsi**: Mengabstraksi seluruh logika penentuan warna badge, border, dan label berdasarkan domain bisnis apotek:
  - `type="pembayaran"`: "Lunas" (Hijau `badge-success`) vs "Belum Lunas" / "Piutang" (Kuning `badge-warning`).
  - `type="penerimaan"`: "Lengkap" (Hijau) vs "Belum Lengkap" (Kuning).
  - `type="stok"`: "Aman" (Hijau), "Menipis" (Kuning), "Habis" / "Minus" (Merah `badge-danger`).
  - `type="expired"`: "Normal" (Hijau), "< 3 Bulan" (Kuning), "< 1 Bulan" (Orange `badge-orange`), "KADALUARSA" (Merah).
  - `type="aktif"`: "Aktif" (Hijau) vs "Nonaktif" (Merah).
  - `type="resep"`: "Wajib Resep" (Biru/Sky `badge-info`) vs "Bebas" (Abu-abu `badge-secondary`).
  - `type="role"`: Superadmin (Merah), Admin (Biru), Apoteker (Kuning), Kasir (Hijau), Gudang (Abu-abu).
  - `type="member"`: Nakes (Indigo), Member Only (Kuning), Pelanggan Tetap (Biru/Hijau), Umum (Abu-abu).
  - `type="promo"`: Aktif, Belum Mulai, Berakhir, Non-aktif.
- [x] **File Terdampak / Status Refactor**:
  - [x] `resources/views/penerimaan/index.blade.php`: Status penerimaan & status pembayaran.
  - [x] `resources/views/penerimaan/show.blade.php`: Status penerimaan, pembayaran, & tagihan lunas.
  - [x] `resources/views/penerimaan/payment.blade.php`: Status penerimaan & pembayaran.
  - [x] `resources/views/penerimaan/susulan.blade.php`: Status penerimaan & pembayaran.
  - [x] `resources/views/barang/index.blade.php`: Status aktif obat/barang.
  - [x] `resources/views/user/index.blade.php`: Role pengguna & status aktif.
  - [x] `resources/views/custom_discount/index.blade.php`: Status promo diskon.
  - [x] `resources/views/laporan/stok.blade.php`: Status kadaluarsa & status sisa stok.
  - [x] `resources/views/penjualan/index.blade.php`: Status membership pelanggan.
  - [x] `resources/views/laporan/diskon.blade.php`: Jenis penggunaan diskon.
  - [x] `resources/views/pelanggan/index.blade.php`: Status member & piutang.
  - [x] `resources/views/pelanggan/show.blade.php`: Mengganti 57 baris style inline dengan komponen badge.

---

## 📊 Laporan Hasil Eksekusi Refaktorisasi

### 1. Eksekusi Target 1 (Dashboard KPI Cards)
* **Target File**: `resources/views/dashboard.blade.php`
* **Perubahan yang Diterapkan**:
  - Merefaktor 6 kartu metrik eksekutif utama di bagian atas dashboard menjadi komponen `<x-kpi-card>`.
  - Kartu yang direfaktor:
    1. **Omzet Kotor**: Variant `kotor`, ikon `heroicon-o-chart-bar`, warna badge `bg-blue-50 text-blue-600`.
    2. **Total Diskon**: Variant `diskon`, ikon `heroicon-o-tag`, warna badge `bg-purple-50 text-purple-600`.
    3. **Omzet Bersih**: Variant `bersih`, ikon `heroicon-o-banknotes`, warna badge `bg-emerald-50 text-emerald-600`.
    4. **Total Member**: Variant `member`, ikon `heroicon-o-user-group`, warna badge `bg-amber-50 text-amber-600`.
    5. **Piutang Supplier**: Ikon `heroicon-o-building-office-2`, warna badge `bg-sky-50 text-sky-600`.
    6. **Piutang Member**: Ikon `heroicon-o-credit-card`, warna badge `bg-rose-50 text-rose-600`.
* **Keuntungan**:
  - Mengurangi baris kode HTML berulang yang sulit dibaca.
  - Memastikan konsistensi penataan layout flexbox, font size, margin, dan shadow.
  - Pengujian sintaks melalui `php artisan view:cache` berjalan sukses tanpa kendala.

### 2. Eksekusi Target 2 (Filter Rentang Tanggal Preset)
* **Target File**: `resources/views/dashboard.blade.php` dan `resources/views/laporan/laba_rugi.blade.php`
* **Komponen Baru**: `resources/views/components/date-range-filter.blade.php` (`<x-date-range-filter>`)
* **Perubahan yang Diterapkan**:
  - Mengekstrak tombol pill preset (*Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini, Custom*) dan form input kalender rentang tanggal (*dari* & *sampai*) beserta tombol *Reset* ke dalam satu komponen reusable.
  - Menyatukan fungsi JavaScript toggle custom date agar aman dan tidak perlu duplikasi script inline di setiap halaman.
  - Menerapkan `<x-date-range-filter>` pada:
    1. **Header Dashboard**: Filter periode operasional apotek.
    2. **Laporan Laba Rugi**: Filter periode analitik finansial apotek.
* **Keuntungan**:
  - Menghapus puluhan baris form manual dan styling tombol pill yang berulang.
  - Memastikan parameter URL periode dan tanggal tersinkronisasi dengan rapi.

### 3. Eksekusi Target 3 (Rollout Directive @rupiah ke Modul Transaksi & Kasir)
* **Target File**:
  - `resources/views/penjualan/show.blade.php`: Struk kasir thermal & modal detail transaksi (harga item, subtotal, diskon obat, total akhir).
  - `resources/views/penjualan/index.blade.php`: Tabel riwayat penjualan & ringkasan cetak rekap grand total.
  - `resources/views/penerimaan/payment.blade.php`: Modal pembayaran faktur hutang supplier (kartu total belanja, PPN, total tagihan, total dibayar, sisa tagihan, kelebihan bayar, batas maksimal nominal input, dan riwayat cicilan).
  - `resources/views/penerimaan/show.blade.php`: Modal rincian faktur penerimaan (subtotal obat, harga beli & jual per batch, ringkasan belanja, PPN, total tagihan, dibayar, dan sisa hutang).
  - `resources/views/penerimaan/index.blade.php`: Tabel daftar faktur (kolom Total Transaksi dan Sisa Piutang/Hutang).
  - `resources/views/penerimaan/print.blade.php`: Dokumen cetak faktur penerimaan (tabel detail barang, rincian biaya PPN & belanja, serta riwayat pembayaran).
* **Perubahan yang Diterapkan**:
  - Menggantikan seluruh formatting manual boilerplate `Rp {{ number_format(..., 0, ',', '.') }}` dengan Blade directive terpusat `@rupiah(...)`.
* **Keuntungan**:
  - Mencegah inkonsistensi simbol mata uang dan pemisah ribuan (`Rp ` standar Indonesia).
  - Kode template Blade menjadi jauh lebih ringkas, bersih, dan mudah dipelihara.
  - Pengujian sintaks dan rendering berjalan 100% aman (lolos validasi `php artisan view:cache`).

### 4. Eksekusi Target 4 (Rollout Directive @tanggalId & @tanggalWaktuId ke Seluruh View Aplikasi)
* **Penyempurnaan Engine Directive**:
  - Directive `@tanggalId` dan `@tanggalWaktuId` di `app/Providers/AppServiceProvider.php` ditingkatkan menggunakan closure dinamis yang mendukung format kustom opsional (default `'d M Y'` atau `'d F Y'`, `'d M Y, H:i'`).
* **Target File yang Direfaktor (20 File View)**:
  - **Penjualan & Kasir**: `penjualan/show.blade.php` (struk & due date), `penjualan/index.blade.php` (tabel riwayat & print summary).
  - **Pelanggan & Diskon**: `pelanggan/show.blade.php` (tanggal lahir, member since, riwayat transaksi), `pelanggan/index.blade.php` (jatuh tempo aktif), `custom_discount/index.blade.php` (tanggal mulai & selesai diskon).
  - **Monitoring & Audit**: `whatsapp-monitor/index.blade.php` (waktu kirim & follow up), `activity_log/index.blade.php` (timestamp log audit layar & cetak).
  - **Barang Rusak**: `rusak/show.blade.php`, `rusak/index.blade.php`, `rusak/print.blade.php` (tanggal lapor & expired date batch).
  - **Penerimaan & Faktur**: `penerimaan/index.blade.php`, `penerimaan/payment.blade.php`, `penerimaan/show.blade.php`, `penerimaan/susulan.blade.php` (tanggal terima, tanggal faktur, jatuh tempo, riwayat pembayaran).
  - **Seluruh Laporan Eksekutif**: `laporan/penjualan.blade.php`, `laporan/penerimaan.blade.php`, `laporan/rusak.blade.php`, `laporan/stok.blade.php`, `laporan/diskon.blade.php`, `laporan/laba_rugi.blade.php`.
* **Keuntungan**:
  - Mengeliminasi duplikasi kode `\Carbon\Carbon::parse(...)` dan pemanggilan `translatedFormat` yang rawan error `null`.
  - Format penanggalan berbahasa Indonesia menjadi seragam, konsisten, dan elegan di seluruh modul apotek.
  - Berhasil diuji dan lolos validasi `php artisan view:cache`.

### 5. Eksekusi Target 5 (Integrasi Backend Eloquent Model Traits: GeneratesCode & Filterable)
* **Penyempurnaan Trait Backend**:
  - `app/Traits/GeneratesCode.php`: Diperkuat dengan `whereNotNull($column)` dan `orderByDesc($column)->orderByDesc('id')` untuk menjamin pemilihan record sequence kode tertinggi secara konsisten.
  - `app/Traits/Filterable.php`: Ditingkatkan untuk mendukung nested relations multi-tingkat (seperti `detailPenerimaan.barang.nama` dan `user.name`) selain kolom flat biasa.
* **Integrasi Model & Controller**:
  - **GeneratesCode**: Diintegrasikan langsung pada model `Pelanggan` (`generateMemberId()` menghasilkan kode urut `MBR-000001` dst).
  - **Filterable**: Diintegrasikan ke 12 model dan controller (Pelanggan, Barang, Supplier, Pabrik, Kategori, Satuan, User, Penjualan, Penerimaan, Rusak, ActivityLog, WhatsAppLog).
* **Keuntungan**:
  - Menghilangkan ratusan baris query boilerplate `where(function($q) { $q->where('col', 'like', ...) })` di belasan controller.
  - Controller menjadi sangat deklaratif, bersih, dan konsisten dalam menangani pencarian, rentang tanggal, dan status aktif.
  - Seluruh service & routing lolos verifikasi `php artisan about` dan `php artisan view:clear`.

### 6. Eksekusi Target 6 (Komponen Notifikasi Reusable Top-Center & Inline Alert)
* **Komponen Baru**:
  - `resources/views/components/toast.blade.php` (`<x-toast />`): Floating toast notification berdesain pill di posisi **Top-Center (Atas Tengah)** (`fixed top-6 left-1/2 -translate-x-1/2 z-[9999]`) agar langsung terlihat oleh kasir/petugas apotek tanpa tertutup area kerja bawah.
  - `resources/views/components/alert.blade.php` (`<x-alert />`): Komponen banner alert inline dengan 4 varian warna, border halus, judul tebal opsional, dan tombol dismiss.
* **Perubahan yang Diterapkan**:
  - Mengintegrasikan `<x-toast />` ke layout utama `resources/views/layouts/app.blade.php`, menggantikan blok script dan DOM hardcoded `#toast-success` dan `#toast-error`.
  - Menerapkan fungsi API global `window.showToast(message, type, duration)` pada `<x-toast>` yang dapat dipanggil langsung dari AJAX mana pun di aplikasi.
  - Merefaktor `resources/views/whatsapp-monitor/index.blade.php`: menghapus duplikasi markup `#fuToastNotification` dan mendelegasikan pemanggilan `showToast` ke helper global `window.showToast`.
  - Menerapkan `<x-alert type="danger" ...>` pada `resources/views/laporan/laba_rugi.blade.php` untuk menampilkan pesan validasi error form.
* **Keuntungan**:
  - Tampilan feedback notifikasi menjadi seragam, modern, dan sangat elegan dengan estetika *dark glassmorphism* (`backdrop-blur-md`).
  - Penempatan **Top-Center** menyelesaikan masalah blindspot user pada notifikasi pojok kanan bawah.
  - Semua pesan AJAX dan flash session Laravel (`success`, `error`, `warning`, `info`) terpusat dalam satu siklus hidup komponen dengan dukungan auto-dismiss dan pause on hover.
  - Lolos pengujian sintaks Blade compiler `php artisan view:cache` & `php artisan view:clear`.

### 7. Eksekusi Target 7 (Komponen Status Badge Cerdas - x-status-badge)
* **Komponen Baru**:
  - `resources/views/components/status-badge.blade.php` (`<x-status-badge />`): Komponen cerdas serbaguna yang mengenkapsulasi seluruh aturan pewarnaan, border, dan label badge di apotek (pembayaran, penerimaan, stok, kadaluarsa, resep, aktif, role, member, promo).
* **Perubahan yang Diterapkan**:
  - Merefaktor 12 view template:
    1. `resources/views/penerimaan/index.blade.php`: Status penerimaan (Lengkap / Belum Lengkap) & status pembayaran (Lunas / Belum Lunas).
    2. `resources/views/penerimaan/show.blade.php`: Status header penerimaan, status pembayaran, dan status tagihan faktur.
    3. `resources/views/penerimaan/payment.blade.php`: Status penerimaan & status pembayaran faktur.
    4. `resources/views/penerimaan/susulan.blade.php`: Status penerimaan & pembayaran penerimaan susulan.
    5. `resources/views/barang/index.blade.php`: Status aktif/nonaktif produk obat.
    6. `resources/views/user/index.blade.php`: Role akun (superadmin, admin, apoteker, kasir, gudang) dan status aktif user.
    7. `resources/views/custom_discount/index.blade.php`: Status promo diskon (Aktif, Belum Mulai, Berakhir, Non-aktif) tanpa logika if-else manual.
    8. `resources/views/laporan/stok.blade.php`: Status kadaluarsa batch obat (Normal, < 3 Bulan, < 1 Bulan, Kadaluarsa) dan status sisa stok (Aman, Menipis, Habis, Minus).
    9. `resources/views/penjualan/index.blade.php`: Status membership pelanggan transaksi.
    10. `resources/views/laporan/diskon.blade.php`: Jenis penggunaan diskon member vs promo.
    11. `resources/views/pelanggan/index.blade.php`: Status tipe member dan status piutang.
    12. `resources/views/pelanggan/show.blade.php`: Menghapus 57 baris style inline manual dan menggantikannya dengan 1 baris `<x-status-badge>`.
* **Keuntungan**:
  - Menghapus lebih dari 120 baris kode boilerplate `if-else` dan duplikasi `match(...)` di belasan template tampilan.
  - Memastikan warna badge status di seluruh aplikasi 100% seragam dan konsisten.
  - Pengujian template berhasil dan lolos validasi `php artisan view:cache` & `php artisan view:clear`.

### 8. Eksekusi Target 8 (Komponen Pagination Wrapper - x-pagination)
* **Komponen Baru**:
  - `resources/views/components/pagination.blade.php` (`<x-pagination />`): Wrapper cerdas untuk tautan paginasi Laravel (`LengthAwarePaginator`) yang otomatis membungkus link dengan container `.mt-4.pagination-container` dan template standar `vendor.pagination.apotek`. Memiliki proteksi kondisional otomatis `@if ($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())` sehingga kontainer div kosong dan margin berlebih tidak akan dirender jika data hanya 1 halaman.
* **Perubahan yang Diterapkan**:
  - Merefaktor 12 view template index yang menggunakan paginasi:
    1. `resources/views/kategori/index.blade.php`: `<x-pagination :paginator="$kategoris" />`
    2. `resources/views/barang/index.blade.php`: `<x-pagination :paginator="$barangs" />`
    3. `resources/views/satuan/index.blade.php`: `<x-pagination :paginator="$satuans" />`
    4. `resources/views/supplier/index.blade.php`: `<x-pagination :paginator="$suppliers" />`
    5. `resources/views/pabrik/index.blade.php`: `<x-pagination :paginator="$pabriks" />`
    6. `resources/views/pelanggan/index.blade.php`: `<x-pagination :paginator="$pelanggans" />`
    7. `resources/views/user/index.blade.php`: `<x-pagination :paginator="$users" />`
    8. `resources/views/penjualan/index.blade.php`: `<x-pagination :paginator="$penjualans" />`
    9. `resources/views/penerimaan/index.blade.php`: `<x-pagination :paginator="$penerimaans" />`
    10. `resources/views/rusak/index.blade.php`: `<x-pagination :paginator="$rusaks" />`
    11. `resources/views/custom_discount/index.blade.php`: `<x-pagination :paginator="$discounts" />`
    12. `resources/views/whatsapp-monitor/index.blade.php`: `<x-pagination :paginator="$logs" />`
* **Keuntungan**:
  - Menghilangkan duplikasi markup container pagination dan pemanggilan template view paginasi di 12 file.
  - Jika ada perubahan desain pagination di kemudian hari, cukup perbarui satu file komponen saja atau ganti prop `:view="..."`.
  - Tampilan rapi dan bersih saat data sedikit (tidak ada sisa container/margin pagination).
  - Lolos pengujian sintaks Blade compiler `php artisan view:cache` & `php artisan view:clear`.

### 9. Eksekusi Target 9 (Komponen Modal Konfirmasi Hapus Reusable - x-modal-confirm-delete)
* **Komponen Baru**:
  - `resources/views/components/modal-confirm-delete.blade.php` (`<x-modal-confirm-delete />`): Dialog modal konfirmasi penghapusan seragam berdesain modern (backdrop blur, kartu putih dengan border halus, icon tempat sampah merah dengan ring lembut, teks peringatan jelas, kotak sorotan nama item yang akan dihapus, tombol batal abu-abu, dan tombol eksekusi merah).
  - Dipasang secara global pada `resources/views/layouts/app.blade.php` sehingga otomatis aktif di semua modul aplikasi.
* **Perubahan & Integrasi yang Diterapkan**:
  - Mengintegrasikan komponen `<x-table-action>`: tombol hapus diubah dari form inline nested dengan popup JavaScript jadul `window.confirm()` menjadi tombol modern yang memanfaatkan event listener global `data-confirm-delete` dan prop `:delete-item`.
  - Mengirimkan `:delete-item` pada seluruh modul Master Data & Promo:
    1. `kategori/index.blade.php`: `:delete-item="$kategori->nama"`
    2. `barang/index.blade.php`: `:delete-item="$barang->nama"`
    3. `satuan/index.blade.php`: `:delete-item="$satuan->nama"`
    4. `supplier/index.blade.php`: `:delete-item="$supplier->nama"`
    5. `pabrik/index.blade.php`: `:delete-item="$pabrik->nama"`
    6. `user/index.blade.php`: `:delete-item="$user->name"`
    7. `custom_discount/index.blade.php`: `:delete-item="$discount->nama"`
  - Merefaktor form delete manual pada modul operasional & laporan:
    8. `pelanggan/index.blade.php`: Mengganti form delete manual dengan tombol `data-confirm-delete` & nama member.
    9. `penerimaan/index.blade.php`: Mengganti tombol submit tanpa konfirmasi dengan modal konfirmasi aman berisi nomor faktur penerimaan.
    10. `rusak/index.blade.php`: Mengganti form delete manual dengan modal konfirmasi berisi nama obat & nomor batch.
    11. `laporan/laba_rugi.blade.php`: Mengganti form delete manual beban operasional dengan modal konfirmasi berisi nama beban & nominal rupiah.
* **Keuntungan**:
  - Mengeliminasi dialog default browser (`window.confirm`) yang kaku, memblokir thread antarmuka, dan tidak konsisten antar sistem operasi.
  - Pengguna mendapatkan kejelasan visual (*visual reassurance*) mengenai item apa yang sedang dihapus sebelum menekan tombol konfirmasi.
  - Menghapus form tag nested yang tidak valid di dalam sel tabel (`<td>`).
  - Lolos pengujian sintaks Blade compiler `php artisan view:cache` & `php artisan view:clear`.

### 10. Eksekusi Target 10 (Komponen Header Halaman Standar - x-page-header)
* **Penyempurnaan Komponen**:
  - `resources/views/components/page-header.blade.php`: Ditingkatkan dengan `$attributes->merge(...)`, tipografi responsif `text-xl sm:text-2xl font-bold font-sans`, dan dukungan prop `:back-url` otomatis yang merender tombol panah kembali elegan (`<x-heroicon-o-arrow-left />`) sebelum judul.
* **Perubahan & Integrasi yang Diterapkan**:
  - Merefaktor 10 view yang sebelumnya masih memakai header manual `<h1>`:
    1. `resources/views/whatsapp-monitor/index.blade.php`: Mengganti div header manual dengan `<x-page-header>`.
    2. `resources/views/penerimaan/index.blade.php`: Header daftar penerimaan beserta tombol "Tambah Faktur Penerimaan Baru".
    3. `resources/views/penerimaan/create.blade.php`: Header form faktur baru dengan tombol kembali `:back-url="route('penerimaan.index')"`.
    4. `resources/views/rusak/index.blade.php`: Header daftar barang rusak beserta tombol aksi "Catat Barang Rusak".
    5. `resources/views/rusak/create.blade.php`: Header form barang rusak dengan tombol kembali `:back-url`.
    6. `resources/views/rusak/show.blade.php`: Header detail barang rusak dengan tombol kembali `:back-url`.
    7. `resources/views/pelanggan/show.blade.php`: Header detail profil member dengan tombol kembali `:back-url` dan tombol Edit Profil.
    8. `resources/views/penjualan/show.blade.php`: Header struk kasir dengan tombol kembali cerdas `:back-url="$backUrl"`, selector ukuran thermal (58mm/80mm), dan tombol print.
    9. `resources/views/laporan/stok.blade.php`: Header monitoring stok dengan tombol Ekspor Excel dan Cetak Laporan.
    10. `resources/views/laporan/rusak.blade.php`: Header laporan rusak dengan tombol Ekspor Excel dan Cetak Laporan.
    11. `resources/views/laporan/penjualan.blade.php`: Header laporan penjualan dengan tombol Ekspor Excel dan Cetak Laporan.
    12. `resources/views/laporan/penerimaan.blade.php`: Header laporan penerimaan dengan tombol Ekspor Excel dan Cetak Laporan.
    13. `resources/views/laporan/diskon.blade.php`: Header laporan diskon dengan tombol Ekspor Excel dan Cetak Laporan.
    14. `resources/views/laporan/laba_rugi.blade.php`: Header laporan laba rugi dengan tombol Ekspor Excel, Cetak, dan Filter Periode `<x-date-range-filter>`.
* **Keuntungan**:
  - 100% halaman aplikasi apotek kini memiliki hierarki judul (`h1`), deskripsi, dan tombol aksi seragam dan responsif.
  - Menghilangkan puluhan baris HTML boilerplate di modul laporan dan transaksi.
  - Lolos pengujian sintaks Blade compiler `php artisan view:cache` & `php artisan view:clear`.

### 11. Ringkasan Modul Sebelumnya yang Sudah Terintegrasi:
* **Master Data (Opsi 1)**: `kategori`, `barang`, `satuan`, `supplier`, `pabrik`, dan `pelanggan` sudah 100% menggunakan `<x-search-input>` dan `<x-form.*>`.
* **Laporan & Cetak Dokumen (Opsi 2)**: `laporan/penjualan`, `laporan/rusak`, `laporan/laba_rugi`, `laporan/stok`, `laporan/penerimaan`, `laporan/diskon`, dan `activity_log` sudah 100% menggunakan `<x-print-kop>` dan `<x-print-signature>` (memangkas >570 baris duplikat).
* **Target 1**: Executive KPI Cards dashboard menggunakan `<x-kpi-card>`.
* **Target 2**: Filter rentang tanggal preset dashboard dan laba rugi menggunakan `<x-date-range-filter>`.
* **Target 3**: Rollout directive mata uang `@rupiah(...)` pada seluruh modul kasir dan faktur penerimaan.
* **Target 4**: Rollout directive penanggalan Indonesia `@tanggalId` dan `@tanggalWaktuId` pada 20 view aplikasi.
* **Target 5**: Integrasi backend Eloquent Model Traits `GeneratesCode` & `Filterable`.
* **Target 6**: Komponen notifikasi reusable `<x-toast>` (Top-Center) & `<x-alert>`.
* **Target 7**: Komponen status badge cerdas `<x-status-badge>`.
* **Target 8**: Komponen pagination wrapper `<x-pagination>`.
* **Target 9**: Komponen modal konfirmasi hapus `<x-modal-confirm-delete>`.
* **Target 10**: Komponen header halaman standar `<x-page-header>`.





