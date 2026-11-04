<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    // Menampilkan data dari view (READ saja) + pencarian, filter, pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $badan = (string) $request->query('badan_hukum', '');
        $status = (string) $request->query('status', '');

        $daftar = Vendor::query()
            ->when($q !== '', fn ($w) => $w->where('nama_vendor', 'like', "%{$q}%"))
            ->when(in_array($badan, ['P', 'C', 'U'], true), fn ($w) => $w->where('badan_hukum', $badan))
            // View menganggap status selain 'A' sebagai Nonaktif
            ->when($status === 'A', fn ($w) => $w->where('status', 'A'))
            ->when($status === 'N', fn ($w) => $w->where(fn ($x) => $x->where('status', '!=', 'A')->orWhereNull('status')))
            ->orderBy('idvendor')
            ->paginate(15)
            ->withQueryString();

        return view('vendor.index', compact('daftar', 'q', 'badan', 'status'));
    }
}
