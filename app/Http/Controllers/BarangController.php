<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    // Menampilkan data dari view (READ saja) + pencarian, filter, pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $jenis = (string) $request->query('jenis', '');
        $idsatuan = (string) $request->query('idsatuan', '');
        $status = (string) $request->query('status', '');

        $daftar = Barang::query()
            ->when($q !== '', fn ($w) => $w->where('nama', 'like', "%{$q}%"))
            ->when(in_array($jenis, ['B', 'D', 'C', 'P'], true), fn ($w) => $w->where('jenis', $jenis))
            ->when($idsatuan !== '', fn ($w) => $w->where('idsatuan', $idsatuan))
            ->when(in_array($status, ['0', '1'], true), fn ($w) => $w->where('status', (int) $status))
            ->orderBy('idbarang')
            ->paginate(15)
            ->withQueryString();

        $daftarSatuan = Satuan::orderBy('nama_satuan')->get();

        return view('barang.index', compact('daftar', 'q', 'jenis', 'idsatuan', 'status', 'daftarSatuan'));
    }
}
