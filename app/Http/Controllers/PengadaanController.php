<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaan;
use App\Models\Pengadaan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengadaanController extends Controller
{
    // Daftar pengadaan (READ saja): pencarian, filter vendor/status/tanggal, pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idvendor = (string) $request->query('idvendor', '');
        $status = (string) $request->query('status', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = Pengadaan::query()
            ->with(['vendor', 'user'])
            ->when($q !== '', fn ($w) => ctype_digit($q)
                ? $w->where('idpengadaan', $q)
                : $w->whereHas('vendor', fn ($v) => $v->where('nama_vendor', 'like', "%{$q}%")))
            ->when($idvendor !== '', fn ($w) => $w->where('vendor_idvendor', $idvendor))
            ->when($status !== '', fn ($w) => $w->where('status', $status))
            ->when($dari !== '', fn ($w) => $w->whereDate('timestamp', '>=', $dari))
            ->when($sampai !== '', fn ($w) => $w->whereDate('timestamp', '<=', $sampai))
            ->orderByDesc('idpengadaan')
            ->paginate(15)
            ->withQueryString();

        $daftarVendor = Vendor::orderBy('nama_vendor')->get();
        // Kode status diambil dari data yang ada (arti kode belum ditetapkan)
        $daftarStatus = Pengadaan::whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('pengadaan.index', compact('daftar', 'q', 'idvendor', 'status', 'dari', 'sampai', 'daftarVendor', 'daftarStatus'));
    }

    // Detail pengadaan (READ saja)
    public function show(int $id): View
    {
        $pengadaan = Pengadaan::with(['vendor', 'user', 'detail.barang', 'penerimaan.user'])->findOrFail($id);

        // Jumlah yang sudah diterima per barang, dijumlahkan dari seluruh penerimaan untuk pengadaan ini
        $diterima = DetailPenerimaan::query()
            ->join('penerimaan', 'penerimaan.idpenerimaan', '=', 'detail_penerimaan.idpenerimaan')
            ->where('penerimaan.idpengadaan', $id)
            ->groupBy('detail_penerimaan.barang_idbarang')
            ->selectRaw('detail_penerimaan.barang_idbarang as idbarang, SUM(detail_penerimaan.jumlah_terima) as total')
            ->pluck('total', 'idbarang');

        return view('pengadaan.show', compact('pengadaan', 'diterima'));
    }
}
