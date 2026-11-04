<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturController extends Controller
{
    // Daftar retur (READ saja). Struktur DB: retur mengacu ke penerimaan (retur pembelian ke vendor).
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idvendor = (string) $request->query('idvendor', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = Retur::query()
            ->with(['penerimaan.pengadaan.vendor', 'user'])
            ->withCount('detail')
            ->when($q !== '' && ctype_digit($q), fn ($w) => $w->where(fn ($x) => $x->where('idretur', $q)->orWhere('idpenerimaan', $q)))
            ->when($idvendor !== '', fn ($w) => $w->whereHas('penerimaan.pengadaan', fn ($p) => $p->where('vendor_idvendor', $idvendor)))
            ->when($dari !== '', fn ($w) => $w->whereDate('created_at', '>=', $dari))
            ->when($sampai !== '', fn ($w) => $w->whereDate('created_at', '<=', $sampai))
            ->orderByDesc('idretur')
            ->paginate(15)
            ->withQueryString();

        $daftarVendor = Vendor::orderBy('nama_vendor')->get();

        return view('retur.index', compact('daftar', 'q', 'idvendor', 'dari', 'sampai', 'daftarVendor'));
    }

    // Detail retur (READ saja)
    public function show(int $id): View
    {
        $retur = Retur::with(['penerimaan.pengadaan.vendor', 'user', 'detail.detailPenerimaan.barang'])->findOrFail($id);

        return view('retur.show', compact('retur'));
    }
}
