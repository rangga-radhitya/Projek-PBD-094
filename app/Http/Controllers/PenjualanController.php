<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

        return view('penjualan.index', compact('daftar', 'q'));
    }

    public function show(int $id): View
    {
        $penjualan = $this->cari($id);

        abort_if($penjualan === null, 404);

        $totalDetail = $this->totalPenjualan($id);

        return view('penjualan.show', compact('penjualan', 'totalDetail'));
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai,
                       DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       u.username, m.persen,
                       (SELECT COUNT(*) FROM detail_penjualan d WHERE d.penjualan_idpenjualan = p.idpenjualan) AS detail_count
                FROM penjualan p
                LEFT JOIN `user` u ON u.iduser = p.iduser
                LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(p.idpenjualan = ? OR u.username LIKE ?)';
                $bind[] = (int) $q;
                $bind[] = '%'.$q.'%';
            } else {
                $where[] = 'u.username LIKE ?';
                $bind[] = '%'.$q.'%';
            }
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.idpenjualan';

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

    private function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai, p.idmargin_penjualan,
                    DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username, m.persen
             FROM penjualan p
             LEFT JOIN `user` u ON u.iduser = p.iduser
             LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan
             WHERE p.idpenjualan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_penjualan, d.idbarang, d.harga_satuan, d.jumlah, d.subtotal,
                    b.nama, s.nama_satuan
             FROM detail_penjualan d
             LEFT JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.penjualan_idpenjualan = ?
             ORDER BY d.iddetail_penjualan',
            [$id]
        );

        return $header;
    }

    /**
     * Total satu transaksi penjualan dari detail (jumlah × harga satuan).
     * Null bila transaksi tidak ada, "0" bila transaksi belum punya detail.
     */
    private function totalPenjualan(int $idpenjualan): ?string
    {
        $ada = DB::selectOne('SELECT 1 AS x FROM penjualan WHERE idpenjualan = ?', [$idpenjualan]);

        if ($ada === null) {
            return null;
        }

        $total = DB::selectOne(
            'SELECT COALESCE(SUM(subtotal), 0) AS total
             FROM detail_penjualan
             WHERE penjualan_idpenjualan = ?',
            [$idpenjualan]
        )->total;

        return (string) $total;
    }
}
