<?php

namespace App\Http\Controllers;

use App\Models\MarginPenjualan;
use Illuminate\View\View;

class MarginPenjualanController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = MarginPenjualan::orderBy('idmargin_penjualan')->get();

        return view('margin_penjualan.index', compact('daftar'));
    }
}
