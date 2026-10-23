<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\View\View;

class RoleController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = Role::orderBy('idrole')->get();

        return view('role.index', compact('daftar'));
    }
}
