<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\SatuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function __construct(
        private BarangService $layanan,
        private SatuanService $satuan
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $jenis = (string) $request->query('jenis', '');
        $idsatuan = (string) $request->query('idsatuan', '');
        $status = (string) $request->query('status', '');

        $daftar = $this->layanan->daftar($q, $jenis, $idsatuan, $status, 15, $request);
        $daftarSatuan = $this->satuan->semua();

        $opsiSatuan = [];
        foreach ($daftarSatuan as $s) {
            $opsiSatuan[(string) $s->idsatuan] = $s->nama_satuan;
        }

        return view('barang.index', compact('daftar', 'q', 'jenis', 'idsatuan', 'status', 'daftarSatuan', 'opsiSatuan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($this->layanan->namaSudahAda($data['nama'])) {
            throw ValidationException::withMessages(['nama' => 'Nama barang sudah dipakai.']);
        }

        $this->layanan->tambah(
            $data['nama'],
            $data['jenis'],
            (int) $data['idsatuan'],
            (int) $data['harga'],
            (int) $data['status']
        );

        return redirect()->route('barang')->with('sukses', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        $data = $this->validasi($request);

        if ($this->layanan->namaSudahAda($data['nama'], $id)) {
            throw ValidationException::withMessages(['nama' => 'Nama barang sudah dipakai.']);
        }

        $this->layanan->ubah(
            $id,
            $data['nama'],
            $data['jenis'],
            (int) $data['idsatuan'],
            (int) $data['harga'],
            (int) $data['status']
        );

        return redirect()->route('barang')->with('sukses', 'Barang berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $barang = $this->layanan->cari($id);

        if ($barang === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        $baru = (int) $barang->status === 1 ? 0 : 1;
        $this->layanan->ubahStatus($id, $baru);

        return redirect()->route('barang')->with('sukses', 'Status barang berhasil diubah menjadi '.($baru === 1 ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->layanan->cari($id) === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        if ($this->layanan->dipakaiTransaksi($id)) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak bisa dihapus karena sudah dipakai pada transaksi atau kartu stok.');
        }

        $this->layanan->hapus($id);

        return redirect()->route('barang')->with('sukses', 'Barang berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validasi(Request $request): array
    {
        $data = $request->validate(
            [
                'nama' => ['required', 'string', 'max:45'],
                'jenis' => ['required', 'in:B,D,C,P'],
                'idsatuan' => ['required', 'integer'],
                'harga' => ['required', 'integer', 'min:0'],
                'status' => ['required', 'in:0,1'],
            ],
            [],
            [
                'nama' => 'nama barang',
                'jenis' => 'jenis',
                'idsatuan' => 'satuan',
                'harga' => 'harga',
                'status' => 'status',
            ]
        );

        if ($this->satuan->cari((int) $data['idsatuan']) === null) {
            throw ValidationException::withMessages(['idsatuan' => 'Satuan yang dipilih tidak ada.']);
        }

        return $data;
    }
}
