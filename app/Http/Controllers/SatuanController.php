<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\View\View;

class SatuanController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = Satuan::orderBy('idsatuan')->get();

        return view('satuan.index', compact('daftar'));
    }
}
