<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Margin penjualan. Baca dari VIEW v_margin_penjualan, tulis ke tabel dasar margin_penjualan.
 */
class MarginPenjualanService
{
    public function daftar(string $status, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT idmargin_penjualan, persen, status, iduser, username,
                       DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') AS dibuat,
                       DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') AS diubah
                FROM v_margin_penjualan";
        $bind = [];

        if (in_array($status, ['0', '1'], true)) {
            $sql .= ' WHERE status = ?';
            $bind[] = (int) $status;
        }

        return Paginasi::buat($sql.' ORDER BY idmargin_penjualan', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function aktif(): array
    {
        return DB::select('SELECT idmargin_penjualan, persen FROM v_margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan');
    }

    public function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idmargin_penjualan, persen, status, iduser FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
    }

    public function tambah(float $persen, int $status, int $iduser): int
    {
        return DB::transaction(function () use ($persen, $status, $iduser) {
            if ($status === 1) {
                DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1');
            }

            DB::insert(
                'INSERT INTO margin_penjualan (created_at, persen, status, iduser, updated_at) VALUES (NOW(), ?, ?, ?, NOW())',
                [$persen, $status, $iduser]
            );

            return (int) DB::getPdo()->lastInsertId();
        });
    }

    public function ubah(int $id, float $persen, int $status): void
    {
        DB::transaction(function () use ($id, $persen, $status) {
            if ($status === 1) {
                DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1 AND idmargin_penjualan <> ?', [$id]);
            }

            DB::update(
                'UPDATE margin_penjualan SET persen = ?, status = ?, updated_at = NOW() WHERE idmargin_penjualan = ?',
                [$persen, $status, $id]
            );
        });
    }

    public function aktifkan(int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1');
            DB::update('UPDATE margin_penjualan SET status = 1, updated_at = NOW() WHERE idmargin_penjualan = ?', [$id]);
        });
    }

    public function dipakaiPenjualan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM penjualan WHERE idmargin_penjualan = ?', [$id])->n > 0;
    }

    public function hapus(int $id): void
    {
        DB::delete('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
    }
}
