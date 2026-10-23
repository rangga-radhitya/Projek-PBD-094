<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\View\View;

class UserController extends Controller
{
    // Menampilkan semua data dari view (READ saja)
    public function index(): View
    {
        $daftar = Pengguna::orderBy('iduser')->get();

        return view('user.index', compact('daftar'));
    }
}
