<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

        return view('role.index', compact('daftar', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['nama_role' => ['required', 'string', 'max:100']],
            [],
            ['nama_role' => 'nama role']
        );

        if ($this->namaSudahAda($data['nama_role'])) {
            throw ValidationException::withMessages(['nama_role' => 'Nama role sudah dipakai.']);
        }

        DB::insert('INSERT INTO role (nama_role) VALUES (?)', [$data['nama_role']]);

        return redirect()->route('role')->with('sukses', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('role')->with('gagal', 'Role tidak ditemukan.');
        }

        $data = $request->validate(
            ['nama_role' => ['required', 'string', 'max:100']],
            [],
            ['nama_role' => 'nama role']
        );

        if ($this->namaSudahAda($data['nama_role'], $id)) {
            throw ValidationException::withMessages(['nama_role' => 'Nama role sudah dipakai.']);
        }

        DB::update('UPDATE role SET nama_role = ? WHERE idrole = ?', [$data['nama_role'], $id]);

        return redirect()->route('role')->with('sukses', 'Role berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('role')->with('gagal', 'Role tidak ditemukan.');
        }

        if ($this->dipakaiUser($id)) {
            return redirect()->route('role')->with('gagal', 'Role tidak bisa dihapus karena masih dipakai user.');
        }

        DB::delete('DELETE FROM role WHERE idrole = ?', [$id]);

        return redirect()->route('role')->with('sukses', 'Role berhasil dihapus.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT idrole, nama_role FROM v_role';
        $bind = [];

        if ($q !== '') {
            $sql .= ' WHERE nama_role LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        $sql .= ' ORDER BY idrole';

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
        return DB::selectOne('SELECT idrole, nama_role FROM v_role WHERE idrole = ?', [$id]);
    }

    private function namaSudahAda(string $nama, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM role WHERE nama_role = ?';
        $bind = [$nama];

        if ($kecuali !== null) {
            $sql .= ' AND idrole <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    private function dipakaiUser(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM `user` WHERE idrole = ?', [$id])->n > 0;
    }
}
