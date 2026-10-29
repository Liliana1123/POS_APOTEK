<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Katalog Hak Akses
    |--------------------------------------------------------------------------
    |
    | Satu-satunya sumber kebenaran untuk permission, halaman "Izin Akses", dan
    | default tiap role. Menambah halaman baru cukup di file ini.
    |
    | role (string) pada users dan pivot role_permissions sengaja TIDAK memakai
    | tabel roles, supaya multi-apotek nanti cukup menambah kolom apotek_id —
    | bukan redesign.
    |
    | ponytail: level izin hanya lihat/kelola per halaman, bukan per aksi
    | (view/create/edit/delete/export). Kalau butuh presisi per aksi, ganti
    | kolom view/manage di role_permissions dengan kolom per aksi — slug, UI,
    | dan User::can() tidak berubah.
    |
    */

    'roles' => [
        'superadmin' => ['label' => 'Superadmin', 'kunci' => true],
        'admin' => ['label' => 'Admin'],
        'apoteker' => ['label' => 'Apoteker'],
        'gudang' => ['label' => 'Admin Gudang'],
        'kasir' => ['label' => 'Kasir'],
    ],

    /*
    | Urutan 'pages' di bawah = urutan menu di sidebar (layouts/app.blade.php),
    | dipakai migration untuk mengisi kolom group + sort. Samakan bila
    | urutan sidebar berubah.
    */
    'pages' => [
        // Sidebar: Data Master
        'master.barang' => ['group' => 'Data Master', 'label' => 'Barang / Produk'],
        'master.kategori' => ['group' => 'Data Master', 'label' => 'Kategori'],
        'master.satuan' => ['group' => 'Data Master', 'label' => 'Satuan'],
        'master.pabrik' => ['group' => 'Data Master', 'label' => 'Pabrik'],
        'master.supplier' => ['group' => 'Data Master', 'label' => 'Supplier'],
        'master.pelanggan' => ['group' => 'Data Master', 'label' => 'Pelanggan / Member'],
        'sistem.kelola-user' => ['group' => 'Data Master', 'label' => 'Kelola User'],
        'sistem.pengaturan' => ['group' => 'Data Master', 'label' => 'Pengaturan Apotek'],

        // Sidebar: Transaksi
        'transaksi.penjualan' => ['group' => 'Transaksi', 'label' => 'Penjualan (Kasir)'],
        'transaksi.penerimaan' => ['group' => 'Transaksi', 'label' => 'Penerimaan Barang'],
        'transaksi.rusak' => ['group' => 'Transaksi', 'label' => 'Barang Rusak'],

        // Sidebar: Laporan
        'laporan.stok' => ['group' => 'Laporan', 'label' => 'Laporan Stok'],
        'laporan.penerimaan' => ['group' => 'Laporan', 'label' => 'Laporan Penerimaan'],
        'laporan.penjualan' => ['group' => 'Laporan', 'label' => 'Laporan Penjualan'],
        'laporan.rusak' => ['group' => 'Laporan', 'label' => 'Laporan Barang Rusak'],
        'laporan.laba-rugi' => ['group' => 'Laporan', 'label' => 'Laporan Laba Rugi'],
        'laporan.diskon' => ['group' => 'Laporan', 'label' => 'Laporan Diskon'],

        // Sidebar: Promo & Log
        'master.custom-discount' => ['group' => 'Promo & Log', 'label' => 'Custom Discount'],
        'sistem.activity-log' => ['group' => 'Promo & Log', 'label' => 'Log Aktivitas'],

        // Dashboard: KPI Utama (6 Kartu)
        'dashboard.kpi-omzet-kotor' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Omzet Kotor'],
        'dashboard.kpi-total-diskon' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Total Diskon'],
        'dashboard.kpi-omzet-bersih' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Omzet Bersih'],
        'dashboard.kpi-total-member' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Total Member'],
        'dashboard.kpi-hutang-supplier' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Hutang Supplier'],
        'dashboard.kpi-piutang-member' => ['group' => 'Dashboard: KPI', 'label' => 'Kartu KPI: Piutang Member'],

        // Dashboard: Grafik & Analisis
        'dashboard.grafik-omzet' => ['group' => 'Dashboard: Grafik & Analisis', 'label' => 'Grafik Tren Penjualan & Omzet'],
        'dashboard.grafik-profit' => ['group' => 'Dashboard: Grafik & Analisis', 'label' => 'Gross Profit & Margin pada Grafik'],
        'dashboard.kondisi-bisnis' => ['group' => 'Dashboard: Grafik & Analisis', 'label' => 'Widget Kondisi Bisnis (Business Health Score)'],

        // Dashboard: Tindak Lanjut & Pintasan
        'dashboard.alert-stok-habis' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Alert Tindak Lanjut: Stok Habis (Kritis)'],
        'dashboard.alert-stok-menipis' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Alert Tindak Lanjut: Stok Menipis (Perhatian)'],
        'dashboard.alert-expired' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Alert Tindak Lanjut: Mendekati Expired < 30 Hari'],
        'dashboard.alert-hutang-tempo' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Alert Tindak Lanjut: Hutang Supplier Jatuh Tempo'],
        'dashboard.alert-piutang-member' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Alert Tindak Lanjut: Piutang Member Terbuka'],
        'dashboard.tombol-buka-kasir' => ['group' => 'Dashboard: Tindak Lanjut', 'label' => 'Tombol Pintasan: Buka Kasir'],

        // Dashboard: Stok & Inventory
        'dashboard.pemantauan-stok' => ['group' => 'Dashboard: Stok & Barang', 'label' => 'Pemantauan Fisik Stok & Expiry Health'],
        'dashboard.nilai-persediaan' => ['group' => 'Dashboard: Stok & Barang', 'label' => 'Valuasi Nilai Modal Persediaan'],
        'dashboard.barang-harus-dibeli' => ['group' => 'Dashboard: Stok & Barang', 'label' => 'Tabel Barang yang Harus Dibeli (Reorder)'],

        // Dashboard: Obat & Karyawan
        'dashboard.obat-terlaris' => ['group' => 'Dashboard: Obat & Karyawan', 'label' => 'Tabel 10 Obat Terlaris'],
        'dashboard.obat-terendah' => ['group' => 'Dashboard: Obat & Karyawan', 'label' => 'Tabel 10 Obat Paling Sedikit Terjual'],
        'dashboard.omzet-obat' => ['group' => 'Dashboard: Obat & Karyawan', 'label' => 'Nominal Omzet (Rp) pada Tabel Obat'],
        'dashboard.kinerja-karyawan' => ['group' => 'Dashboard: Obat & Karyawan', 'label' => 'Tabel Kinerja & Ranking Karyawan'],
        'dashboard.dokter-resep' => ['group' => 'Dashboard: Obat & Karyawan', 'label' => 'Tabel Dokter Penyumbang Resep'],
    ],

    /*
    | Default tiap role. '*' = semua halaman, level kelola.
    | Default ini ditulis migration (bukan seeder) supaya user yang sudah ada
    | di production tetap punya akses setelah permission diaktifkan.
    */
    'defaults' => [
        'superadmin' => '*',
        'admin' => '*',
        'apoteker' => [
            'transaksi.penjualan' => 'kelola',
            'master.barang' => 'lihat',
            'dashboard.kpi-total-member' => 'lihat',
            'dashboard.alert-stok-habis' => 'lihat',
            'dashboard.alert-stok-menipis' => 'lihat',
            'dashboard.alert-expired' => 'lihat',
            'dashboard.tombol-buka-kasir' => 'lihat',
            'dashboard.pemantauan-stok' => 'lihat',
            'dashboard.barang-harus-dibeli' => 'lihat',
            'dashboard.obat-terlaris' => 'lihat',
            'dashboard.obat-terendah' => 'lihat',
            'dashboard.dokter-resep' => 'lihat',
        ],
        'kasir' => [
            'transaksi.penjualan' => 'kelola',
            'dashboard.kpi-total-member' => 'lihat',
            'dashboard.alert-stok-habis' => 'lihat',
            'dashboard.alert-stok-menipis' => 'lihat',
            'dashboard.tombol-buka-kasir' => 'lihat',
            'dashboard.obat-terlaris' => 'lihat',
            'dashboard.obat-terendah' => 'lihat',
        ],
        'gudang' => [
            'master.barang' => 'kelola',
            'master.kategori' => 'lihat',
            'master.satuan' => 'lihat',
            'master.pabrik' => 'lihat',
            'master.supplier' => 'kelola',
            'transaksi.penerimaan' => 'kelola',
            'transaksi.rusak' => 'kelola',
            'laporan.stok' => 'lihat',
            'laporan.penerimaan' => 'lihat',
            'laporan.rusak' => 'lihat',
            'dashboard.alert-stok-habis' => 'lihat',
            'dashboard.alert-stok-menipis' => 'lihat',
            'dashboard.alert-expired' => 'lihat',
            'dashboard.pemantauan-stok' => 'lihat',
            'dashboard.barang-harus-dibeli' => 'lihat',
        ],
    ],

];
