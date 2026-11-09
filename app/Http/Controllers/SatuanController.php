<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SatuanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

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

        if ($this->namaSudahAda($data['nama_satuan'])) {
            throw ValidationException::withMessages(['nama_satuan' => 'Nama satuan sudah dipakai.']);
        }

        DB::insert('INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)', [$data['nama_satuan'], (int) $data['status']]);

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
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

        if ($this->namaSudahAda($data['nama_satuan'], $id)) {
            throw ValidationException::withMessages(['nama_satuan' => 'Nama satuan sudah dipakai.']);
        }

        DB::update(
            'UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?',
            [$data['nama_satuan'], (int) $data['status'], $id]
        );

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil diperbarui.');
    }

    public function status(int $id): RedirectResponse
    {
        $satuan = $this->cari($id);

        if ($satuan === null) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak ditemukan.');
        }

        $baru = (int) $satuan->status === 1 ? 0 : 1;
        DB::update('UPDATE satuan SET status = ? WHERE idsatuan = ?', [$baru, $id]);

        return redirect()->route('satuan')->with('sukses', 'Status satuan berhasil diubah menjadi '.($baru === 1 ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak ditemukan.');
        }

        if ($this->dipakaiBarang($id)) {
            return redirect()->route('satuan')->with('gagal', 'Satuan tidak bisa dihapus karena masih dipakai barang.');
        }

        DB::delete('DELETE FROM satuan WHERE idsatuan = ?', [$id]);

        return redirect()->route('satuan')->with('sukses', 'Satuan berhasil dihapus.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idsatuan, nama_satuan, status, status_teks FROM v_satuan';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'nama_satuan LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' ORDER BY idsatuan';

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

    private function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idsatuan, nama_satuan, status FROM satuan WHERE idsatuan = ?', [$id]);
    }

    private function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM satuan WHERE nama_satuan = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idsatuan <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    private function dipakaiBarang(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM barang WHERE idsatuan = ?', [$id])->n > 0;
    }
}
