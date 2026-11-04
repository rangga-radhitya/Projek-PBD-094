<?php

namespace App\Http\Controllers;

use App\Services\KartuStokService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KartuStokController extends Controller
{
    public function __construct(private KartuStokService $layanan) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idbarang = (string) $request->query('idbarang', '');
        $jenis = (string) $request->query('jenis', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = $this->layanan->daftar($q, $idbarang, $jenis, $dari, $sampai, 20, $request);
        $daftarBarang = $this->layanan->daftarBarang();
        $daftarJenis = $this->layanan->daftarJenis();

        return view('kartu_stok.index', compact('daftar', 'q', 'idbarang', 'jenis', 'dari', 'sampai', 'daftarBarang', 'daftarJenis'));
    }
}
