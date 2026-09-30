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
        'admin'      => ['label' => 'Admin'],
        'apoteker'   => ['label' => 'Apoteker'],
        'kasir'      => ['label' => 'Kasir'],
    ],

    'pages' => [
        'master.barang'         => ['group' => 'Data Master', 'label' => 'Barang / Produk'],
        'master.kategori'        => ['group' => 'Data Master', 'label' => 'Kategori'],
        'master.satuan'          => ['group' => 'Data Master', 'label' => 'Satuan'],
        'master.pabrik'          => ['group' => 'Data Master', 'label' => 'Pabrik'],
        'master.supplier'        => ['group' => 'Data Master', 'label' => 'Supplier'],
        'master.pelanggan'       => ['group' => 'Data Master', 'label' => 'Pelanggan / Member'],
        'master.custom-discount' => ['group' => 'Data Master', 'label' => 'Custom Discount'],

        'transaksi.penjualan'  => ['group' => 'Transaksi', 'label' => 'Penjualan (Kasir)'],
        'transaksi.penerimaan' => ['group' => 'Transaksi', 'label' => 'Penerimaan Barang'],
        'transaksi.rusak'      => ['group' => 'Transaksi', 'label' => 'Barang Rusak'],

        'laporan.stok'       => ['group' => 'Laporan', 'label' => 'Laporan Stok'],
        'laporan.penerimaan' => ['group' => 'Laporan', 'label' => 'Laporan Penerimaan'],
        'laporan.penjualan'  => ['group' => 'Laporan', 'label' => 'Laporan Penjualan'],
        'laporan.rusak'      => ['group' => 'Laporan', 'label' => 'Laporan Barang Rusak'],
        'laporan.laba-rugi'  => ['group' => 'Laporan', 'label' => 'Laporan Laba Rugi'],
        'laporan.diskon'     => ['group' => 'Laporan', 'label' => 'Laporan Diskon'],

        'sistem.kelola-user'  => ['group' => 'Sistem', 'label' => 'Kelola User'],
        'sistem.pengaturan'   => ['group' => 'Sistem', 'label' => 'Pengaturan Apotek'],
        'sistem.activity-log' => ['group' => 'Sistem', 'label' => 'Log Aktivitas'],
    ],

    /*
    | Default tiap role. '*' = semua halaman, level kelola.
    | Default ini ditulis migration (bukan seeder) supaya user yang sudah ada
    | di production tetap punya akses setelah permission diaktifkan.
    */
    'defaults' => [
        'superadmin' => '*',
        'admin'      => '*',
        'apoteker'   => [
            'transaksi.penjualan' => 'kelola',
            'master.barang'       => 'lihat',
        ],
        'kasir' => [
            'transaksi.penjualan' => 'kelola',
        ],
    ],

];
