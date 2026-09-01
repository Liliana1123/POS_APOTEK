# Software Requirements Specification (SRS)
# Aplikasi Point of Sale (POS) Apotek

**Versi Dokumen:** 1.0
**Tanggal:** 26 Agustus 2026
**Status:** Draft

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini menjelaskan kebutuhan fungsional dan non-fungsional dari **Aplikasi POS Apotek**, sebuah sistem yang digunakan untuk mengelola operasional transaksi harian apotek: penerimaan barang dari supplier, penjualan barang ke pelanggan, pencatatan barang rusak/kadaluarsa, serta pelaporan stok dan keuangan. Dokumen ini menjadi acuan bagi tim pengembang, penguji (QA), dan pemilik produk (product owner) selama proses pengembangan.

### 1.2 Ruang Lingkup
Aplikasi mencakup 7 modul utama:

1. **Modul Login** — autentikasi pengguna & manajemen data user.
2. **Modul Data Dasar** — master data pendukung: info apotek, kategori barang, supplier, pabrik, satuan.
3. **Modul Pelanggan** — master data pelanggan.
4. **Modul Penerimaan** — pencatatan faktur barang masuk dari supplier beserta detail barang per batch.
5. **Modul Penjualan** — pencatatan faktur transaksi penjualan ke pelanggan beserta detail barang terjual.
6. **Modul Barang Rusak** — pencatatan barang rusak/kadaluarsa yang mengurangi stok tersedia.
7. **Modul Laporan** — laporan stok, penerimaan, penjualan, barang rusak, dan laba-rugi/pendapatan.

Aplikasi **tidak** mencakup (di luar ruang lingkup versi ini): resep dokter/e-prescription, integrasi BPJS/asuransi, multi-cabang/multi-gudang, pembelian kredit ke banyak termin (hutang bertahap), dan integrasi pembayaran digital (payment gateway). Item-item ini dapat menjadi pengembangan lanjutan (lihat Bagian 11).

### 1.3 Definisi, Akronim, dan Singkatan

| Istilah | Keterangan |
|---|---|
| POS | Point of Sale, sistem kasir/transaksi penjualan |
| Faktur | Dokumen bukti transaksi (penerimaan atau penjualan) |
| Batch | Satu baris kedatangan barang pada satu faktur penerimaan, dengan nomor batch & tanggal kadaluarsa spesifik |
| No. Batch | Nomor identifikasi produksi dari pabrik untuk melacak asal & masa berlaku obat |
| Lunas | Status pembayaran faktur (0 = belum lunas/hutang atau piutang, 1 = lunas) |
| FEFO | *First Expired, First Out* — strategi pengeluaran stok berdasarkan barang yang paling dekat kadaluarsa |
| SRS | Software Requirements Specification |

### 1.4 Referensi
- Kerangka modul dan skema tabel inti (`penerimaan`, `detail_penerimaan`, `penjualan`, `detail_penjualan`, `rusak`) sebagaimana ditentukan oleh pemilik produk.
- Pola penulisan dokumentasi mengacu pada dokumen referensi internal *Dokumentasi Aplikasi SPM Kesehatan* (struktur bab, gaya penulisan skema data, matriks peran, dan diagram alur kerja).

---

## 2. Deskripsi Umum Sistem

### 2.1 Perspektif Produk
Aplikasi POS Apotek adalah aplikasi **berdiri sendiri (standalone)** berbasis web, digunakan oleh satu apotek (single-tenant) untuk mengelola siklus: **barang masuk (penerimaan) → stok → barang keluar (penjualan/rusak) → laporan**. Setiap detail barang yang diterima (`detail_penerimaan`) menjadi unit stok yang dapat dijual atau dinyatakan rusak — bukan agregat stok per nama obat, melainkan **stok per batch** (mendukung pelacakan No. Batch dan tanggal kadaluarsa per kedatangan).

