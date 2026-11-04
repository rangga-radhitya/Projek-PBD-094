<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $layanan) {}

    public function index(): View
    {
        $hari = now()->toDateString();

        $ringkas = $this->layanan->ringkasan($hari);
        $marginAktif = $this->layanan->marginAktif();
        $penjualanTerakhir = $this->layanan->penjualanTerakhir();
        $pengadaanTerakhir = $this->layanan->pengadaanTerakhir();

        return view('dashboard.index', compact('ringkas', 'marginAktif', 'penjualanTerakhir', 'pengadaanTerakhir', 'hari'));
    }
}
