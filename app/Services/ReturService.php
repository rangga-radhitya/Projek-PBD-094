<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Retur barang ke vendor. Mengacu ke penerimaan.
 * Simpan lewat Stored Procedure sp_tambah_retur / sp_tambah_detail_retur.
 */
class ReturService
{
    public function daftar(string $q, string $idvendor, string $dari, string $sampai, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT r.idretur, r.idpenerimaan,
                       DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username,
                       (SELECT COUNT(*) FROM detail_retur d WHERE d.idretur = r.idretur) AS detail_count
                FROM retur r
                LEFT JOIN penerimaan pn ON pn.idpenerimaan = r.idpenerimaan
                LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = r.iduser";
        $where = [];
        $bind = [];

        if ($q !== '' && ctype_digit($q)) {
            $where[] = '(r.idretur = ? OR r.idpenerimaan = ?)';
            $bind[] = (int) $q;
            $bind[] = (int) $q;
        }

        if ($idvendor !== '' && ctype_digit($idvendor)) {
            $where[] = 'p.vendor_idvendor = ?';
            $bind[] = (int) $idvendor;
        }

        if ($dari !== '') {
            $where[] = 'r.created_at >= ?';
            $bind[] = $dari.' 00:00:00';
        }

        if ($sampai !== '') {
            $where[] = 'r.created_at <= ?';
            $bind[] = $sampai.' 23:59:59';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY r.idretur DESC', $bind, $perHalaman, $request);
    }

    public function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT r.idretur, r.idpenerimaan,
                    DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM retur r
             LEFT JOIN penerimaan pn ON pn.idpenerimaan = r.idpenerimaan
             LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = r.iduser
             WHERE r.idretur = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_retur, d.jumlah, d.alasan, d.iddetail_penerimaan,
                    dp.barang_idbarang, dp.jumlah_terima, b.nama, s.nama_satuan
             FROM detail_retur d
             LEFT JOIN detail_penerimaan dp ON dp.iddetail_penerimaan = d.iddetail_penerimaan
             LEFT JOIN barang b ON b.idbarang = dp.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idretur = ?
             ORDER BY d.iddetail_retur',
            [$id]
        );

        return $header;
    }

    /**
     * Penerimaan yang masih punya barang bisa diretur.
     *
     * @return array<int, object>
     */
    public function penerimaanTersedia(): array
    {
        return DB::select(
            "SELECT pn.idpenerimaan, v.nama_vendor,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    SUM(GREATEST(dp.jumlah_terima - COALESCE(r.jumlah_retur, 0), 0)) AS sisa
             FROM penerimaan pn
             JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             JOIN vendor v ON v.idvendor = p.vendor_idvendor
             JOIN detail_penerimaan dp ON dp.idpenerimaan = pn.idpenerimaan
             LEFT JOIN (
                 SELECT iddetail_penerimaan, SUM(jumlah) AS jumlah_retur
                 FROM detail_retur
                 GROUP BY iddetail_penerimaan
             ) r ON r.iddetail_penerimaan = dp.iddetail_penerimaan
             GROUP BY pn.idpenerimaan, pn.created_at, v.nama_vendor
             HAVING sisa > 0
             ORDER BY pn.idpenerimaan DESC"
        );
    }

    /**
     * Rincian barang pada satu penerimaan yang masih bisa diretur.
     *
     * @return array<int, object>
     */
    public function detailTersedia(int $idpenerimaan): array
    {
        return DB::select(
            'SELECT dp.iddetail_penerimaan, dp.barang_idbarang AS idbarang,
                    dp.jumlah_terima, b.nama, s.nama_satuan,
                    COALESCE(r.jumlah_retur, 0) AS sudah_retur,
                    GREATEST(dp.jumlah_terima - COALESCE(r.jumlah_retur, 0), 0) AS sisa
             FROM detail_penerimaan dp
             JOIN barang b ON b.idbarang = dp.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             LEFT JOIN (
                 SELECT iddetail_penerimaan, SUM(jumlah) AS jumlah_retur
                 FROM detail_retur
                 GROUP BY iddetail_penerimaan
             ) r ON r.iddetail_penerimaan = dp.iddetail_penerimaan
             WHERE dp.idpenerimaan = ?
             ORDER BY dp.iddetail_penerimaan',
            [$idpenerimaan]
        );
    }

    /**
     * @param  array<int, array{iddetail_penerimaan:int, jumlah:int, alasan:?string}>  $items
     */
    public function tambah(int $idpenerimaan, int $iduser, array $items): int
    {
        return DB::transaction(function () use ($idpenerimaan, $iduser, $items) {
            DB::statement('CALL sp_tambah_retur(?, ?)', [$idpenerimaan, $iduser]);
            $id = (int) DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;

            foreach ($items as $item) {
                DB::statement('CALL sp_tambah_detail_retur(?, ?, ?, ?)', [
                    $id,
                    (int) $item['iddetail_penerimaan'],
                    (int) $item['jumlah'],
                    $item['alasan'] ?? null,
                ]);
            }

            return $id;
        });
    }

    public function hapus(int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::statement('CALL sp_hapus_retur(?)', [$id]);
        });
    }
}