### 2.2 Ringkasan Fungsi Produk
| Modul | Fungsi Utama |
|---|---|
| Login | Autentikasi pengguna, manajemen akun pengguna (CRUD user & role) |
| Data Dasar | Kelola profil apotek, kategori barang, supplier, pabrik, satuan |
| Pelanggan | Kelola data pelanggan (umum & member) |
| Penerimaan | Catat faktur barang diterima dari supplier beserta detail per batch, menambah stok |
| Penjualan | Catat faktur transaksi penjualan ke pelanggan beserta detail barang terjual, mengurangi stok |
| Barang Rusak | Catat barang rusak/kadaluarsa yang mengurangi stok dari suatu batch |
| Laporan | Menyajikan laporan stok, penerimaan, penjualan, barang rusak, laba-rugi/pendapatan |

### 2.3 Karakteristik Pengguna

| Peran | Deskripsi |
|---|---|
| **Admin** | Pemilik/pengelola apotek. Akses penuh ke seluruh modul termasuk data dasar, manajemen user, dan seluruh laporan. |
| **Apoteker/Kasir** | Petugas operasional harian. Akses ke modul Penjualan, Penerimaan, Barang Rusak, dan Pelanggan; akses laporan bersifat terbatas (lihat Bagian 4). |

> **Catatan asumsi:** Kerangka modul yang diberikan mencantumkan "Data User" tanpa merinci daftar peran (role). Dokumen ini mengasumsikan dua peran minimal (**Admin** dan **Kasir**) yang lazim pada aplikasi POS apotek skala kecil-menengah. Jumlah dan nama peran dapat disesuaikan pada tahap desain teknis lanjutan (lihat matriks hak akses Bagian 4.2).

### 2.4 Batasan (Constraints)
- Aplikasi berjalan sebagai **single-tenant** (satu instansiasi = satu apotek); tidak ada isolasi multi-apotek pada versi ini.
- Transaksi penjualan mengasumsikan **stok tersedia secara real-time** dari `detail_penerimaan.stok`; tidak ada dukungan stok negatif.
- Perhitungan laba dihitung dari selisih `harga_jual` dan `harga_beli` pada level `detail_penerimaan` yang terjual, dikurangi diskon — bukan metode akuntansi biaya rata-rata (average costing) atau FIFO nilai moneter.

---

## 3. Arsitektur Aplikasi

### 3.1 Stack Teknologi

| Layer | Teknologi |
|---|---|
| Bahasa Pemrograman | PHP ^8.3 |
| Backend Framework | Laravel 13 |
| Basis Data | MySQL |
| Arsitektur | MVC (Model-View-Controller), monolithic web application |

### 3.2 Gambaran Alur Data Utama

```
[Supplier] → Modul Penerimaan → detail_penerimaan.stok (+)
                                        ↓
                          [Stok Batch Tersedia untuk Dijual]
                                        ↓
        ┌───────────────────────────────┴───────────────────────────────┐
        ↓                                                                ↓
Modul Penjualan → detail_penerimaan.stok (−)          Modul Barang Rusak → detail_penerimaan.stok (−)
        ↓                                                                ↓
                              Modul Laporan (stok, penerimaan, penjualan, rusak, laba-rugi)
```

Prinsip inti desain data: **stok bukan kolom agregat terpisah**, melainkan melekat pada `detail_penerimaan.stok` per batch. Setiap transaksi penjualan (`detail_penjualan`) dan pencatatan barang rusak (`rusak`) mengacu langsung ke `detail_penerimaan_id`, sehingga pengurangan stok selalu tertelusur ke batch asal (mendukung pelacakan No. Batch & FEFO).

---

## 4. Manajemen Peran & Hak Akses

### 4.1 Data User
Modul Data User mengelola akun pengguna sistem dengan atribut minimal: nama, username/email, password (terenkripsi), peran (role), dan status aktif. Hanya **Admin** yang dapat membuat, mengubah, menonaktifkan, atau menghapus akun user lain.

### 4.2 Matriks Hak Akses

