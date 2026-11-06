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

        $daftar = $this->layanan->daftar($q, 20, $request);

        return view('kartu_stok.index', compact('daftar', 'q'));
    }
}
