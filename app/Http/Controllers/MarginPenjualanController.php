<?php

namespace App\Http\Controllers;

use App\Services\MarginPenjualanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarginPenjualanController extends Controller
{
    public function __construct(private MarginPenjualanService $layanan) {}

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $daftar = $this->layanan->daftar($status, 15, $request);
        $aktif = $this->layanan->aktif();

        return view('margin_penjualan.index', compact('daftar', 'status', 'aktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'persen' => ['required', 'numeric', 'min:0', 'max:1000'],
                'status' => ['required', 'in:0,1'],
            ],
            [],
            ['persen' => 'persen margin', 'status' => 'status']
        );

        $this->layanan->tambah((float) $data['persen'], (int) $data['status'], self::ID_USER_DEFAULT);

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin penjualan tidak ditemukan.');
        }

        $data = $request->validate(
            [
                'persen' => ['required', 'numeric', 'min:0', 'max:1000'],
                'status' => ['required', 'in:0,1'],
            ],
            [],
            ['persen' => 'persen margin', 'status' => 'status']
        );

        $this->layanan->ubah($id, (float) $data['persen'], (int) $data['status']);

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil diperbarui.');
    }

    public function aktifkan(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin penjualan tidak ditemukan.');
        }

        $this->layanan->aktifkan($id);

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan diaktifkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin penjualan tidak ditemukan.');
        }

        if ($this->layanan->dipakaiPenjualan($id)) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin tidak bisa dihapus karena sudah dipakai penjualan.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil dihapus.');
    }
}