| Fungsi | Admin | Kasir |
|---|:---:|:---:|
| Login aplikasi | ✅ | ✅ |
| Kelola data user | ✅ | ❌ |
| Kelola data dasar (info apotek, kategori, supplier, pabrik, satuan) | ✅ | ❌ (read-only) |
| Kelola data pelanggan | ✅ | ✅ |
| Input faktur penerimaan barang | ✅ | ✅ *(opsional, sesuai kebijakan)* |
| Input transaksi penjualan | ✅ | ✅ |
| Input barang rusak | ✅ | ✅ |
| Lihat laporan stok & penerimaan | ✅ | ✅ *(read-only)* |
| Lihat laporan penjualan & barang rusak | ✅ | ✅ *(scoped ke transaksi milik sendiri, opsional)* |
| Lihat laporan laba-rugi/pendapatan | ✅ | ❌ |

> Batasan akses Kasir terhadap laporan laba-rugi bersifat rekomendasi umum praktik apotek (informasi margin/harga beli bersifat sensitif); dapat dikonfigurasi ulang sesuai kebijakan bisnis pemilik apotek.

---

## 5. Struktur Data (Skema Basis Data)

> Skema pada sub-bab 5.6 – 5.10 mengikuti struktur yang telah ditentukan oleh pemilik produk. Skema pada sub-bab 5.1 – 5.5 (Data Dasar) dirancang untuk mendukung relasi FK yang direferensikan oleh `detail_penerimaan` dan `penjualan`.

### 5.1 Users *(Modul Login)*
- `id`, `nama`, `username` (unique), `email` (unique, nullable), `password`, `role` (`admin` / `kasir`), `aktif` (boolean, default `1`), timestamps.

### 5.2 Info Apotek *(Modul Data Dasar — data tunggal/singleton)*
- `id`, `nama_apotek`, `alamat`, `telepon`, `email`, `no_izin_sia` (Surat Izin Apotek), `nama_apoteker_penanggung_jawab`, `no_sipa`, `logo`, timestamps.

### 5.3 Kategori Barang *(Modul Data Dasar)*
- `id`, `nama_kategori`, `keterangan` (nullable), timestamps.

### 5.4 Supplier *(Modul Data Dasar)*
- `id`, `nama_supplier`, `alamat`, `telepon`, `email` (nullable), `contact_person` (nullable), timestamps.

### 5.5 Pabrik *(Modul Data Dasar)*
- `id`, `nama_pabrik`, `alamat` (nullable), `telepon` (nullable), timestamps.

### 5.5b Satuan *(Modul Data Dasar)*
- `id`, `nama_satuan` (mis. Box, Strip, Tablet, Botol, Tube), timestamps.

### 5.5c Pelanggan *(Modul Pelanggan)*
- `id`, `nama_pelanggan`, `alamat` (nullable), `telepon` (nullable), timestamps.
- Mendukung pelanggan umum (walk-in) melalui satu baris default "Umum" agar `penjualan.pelanggan_id` selalu terisi.

### 5.6 Penerimaan *(Modul Penerimaan — faktur barang diterima)*
- `id`
- `user_id` — FK ke `users`, mencatat petugas yang menginput faktur
- `tanggal` — tanggal faktur diterima
- `no_faktur` — nomor faktur dari supplier (unique per supplier disarankan)
- `supplier_id` — FK ke `supplier`
- `lunas` — boolean (0 = belum lunas/hutang ke supplier, 1 = lunas)

### 5.7 Detail Penerimaan *(detail barang per batch dalam satu faktur)*
- `id`
- `penerimaan_id` — FK ke `penerimaan`
- `pabrik_id` — FK ke `pabrik`
- `kategori_id` — FK ke `kategori_barang`
- `satuan_id` — FK ke `satuan`
- `barcode` — kode barang/scan
- `no_batch` — nomor batch produksi
- `harga_beli`
- `harga_jual`
- `expired_date` — tanggal kadaluarsa batch
- `jumlah` — jumlah barang diterima pada batch ini
- `stok` — jumlah stok tersisa yang tersedia untuk transaksi (awal = `jumlah`, berkurang saat terjual/rusak)
- `aktif` — boolean, menandai batch masih dapat dijual (mis. dinonaktifkan otomatis saat `stok = 0` atau kadaluarsa)

