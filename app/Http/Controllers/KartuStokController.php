<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KartuStok;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KartuStokController extends Controller
{
    // Riwayat pergerakan stok (READ saja)
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idbarang = (string) $request->query('idbarang', '');
        $jenis = (string) $request->query('jenis', '');
        $dari = $this->tanggal($request, 'dari');
        $sampai = $this->tanggal($request, 'sampai');

        $daftar = KartuStok::query()
            ->with('barang')
            ->when($q !== '' && ctype_digit($q), fn ($w) => $w->where('idtransaksi', $q))
            ->when($idbarang !== '', fn ($w) => $w->where('idbarang', $idbarang))
            ->when($jenis !== '', fn ($w) => $w->where('jenis_transaksi', $jenis))
            ->when($dari !== '', fn ($w) => $w->whereDate('created_at', '>=', $dari))
            ->when($sampai !== '', fn ($w) => $w->whereDate('created_at', '<=', $sampai))
            ->orderByDesc('created_at')
            ->orderByDesc('idkartu_stok')
            ->paginate(20)
            ->withQueryString();

        $daftarBarang = Barang::orderBy('nama')->get();
        // Kode jenis transaksi diambil dari data yang ada (arti kode belum ditetapkan)
        $daftarJenis = KartuStok::whereNotNull('jenis_transaksi')->distinct()->orderBy('jenis_transaksi')->pluck('jenis_transaksi');

        return view('kartu_stok.index', compact('daftar', 'q', 'idbarang', 'jenis', 'dari', 'sampai', 'daftarBarang', 'daftarJenis'));
    }
}
