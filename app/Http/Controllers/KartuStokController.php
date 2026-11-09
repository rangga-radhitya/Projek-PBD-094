<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KartuStokController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 20, $request);

        return view('kartu_stok.index', compact('daftar', 'q'));
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
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

        $sql .= ' ORDER BY ks.created_at, ks.idkartu_stok';

        $perHalaman = max(1, $perHalaman);
        $total = (int) (DB::selectOne('SELECT COUNT(*) AS jumlah FROM ('.$sql.') AS hasil', $bind)->jumlah ?? 0);

        $halaman = max(1, (int) LengthAwarePaginator::resolveCurrentPage());
        $offset = ($halaman - 1) * $perHalaman;

        $baris = $total === 0
            ? []
            : DB::select($sql.' LIMIT '.$perHalaman.' OFFSET '.$offset, $bind);

        return new LengthAwarePaginator($baris, $total, $perHalaman, $halaman, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