> **Catatan penting:** skema di atas tidak menyertakan kolom nama barang secara eksplisit. Karena setiap batch berpotensi berasal dari barang yang berbeda, direkomendasikan menambahkan kolom **`nama_barang`** pada `detail_penerimaan` (bukan tabel master terpisah) agar setiap batch tetap dapat diidentifikasi secara mandiri tanpa mengubah struktur relasi yang telah ditentukan. Ini adalah rekomendasi tambahan, bukan bagian dari skema asli yang diberikan — mohon konfirmasi ke pemilik produk sebelum implementasi.

### 5.8 Penjualan *(Modul Penjualan — faktur transaksi penjualan)*
- `id`
- `user_id` — FK ke `users`, kasir yang melayani transaksi
- `pelanggan_id` — FK ke `pelanggan`
- `tanggal`
- `no_faktur` — nomor faktur penjualan (auto-generate, unique)
- `lunas` — boolean (0 = belum lunas/piutang pelanggan, 1 = lunas)
- `total` — kolom tersimpan (stored), hasil agregasi `SUM(detail_penjualan.subtotal)`, diperbarui setiap kali detail ditambah/diubah/dihapus

### 5.9 Detail Penjualan *(barang yang terjual dalam satu faktur penjualan)*
- `id`
- `penjualan_id` — FK ke `penjualan`
- `detail_penerimaan_id` — FK ke `detail_penerimaan`, menautkan barang terjual ke batch stok asal
- `harga_jual` — harga jual saat transaksi (disalin dari `detail_penerimaan.harga_jual` saat transaksi, agar histori harga tidak berubah bila harga master diubah kemudian)
- `jumlah`
- `diskon` — nilai atau persentase diskon per baris
- `subtotal` — `(harga_jual × jumlah) − diskon`

### 5.10 Rusak *(Modul Barang Rusak)*
- `id`
- `detail_penerimaan_id` — FK ke `detail_penerimaan`, batch mana yang rusak
- `tanggal`
- `jumlah` — jumlah barang rusak yang dicatat
- `keterangan` — alasan (rusak fisik, kadaluarsa, dsb.)

### 5.11 Diagram Relasi (Ringkas)

```
users 1—N penerimaan
users 1—N penjualan
supplier 1—N penerimaan
pelanggan 1—N penjualan

penerimaan 1—N detail_penerimaan
  detail_penerimaan N—1 pabrik
  detail_penerimaan N—1 kategori_barang
  detail_penerimaan N—1 satuan

penjualan 1—N detail_penjualan
  detail_penjualan N—1 detail_penerimaan

detail_penerimaan 1—N rusak
```

---

## 6. Kebutuhan Fungsional

### 6.1 Modul Login

| ID | Kebutuhan |
|---|---|
| FR-01 | Sistem harus menyediakan halaman login dengan validasi username/email dan password. |
| FR-02 | Sistem harus menolak login untuk akun yang berstatus tidak aktif. |
| FR-03 | Sistem harus menyediakan fungsi logout. |
| FR-04 | Admin dapat menambah, mengubah, menonaktifkan, dan menghapus data user. |
| FR-05 | Sistem harus mengenkripsi password (hashing) dan tidak pernah menampilkan password dalam bentuk plain text. |

### 6.2 Modul Data Dasar

| ID | Kebutuhan |
|---|---|
| FR-06 | Admin dapat mengelola (CRUD) Info Apotek — data ini bersifat tunggal (satu baris data per instalasi). |
| FR-07 | Admin dapat mengelola (CRUD) Kategori Barang. |
| FR-08 | Admin dapat mengelola (CRUD) Supplier. |
| FR-09 | Admin dapat mengelola (CRUD) Pabrik. |
| FR-10 | Admin dapat mengelola (CRUD) Satuan. |
| FR-11 | Data dasar yang sudah direferensikan oleh transaksi (mis. kategori yang dipakai `detail_penerimaan`) tidak boleh dihapus permanen; sistem menerapkan soft-delete atau validasi larangan hapus jika masih direferensikan. |

