<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KartuStok;
use App\Models\MarginPenjualan;
use App\Models\Pengadaan;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Pengguna;
use App\Models\Retur;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Ringkasan: seluruh angka dihitung langsung dari database (READ saja)
    public function index(): View
    {
        $hari = now()->toDateString(); // zona waktu aplikasi (config/app.php: UTC)

        $ringkas = [
            'barang_aktif' => Barang::where('status', 1)->count(),
            'vendor_aktif' => Vendor::where('status', 'A')->count(),
            'user' => Pengguna::count(),
            'pengadaan' => Pengadaan::count(),
            'penerimaan' => Penerimaan::count(),
            'penjualan' => Penjualan::count(),
            'retur' => Retur::count(),
            'kartu_stok' => KartuStok::count(),
            'penjualan_hari_ini' => Penjualan::whereDate('created_at', $hari)->count(),
            'nilai_hari_ini' => (int) Penjualan::whereDate('created_at', $hari)->sum('total_nilai'),
        ];

        $marginAktif = MarginPenjualan::where('status', 1)->orderBy('idmargin_penjualan')->get();
        $penjualanTerakhir = Penjualan::with('user')->orderByDesc('idpenjualan')->limit(5)->get();
        $pengadaanTerakhir = Pengadaan::with('vendor')->orderByDesc('idpengadaan')->limit(5)->get();

        return view('dashboard.index', compact('ringkas', 'marginAktif', 'penjualanTerakhir', 'pengadaanTerakhir', 'hari'));
    }
}
