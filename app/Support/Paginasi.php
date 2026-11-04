<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Pagination untuk hasil SQL mentah (tanpa Eloquent / Query Builder).
 *
 * LIMIT dan OFFSET diinterpolasi setelah cast (int) karena koneksi MySQL
 * proyek ini memakai prepared statement native (ATTR_EMULATE_PREPARES = OFF),
 * sehingga LIMIT ? / OFFSET ? tidak dapat diikat sebagai parameter.
 */
class Paginasi
{
    public static function buat(string $sql, array $bindings, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $perHalaman = max(1, $perHalaman);

        $total = (int) (DB::selectOne('SELECT COUNT(*) AS jumlah FROM ('.$sql.') AS hasil', $bindings)->jumlah ?? 0);

        $halaman = max(1, (int) LengthAwarePaginator::resolveCurrentPage());
        $offset = ($halaman - 1) * $perHalaman;

        $baris = $total === 0
            ? []
            : DB::select($sql.' LIMIT '.$perHalaman.' OFFSET '.$offset, $bindings);

        return new LengthAwarePaginator($baris, $total, $perHalaman, $halaman, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
