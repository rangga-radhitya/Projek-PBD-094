<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q);
        $daftarSatuan = $this->semuaSatuan();

        $opsiSatuan = [];
        foreach ($daftarSatuan as $s) {
            $opsiSatuan[(string) $s->idsatuan] = $s->nama_satuan;
        }

        return view('barang.index', compact('daftar', 'q', 'daftarSatuan', 'opsiSatuan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($this->namaSudahAda($data['nama'])) {
            throw ValidationException::withMessages(['nama' => 'Nama barang sudah dipakai.']);
        }

        DB::insert(
            'INSERT INTO barang (nama, jenis, idsatuan, harga, status) VALUES (?, ?, ?, ?, ?)',
            [$data['nama'], $data['jenis'], (int) $data['idsatuan'], (int) $data['harga'], (int) $data['status']]
        );

        return redirect()->route('barang')->with('sukses', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        $data = $this->validasi($request);

        if ($this->namaSudahAda($data['nama'], $id)) {
            throw ValidationException::withMessages(['nama' => 'Nama barang sudah dipakai.']);
        }

        DB::update(
            'UPDATE barang SET nama = ?, jenis = ?, idsatuan = ?, harga = ?, status = ? WHERE idbarang = ?',
            [$data['nama'], $data['jenis'], (int) $data['idsatuan'], (int) $data['harga'], (int) $data['status'], $id]
        );

        return redirect()->route('barang')->with('sukses', 'Barang berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $barang = $this->cari($id);

        if ($barang === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        $baru = (int) $barang->status === 1 ? 0 : 1;
        DB::update('UPDATE barang SET status = ? WHERE idbarang = ?', [$baru, $id]);

        return redirect()->route('barang')->with('sukses', 'Status barang berhasil diubah menjadi '.($baru === 1 ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak ditemukan.');
        }

        if ($this->dipakaiTransaksi($id)) {
            return redirect()->route('barang')->with('gagal', 'Barang tidak bisa dihapus karena sudah dipakai pada transaksi atau kartu stok.');
        }

        DB::delete('DELETE FROM barang WHERE idbarang = ?', [$id]);

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

        if ($this->cariSatuan((int) $data['idsatuan']) === null) {
            throw ValidationException::withMessages(['idsatuan' => 'Satuan yang dipilih tidak ada.']);
        }

        return $data;
    }

    /** @return array<int, object> */
    private function daftar(string $q): array
    {
        $sql = 'SELECT vb.idbarang, vb.nama, vb.jenis, vb.jenis_teks, vb.idsatuan, vb.nama_satuan,
                       vb.harga, vb.status,
                       COALESCE((SELECT ks.stock FROM kartu_stok ks
                                  WHERE ks.idbarang = vb.idbarang
                                  ORDER BY ks.idkartu_stok DESC LIMIT 1), 0) AS stok
                FROM v_barang vb';
        $bind = [];

        if ($q !== '') {
            $sql .= ' WHERE vb.nama LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        $sql .= ' ORDER BY vb.idbarang';

        return DB::select($sql, $bind);
    }

    /** @return array<int, object> */
    private function semua(): array
    {
        return DB::select('SELECT idbarang, nama, harga, idsatuan, status FROM barang ORDER BY nama');
    }

    /** @return array<int, object> */
    private function aktif(): array
    {
        return DB::select('SELECT idbarang, nama, harga FROM barang WHERE status = 1 ORDER BY nama');
    }

    /** @return array<int, object> */
    private function semuaSatuan(): array
    {
        return DB::select('SELECT idsatuan, nama_satuan FROM satuan ORDER BY nama_satuan');
    }

    private function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idbarang, nama, jenis, idsatuan, harga, status FROM barang WHERE idbarang = ?', [$id]);
    }

    private function cariSatuan(int $id): ?object
    {
        return DB::selectOne('SELECT idsatuan, nama_satuan, status FROM satuan WHERE idsatuan = ?', [$id]);
    }

    private function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM barang WHERE nama = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idbarang <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    private function dipakaiTransaksi(int $id): bool
    {
        $n = (int) DB::selectOne(
            'SELECT (SELECT COUNT(*) FROM detail_pengadaan WHERE idbarang = ?)
                  + (SELECT COUNT(*) FROM detail_penerimaan WHERE barang_idbarang = ?)
                  + (SELECT COUNT(*) FROM detail_penjualan WHERE idbarang = ?)
                  + (SELECT COUNT(*) FROM kartu_stok WHERE idbarang = ?) AS n',
            [$id, $id, $id, $id]
        )->n;

        return $n > 0;
    }
}
