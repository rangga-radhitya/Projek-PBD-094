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
        'label' => 'Tabel Master',
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
        'label' => 'Transaksi',
        'items' => [
            ['label' => 'Pengadaan',  'route' => 'pengadaan.index',  'match' => ['pengadaan', 'pengadaan/*']],
            ['label' => 'Penerimaan', 'route' => 'penerimaan.index', 'match' => ['penerimaan', 'penerimaan/*']],
            ['label' => 'Penjualan',  'route' => 'penjualan.index',  'match' => ['penjualan', 'penjualan/*']],
            ['label' => 'Retur',      'route' => 'retur.index',      'match' => ['retur', 'retur/*']],
        ],
    ],
    [
        'label' => 'Persediaan',
        'items' => [
            ['label' => 'Kartu Stok', 'route' => 'kartu_stok', 'match' => ['kartu_stok', 'kartu_stok/*']],
        ],
    ],
];
