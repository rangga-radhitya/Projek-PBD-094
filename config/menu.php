<?php

/*
 * Daftar menu sidebar (satu sumber). Dibaca oleh resources/views/layouts/app.blade.php.
 *
 * - 'route'  : nama route (di-resolve di Blade, bukan di sini).
 * - 'params' : parameter route (opsional).
 * - 'match'  : pola path untuk penanda menu aktif (request()->is()).
 * - Entri tanpa 'items' adalah menu tunggal; entri dengan 'items' adalah grup.
 *
 * Halaman "Detail ..." tidak masuk sidebar: dibuka dari baris pada halaman daftar.
 */
return [
    [
        'label' => 'Dashboard',
        'route' => 'dashboard',
        'match' => ['dashboard'],
    ],
    [
        'label' => 'Master Data',
        'items' => [
            ['label' => 'Role',             'route' => 'role',             'match' => ['role', 'role/*']],
            ['label' => 'User',             'route' => 'user',             'match' => ['user', 'user/*']],
            ['label' => 'Satuan',           'route' => 'satuan',           'match' => ['satuan', 'satuan/*']],
            ['label' => 'Vendor',           'route' => 'vendor',           'match' => ['vendor', 'vendor/*']],
            ['label' => 'Margin Penjualan', 'route' => 'margin_penjualan', 'match' => ['margin_penjualan', 'margin_penjualan/*']],
            ['label' => 'Barang',           'route' => 'barang',           'match' => ['barang', 'barang/*']],
        ],
    ],
    [
        'label' => 'Pengadaan',
        'items' => [
            ['label' => 'Data Pengadaan', 'route' => 'pengadaan.index', 'match' => ['pengadaan', 'pengadaan/*']],
        ],
    ],
    [
        'label' => 'Penerimaan',
        'items' => [
            ['label' => 'Data Penerimaan', 'route' => 'penerimaan.index', 'match' => ['penerimaan', 'penerimaan/*']],
        ],
    ],
    [
        'label' => 'Penjualan',
        'items' => [
            ['label' => 'Data Penjualan', 'route' => 'penjualan.index', 'match' => ['penjualan', 'penjualan/*']],
        ],
    ],
    [
        'label' => 'Retur',
        'items' => [
            ['label' => 'Data Retur', 'route' => 'retur.index', 'match' => ['retur', 'retur/*']],
        ],
    ],
    [
        'label' => 'Persediaan',
        'items' => [
            ['label' => 'Kartu Stok', 'route' => 'kartu_stok', 'match' => ['kartu_stok', 'kartu_stok/*']],
        ],
    ],
    [
        'label' => 'Laporan',
        'items' => [
            ['label' => 'Laporan Penjualan',  'route' => 'laporan.show', 'params' => ['jenis' => 'penjualan'],  'match' => ['laporan/penjualan']],
            ['label' => 'Laporan Pengadaan',  'route' => 'laporan.show', 'params' => ['jenis' => 'pengadaan'],  'match' => ['laporan/pengadaan']],
            ['label' => 'Laporan Penerimaan', 'route' => 'laporan.show', 'params' => ['jenis' => 'penerimaan'], 'match' => ['laporan/penerimaan']],
            ['label' => 'Laporan Retur',      'route' => 'laporan.show', 'params' => ['jenis' => 'retur'],      'match' => ['laporan/retur']],
            ['label' => 'Laporan Stok',       'route' => 'laporan.show', 'params' => ['jenis' => 'stok'],       'match' => ['laporan/stok']],
        ],
    ],
];
