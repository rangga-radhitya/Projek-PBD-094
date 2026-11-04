<?php

namespace App\Http\Controllers;

use App\Models\MarginPenjualan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarginPenjualanController extends Controller
{
    // Menampilkan data dari view (READ saja) + filter status, pagination
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $daftar = MarginPenjualan::query()
            ->when(in_array($status, ['0', '1'], true), fn ($w) => $w->where('status', (int) $status))
            ->orderBy('idmargin_penjualan')
            ->paginate(15)
            ->withQueryString();

        // Margin aktif (status = 1) untuk ditandai di atas daftar
        $aktif = MarginPenjualan::where('status', 1)->orderBy('idmargin_penjualan')->get();

        return view('margin_penjualan.index', compact('daftar', 'status', 'aktif'));
    }
}
