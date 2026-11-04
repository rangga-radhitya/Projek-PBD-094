<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SatuanController extends Controller
{
    // Menampilkan data dari view (READ saja) + pencarian, filter status, pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $daftar = Satuan::query()
            ->when($q !== '', fn ($w) => $w->where('nama_satuan', 'like', "%{$q}%"))
            ->when(in_array($status, ['0', '1'], true), fn ($w) => $w->where('status', (int) $status))
            ->orderBy('idsatuan')
            ->paginate(15)
            ->withQueryString();

        return view('satuan.index', compact('daftar', 'q', 'status'));
    }
}