### 6.3 Modul Pelanggan

| ID | Kebutuhan |
|---|---|
| FR-12 | Sistem menyediakan CRUD data pelanggan (nama, alamat, telepon). |
| FR-13 | Sistem menyediakan satu data pelanggan default "Umum" untuk transaksi tanpa identitas pelanggan spesifik. |

### 6.4 Modul Penerimaan

| ID | Kebutuhan |
|---|---|
| FR-14 | Kasir/Admin dapat membuat faktur penerimaan baru dengan memilih supplier, tanggal, dan nomor faktur. |
| FR-15 | Sistem membangkitkan/memvalidasi keunikan `no_faktur` per supplier untuk mencegah duplikasi input. |
| FR-16 | Pengguna dapat menambahkan satu atau lebih baris detail barang diterima (pabrik, kategori, satuan, barcode, no. batch, harga beli, harga jual, tanggal kadaluarsa, jumlah) pada satu faktur penerimaan. |
| FR-17 | Saat detail penerimaan disimpan, sistem menginisialisasi `stok = jumlah` dan `aktif = true` pada batch tersebut. |
| FR-18 | Pengguna dapat mengubah status `lunas` pada faktur penerimaan (pelunasan hutang ke supplier). |
| FR-19 | Sistem memvalidasi bahwa `expired_date` tidak boleh berada di masa lalu relatif terhadap `tanggal` penerimaan. |
| FR-20 | Pengguna dapat mengedit/menghapus baris detail penerimaan **hanya jika** belum ada transaksi penjualan/rusak yang mereferensikan batch tersebut (`stok` masih sama dengan `jumlah`). |

### 6.5 Modul Penjualan

| ID | Kebutuhan |
|---|---|
| FR-21 | Kasir dapat membuat faktur penjualan baru dengan memilih/menambahkan pelanggan dan tanggal transaksi. |
| FR-22 | Sistem membangkitkan nomor faktur penjualan (`no_faktur`) secara otomatis dan unik. |
| FR-23 | Kasir dapat mencari barang berdasarkan barcode atau nama barang, menampilkan hanya batch dengan `aktif = true` dan `stok > 0`. |
| FR-24 | Ketika barang yang sama (barcode) tersedia pada beberapa batch dengan tanggal kadaluarsa berbeda, sistem merekomendasikan batch dengan `expired_date` terdekat terlebih dahulu (prinsip FEFO), namun kasir tetap dapat memilih batch lain secara manual. |
| FR-25 | Sistem menyalin `harga_jual` dari `detail_penerimaan` ke `detail_penjualan` pada saat baris ditambahkan (snapshot harga transaksi). |
| FR-26 | Kasir dapat menerapkan diskon per baris barang (`diskon`) sebelum faktur disimpan. |
| FR-27 | Sistem menghitung `subtotal` per baris = `(harga_jual × jumlah) − diskon`, dan `total` faktur = `SUM(subtotal)`. |
| FR-28 | Sistem harus **menolak** penjualan bila `jumlah` yang diminta melebihi `stok` tersedia pada batch (`detail_penerimaan.stok`) yang dipilih. |
| FR-29 | Saat faktur penjualan disimpan, sistem mengurangi `detail_penerimaan.stok` sejumlah `jumlah` yang terjual pada batch terkait, dan menonaktifkan (`aktif = false`) batch bila `stok` mencapai 0. |
| FR-30 | Pengguna dapat mengubah status `lunas` pada faktur penjualan. |
| FR-31 | Pembatalan/penghapusan faktur penjualan (jika diizinkan kebijakan bisnis) harus mengembalikan (`rollback`) `stok` ke batch asal. |

### 6.6 Modul Barang Rusak

