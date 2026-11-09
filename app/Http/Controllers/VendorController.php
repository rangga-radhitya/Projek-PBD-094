<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

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

        if ($this->namaSudahAda($data['nama_vendor'])) {
            throw ValidationException::withMessages(['nama_vendor' => 'Nama vendor sudah dipakai.']);
        }

        DB::insert(
            'INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)',
            [$data['nama_vendor'], $data['badan_hukum'], $data['status']]
        );

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
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

        if ($this->namaSudahAda($data['nama_vendor'], $id)) {
            throw ValidationException::withMessages(['nama_vendor' => 'Nama vendor sudah dipakai.']);
        }

        DB::update(
            'UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?',
            [$data['nama_vendor'], $data['badan_hukum'], $data['status'], $id]
        );

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $vendor = $this->cari($id);

        if ($vendor === null) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak ditemukan.');
        }

        $baru = $vendor->status === 'A' ? 'N' : 'A';
        DB::update('UPDATE vendor SET status = ? WHERE idvendor = ?', [$baru, $id]);

        return redirect()->route('vendor')->with('sukses', 'Status vendor berhasil diubah menjadi '.($baru === 'A' ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak ditemukan.');
        }

        if ($this->dipakaiPengadaan($id)) {
            return redirect()->route('vendor')->with('gagal', 'Vendor tidak bisa dihapus karena masih dipakai pengadaan.');
        }

        DB::delete('DELETE FROM vendor WHERE idvendor = ?', [$id]);

        return redirect()->route('vendor')->with('sukses', 'Vendor berhasil dihapus.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idvendor, nama_vendor, badan_hukum, badan_hukum_teks, status, status_teks FROM v_vendor';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'nama_vendor LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' ORDER BY idvendor';

        $perHalaman = max(1, $perHalaman);
        $total = (int) (DB::selectOne('SELECT COUNT(*) AS jumlah FROM ('.$sql.') AS hasil', $bind)->jumlah ?? 0);

        $halaman = max(1, (int) LengthAwarePaginator::resolveCurrentPage());
        $offset = ($halaman - 1) * $perHalaman;

        $baris = $total === 0
            ? []
            : DB::select($sql.' LIMIT '.$perHalaman.' OFFSET '.$offset, $bind);

        return new LengthAwarePaginator($baris, $total, $perHalaman, $halaman, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }

    /** @return array<int, object> */
    private function semua(): array
    {
        return DB::select('SELECT idvendor, nama_vendor FROM vendor ORDER BY nama_vendor');
    }

    private function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idvendor, nama_vendor, badan_hukum, status FROM vendor WHERE idvendor = ?', [$id]);
    }

    private function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM vendor WHERE nama_vendor = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idvendor <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    private function dipakaiPengadaan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM pengadaan WHERE vendor_idvendor = ?', [$id])->n > 0;
    }
}
