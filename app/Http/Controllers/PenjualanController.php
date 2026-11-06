<?php

namespace App\Http\Controllers;

use App\Services\PenjualanService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function __construct(private PenjualanService $layanan) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->layanan->daftar($q, 15, $request);

        return view('penjualan.index', compact('daftar', 'q'));
    }

    public function show(int $id): View
    {
        $penjualan = $this->layanan->cari($id);

        abort_if($penjualan === null, 404);

        $totalDetail = $this->layanan->totalPenjualan($id);

        return view('penjualan.show', compact('penjualan', 'totalDetail'));
    }
}
