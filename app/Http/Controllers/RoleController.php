<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    // Menampilkan data dari view (READ saja) + pencarian & pagination
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = Role::query()
            ->when($q !== '', fn ($w) => $w->where('nama_role', 'like', "%{$q}%"))
            ->orderBy('idrole')
            ->paginate(15)
            ->withQueryString();

        return view('role.index', compact('daftar', 'q'));
    }
}
