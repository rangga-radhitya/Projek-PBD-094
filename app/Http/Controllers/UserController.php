<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);
        $daftarRole = $this->semuaRole();

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

        if ($this->usernameSudahAda($data['username'])) {
            throw ValidationException::withMessages(['username' => 'Username sudah dipakai.']);
        }

        if (! $this->roleAda((int) $data['idrole'])) {
            throw ValidationException::withMessages(['idrole' => 'Role yang dipilih tidak ada.']);
        }

        DB::insert(
            'INSERT INTO `user` (username, password, idrole) VALUES (?, ?, ?)',
            [$data['username'], Hash::make($data['password']), (int) $data['idrole']]
        );

        return redirect()->route('user')->with('sukses', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
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

        if ($this->usernameSudahAda($data['username'], $id)) {
            throw ValidationException::withMessages(['username' => 'Username sudah dipakai.']);
        }

        if (! $this->roleAda((int) $data['idrole'])) {
            throw ValidationException::withMessages(['idrole' => 'Role yang dipilih tidak ada.']);
        }

        $password = $data['password'] ?? null;

        if ($password !== null && $password !== '') {
            DB::update(
                'UPDATE `user` SET username = ?, idrole = ?, password = ? WHERE iduser = ?',
                [$data['username'], (int) $data['idrole'], Hash::make($password), $id]
            );
        } else {
            DB::update(
                'UPDATE `user` SET username = ?, idrole = ? WHERE iduser = ?',
                [$data['username'], (int) $data['idrole'], $id]
            );
        }

        return redirect()->route('user')->with('sukses', 'User berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('user')->with('gagal', 'User tidak ditemukan.');
        }

        if ($this->dipakaiTransaksi($id)) {
            return redirect()->route('user')->with('gagal', 'User tidak bisa dihapus karena sudah dipakai pada transaksi.');
        }

        DB::delete('DELETE FROM `user` WHERE iduser = ?', [$id]);

        return redirect()->route('user')->with('sukses', 'User berhasil dihapus.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = 'SELECT iduser, username, idrole, nama_role FROM v_user';
        $where = [];
        $bind = [];

        if ($q !== '') {
            $where[] = 'username LIKE ?';
            $bind[] = '%'.$q.'%';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' ORDER BY iduser';

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
    private function semuaRole(): array
    {
        return DB::select('SELECT idrole, nama_role FROM v_role ORDER BY nama_role');
    }

    private function cari(int $id): ?object
    {
        return DB::selectOne('SELECT iduser, username, idrole FROM `user` WHERE iduser = ?', [$id]);
    }

    private function usernameSudahAda(string $username, ?int $kecuali = null): bool
    {
        $sql = 'SELECT COUNT(*) AS n FROM `user` WHERE username = ?';
        $bind = [$username];

        if ($kecuali !== null) {
            $sql .= ' AND iduser <> ?';
            $bind[] = $kecuali;
        }

        return (int) DB::selectOne($sql, $bind)->n > 0;
    }

    private function roleAda(int $idrole): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM role WHERE idrole = ?', [$idrole])->n > 0;
    }

    private function dipakaiTransaksi(int $id): bool
    {
        $n = (int) DB::selectOne(
            'SELECT (SELECT COUNT(*) FROM pengadaan WHERE user_iduser = ?)
                  + (SELECT COUNT(*) FROM penerimaan WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM penjualan WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM retur WHERE iduser = ?)
                  + (SELECT COUNT(*) FROM margin_penjualan WHERE iduser = ?) AS n',
            [$id, $id, $id, $id, $id]
        )->n;

        return $n > 0;
    }
}
