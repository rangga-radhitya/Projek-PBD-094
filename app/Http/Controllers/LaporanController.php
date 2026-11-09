<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

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
            'filter' => ['Periode', 'Vendor'],
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

    public function show(Request $request, string $jenis): View
    {
        abort_unless(array_key_exists($jenis, self::LAPORAN), 404);

        $data = ['jenis' => $jenis, 'laporan' => self::LAPORAN[$jenis]];

        // Hanya Laporan Penjualan yang sudah punya query rekap.
        if ($jenis === 'penjualan') {
            $data += $this->laporanPenjualan($request);
        }

        return view('laporan.show', $data);
    }

    /**
     * Menghitung rekap penjualan bila kedua tanggal diisi.
     */
    private function laporanPenjualan(Request $request): array
    {
        $dari = trim((string) $request->query('dari', ''));
        $sampai = trim((string) $request->query('sampai', ''));
        $hasil = ['dari' => $dari, 'sampai' => $sampai, 'rincian' => null, 'rekap' => null, 'galat' => null];

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

        if ($dari > $sampai) {
            $hasil['galat'] = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.';

            return $hasil;
        }

        try {
            $laporan = $this->laporanPeriode($dari, $sampai);
            $hasil['rincian'] = $laporan['rincian'];
            $hasil['rekap'] = $laporan['rekap'];
        } catch (Throwable $e) {
            report($e);
            $hasil['galat'] = 'Laporan gagal dimuat. Periksa koneksi dan konfigurasi database.';
        }

        return $hasil;
    }

    /**
     * Rekap penjualan pada rentang tanggal (format Y-m-d, kedua ujung termasuk).
     *
     * @return array{rincian: array<int, array<string, mixed>>, rekap: array<string, mixed>}
     */
    private function laporanPeriode(string $awal, string $akhir): array
    {
        $rincian = DB::select(
            "SELECT p.idpenjualan, p.created_at, u.username AS kasir,
                    (SELECT COUNT(*) FROM detail_penjualan d
                      WHERE d.penjualan_idpenjualan = p.idpenjualan) AS jumlah_item,
                    (SELECT COALESCE(SUM(d.subtotal), 0) FROM detail_penjualan d
                      WHERE d.penjualan_idpenjualan = p.idpenjualan) AS subtotal_hitung,
                    p.ppn, p.total_nilai
             FROM penjualan p
             LEFT JOIN `user` u ON u.iduser = p.iduser
             WHERE p.created_at >= ?
               AND p.created_at < DATE_ADD(?, INTERVAL 1 DAY)
               AND EXISTS (SELECT 1 FROM detail_penjualan d
                            WHERE d.penjualan_idpenjualan = p.idpenjualan)
             ORDER BY p.created_at, p.idpenjualan",
            [$awal, $akhir]
        );

        $rekap = DB::selectOne(
            "SELECT COUNT(*) AS jumlah_transaksi,
                    COALESCE(SUM(t.subtotal_hitung), 0) AS total_pendapatan,
                    COALESCE(SUM(t.ppn), 0) AS total_ppn,
                    COALESCE(SUM(t.total_nilai), 0) AS total_nilai_tercatat
             FROM (SELECT (SELECT COALESCE(SUM(d.subtotal), 0) FROM detail_penjualan d
                            WHERE d.penjualan_idpenjualan = p.idpenjualan) AS subtotal_hitung,
                          p.ppn, p.total_nilai
                   FROM penjualan p
                   WHERE p.created_at >= ?
                     AND p.created_at < DATE_ADD(?, INTERVAL 1 DAY)
                     AND EXISTS (SELECT 1 FROM detail_penjualan d
                                  WHERE d.penjualan_idpenjualan = p.idpenjualan)) t",
            [$awal, $akhir]
        );

        return [
            'rincian' => array_map(fn ($r) => (array) $r, $rincian),
            'rekap' => $rekap === null ? [
                'jumlah_transaksi' => 0,
                'total_pendapatan' => 0,
                'total_ppn' => 0,
                'total_nilai_tercatat' => 0,
            ] : (array) $rekap,
        ];
    }
}
