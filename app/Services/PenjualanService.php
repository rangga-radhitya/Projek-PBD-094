<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Penjualan: daftar, detail, dan rekap periode memakai SQL biasa.
 */
class PenjualanService
{
    public function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai,
                       DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       u.username, m.persen,
                       (SELECT COUNT(*) FROM detail_penjualan d WHERE d.penjualan_idpenjualan = p.idpenjualan) AS detail_count
                FROM penjualan p
                LEFT JOIN `user` u ON u.iduser = p.iduser
                LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(p.idpenjualan = ? OR u.username LIKE ?)';
                $bind[] = (int) $q;
                $bind[] = '%'.$q.'%';
            } else {
                $where[] = 'u.username LIKE ?';
                $bind[] = '%'.$q.'%';
            }
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY p.idpenjualan DESC', $bind, $perHalaman, $request);
    }

    public function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai, p.idmargin_penjualan,
                    DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username, m.persen
             FROM penjualan p
             LEFT JOIN `user` u ON u.iduser = p.iduser
             LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan
             WHERE p.idpenjualan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_penjualan, d.idbarang, d.harga_satuan, d.jumlah, d.subtotal,
                    b.nama, s.nama_satuan
             FROM detail_penjualan d
             LEFT JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.penjualan_idpenjualan = ?
             ORDER BY d.iddetail_penjualan',
            [$id]
        );

        return $header;
    }

    /**
     * Rekap penjualan pada rentang tanggal (format Y-m-d, kedua ujung termasuk).
     *
     * @return array{rincian: array<int, array<string, mixed>>, rekap: array<string, mixed>}
     */
    public function laporanPeriode(string $awal, string $akhir): array
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

    /**
     * Total satu transaksi penjualan dari detail (jumlah × harga satuan).
     * Null bila transaksi tidak ada, "0" bila transaksi belum punya detail.
     */
    public function totalPenjualan(int $idpenjualan): ?string
    {
        $ada = DB::selectOne('SELECT 1 AS x FROM penjualan WHERE idpenjualan = ?', [$idpenjualan]);

        if ($ada === null) {
            return null;
        }

        $total = DB::selectOne(
            'SELECT COALESCE(SUM(subtotal), 0) AS total
             FROM detail_penjualan
             WHERE penjualan_idpenjualan = ?',
            [$idpenjualan]
        )->total;

        return (string) $total;
    }
}
