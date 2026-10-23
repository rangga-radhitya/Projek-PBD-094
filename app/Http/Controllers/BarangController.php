<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\View\View;

class BarangController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = Barang::orderBy('idbarang')->get();

        return view('barang.index', compact('daftar'));
    }
}
