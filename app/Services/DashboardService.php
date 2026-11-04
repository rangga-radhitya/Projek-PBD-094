<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Ringkasan dashboard. Semua angka dihitung dengan SQL mentah.
 */
class DashboardService
{
    /** @return array<string, int> */
    public function ringkasan(string $hari): array
    {
        $r = DB::selectOne(
            "SELECT
                (SELECT COUNT(*) FROM barang WHERE status = 1)  AS barang_aktif,
                (SELECT COUNT(*) FROM vendor WHERE status = 'A') AS vendor_aktif,
                (SELECT COUNT(*) FROM `user`)                    AS user,
                (SELECT COUNT(*) FROM pengadaan)                 AS pengadaan,
                (SELECT COUNT(*) FROM penerimaan)                AS penerimaan,
                (SELECT COUNT(*) FROM penjualan)                 AS penjualan,
                (SELECT COUNT(*) FROM retur)                     AS retur,
                (SELECT COUNT(*) FROM kartu_stok)                AS kartu_stok,
                (SELECT COUNT(*) FROM penjualan WHERE DATE(created_at) = ?) AS penjualan_hari_ini,
                (SELECT COALESCE(SUM(total_nilai), 0) FROM penjualan WHERE DATE(created_at) = ?) AS nilai_hari_ini",
            [$hari, $hari]
        );

        return [
            'barang_aktif' => (int) $r->barang_aktif,
            'vendor_aktif' => (int) $r->vendor_aktif,
            'user' => (int) $r->user,
            'pengadaan' => (int) $r->pengadaan,
            'penerimaan' => (int) $r->penerimaan,
            'penjualan' => (int) $r->penjualan,
            'retur' => (int) $r->retur,
            'kartu_stok' => (int) $r->kartu_stok,
            'penjualan_hari_ini' => (int) $r->penjualan_hari_ini,
            'nilai_hari_ini' => (int) $r->nilai_hari_ini,
        ];
    }

    /** @return array<int, object> */
    public function marginAktif(): array
    {
        return DB::select('SELECT idmargin_penjualan, persen FROM margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan');
    }

    /** @return array<int, object> */
    public function penjualanTerakhir(int $batas = 5): array
    {
        return DB::select(
            "SELECT p.idpenjualan, DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username, p.total_nilai
             FROM penjualan p
             LEFT JOIN `user` u ON u.iduser = p.iduser
             ORDER BY p.idpenjualan DESC
             LIMIT ".(int) $batas
        );
    }

    /** @return array<int, object> */
    public function pengadaanTerakhir(int $batas = 5): array
    {
        return DB::select(
            "SELECT p.idpengadaan, DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, p.total_nilai
             FROM pengadaan p
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             ORDER BY p.idpengadaan DESC
             LIMIT ".(int) $batas
        );
    }
}
