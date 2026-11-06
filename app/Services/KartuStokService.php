<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Riwayat pergerakan stok (kartu_stok). Baca saja.
 */
class KartuStokService
{
    public function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT ks.idkartu_stok, ks.jenis_transaksi, ks.masuk, ks.keluar, ks.stock, ks.idtransaksi, ks.idbarang,
                       DATE_FORMAT(ks.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       b.nama AS nama_barang
                FROM kartu_stok ks
                LEFT JOIN barang b ON b.idbarang = ks.idbarang";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(ks.idtransaksi = ? OR b.nama LIKE ?)';
                $bind[] = (int) $q;
                $bind[] = '%'.$q.'%';
            } else {
                $where[] = 'b.nama LIKE ?';
                $bind[] = '%'.$q.'%';
            }
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY ks.created_at DESC, ks.idkartu_stok DESC', $bind, $perHalaman, $request);
    }
}
