<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Data barang. Baca dari VIEW v_barang, tulis ke tabel dasar barang.
 */
class BarangService
{
    public function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idbarang, nama, jenis, jenis_teks, idsatuan, nama_satuan, harga, status,
                       fn_stok_barang(idbarang) AS stok
                FROM v_barang';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'nama LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY idbarang', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function semua(): array
    {
        return DB::select('SELECT idbarang, nama, harga, idsatuan, status FROM barang ORDER BY nama');
    }

    /** @return array<int, object> */
    public function aktif(): array
    {
        return DB::select('SELECT idbarang, nama, harga FROM barang WHERE status = 1 ORDER BY nama');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idbarang, nama, jenis, idsatuan, harga, status FROM barang WHERE idbarang = ?', [$id]);
    }

    public function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM barang WHERE nama = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idbarang <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    public function tambah(string $nama, string $jenis, int $idsatuan, int $harga, int $status): int
    {
        DB::insert(
            'INSERT INTO barang (nama, jenis, idsatuan, harga, status) VALUES (?, ?, ?, ?, ?)',
            [$nama, $jenis, $idsatuan, $harga, $status]
        );

        return (int) DB::getPdo()->lastInsertId();
    }

    public function ubah(int $id, string $nama, string $jenis, int $idsatuan, int $harga, int $status): void
    {
        DB::update(
            'UPDATE barang SET nama = ?, jenis = ?, idsatuan = ?, harga = ?, status = ? WHERE idbarang = ?',
            [$nama, $jenis, $idsatuan, $harga, $status, $id]
        );
    }

    public function ubahStatus(int $id, int $status): void
    {
        DB::update('UPDATE barang SET status = ? WHERE idbarang = ?', [$status, $id]);
    }

    public function dipakaiTransaksi(int $id): bool
    {
        $n = (int) DB::selectOne(
            'SELECT (SELECT COUNT(*) FROM detail_pengadaan WHERE idbarang = ?)
                  + (SELECT COUNT(*) FROM detail_penerimaan WHERE barang_idbarang = ?)
                  + (SELECT COUNT(*) FROM detail_penjualan WHERE idbarang = ?)
                  + (SELECT COUNT(*) FROM kartu_stok WHERE idbarang = ?) AS n',
            [$id, $id, $id, $id]
        )->n;

        return $n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM barang WHERE idbarang = ?', [$id]);
    }
}
