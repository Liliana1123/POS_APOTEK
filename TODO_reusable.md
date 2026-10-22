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
- [x] **Lokasi**: `app/Providers/AppServiceProvider.php` (Sudah didaftarkan & diuji)
- [x] **Deskripsi**: Menyeragamkan parsing tanggal Carbon Indonesia (`d M Y` dan `d M Y, H:i`).
- **Contoh Penggunaan**:
  ```blade
  @tanggalId($penjualan->created_at)
  @tanggalWaktuId($activity->created_at)
  ```

---

## Fase 4: Eloquent Model Traits (Backend Logic)

### 4.1 Trait Generator Kode Otomatis (`GeneratesCode`)
- [x] **Lokasi**: `app/Traits/GeneratesCode.php` (Sudah dibuat)
- [x] **Deskripsi**: Logika terpusat untuk membuat kode berurut apotek dengan prefix (contoh: `P-0001`, `TRX-20261022-0001`).
- **Contoh Penggunaan pada Model**:
  ```php
  use App\Traits\GeneratesCode;

  class Barang extends Model {
      use GeneratesCode;
      
      public static function boot() {
          parent::boot();
          static::creating(function ($model) {
              $model->kode_apotek = self::generateSequentialCode('P', 'kode_apotek', 4);
          });
      }
  }
  ```

### 4.2 Trait Filter Query Eloquent (`Filterable`)
- [x] **Lokasi**: `app/Traits/Filterable.php` (Sudah dibuat)
- [x] **Deskripsi**: Query scopes yang sering dipakai controller:
  - `scopeSearch($query, $columns, $term)`
  - `scopeDateRange($query, $column, $from, $to)`
  - `scopeFilterStatus($query, $column, $status)`

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

### 2. Ringkasan Modul Sebelumnya yang Sudah Terintegrasi:
* **Master Data (Opsi 1)**: `kategori`, `barang`, `satuan`, `supplier`, `pabrik`, dan `pelanggan` sudah 100% menggunakan `<x-search-input>` dan `<x-form.*>`.
* **Laporan & Cetak Dokumen (Opsi 2)**: `laporan/penjualan`, `laporan/rusak`, `laporan/laba_rugi`, `laporan/stok`, `laporan/penerimaan`, `laporan/diskon`, dan `activity_log` sudah 100% menggunakan `<x-print-kop>` dan `<x-print-signature>` (memangkas >570 baris duplikat).
