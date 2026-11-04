<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Penerimaan barang. Simpan lewat Stored Procedure sp_tambah_penerimaan /
 * sp_tambah_detail_penerimaan. Stok masuk dicatat otomatis oleh trigger
 * trg_detail_penerimaan_after_insert.
 */
class PenerimaanService
{
    public function daftar(string $q, string $idvendor, string $status, string $dari, string $sampai, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT pn.idpenerimaan, pn.status, pn.idpengadaan,
                       DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username,
                       (SELECT COUNT(*) FROM detail_penerimaan d WHERE d.idpenerimaan = pn.idpenerimaan) AS detail_count
                FROM penerimaan pn
                LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = pn.iduser";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(pn.idpenerimaan = ? OR pn.idpengadaan = ?)';
                $bind[] = (int) $q;
                $bind[] = (int) $q;
            } else {
                $where[] = 'v.nama_vendor LIKE ?';
                $bind[] = '%'.$q.'%';
            }
        }

        if ($idvendor !== '' && ctype_digit($idvendor)) {
            $where[] = 'p.vendor_idvendor = ?';
            $bind[] = (int) $idvendor;
        }

        if ($status !== '') {
            $where[] = 'pn.status = ?';
            $bind[] = $status;
        }

        if ($dari !== '') {
            $where[] = 'pn.created_at >= ?';
            $bind[] = $dari.' 00:00:00';
        }

        if ($sampai !== '') {
            $where[] = 'pn.created_at <= ?';
            $bind[] = $sampai.' 23:59:59';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY pn.idpenerimaan DESC', $bind, $perHalaman, $request);
    }

    public function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT pn.idpenerimaan, pn.status, pn.idpengadaan,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM penerimaan pn
             LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = pn.iduser
             WHERE pn.idpenerimaan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_penerimaan, d.barang_idbarang, d.jumlah_terima,
                    d.harga_satuan_terima, d.sub_total_terima, b.nama, s.nama_satuan
             FROM detail_penerimaan d
             LEFT JOIN barang b ON b.idbarang = d.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idpenerimaan = ?
             ORDER BY d.iddetail_penerimaan',
            [$id]
        );

        $header->retur = DB::select(
            "SELECT r.idretur, DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal, u.username
             FROM retur r
             LEFT JOIN `user` u ON u.iduser = r.iduser
             WHERE r.idpenerimaan = ?
             ORDER BY r.idretur",
            [$id]
        );

        return $header;
    }

    /**
     * Pengadaan yang masih memiliki sisa barang belum diterima.
     *
     * @return array<int, object>
     */
    public function pengadaanTersedia(): array
    {
        return DB::select(
            "SELECT p.idpengadaan, v.nama_vendor,
                    DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                    SUM(GREATEST(d.jumlah - COALESCE(t.terima, 0), 0)) AS sisa
             FROM pengadaan p
             JOIN vendor v ON v.idvendor = p.vendor_idvendor
             JOIN detail_pengadaan d ON d.idpengadaan = p.idpengadaan
             LEFT JOIN (
                 SELECT pn.idpengadaan, dp.barang_idbarang, SUM(dp.jumlah_terima) AS terima
                 FROM detail_penerimaan dp
                 JOIN penerimaan pn ON pn.idpenerimaan = dp.idpenerimaan
                 GROUP BY pn.idpengadaan, dp.barang_idbarang
             ) t ON t.idpengadaan = p.idpengadaan AND t.barang_idbarang = d.idbarang
             GROUP BY p.idpengadaan, v.nama_vendor, p.timestamp
             HAVING sisa > 0
             ORDER BY p.idpengadaan DESC"
        );
    }

    /**
     * Rincian sisa per barang untuk satu pengadaan.
     *
     * @return array<int, object>
     */
    public function sisaPengadaan(int $idpengadaan): array
    {
        return DB::select(
            'SELECT d.idbarang, b.nama, s.nama_satuan, d.harga_satuan,
                    d.jumlah AS dipesan,
                    COALESCE(t.terima, 0) AS diterima,
                    GREATEST(d.jumlah - COALESCE(t.terima, 0), 0) AS sisa
             FROM detail_pengadaan d
             JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             LEFT JOIN (
                 SELECT dp.barang_idbarang, SUM(dp.jumlah_terima) AS terima
                 FROM detail_penerimaan dp
                 JOIN penerimaan pn ON pn.idpenerimaan = dp.idpenerimaan
                 WHERE pn.idpengadaan = ?
                 GROUP BY dp.barang_idbarang
             ) t ON t.barang_idbarang = d.idbarang
             WHERE d.idpengadaan = ?
             ORDER BY d.iddetail_pengadaan',
            [$idpengadaan, $idpengadaan]
        );
    }

    /**
     * @param  array<int, array{idbarang:int, jumlah_terima:int, harga_satuan_terima:int}>  $items
     */
    public function tambah(int $idpengadaan, int $iduser, string $status, array $items): int
    {
        return DB::transaction(function () use ($idpengadaan, $iduser, $status, $items) {
            DB::statement('CALL sp_tambah_penerimaan(?, ?, ?)', [$idpengadaan, $iduser, $status]);
            $id = (int) DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;

            foreach ($items as $item) {
                DB::statement('CALL sp_tambah_detail_penerimaan(?, ?, ?, ?)', [
                    $id,
                    (int) $item['idbarang'],
                    (int) $item['jumlah_terima'],
                    (int) $item['harga_satuan_terima'],
                ]);
            }

            return $id;
        });
    }

    public function hapus(int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::statement('CALL sp_hapus_penerimaan(?)', [$id]);
        });
    }

    /** @return array<int, string> */
    public function daftarStatus(): array
    {
        $baris = DB::select('SELECT DISTINCT status FROM penerimaan WHERE status IS NOT NULL ORDER BY status');

        return array_map(fn ($r) => $r->status, $baris);
    }
}
