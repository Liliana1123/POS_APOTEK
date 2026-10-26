# Roadmap & Rencana Konsolidasi Komponen Modal (modal-consolidation-roadmap.md)

Dokumen ini berisi arsitektur standardisasi, audit inventaris file, dan panduan langkah demi langkah untuk meleburkan (*consolidate*) komponen-komponen modal yang redundan/terlalu banyak di dalam proyek **POS Apotek**, menuju arsitektur modal yang ramping, terpadu, dan berstandar DRY (*Don't Repeat Yourself*).

---

## 📊 1. Ringkasan Eksekutif & Masalah (Executive Summary)

### Masalah Saat Ini:
1. **Proliferasi File Modal:** Terdapat total **6 komponen modal** di folder global `resources/views/components/` dan lebih dari **8 file modal parsial** yang tersebar di sub-folder modul, menciptakan fragmentasi kode yang membingungkan pengembang.
2. **File Proxy Redundan / Sisa Transisi:** File `modal-confirm-delete.blade.php` dan `modal-confirm-action.blade.php` hanya berisi 2 baris proxy pemanggil ke `x-modal-confirm`. File-file ini sudah tidak lagi dipanggil di view mana pun karena seluruh aplikasi telah beralih ke singleton `<x-modal-confirm />` global.
3. **Fragmentasi Modal Modul Tertentu:** Modul seperti `pelanggan` memiliki hingga 5 file modal terpisah (`modal-detail`, `modal-card`, `modal-edit`, `modal-piutang`, `modal-whatsapp-piutang`) yang dapat disederhanakan.

### Target Hasil Akhir:
* **Hanya 3 Komponen Modal Inti Global:** Menetapkan standar baku hierarki modal aplikasi.
* **Eliminasi File Redundan:** Menghapus file wrapper proxy yang tidak lagi diperlukan.
* **Pengujian 100% Valid:** Memastikan seluruh unit test komponen dan rendering blade tetap lulus tanpa regresi.

---

## 🏛️ 2. Standar Arsitektur: 3 Komponen Modal Inti (Core 3 Modals)

Seluruh kebutuhan antarmuka popup di aplikasi POS Apotek distandarisasi ke dalam **3 jenis modal inti**:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                   ARSITEKTUR MODAL TERPADU POS APOTEK                       │
├─────────────────────┬───────────────────────────┬───────────────────────────┤
│ 1. <x-modal>        │ 2. <x-modal-form>         │ 3. <x-modal-confirm>      │
├─────────────────────┼───────────────────────────┼───────────────────────────┤
│ • Container Umum    │ • Engine Form CRUD AJAX   │ • Dialog Konfirmasi Aksi  │
│ • Konten Bebas      │ • Create / Edit Switcher  │ • Singleton di app.blade  │
│ • Slot: Header,     │ • Inline Error Validation │ • Icon & Tema Dinamis     │
│   Body, Footer      │ • Auto Progress Bar AJAX  │ • window.confirmAction()  │
│ • Digunakan di:     │ • Digunakan di:           │   window.confirmDelete()  │
│   - Detail / Info   │   - 8 Master Data         │ • Digunakan di:           │
│   - Preview Faktur  │     (Barang, Satuan,      │   - Seluruh tabel & aksi  │
│   - Form Khusus     │      Kategori, Supplier,  │     kritis (Hapus, Void,  │
│   - Import Excel    │      User, Member, dll.)  │     Bayar, Status, dll.)  │
└─────────────────────┴───────────────────────────┴───────────────────────────┘
```

---

## 📋 3. Matriks Inventaris Komponen Modal Global (`components/`)

| File Komponen | Status Saat Ini | Usulan Aksi | Alasan & Justifikasi |
|---|---|---|---|
| [modal.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal.blade.php) | 🟢 **Aktif (Core)** | **Pertahankan** | Pondasi container generik untuk view detail, preview nota, dan custom modal. |
| [modal-form.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-form.blade.php) | 🟢 **Aktif (Core)** | **Pertahankan** | Mesin modal form AJAX yang sangat optimal untuk 8 entitas master data. |
| [modal-confirm.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-confirm.blade.php) | 🟢 **Aktif (Core)** | **Pertahankan** | Komponen singleton global konfirmasi aksi & delete dengan integrasi JavaScript interaktif. |
| [modal-confirm-delete.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-confirm-delete.blade.php) | 🔴 **Redundan (Proxy)** | **Leburkan / Hapus** | Hanya wrapper 2 baris ke `<x-modal-confirm>`. Sudah 0 pemanggil di seluruh views aplikasi. |
| [modal-confirm-action.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-confirm-action.blade.php) | 🔴 **Redundan (Proxy)** | **Leburkan / Hapus** | Hanya wrapper 2 baris ke `<x-modal-confirm>`. Sudah 0 pemanggil di seluruh views aplikasi. |
| [keyboard-shortcut-modal.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/keyboard-shortcut-modal.blade.php) | 🟢 **Aktif (Widget)** | **Pertahankan** | Modal khusus panduan keyboard shortcut (F1-F12, Shift+?, dll). |

---

## 📁 4. Matriks Inventaris Modal Parsial di Modul Halaman

| Modul & Path | File Modal | Peran / Fungsi | Analisis & Rencana Konsolidasi |
|---|---|---|---|
| **Penerimaan**<br>`penerimaan/partials/` | `modal-tambah.blade.php` | Form Tambah Faktur & Item | 🟢 Sudah menggunakan `<x-modal>`, sangat modular dan rapi pasca pemisahan DC-09. |
| **Barang**<br>`barang/partials/` | `modal-import.blade.php` | Upload Excel Data Barang | 🟢 Berfungsi optimal dengan dropzone & `<x-modal>`. |
| **Barang**<br>`barang/partials/` | `modal-detail.blade.php` | Tampilan Informasi Obat | 🟢 Berfungsi optimal menggunakan `<x-modal>`. |
| **Pelanggan**<br>`pelanggan/partials/` | `modal-detail.blade.php` | Informasi Profil Member | 🟡 Dapat disatukan dengan `modal-card.blade.php`. |
| **Pelanggan**<br>`pelanggan/partials/` | `modal-card.blade.php` | Kartu Member Virtual | 🟡 Menjadi bagian/tab terpadu dari detail profil member. |
| **Pelanggan**<br>`pelanggan/partials/` | `modal-edit.blade.php` | Form Edit Member | 🟢 Menggunakan form AJAX CRUD. |
| **Pelanggan**<br>`pelanggan/partials/` | `modal-piutang.blade.php` | Riwayat Piutang Member | 🟢 Menampilkan tabel riwayat invoice dan saldo piutang. |
| **Pelanggan**<br>`pelanggan/partials/` | `modal-whatsapp-piutang.blade.php` | Engine Penagihan WhatsApp | 🟢 Terintegrasi erat dengan WhatsApp CRM Gateway (DC-10). |

---

## 🚀 5. Rencana Tahapan Eksekusi (Action Plan)

### Fase 1: Peleburan Komponen Global Redundan (Prioritas Utama)
- [ ] **Langkah 1.1:** Hapus file proxy [modal-confirm-delete.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-confirm-delete.blade.php).
- [ ] **Langkah 1.2:** Hapus file proxy [modal-confirm-action.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/components/modal-confirm-action.blade.php).
- [ ] **Langkah 1.3:** Perbarui pengujian di [tests/Unit/ReusableComponentsTest.php](file:///c:/xampp/htdocs/pos-apotek/tests/Unit/ReusableComponentsTest.php) dari semula menguji `x-modal-confirm-delete` menjadi langsung menguji komponen utama `<x-modal-confirm>`.
- [ ] **Langkah 1.4:** Jalankan `php artisan test --filter=ReusableComponentsTest` untuk memastikan seluruh 11 pengujian tetap lulus 100%.

### Fase 2: Konsolidasi Parsial Modul Pelanggan (Opsional / Iteratif)
- [ ] **Langkah 2.1:** Analisis integrasi `modal-card.blade.php` ke dalam `modal-detail.blade.php` dengan tata letak visual tab (Profil Member & Kartu Digital).
- [ ] **Langkah 2.2:** Verifikasi event JavaScript pemanggil modal kartu agar tetap merespons tombol preview kartu member di tabel data.

### Fase 3: Dokumentasi & Pembaruan Deadcode Tracker
- [ ] **Langkah 3.1:** Catat penghapusan file proxy ke dalam [deadcode-removal.md](file:///c:/xampp/htdocs/pos-apotek/deadcode-removal.md) sebagai item terselesaikan (DC-11).
- [ ] **Langkah 3.2:** Commit perubahan dan push ke branch `risang-fitur-data-master`.

---

## 🛡️ 6. Jaminan Kualitas & Verifikasi (Quality Assurance)

Setiap tahapan peleburan modal wajib melalui protokol pengujian berlapis:
1. **Blade Syntax & Render Check:** Menjalankan script validasi `test_render_views.php` untuk memastikan seluruh view yang merender layout tidak mengalami error `ViewNotFoundException` atau `ComponentNotFoundException`.
2. **PHPUnit Automated Test:** Menjalankan `php artisan test --filter=ReusableComponentsTest`.
3. **Audit Bebas Simbol Keyboard:** Memastikan tidak ada karakter emoji atau teks keyboard `-`, `+`, `>`, `<` yang digunakan sebagai pengganti ikon di modal.
