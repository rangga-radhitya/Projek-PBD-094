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
    public function daftar(string $q, string $idbarang, string $jenis, string $dari, string $sampai, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT ks.idkartu_stok, ks.jenis_transaksi, ks.masuk, ks.keluar, ks.stock, ks.idtransaksi, ks.idbarang,
                       DATE_FORMAT(ks.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       b.nama AS nama_barang
                FROM kartu_stok ks
                LEFT JOIN barang b ON b.idbarang = ks.idbarang";
        $where = [];
        $bind = [];

        if ($q !== '' && ctype_digit($q)) {
            $where[] = 'ks.idtransaksi = ?';
            $bind[] = (int) $q;
        }

        if ($idbarang !== '' && ctype_digit($idbarang)) {
            $where[] = 'ks.idbarang = ?';
            $bind[] = (int) $idbarang;
        }

        if ($jenis !== '') {
            $where[] = 'ks.jenis_transaksi = ?';
            $bind[] = $jenis;
        }

        if ($dari !== '') {
            $where[] = 'ks.created_at >= ?';
            $bind[] = $dari.' 00:00:00';
        }

        if ($sampai !== '') {
            $where[] = 'ks.created_at <= ?';
            $bind[] = $sampai.' 23:59:59';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY ks.created_at DESC, ks.idkartu_stok DESC', $bind, $perHalaman, $request);
    }

    /** @return array<int, object> */
    public function daftarBarang(): array
    {
        return DB::select('SELECT idbarang, nama FROM barang ORDER BY nama');
    }

    /** @return array<int, string> */
    public function daftarJenis(): array
    {
        $baris = DB::select('SELECT DISTINCT jenis_transaksi FROM kartu_stok WHERE jenis_transaksi IS NOT NULL ORDER BY jenis_transaksi');

        return array_map(fn ($r) => $r->jenis_transaksi, $baris);
    }
}
