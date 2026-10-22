# Status Proyek & Arsitektur POS Apotek Ku

## 1. Ringkasan Eksekutif & Status Terkini
Dokumen ini mencatat status teknis, kondisi arsitektur, rekam jejak optimasi, serta baseline performa historis aplikasi **POS Apotek Ku**. Dokumen ini disusun untuk mengawal diagnosis masalah regresi navigasi yang dialami setelah fase integrasi kolaborasi tim developer.

- **Branch Aktif**: `optimize/performance-optimization`
- **Status Git Saat Pemeriksaan**: Clean (`nothing to commit, working tree clean`)
- **Status Pengukuran Server**: **Selesai** (Query count identik 100% dengan Phase 14; backend/DB bersih dari regresi)
- **Status Pengukuran Browser Profiling**: **Belum Tersedia** (Automasi browser tidak terpasang; panduan manual DevTools disiapkan)
- **Status Audit Regresi**: **Tahap Investigasi Frontend / Dev Bundler** (Server & DB tereliminasi dari suspek)
- **Masalah Aktif Utama**: Navigasi menu antar halaman terasa lambat (sluggish/unresponsive) pada hampir seluruh halaman setelah penggabungan kerja multi-developer (diduga akibat full reload lifecycle JS dan Vite dev bundler).

---

## 2. Stack Teknologi Proyek

| Komponen | Spesifikasi / Paket | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.0 (PHP ^8.2) | Full-stack monolith dengan Blade templating |
| **Database** | MySQL / MariaDB (XAMPP environment) | Menggunakan Eloquent ORM & Query Builder |
| **Frontend Styling** | Tailwind CSS v4.0.0 (`@tailwindcss/vite`) | Utility-first styling via Vite plugin |
| **Asset Bundler** | Vite v7.0.7 (`laravel-vite-plugin` v2.0.0) | Bundling CSS & modul JavaScript mandiri per halaman |
| **UI Icons & Helpers** | Blade UI Kit (`blade-heroicons`, `blade-icons`) | Render SVG inline dari komponen Blade |
| **Fitur Pendukung** | OpenSpout 4.28 (Excel/CSV Export-Import), Captcha, QRCode (JS) | Pemrosesan streaming file & barcode/QR generator |
| **Proses Eksekusi Dev** | Concurrently | Menjalankan `php artisan serve`, queue worker, `pail`, dan `npm run dev` |

---

## 3. Rekapitulasi Optimasi & Riwayat Fase 1–14

Berdasarkan riwayat arsitektur dan dokumentasi optimasi POS Apotek Ku:

1. **Phase 1 – Query Elimination & N+1 Fixing**: Audit query relasi pada modul master data (kategori, satuan, rak, pabrik, supplier).
2. **Phase 2 – Eager Loading Optimization**: Implementasi `with()` dan `withCount()` pada katalog transaksi dan pelaporan.
3. **Phase 3 – Indexing & Database Schema**: Penambahan index pada kolom foreign key, kolom status aktif, dan rentang tanggal transaksi.
4. **Phase 4 – Pagination & Memory Limit Handling**: Pembatasan lazy collection dan standarisasi limit pagination pada data master dan invoice.
5. **Phase 5 – Caching Master Settings**: Implementasi `Cache::remember` untuk info apotek (6 jam) guna memangkas query berulang di setiap render layout shell.
6. **Phase 6 – Database Query Deduplication**: Menghilangkan subquery redundan pada penghitungan stok apotek dan batch kedaluwarsa obat.
7. **Phase 7 – View Partials & Modal Standardization**: Migrasi dari halaman create/edit terpisah ke modal terpusat (`<x-modal-form>`) untuk master data sederhana sesuai panduan `assets.md`.
8. **Phase 8 – UI Component Consolidation**: Konsolidasi komponen Blade (`<x-page-header>`, `<x-card-filter>`, `<x-table-action>`, `<x-empty-state>`).
9. **Phase 9 – Streaming Export & Import**: Penggunaan OpenSpout untuk memproses data ribuan baris tanpa lonjakan konsumsi memori server.
10. **Phase 10 – Granular Role-Based Access Control (RBAC)**: Optimasi pengecekan izin akses superadmin/kasir/apoteker dengan query matrix permissions yang efisien.
11. **Phase 11 – Transaction Processing Batching**: Reduksi round-trip query saat checkout kasir dan penerimaan pesanan.
12. **Phase 12 – Asset Pipeline Clean-up**: Pembersihan unused CSS dan eliminasi inline scripting yang tidak terstruktur.
13. **Phase 13A – JavaScript & Asset Extraction**:
    - Ekstraksi script Blade monolitik dari view layout dan view halaman ke modul JavaScript terpisah:
      - `resources/js/shared/layout.js` (interaksi shell layout, sidebar accordion, idle auto-logout, real-time clock).
      - `resources/js/pages/barang-index.js`
      - `resources/js/pages/penerimaan-index.js`
      - `resources/js/pages/penjualan-create.js`
    - Konfigurasi multi-entry point pada `vite.config.js`.
    - **Status saat ini**: Asset extraction Phase 13A dipertahankan.
14. **Phase 13B / Hotfix – Rollback Hotwire Turbo**:
    - Eksperimen SPA-like navigation menggunakan Hotwire Turbo sempat dievaluasi, namun **telah di-rollback** demi kestabilan lifecycle event script tradisional, event listener, dan re-inisialisasi modal form.
15. **Phase 14 – Baseline Stabil Akhir**:
    - Titik referensi performa stabil backend server & query sebelum penggabungan besar kerja tim multi-developer.

---

## 4. Baseline Historis Phase 14

> [!IMPORTANT]
> **Catatan Validitas**: Angka di bawah ini adalah **baseline historis dari Phase 14**, BUKAN hasil benchmark kondisi sistem terbaru saat ini. Angka ini digunakan sebagai tolok ukur referensi komparatif saat menjalankan audit performa.

| Modul / Rute Halaman | Query Count | Total Query Time | Total Server Time | Ukuran Payload HTML |
| :--- | :---: | :---: | :---: | :---: |
| **Barang Index** (`barang.index`) | 11 queries | 7.73 ms | 155.89 ms | 160.42 KB |
| **Penerimaan Index** (`penerimaan.index`) | 56 queries | 30.74 ms | 157.27 ms | 114.57 KB |
| **Penjualan Kasir** (`penjualan.create`) | 4 queries | 3.93 ms | 94.75 ms | 65.49 KB |

---

## 5. Ringkasan Masalah Aktif & Observasi Awal

- **Gejala**: Navigasi menu sidebar dan perpindahan antar rute terasa lambat, terdapat jeda (lag/freeze) sebelum konten baru ter-render.
- **Dampak**: Menurunkan responsivitas kerja kasir dan staf apotek saat berpindah menu.
- **Fokus Tindakan**: Menyiapkan lembar audit komprehensif (`docs/PERFORMANCE_REGRESSION_AUDIT.md`) untuk mengumpulkan data riil server timing dan browser profiling sebelum memutuskan tindakan refactoring atau tuning kode.
    