<?php

namespace App\Http\Controllers;

use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(private VendorService $layanan) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->layanan->daftar($q, 15, $request);

        return view('vendor.index', compact('daftar', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'nama_vendor' => ['required', 'string', 'max:100'],
                'badan_hukum' => ['required', 'in:P,C,U'],
                'status' => ['required', 'in:A,N'],
            ],
            [],
            ['nama_vendor' => 'nama vendor', 'badan_hukum' => 'badan hukum', 'status' => 'status']
        );

        if ($this->layanan->namaSudahAda($data['nama_vendor'])) {
            throw ValidationException::withMessages(['nama_vendor' => 'Nama vendor sudah dipakai.']);
        }

        $this->layanan->tambah($data['nama_vendor'], $data['badan_hukum'], $data['status']);

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak ditemukan.');
        }

        $data = $request->validate(
            [
                'nama_vendor' => ['required', 'string', 'max:100'],
                'badan_hukum' => ['required', 'in:P,C,U'],
                'status' => ['required', 'in:A,N'],
            ],
            [],
            ['nama_vendor' => 'nama vendor', 'badan_hukum' => 'badan hukum', 'status' => 'status']
        );

        if ($this->layanan->namaSudahAda($data['nama_vendor'], $id)) {
            throw ValidationException::withMessages(['nama_vendor' => 'Nama vendor sudah dipakai.']);
        }

        $this->layanan->ubah($id, $data['nama_vendor'], $data['badan_hukum'], $data['status']);

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $vendor = $this->layanan->cari($id);

        if ($vendor === null) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak ditemukan.');
        }

        $baru = $vendor->status === 'A' ? 'N' : 'A';
        $this->layanan->ubahStatus($id, $baru);

        return redirect()->route('vendor')->with('sukses', 'Status vendor berhasil diubah menjadi '.($baru === 'A' ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak ditemukan.');
        }

        if ($this->layanan->dipakaiPengadaan($id)) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak bisa dihapus karena masih dipakai pengadaan.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil dihapus.');
    }
}
