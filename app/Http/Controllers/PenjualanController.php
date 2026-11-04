<?php

namespace App\Http\Controllers;

use App\Services\PenggunaService;
use App\Services\PenjualanService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PDOException;

class PenjualanController extends Controller
{
    public function __construct(
        private PenjualanService $layanan,
        private PenggunaService $pengguna
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $iduser = (string) $request->query('iduser', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = $this->layanan->daftar($q, $iduser, $dari, $sampai, 15, $request);
        $daftarUser = $this->pengguna->semua();

        return view('penjualan.index', compact('daftar', 'q', 'iduser', 'dari', 'sampai', 'daftarUser'));
    }

    public function show(int $id): View
    {
        $penjualan = $this->layanan->cari($id);

        abort_if($penjualan === null, 404);

        $totalFungsi = null;

        try {
            $totalFungsi = $this->layanan->totalPenjualan($id);
        } catch (PDOException $e) {
            if (! PenjualanService::objekBelumTerpasang($e)) {
                report($e);
            }
        }

        return view('penjualan.show', compact('penjualan', 'totalFungsi'));
    }
}
