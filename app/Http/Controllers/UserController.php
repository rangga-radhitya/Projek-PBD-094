<?php

namespace App\Http\Controllers;

use App\Services\PenggunaService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private PenggunaService $layanan,
        private RoleService $role
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->layanan->daftar($q, 15, $request);
        $daftarRole = $this->role->semua();

        return view('user.index', compact('daftar', 'q', 'daftarRole'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'username' => ['required', 'string', 'max:45'],
                'password' => ['required', 'string', 'min:6', 'max:100'],
                'idrole' => ['required', 'integer'],
            ],
            [],
            ['username' => 'username', 'password' => 'password', 'idrole' => 'role']
        );

        if ($this->layanan->usernameSudahAda($data['username'])) {
            throw ValidationException::withMessages(['username' => 'Username sudah dipakai.']);
        }

        if (! $this->layanan->roleAda((int) $data['idrole'])) {
            throw ValidationException::withMessages(['idrole' => 'Role yang dipilih tidak ada.']);
        }

        $this->layanan->tambah($data['username'], $data['password'], (int) $data['idrole']);

        return redirect()->route('user')->with('sukses', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('user')->with('gagal', 'User tidak ditemukan.');
        }

        $data = $request->validate(
            [
                'username' => ['required', 'string', 'max:45'],
                'password' => ['nullable', 'string', 'min:6', 'max:100'],
                'idrole' => ['required', 'integer'],
            ],
            [],
            ['username' => 'username', 'password' => 'password', 'idrole' => 'role']
        );

        if ($this->layanan->usernameSudahAda($data['username'], $id)) {
            throw ValidationException::withMessages(['username' => 'Username sudah dipakai.']);
        }

        if (! $this->layanan->roleAda((int) $data['idrole'])) {
            throw ValidationException::withMessages(['idrole' => 'Role yang dipilih tidak ada.']);
        }

        $this->layanan->ubah($id, $data['username'], (int) $data['idrole'], $data['password'] ?? null);

        return redirect()->route('user')->with('sukses', 'User berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('user')->with('gagal', 'User tidak ditemukan.');
        }

        if ($this->layanan->dipakaiTransaksi($id)) {
            return redirect()->route('user')->with('gagal', 'User tidak bisa dihapus karena sudah dipakai pada transaksi.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('user')->with('sukses', 'User berhasil dihapus.');
    }
}
