<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\View\View;

class VendorController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = Vendor::orderBy('idvendor')->get();

        return view('vendor.index', compact('daftar'));
    }
}
