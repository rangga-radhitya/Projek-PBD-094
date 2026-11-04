<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Daftar laporan yang direncanakan. Isi laporan belum tersedia (butuh query agregasi di backend),
     * sehingga halaman hanya menampilkan struktur, filter yang kelak dipakai, dan jalan pintas ke data yang sudah ada.
     */
    public const LAPORAN = [
        'penjualan' => [
            'judul' => 'Laporan Penjualan',
            'deskripsi' => 'Rekap penjualan per periode, per barang, dan per kasir.',
            'sumber' => 'penjualan, detail_penjualan, barang, user',
            'filter' => ['Periode', 'Kasir', 'Barang'],
            'sementara' => ['route' => 'penjualan.index', 'label' => 'Data Penjualan'],
        ],
        'pengadaan' => [
            'judul' => 'Laporan Pengadaan',
            'deskripsi' => 'Rekap pengadaan per periode dan per vendor.',
            'sumber' => 'pengadaan, detail_pengadaan, vendor',
            'filter' => ['Periode', 'Vendor', 'Status'],
            'sementara' => ['route' => 'pengadaan.index', 'label' => 'Data Pengadaan'],
        ],
        'penerimaan' => [
            'judul' => 'Laporan Penerimaan',
            'deskripsi' => 'Rekap penerimaan barang per periode dan per vendor.',
            'sumber' => 'penerimaan, detail_penerimaan, pengadaan, vendor',
            'filter' => ['Periode', 'Vendor'],
            'sementara' => ['route' => 'penerimaan.index', 'label' => 'Data Penerimaan'],
        ],
        'retur' => [
            'judul' => 'Laporan Retur',
            'deskripsi' => 'Rekap retur ke vendor beserta alasan.',
            'sumber' => 'retur, detail_retur, detail_penerimaan, vendor',
            'filter' => ['Periode', 'Vendor'],
            'sementara' => ['route' => 'retur.index', 'label' => 'Data Retur'],
        ],
        'stok' => [
            'judul' => 'Laporan Stok',
            'deskripsi' => 'Stok saat ini dan mutasi barang pada suatu periode.',
            'sumber' => 'kartu_stok, barang',
            'filter' => ['Periode', 'Barang'],
            'sementara' => ['route' => 'kartu_stok', 'label' => 'Kartu Stok'],
        ],
    ];

    public function index(): View
    {
        return view('laporan.index', ['laporan' => self::LAPORAN]);
    }

    public function show(string $jenis): View
    {
        abort_unless(array_key_exists($jenis, self::LAPORAN), 404);

        return view('laporan.show', ['jenis' => $jenis, 'laporan' => self::LAPORAN[$jenis]]);
    }
}
