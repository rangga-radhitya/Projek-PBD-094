<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Data role. Baca dari VIEW v_role, tulis ke tabel dasar role.
 * Semua query memakai SQL mentah (tanpa ORM / Query Builder).
 */
class RoleService
{
    public function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idrole, nama_role FROM v_role';
        $bind = [];

        if ($q !== '') {
            $sql .= ' WHERE nama_role LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        return Paginasi::buat($sql.' ORDER BY idrole', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function semua(): array
    {
        return DB::select('SELECT idrole, nama_role FROM v_role ORDER BY nama_role');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idrole, nama_role FROM v_role WHERE idrole = ?', [$id]);
    }

    public function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM role WHERE nama_role = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idrole <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    public function tambah(string $nama): int
    {
        DB::insert('INSERT INTO role (nama_role) VALUES (?)', [$nama]);

        return (int) DB::getPdo()->lastInsertId();
    }

    public function ubah(int $id, string $nama): void
    {
        DB::update('UPDATE role SET nama_role = ? WHERE idrole = ?', [$nama, $id]);
    }

    public function dipakaiUser(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM `user` WHERE idrole = ?', [$id])->n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM role WHERE idrole = ?', [$id]);
    }
}
