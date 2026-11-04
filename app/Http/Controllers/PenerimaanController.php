<?php

namespace App\Http\Controllers;

use App\Models\Penerimaan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerimaanController extends Controller
{
    // Daftar penerimaan (READ saja)
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idvendor = (string) $request->query('idvendor', '');
        $status = (string) $request->query('status', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = Penerimaan::query()
            ->with(['pengadaan.vendor', 'user'])
            ->withCount('detail')
            ->when($q !== '', fn ($w) => ctype_digit($q)
                ? $w->where(fn ($x) => $x->where('idpenerimaan', $q)->orWhere('idpengadaan', $q))
                : $w->whereHas('pengadaan.vendor', fn ($v) => $v->where('nama_vendor', 'like', "%{$q}%")))
            ->when($idvendor !== '', fn ($w) => $w->whereHas('pengadaan', fn ($p) => $p->where('vendor_idvendor', $idvendor)))
            ->when($status !== '', fn ($w) => $w->where('status', $status))
            ->when($dari !== '', fn ($w) => $w->whereDate('created_at', '>=', $dari))
            ->when($sampai !== '', fn ($w) => $w->whereDate('created_at', '<=', $sampai))
            ->orderByDesc('idpenerimaan')
            ->paginate(15)
            ->withQueryString();

        $daftarVendor = Vendor::orderBy('nama_vendor')->get();
        $daftarStatus = Penerimaan::whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('penerimaan.index', compact('daftar', 'q', 'idvendor', 'status', 'dari', 'sampai', 'daftarVendor', 'daftarStatus'));
    }

    // Detail penerimaan (READ saja)
    public function show(int $id): View
    {
        $penerimaan = Penerimaan::with(['pengadaan.vendor', 'user', 'detail.barang', 'retur.user'])->findOrFail($id);

        return view('penerimaan.show', compact('penerimaan'));
    }
}
