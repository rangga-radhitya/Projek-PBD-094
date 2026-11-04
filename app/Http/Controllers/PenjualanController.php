<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    // Daftar penjualan (READ saja)
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $iduser = (string) $request->query('iduser', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = Penjualan::query()
            ->with(['user', 'margin'])
            ->withCount('detail')
            ->when($q !== '' && ctype_digit($q), fn ($w) => $w->where('idpenjualan', $q))
            ->when($iduser !== '', fn ($w) => $w->where('iduser', $iduser))
            ->when($dari !== '', fn ($w) => $w->whereDate('created_at', '>=', $dari))
            ->when($sampai !== '', fn ($w) => $w->whereDate('created_at', '<=', $sampai))
            ->orderByDesc('idpenjualan')
            ->paginate(15)
            ->withQueryString();

        $daftarUser = Pengguna::orderBy('username')->get();

        return view('penjualan.index', compact('daftar', 'q', 'iduser', 'dari', 'sampai', 'daftarUser'));
    }

    // Detail penjualan (READ saja)
    public function show(int $id): View
    {
        $penjualan = Penjualan::with(['user', 'margin', 'detail.barang'])->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }
}
