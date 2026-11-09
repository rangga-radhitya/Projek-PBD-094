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

class PenerimaanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

        return view('penerimaan.index', compact('daftar', 'q'));
    }

    public function show(int $id): View
    {
        $penerimaan = $this->cari($id);

        abort_if($penerimaan === null, 404);

        return view('penerimaan.show', compact('penerimaan'));
    }

    public function create(Request $request): View
    {
        $idpengadaan = (int) $request->query('idpengadaan', 0);

        $daftarPengadaan = $this->pengadaanTersedia();
        $pengadaan = null;
        $rincian = [];

        if ($idpengadaan > 0) {
            $pengadaan = $this->cariPengadaan($idpengadaan);

            if ($pengadaan !== null) {
                $rincian = $this->sisaPengadaan($idpengadaan);
            }
        }

        return view('penerimaan.create', compact('daftarPengadaan', 'pengadaan', 'rincian', 'idpengadaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idpengadaan' => ['required', 'integer'],
                'items' => ['required', 'array'],
                'items.*.idbarang' => ['required', 'integer'],
                'items.*.jumlah_terima' => ['required', 'integer', 'min:0'],
                'items.*.harga_satuan_terima' => ['required', 'integer', 'min:0'],
            ],
            [],
            [
                'idpengadaan' => 'pengadaan',
                'items' => 'daftar barang',
                'items.*.idbarang' => 'barang',
                'items.*.jumlah_terima' => 'jumlah terima',
                'items.*.harga_satuan_terima' => 'harga satuan terima',
            ]
        );

        if ($this->cariPengadaan((int) $data['idpengadaan']) === null) {
            throw ValidationException::withMessages(['idpengadaan' => 'Pengadaan yang dipilih tidak ada.']);
        }

        $items = array_values(array_filter(
            $data['items'],
            fn ($i) => (int) $i['jumlah_terima'] > 0
        ));

        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Isi jumlah terima minimal untuk satu barang.']);
        }

        try {
            $id = $this->tambah(
                (int) $data['idpengadaan'],
                self::ID_USER_DEFAULT,
                'D',
                $items
            );
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Penerimaan gagal disimpan.');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return redirect()->route('penerimaan.show', $id)->with('sukses', 'Penerimaan berhasil disimpan dan stok sudah diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('penerimaan.index')->with('gagal', 'Penerimaan tidak ditemukan.');
        }

        try {
            DB::transaction(function () use ($id) {
                DB::statement('CALL sp_hapus_penerimaan(?)', [$id]);
            });
        } catch (PDOException $e) {
            return redirect()->route('penerimaan.index')->with('gagal', $e->errorInfo[2] ?? 'Penerimaan gagal dihapus.');
        } catch (RuntimeException $e) {
            return redirect()->route('penerimaan.index')->with('gagal', $e->getMessage());
        }

        return redirect()->route('penerimaan.index')->with('sukses', 'Penerimaan dihapus dan stok dikoreksi.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT pn.idpenerimaan, pn.status, pn.idpengadaan,
                       DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username,
                       (SELECT COUNT(*) FROM detail_penerimaan d WHERE d.idpenerimaan = pn.idpenerimaan) AS detail_count
                FROM penerimaan pn
                LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = pn.iduser";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(pn.idpenerimaan = ? OR pn.idpengadaan = ? OR v.nama_vendor LIKE ?)';
                $bind[] = (int) $q;
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

        $sql .= ' ORDER BY pn.idpenerimaan';

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
            "SELECT pn.idpenerimaan, pn.status, pn.idpengadaan,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM penerimaan pn
             LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = pn.iduser
             WHERE pn.idpenerimaan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_penerimaan, d.barang_idbarang, d.jumlah_terima,
                    d.harga_satuan_terima, d.sub_total_terima, b.nama, s.nama_satuan
             FROM detail_penerimaan d
             LEFT JOIN barang b ON b.idbarang = d.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idpenerimaan = ?
             ORDER BY d.iddetail_penerimaan',
            [$id]
        );

        $header->retur = DB::select(
            "SELECT r.idretur, DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal, u.username
             FROM retur r
             LEFT JOIN `user` u ON u.iduser = r.iduser
             WHERE r.idpenerimaan = ?
             ORDER BY r.idretur",
            [$id]
        );

        return $header;
    }

    /**
     * Pengadaan yang masih memiliki sisa barang belum diterima.
     *
     * @return array<int, object>
     */
    private function pengadaanTersedia(): array
    {
        return DB::select(
            "SELECT p.idpengadaan, v.nama_vendor,
                    DATE_FORMAT(p.timestamp, '%d/%m/%Y %H:%i') AS tanggal,
                    SUM(GREATEST(d.jumlah - COALESCE(t.terima, 0), 0)) AS sisa
             FROM pengadaan p
             JOIN vendor v ON v.idvendor = p.vendor_idvendor
             JOIN detail_pengadaan d ON d.idpengadaan = p.idpengadaan
             LEFT JOIN (
                 SELECT pn.idpengadaan, dp.barang_idbarang, SUM(dp.jumlah_terima) AS terima
                 FROM detail_penerimaan dp
                 JOIN penerimaan pn ON pn.idpenerimaan = dp.idpenerimaan
                 GROUP BY pn.idpengadaan, dp.barang_idbarang
             ) t ON t.idpengadaan = p.idpengadaan AND t.barang_idbarang = d.idbarang
             GROUP BY p.idpengadaan, v.nama_vendor, p.timestamp
             HAVING sisa > 0
             ORDER BY p.idpengadaan"
        );
    }

    /**
     * Rincian sisa per barang untuk satu pengadaan.
     *
     * @return array<int, object>
     */
    private function sisaPengadaan(int $idpengadaan): array
    {
        return DB::select(
            'SELECT d.idbarang, b.nama, s.nama_satuan, d.harga_satuan,
                    d.jumlah AS dipesan,
                    COALESCE(t.terima, 0) AS diterima,
                    GREATEST(d.jumlah - COALESCE(t.terima, 0), 0) AS sisa
             FROM detail_pengadaan d
             JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             LEFT JOIN (
                 SELECT dp.barang_idbarang, SUM(dp.jumlah_terima) AS terima
                 FROM detail_penerimaan dp
                 JOIN penerimaan pn ON pn.idpenerimaan = dp.idpenerimaan
                 WHERE pn.idpengadaan = ?
                 GROUP BY dp.barang_idbarang
             ) t ON t.barang_idbarang = d.idbarang
             WHERE d.idpengadaan = ?
             ORDER BY d.iddetail_pengadaan',
            [$idpengadaan, $idpengadaan]
        );
    }

    /**
     * @param  array<int, array{idbarang:int, jumlah_terima:int, harga_satuan_terima:int}>  $items
     */
    private function tambah(int $idpengadaan, int $iduser, string $status, array $items): int
    {
        return DB::transaction(function () use ($idpengadaan, $iduser, $status, $items) {
            DB::insert(
                'INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
                 VALUES (NOW(), ?, ?, ?)',
                [$status, $idpengadaan, $iduser]
            );
            $id = (int) DB::getPdo()->lastInsertId();

            foreach ($items as $item) {
                $idbarang = (int) $item['idbarang'];
                $jumlah = (int) $item['jumlah_terima'];
                $harga = (int) $item['harga_satuan_terima'];

                if ($jumlah <= 0) {
                    throw new RuntimeException('Jumlah terima harus lebih dari 0.');
                }

                // Kunci baris pesanan agar pemeriksaan sisa berurutan per barang.
                $pesanan = DB::selectOne(
                    'SELECT jumlah FROM detail_pengadaan
                     WHERE idpengadaan = ? AND idbarang = ?
                     LIMIT 1 FOR UPDATE',
                    [$idpengadaan, $idbarang]
                );

                $diterima = (int) DB::selectOne(
                    'SELECT COALESCE(SUM(dp.jumlah_terima), 0) AS total
                     FROM detail_penerimaan dp
                     JOIN penerimaan p ON p.idpenerimaan = dp.idpenerimaan
                     WHERE p.idpengadaan = ? AND dp.barang_idbarang = ?',
                    [$idpengadaan, $idbarang]
                )->total;

                if ($pesanan === null || $diterima + $jumlah > (int) $pesanan->jumlah) {
                    throw new RuntimeException('Jumlah terima melebihi jumlah yang dipesan.');
                }

                DB::insert(
                    'INSERT INTO detail_penerimaan
                        (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
                     VALUES (?, ?, ?, ?, ?)',
                    [$id, $idbarang, $jumlah, $harga, $harga * $jumlah]
                );
            }

            return $id;
        });
    }

    private function cariPengadaan(int $id): ?object
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
}
