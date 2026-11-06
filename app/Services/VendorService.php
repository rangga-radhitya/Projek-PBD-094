<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Data vendor. Baca dari VIEW v_vendor, tulis ke tabel dasar vendor.
 */
class VendorService
{
    public function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idvendor, nama_vendor, badan_hukum, badan_hukum_teks, status, status_teks FROM v_vendor';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'nama_vendor LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY idvendor', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function semua(): array
    {
        return DB::select('SELECT idvendor, nama_vendor FROM vendor ORDER BY nama_vendor');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idvendor, nama_vendor, badan_hukum, status FROM vendor WHERE idvendor = ?', [$id]);
    }

    public function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM vendor WHERE nama_vendor = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idvendor <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    public function tambah(string $nama, string $badan, string $status): int
    {
        DB::insert('INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)', [$nama, $badan, $status]);

        return (int) DB::getPdo()->lastInsertId();
    }

    public function ubah(int $id, string $nama, string $badan, string $status): void
    {
        DB::update(
            'UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?',
            [$nama, $badan, $status, $id]
        );
    }

    public function ubahStatus(int $id, string $status): void
    {
        DB::update('UPDATE vendor SET status = ? WHERE idvendor = ?', [$status, $id]);
    }

    public function dipakaiPengadaan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM pengadaan WHERE vendor_idvendor = ?', [$id])->n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM vendor WHERE idvendor = ?', [$id]);
    }
}
