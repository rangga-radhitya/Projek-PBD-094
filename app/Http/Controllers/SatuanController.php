<?php

namespace App\Http\Controllers;

use App\Services\SatuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SatuanController extends Controller
{
    public function __construct(private SatuanService $layanan) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->layanan->daftar($q, 15, $request);

        return view('satuan.index', compact('daftar', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'nama_satuan' => ['required', 'string', 'max:45'],
                'status' => ['required', 'in:0,1'],
            ],
            [],
            ['nama_satuan' => 'nama satuan', 'status' => 'status']
        );

        if ($this->layanan->namaSudahAda($data['nama_satuan'])) {
            throw ValidationException::withMessages(['nama_satuan' => 'Nama satuan sudah dipakai.']);
        }

        $this->layanan->tambah($data['nama_satuan'], (int) $data['status']);

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak ditemukan.');
        }

        $data = $request->validate(
            [
                'nama_satuan' => ['required', 'string', 'max:45'],
                'status' => ['required', 'in:0,1'],
            ],
            [],
            ['nama_satuan' => 'nama satuan', 'status' => 'status']
        );

        if ($this->layanan->namaSudahAda($data['nama_satuan'], $id)) {
            throw ValidationException::withMessages(['nama_satuan' => 'Nama satuan sudah dipakai.']);
        }

        $this->layanan->ubah($id, $data['nama_satuan'], (int) $data['status']);

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $satuan = $this->layanan->cari($id);

        if ($satuan === null) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak ditemukan.');
        }

        $baru = (int) $satuan->status === 1 ? 0 : 1;
        $this->layanan->ubahStatus($id, $baru);

        return redirect()->route('satuan')->with('sukses', 'Status satuan berhasil diubah menjadi '.($baru === 1 ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak ditemukan.');
        }

        if ($this->layanan->dipakaiBarang($id)) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak bisa dihapus karena masih dipakai barang.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil dihapus.');
    }
}
