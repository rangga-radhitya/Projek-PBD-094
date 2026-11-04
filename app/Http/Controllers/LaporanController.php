<?php

namespace App\Http\Controllers;

use App\Services\PenjualanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use PDOException;

class LaporanController extends Controller
{
    /**
     * Daftar laporan yang direncanakan. Isi laporan belum tersedia (butuh query agregasi di backend),
     * sehingga halaman hanya menampilkan struktur, filter yang kelak dipakai, dan jalan pintas ke data yang sudah ada.
     */
    public const LAPORAN = [
        'penjualan' => [
            'judul' => 'Laporan Penjualan',
            'deskripsi' => 'Rekap penjualan per periode: jumlah transaksi, total pendapatan, dan rincian per transaksi.',
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

    public function show(Request $request, string $jenis, PenjualanService $penjualan): View
    {
        abort_unless(array_key_exists($jenis, self::LAPORAN), 404);

        $data = ['jenis' => $jenis, 'laporan' => self::LAPORAN[$jenis]];

        // Hanya Laporan Penjualan yang sudah memakai objek database (sp_laporan_penjualan_periode).
        if ($jenis === 'penjualan') {
            $data += $this->laporanPenjualan($request, $penjualan);
        }

        return view('laporan.show', $data);
    }

    /**
     * Memanggil Stored Procedure bila kedua tanggal diisi. Aturan rentang (awal > akhir, kosong)
     * ditegakkan oleh Stored Procedure; pesannya ditampilkan apa adanya.
     */
    private function laporanPenjualan(Request $request, PenjualanService $penjualan): array
    {
        $dari = trim((string) $request->query('dari', ''));
        $sampai = trim((string) $request->query('sampai', ''));
        $hasil = ['dari' => $dari, 'sampai' => $sampai, 'rincian' => null, 'rekap' => null, 'galat' => null, 'belumTerpasang' => false];

        if ($dari === '' && $sampai === '') {
            return $hasil; // belum mencari
        }

        if ($dari === '' || $sampai === '') {
            $hasil['galat'] = 'Tanggal awal dan tanggal akhir wajib diisi.';

            return $hasil;
        }

        foreach (['Tanggal awal' => $dari, 'Tanggal akhir' => $sampai] as $nama => $nilai) {
            $tgl = DateTime::createFromFormat('!Y-m-d', $nilai);
            if (! $tgl || $tgl->format('Y-m-d') !== $nilai) {
                $hasil['galat'] = $nama.' tidak valid. Gunakan format tanggal yang benar.';

                return $hasil;
            }
        }

        try {
            $laporan = $penjualan->laporanPeriode($dari, $sampai);
            $hasil['rincian'] = $laporan['rincian'];
            $hasil['rekap'] = $laporan['rekap'];
        } catch (InvalidArgumentException $e) {
            $hasil['galat'] = $e->getMessage();
        } catch (PDOException $e) {
            if (PenjualanService::objekBelumTerpasang($e)) {
                $hasil['belumTerpasang'] = true;
            } else {
                report($e);
                $hasil['galat'] = 'Laporan gagal dimuat. Periksa koneksi dan konfigurasi database.';
            }
        }

        return $hasil;
    }
}