| ID | Kebutuhan |
|---|---|
| FR-32 | Pengguna dapat mencatat barang rusak dengan memilih batch (`detail_penerimaan`) sumber, tanggal, jumlah, dan keterangan alasan. |
| FR-33 | Sistem harus menolak pencatatan bila `jumlah` rusak melebihi `stok` tersedia pada batch tersebut. |
| FR-34 | Saat data rusak disimpan, sistem mengurangi `detail_penerimaan.stok` sejumlah `jumlah` yang dicatat rusak, dan menonaktifkan batch bila `stok` mencapai 0. |
| FR-35 | Sistem dapat menandai otomatis batch sebagai kandidat "rusak/kadaluarsa" ketika `expired_date` telah lewat, sebagai bantuan pengingat bagi pengguna (tidak otomatis mengurangi stok tanpa konfirmasi pengguna). |

### 6.7 Modul Laporan

| ID | Kebutuhan | Ringkasan Isi Laporan |
|---|---|---|
| FR-36 | **Laporan Data Stok** | Daftar seluruh batch aktif (`detail_penerimaan`) dengan kolom: nama/barcode barang, kategori, pabrik, no. batch, tanggal kadaluarsa, harga beli, harga jual, stok tersisa. Dapat difilter per kategori, per supplier (via `penerimaan`), dan status hampir/telah kadaluarsa. |
| FR-37 | **Laporan Penerimaan Barang** | Daftar faktur penerimaan pada rentang tanggal tertentu, dengan rincian per faktur: supplier, tanggal, jumlah item, total nilai pembelian, status lunas. Dapat di-drill-down ke detail per batch. |
| FR-38 | **Laporan Penjualan Barang** | Daftar faktur penjualan pada rentang tanggal tertentu: pelanggan, kasir, total penjualan, status lunas. Dapat di-drill-down ke detail barang terjual per faktur. |
| FR-39 | **Laporan Barang Rusak** | Daftar pencatatan barang rusak pada rentang tanggal tertentu: nama barang, no. batch, jumlah rusak, keterangan, estimasi kerugian (`jumlah × harga_beli`). |
| FR-40 | **Laporan Laba-Rugi/Pendapatan** | Pendapatan = `SUM(detail_penjualan.subtotal)` pada rentang tanggal; Harga Pokok Penjualan (HPP) = `SUM(jumlah_terjual × harga_beli)` dari batch terkait; Laba Kotor = Pendapatan − HPP; dapat dikurangi estimasi kerugian dari Laporan Barang Rusak untuk Laba Bersih. Laporan dapat difilter per hari/minggu/bulan/rentang kustom. |
| FR-41 | Seluruh laporan pada modul ini dapat difilter berdasarkan rentang tanggal, dan mendukung ekspor (mis. PDF/Excel) — detail teknis ekspor menjadi keputusan desain lanjutan. |

---

## 7. Alur Kerja (Workflow) Data

```
[Admin/Kasir]
  1. Input faktur Penerimaan + detail barang per batch
        → detail_penerimaan.stok = jumlah, aktif = true
        ↓
[Batch tersedia untuk dijual — dapat dicari via barcode]
        ↓
   ┌────────────────────────────┬──────────────────────────────┐
   ↓                            ↓                               
[Kasir: Transaksi Penjualan]                       [Admin/Kasir: Catat Barang Rusak]
  2a. Pilih batch (barcode, prioritas FEFO)          2b. Pilih batch, catat jumlah & alasan rusak
  3a. Validasi jumlah ≤ stok                          3b. Validasi jumlah ≤ stok
  4a. Simpan detail_penjualan (snapshot harga)         4b. Simpan baris rusak
  5a. Kurangi detail_penerimaan.stok                   5b. Kurangi detail_penerimaan.stok
  6a. Jika stok = 0 → aktif = false                    6b. Jika stok = 0 → aktif = false
        ↓                                                       ↓
        └───────────────────────────┬───────────────────────────┘
                                     ↓
                        [Modul Laporan]
        Laporan Stok • Penerimaan • Penjualan • Barang Rusak • Laba-Rugi
```

---

