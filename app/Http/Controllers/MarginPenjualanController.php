<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarginPenjualanController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $daftar = $this->daftar($status, 15, $request);
        $aktif = $this->aktif();

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

        DB::transaction(function () use ($data) {
            $status = (int) $data['status'];

            if ($status === 1) {
                DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1');
            }

            DB::insert(
                'INSERT INTO margin_penjualan (created_at, persen, status, iduser, updated_at) VALUES (NOW(), ?, ?, ?, NOW())',
                [(float) $data['persen'], $status, self::ID_USER_DEFAULT]
            );
        });

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
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

        DB::transaction(function () use ($id, $data) {
            if ((int) $data['status'] === 1) {
                DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1 AND idmargin_penjualan <> ?', [$id]);
            }

            DB::update(
                'UPDATE margin_penjualan SET persen = ?, status = ?, updated_at = NOW() WHERE idmargin_penjualan = ?',
                [(float) $data['persen'], (int) $data['status'], $id]
            );
        });

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil diperbarui.');
    }

    public function aktifkan(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin penjualan tidak ditemukan.');
        }

        DB::transaction(function () use ($id) {
            DB::update('UPDATE margin_penjualan SET status = 0 WHERE status = 1');
            DB::update('UPDATE margin_penjualan SET status = 1, updated_at = NOW() WHERE idmargin_penjualan = ?', [$id]);
        });

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan diaktifkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin penjualan tidak ditemukan.');
        }

        if ($this->dipakaiPenjualan($id)) {
            return redirect()->route('margin_penjualan')->with('gagal', 'Margin tidak bisa dihapus karena sudah dipakai penjualan.');
        }

        DB::delete('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);

        return redirect()->route('margin_penjualan')->with('sukses', 'Margin penjualan berhasil dihapus.');
    }

    private function daftar(string $status, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT idmargin_penjualan, persen, status, iduser, username,
                       DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') AS dibuat,
                       DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') AS diubah
                FROM v_margin_penjualan";
        $bind = [];

        if (in_array($status, ['0', '1'], true)) {
            $sql .= ' WHERE status = ?';
            $bind[] = (int) $status;
        }

        $sql .= ' ORDER BY idmargin_penjualan';

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
    private function aktif(): array
    {
        return DB::select('SELECT idmargin_penjualan, persen FROM v_margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan');
    }

    private function cari(int $id): ?object
    {
        return DB::selectOne('SELECT idmargin_penjualan, persen, status, iduser FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
    }

    private function dipakaiPenjualan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM penjualan WHERE idmargin_penjualan = ?', [$id])->n > 0;
    }
}
