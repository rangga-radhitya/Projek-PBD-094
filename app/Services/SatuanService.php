<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Data satuan. Baca dari VIEW v_satuan, tulis ke tabel dasar satuan.
 */
class SatuanService
{
    public function daftar(string $q, string $status, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idsatuan, nama_satuan, status, status_teks FROM v_satuan';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'nama_satuan LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if (in_array($status, ['0', '1'], true)) {
            $where[] = 'status = ?';
            $bind[] = (int) $status;
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY idsatuan', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function semua(): array
    {
        return DB::select('SELECT idsatuan, nama_satuan FROM satuan ORDER BY nama_satuan');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idsatuan, nama_satuan, status FROM satuan WHERE idsatuan = ?', [$id]);
    }

    public function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM satuan WHERE nama_satuan = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idsatuan <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    public function tambah(string $nama, int $status): int
    {
        DB::insert('INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)', [$nama, $status]);

        return (int) DB::getPdo()->lastInsertId();
    }

    public function ubah(int $id, string $nama, int $status): void
    {
        DB::update('UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?', [$nama, $status, $id]);
    }

    public function ubahStatus(int $id, int $status): void
    {
        DB::update('UPDATE satuan SET status = ? WHERE idsatuan = ?', [$status, $id]);
    }

    public function dipakaiBarang(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM barang WHERE idsatuan = ?', [$id])->n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM satuan WHERE idsatuan = ?', [$id]);
    }
}