## 8. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| **Konsistensi stok** | Pengurangan `detail_penerimaan.stok` pada transaksi penjualan dan barang rusak harus dilakukan dalam **transaksi basis data (DB transaction)** untuk mencegah race condition saat beberapa kasir bertransaksi bersamaan pada batch yang sama. |
| **Validasi data** | Validasi jumlah terhadap stok tersedia dilakukan di level aplikasi sebelum commit; unique constraint pada `no_faktur` per konteks (penerimaan per supplier, penjualan global) mencegah duplikasi. |
| **Audit trail** | Setiap faktur penerimaan dan penjualan mencatat `user_id` pelaku input; disarankan menambah `created_at`/`updated_at` pada seluruh tabel transaksi untuk jejak waktu. |
| **Keamanan** | Password di-hash (bcrypt/argon2); akses ke modul dan aksi dibatasi berdasarkan peran (role-based access control) sesuai matriks Bagian 4.2. |
| **Kinerja** | Pencarian barang via barcode pada modul Penjualan harus responsif (< 1 detik) meski jumlah batch aktif besar; disarankan indexing pada kolom `barcode`, `no_faktur`, dan `expired_date`. |
| **Integritas referensial** | Data dasar (kategori, supplier, pabrik, satuan) yang telah direferensikan oleh transaksi tidak dapat dihapus permanen (soft-delete atau proteksi FK `restrict`). |
| **Ketertelusuran (traceability)** | Setiap unit barang yang terjual atau rusak harus dapat ditelusuri kembali ke batch penerimaan asal (`detail_penerimaan_id`) untuk kebutuhan audit dan penarikan produk (recall). |
| **Ekspor laporan** | Seluruh laporan pada Bagian 6.7 mendukung ekspor ke format cetak/Excel/PDF (teknologi implementasi menjadi keputusan desain lanjutan). |

---

## 9. Aturan Bisnis Utama (Business Rules)

1. Stok sebuah barang **tidak diagregasi** lintas batch secara otomatis pada level sistem — total stok suatu jenis barang adalah hasil `SUM(stok)` dari seluruh batch aktif dengan barcode yang sama; ini dihitung saat dibutuhkan (mis. Laporan Data Stok), bukan disimpan sebagai kolom terpisah.
2. `detail_penjualan.harga_jual` **tidak mengikuti** perubahan `detail_penerimaan.harga_jual` di kemudian hari — nilai disalin (snapshot) saat transaksi terjadi, agar histori faktur penjualan tidak berubah retroaktif.
3. Batch dengan `aktif = false` tidak muncul pada pencarian barang di modul Penjualan, namun tetap tampil pada Laporan Data Stok (dengan penanda tidak aktif) untuk kebutuhan audit.
4. Penghapusan faktur penerimaan yang detailnya sudah pernah terjual/rusak **tidak diperbolehkan** — hanya dapat dinonaktifkan/dibatalkan dengan jejak status, untuk menjaga integritas riwayat transaksi.
5. Nilai `lunas` pada `penerimaan` merepresentasikan status hutang apotek ke supplier; nilai `lunas` pada `penjualan` merepresentasikan status piutang dari pelanggan (khusus transaksi non-tunai/kredit pelanggan tertentu).

---

## 10. Rencana Pengembangan Lanjutan (Opsional)

1. Dukungan multi-cabang/multi-gudang dengan stok per lokasi.
2. Integrasi e-resep dan pelaporan ke sistem farmasi nasional (SATUSEHAT/SIPNAP untuk obat golongan tertentu).
3. Pembayaran non-tunai terintegrasi (QRIS/payment gateway) pada modul Penjualan.
4. Notifikasi otomatis untuk stok menipis dan barang mendekati tanggal kadaluarsa.
5. Manajemen hutang-piutang bertahap (cicilan) untuk faktur `lunas = 0`.
6. Penambahan tabel master **Barang** terpisah (bila ke depan dibutuhkan katalog barang lintas batch yang lebih kaya, mis. deskripsi, gambar, komposisi) — saat ini nama barang direkomendasikan melekat pada `detail_penerimaan` (lihat catatan Bagian 5.7).