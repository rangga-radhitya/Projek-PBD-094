<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Pengadaan. Baca dengan SQL mentah; simpan header + detail dalam satu
 * transaksi aplikasi (tanpa stored procedure).
 */
class PengadaanService
{
    public function daftar(string $q, string $idvendor, string $status, string $dari, string $sampai, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT p.idpengadaan, p.status, p.subtotal_nilai, p.ppn, p.total_nilai,
                       DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username
                FROM pengadaan p
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = p.user_iduser";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = 'p.idpengadaan = ?';
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
            $where[] = 'p.status = ?';
            $bind[] = $status;
        }

        if ($dari !== '') {
            $where[] = 'p.timestamp >= ?';
            $bind[] = $dari.' 00:00:00';
        }

        if ($sampai !== '') {
            $where[] = 'p.timestamp <= ?';
            $bind[] = $sampai.' 23:59:59';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY p.idpengadaan DESC', $bind, $perHalaman, $request);
    }

    public function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT p.idpengadaan, p.status, p.subtotal_nilai, p.ppn, p.total_nilai, p.vendor_idvendor,
                    DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM pengadaan p
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = p.user_iduser
             WHERE p.idpengadaan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_pengadaan, d.idbarang, d.harga_satuan, d.jumlah, d.sub_total,
                    b.nama, s.nama_satuan
             FROM detail_pengadaan d
             LEFT JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idpengadaan = ?
             ORDER BY d.iddetail_pengadaan',
            [$id]
        );

        $header->penerimaan = DB::select(
            "SELECT pn.idpenerimaan, pn.status,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username
             FROM penerimaan pn
             LEFT JOIN `user` u ON u.iduser = pn.iduser
             WHERE pn.idpengadaan = ?
             ORDER BY pn.idpenerimaan",
            [$id]
        );

        return $header;
    }

    /**
     * Jumlah yang sudah diterima per barang untuk satu pengadaan.
     *
     * @return array<int, int> idbarang => total diterima
     */
    public function diterimaPerBarang(int $idpengadaan): array
    {
        $baris = DB::select(
            'SELECT dp.barang_idbarang AS idbarang, SUM(dp.jumlah_terima) AS total
             FROM detail_penerimaan dp
             JOIN penerimaan p ON p.idpenerimaan = dp.idpenerimaan
             WHERE p.idpengadaan = ?
             GROUP BY dp.barang_idbarang',
            [$idpengadaan]
        );

        $peta = [];
        foreach ($baris as $b) {
            $peta[(int) $b->idbarang] = (int) $b->total;
        }

        return $peta;
    }

    /** @return array<int, string> */
    public function daftarStatus(): array
    {
        $baris = DB::select('SELECT DISTINCT status FROM pengadaan WHERE status IS NOT NULL ORDER BY status');

        return array_map(fn ($r) => $r->status, $baris);
    }

    /**
     * Simpan header + detail pengadaan dalam satu transaksi.
     *
     * @param  array<int, array{idbarang:int, harga_satuan:int, jumlah:int}>  $items
     */
    public function tambah(int $iduser, int $idvendor, string $status, array $items, int $ppnPersen): int
    {
        return DB::transaction(function () use ($iduser, $idvendor, $status, $items, $ppnPersen) {
            DB::insert(
                'INSERT INTO pengadaan (timestamp, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
                 VALUES (NOW(), ?, ?, ?, 0, 0, 0)',
                [$iduser, $status, $idvendor]
            );
            $id = (int) DB::getPdo()->lastInsertId();

            foreach ($items as $item) {
                $harga = (int) $item['harga_satuan'];
                $jumlah = (int) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new RuntimeException('Jumlah barang pengadaan harus lebih dari 0.');
                }

                if ($harga < 0) {
                    throw new RuntimeException('Harga satuan tidak boleh negatif.');
                }

                DB::insert(
                    'INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
                     VALUES (?, ?, ?, ?, ?)',
                    [$harga, $jumlah, $harga * $jumlah, (int) $item['idbarang'], $id]
                );
            }

            $subtotal = (int) DB::selectOne(
                'SELECT COALESCE(SUM(sub_total), 0) AS total FROM detail_pengadaan WHERE idpengadaan = ?',
                [$id]
            )->total;

            $ppn = (int) round($subtotal * $ppnPersen / 100);

            DB::update(
                'UPDATE pengadaan SET subtotal_nilai = ?, ppn = ?, total_nilai = ? WHERE idpengadaan = ?',
                [$subtotal, $ppn, $subtotal + $ppn, $id]
            );

            return $id;
        });
    }

    public function dipakaiPenerimaan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM penerimaan WHERE idpengadaan = ?', [$id])->n > 0;
    }

    public function hapus(int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::delete('DELETE FROM detail_pengadaan WHERE idpengadaan = ?', [$id]);
            DB::delete('DELETE FROM pengadaan WHERE idpengadaan = ?', [$id]);
        });
    }
}
