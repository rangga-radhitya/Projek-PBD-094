<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data akun pengguna. Baca dari VIEW v_user (tanpa kolom password),
 * tulis ke tabel dasar `user`.
 */
class PenggunaService
{
    public function daftar(string $q, string $idrole, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT iduser, username, idrole, nama_role FROM v_user';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'username LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($idrole !== '' && ctype_digit($idrole)) {
            $where[] = 'idrole = ?';
            $bind[] = (int) $idrole;
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY iduser', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function semua(): array
    {
        return DB::select('SELECT iduser, username FROM `user` ORDER BY username');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT iduser, username, idrole FROM `user` WHERE iduser = ?', [$id]);
    }

    public function usernameSudahAda(string $username, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM `user` WHERE username = ?';
        $bind = [$username];

        if ($kecuali !== null) {
            $sql .= ' AND iduser <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    public function roleAda(int $idrole): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM role WHERE idrole = ?', [$idrole])->n > 0;
    }

    public function tambah(string $username, string $password, int $idrole): int
    {
        DB::insert(
            'INSERT INTO `user` (username, password, idrole) VALUES (?, ?, ?)',
            [$username, Hash::make($password), $idrole]
        );

        return (int) DB::getPdo()->lastInsertId();
    }

    public function ubah(int $id, string $username, int $idrole, ?string $password = null): void
    {
        if ($password !== null && $password !== '') {
            DB::update(
                'UPDATE `user` SET username = ?, idrole = ?, password = ? WHERE iduser = ?',
                [$username, $idrole, Hash::make($password), $id]
            );

            return;
        }

        DB::update('UPDATE `user` SET username = ?, idrole = ? WHERE iduser = ?', [$username, $idrole, $id]);
    }

    public function dipakaiTransaksi(int $id): bool
    {
        $n = (int) DB::selectOne(
            'SELECT (SELECT COUNT(*) FROM pengadaan WHERE user_iduser = ?)
                  + (SELECT COUNT(*) FROM penerimaan WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM penjualan WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM retur WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM margin_penjualan WHERE iduser = ?) AS n',
            [$id, $id, $id, $id, $id]
        )->n;

        return $n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM `user` WHERE iduser = ?', [$id]);
    }
}
