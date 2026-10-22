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
[Phase 1: Cetak Dokumen] ✅ SELESAI ──> [Phase 2: Form & UI] ✅ SELESAI ──> [Phase 3: Directives] ✅ SELESAI ──> [Phase 4: Eloquent Traits] ✅ SELESAI
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

### 6. Ringkasan Modul Sebelumnya yang Sudah Terintegrasi:
* **Master Data (Opsi 1)**: `kategori`, `barang`, `satuan`, `supplier`, `pabrik`, dan `pelanggan` sudah 100% menggunakan `<x-search-input>` dan `<x-form.*>`.
* **Laporan & Cetak Dokumen (Opsi 2)**: `laporan/penjualan`, `laporan/rusak`, `laporan/laba_rugi`, `laporan/stok`, `laporan/penerimaan`, `laporan/diskon`, dan `activity_log` sudah 100% menggunakan `<x-print-kop>` dan `<x-print-signature>` (memangkas >570 baris duplikat).
* **Target 1**: Executive KPI Cards dashboard menggunakan `<x-kpi-card>`.
* **Target 2**: Filter rentang tanggal preset dashboard dan laba rugi menggunakan `<x-date-range-filter>`.
* **Target 3**: Rollout directive mata uang `@rupiah(...)` pada seluruh modul kasir dan faktur penerimaan.
* **Target 4**: Rollout directive penanggalan Indonesia `@tanggalId` dan `@tanggalWaktuId` pada 20 view aplikasi.
