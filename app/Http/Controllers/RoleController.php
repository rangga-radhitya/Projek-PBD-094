<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private RoleService $layanan) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->layanan->daftar($q, 15, $request);

        return view('role.index', compact('daftar', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['nama_role' => ['required', 'string', 'max:100']],
            [],
            ['nama_role' => 'nama role']
        );

        if ($this->layanan->namaSudahAda($data['nama_role'])) {
            throw ValidationException::withMessages(['nama_role' => 'Nama role sudah dipakai.']);
        }

        $this->layanan->tambah($data['nama_role']);

        return redirect()->route('role')->with('sukses', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('role')->with('gagal', 'Role tidak ditemukan.');
        }

        $data = $request->validate(
            ['nama_role' => ['required', 'string', 'max:100']],
            [],
            ['nama_role' => 'nama role']
        );

        if ($this->layanan->namaSudahAda($data['nama_role'], $id)) {
            throw ValidationException::withMessages(['nama_role' => 'Nama role sudah dipakai.']);
        }

        $this->layanan->ubah($id, $data['nama_role']);

        return redirect()->route('role')->with('sukses', 'Role berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('role')->with('gagal', 'Role tidak ditemukan.');
        }

        if ($this->layanan->dipakaiUser($id)) {
            return redirect()->route('role')->with('gagal', 'Role tidak bisa dihapus karena masih dipakai user.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('role')->with('sukses', 'Role berhasil dihapus.');
    }
}
