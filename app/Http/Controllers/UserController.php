<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    // Menampilkan data dari view (READ saja) + pencarian, filter role, pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $idrole = (string) $request->query('idrole', '');

        $daftar = Pengguna::query()
            ->when($q !== '', fn ($w) => $w->where('username', 'like', "%{$q}%"))
            ->when($idrole !== '', fn ($w) => $w->where('idrole', $idrole))
            ->orderBy('iduser')
            ->paginate(15)
            ->withQueryString();

        $daftarRole = Role::orderBy('nama_role')->get();

        return view('user.index', compact('daftar', 'q', 'idrole', 'daftarRole'));
    }
}
