<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;
use RuntimeException;

class PengadaanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

        return view('pengadaan.index', compact('daftar', 'q'));
    }

    public function show(int $id): View
    {
        $pengadaan = $this->cari($id);

        abort_if($pengadaan === null, 404);

        $diterima = $this->diterimaPerBarang($id);

        return view('pengadaan.show', compact('pengadaan', 'diterima'));
    }

    public function create(): View
    {
        $daftarVendor = $this->semuaVendor();
        $daftarBarang = $this->barangAktif();

        return view('pengadaan.create', compact('daftarVendor', 'daftarBarang'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idvendor' => ['required', 'integer'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.idbarang' => ['required', 'integer'],
                'items.*.harga_satuan' => ['required', 'integer', 'min:0'],
                'items.*.jumlah' => ['required', 'integer', 'min:1'],
            ],
            [],
            [
                'idvendor' => 'vendor',
                'items' => 'daftar barang',
                'items.*.idbarang' => 'barang',
                'items.*.harga_satuan' => 'harga satuan',
                'items.*.jumlah' => 'jumlah',
            ]
        );

        if ($this->cariVendor((int) $data['idvendor']) === null) {
            throw ValidationException::withMessages(['idvendor' => 'Vendor yang dipilih tidak ada.']);
        }

        $idBarang = array_map(fn ($i) => (int) $i['idbarang'], $data['items']);

        if (count($idBarang) !== count(array_unique($idBarang))) {
            throw ValidationException::withMessages(['items' => 'Satu barang tidak boleh dipilih lebih dari sekali.']);
        }

        foreach ($idBarang as $idb) {
            if ($this->cariBarang($idb) === null) {
                throw ValidationException::withMessages(['items' => 'Ada barang yang tidak ditemukan.']);
            }
        }

        try {
            $id = $this->tambah(
                self::ID_USER_DEFAULT,
                (int) $data['idvendor'],
                'D',
                $data['items'],
                self::PPN_PERSEN
            );
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Pengadaan gagal disimpan.');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return redirect()->route('pengadaan.show', $id)->with('sukses', 'Pengadaan berhasil disimpan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('pengadaan.index')->with('gagal', 'Pengadaan tidak ditemukan.');
        }

        if ($this->dipakaiPenerimaan($id)) {
            return redirect()->route('pengadaan.index')->with('gagal', 'Pengadaan tidak bisa dihapus karena sudah memiliki penerimaan.');
        }

        DB::transaction(function () use ($id) {
            DB::delete('DELETE FROM detail_pengadaan WHERE idpengadaan = ?', [$id]);
            DB::delete('DELETE FROM pengadaan WHERE idpengadaan = ?', [$id]);
        });

        return redirect()->route('pengadaan.index')->with('sukses', 'Pengadaan berhasil dihapus.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT p.idpengadaan, p.status, p.subtotal_nilai, p.ppn, p.total_nilai,
                       DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username
                FROM pengadaan p
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = p.user_iduser";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(p.idpengadaan = ? OR v.nama_vendor LIKE ?)';
                $bind[] = (int) $q;
                $bind[] = '%'.$q.'%';
            } else {
                $where[] = 'v.nama_vendor LIKE ?';
                $bind[] = '%'.$q.'%';
            }
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.idpengadaan';

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
        $header = DB::selectOne(
            "SELECT p.idpengadaan, p.status, p.subtotal_nilai, p.ppn, p.total_nilai, p.vendor_idvendor,
                    DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM pengadaan p
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = p.user_iduser
             WHERE p.idpengadaan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_pengadaan, d.idbarang, d.harga_satuan, d.jumlah, d.sub_total,
                    b.nama, s.nama_satuan
             FROM detail_pengadaan d
             LEFT JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idpengadaan = ?
             ORDER BY d.iddetail_pengadaan',
            [$id]
        );

        $header->penerimaan = DB::select(
            "SELECT pn.idpenerimaan, pn.status,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username
             FROM penerimaan pn
             LEFT JOIN `user` u ON u.iduser = pn.iduser
             WHERE pn.idpengadaan = ?
             ORDER BY pn.idpenerimaan",
            [$id]
        );

        return $header;
    }

    /**
     * Jumlah yang sudah diterima per barang untuk satu pengadaan.
     *
     * @return array<int, int> idbarang => total diterima
     */
    private function diterimaPerBarang(int $idpengadaan): array
    {
        $baris = DB::select(
            'SELECT dp.barang_idbarang AS idbarang, SUM(dp.jumlah_terima) AS total
             FROM detail_penerimaan dp
             JOIN penerimaan p ON p.idpenerimaan = dp.idpenerimaan
             WHERE p.idpengadaan = ?
             GROUP BY dp.barang_idbarang',
            [$idpengadaan]
        );

        $peta = [];
        foreach ($baris as $b) {
            $peta[(int) $b->idbarang] = (int) $b->total;
        }

        return $peta;
    }

    /**
     * Simpan header + detail pengadaan dalam satu transaksi.
     *
     * @param  array<int, array{idbarang:int, harga_satuan:int, jumlah:int}>  $items
     */
    private function tambah(int $iduser, int $idvendor, string $status, array $items, int $ppnPersen): int
    {
        return DB::transaction(function () use ($iduser, $idvendor, $status, $items, $ppnPersen) {
            DB::insert(
                'INSERT INTO pengadaan (timestamp, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
                 VALUES (NOW(), ?, ?, ?, 0, 0, 0)',
                [$iduser, $status, $idvendor]
            );
            $id = (int) DB::getPdo()->lastInsertId();

            foreach ($items as $item) {
                $harga = (int) $item['harga_satuan'];
                $jumlah = (int) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new RuntimeException('Jumlah barang pengadaan harus lebih dari 0.');
                }

                if ($harga < 0) {
                    throw new RuntimeException('Harga satuan tidak boleh negatif.');
                }

                DB::insert(
                    'INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
                     VALUES (?, ?, ?, ?, ?)',
                    [$harga, $jumlah, $harga * $jumlah, (int) $item['idbarang'], $id]
                );
            }

            $subtotal = (int) DB::selectOne(
                'SELECT COALESCE(SUM(sub_total), 0) AS total FROM detail_pengadaan WHERE idpengadaan = ?',
                [$id]
            )->total;

            $ppn = (int) round($subtotal * $ppnPersen / 100);

            DB::update(
                'UPDATE pengadaan SET subtotal_nilai = ?, ppn = ?, total_nilai = ? WHERE idpengadaan = ?',
                [$subtotal, $ppn, $subtotal + $ppn, $id]
            );

            return $id;
        });
    }

    private function dipakaiPenerimaan(int $id): bool
    {
        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM penerimaan WHERE idpengadaan = ?', [$id])->n > 0;
    }

    /** @return array<int, object> */
    private function semuaVendor(): array
    {
        return DB::select('SELECT idvendor, nama_vendor FROM vendor ORDER BY nama_vendor');
    }

    private function cariVendor(int $id): ?object
    {
        return DB::selectOne('SELECT idvendor, nama_vendor, badan_hukum, status FROM vendor WHERE idvendor = ?', [$id]);
    }

    /** @return array<int, object> */
    private function barangAktif(): array
    {
        return DB::select('SELECT idbarang, nama, harga FROM barang WHERE status = 1 ORDER BY nama');
    }

    private function cariBarang(int $id): ?object
    {
        return DB::selectOne('SELECT idbarang, nama, jenis, idsatuan, harga, status FROM barang WHERE idbarang = ?', [$id]);
    }
}
