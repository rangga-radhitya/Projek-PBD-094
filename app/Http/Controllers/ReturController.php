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

class ReturController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftar = $this->daftar($q, 15, $request);

        return view('retur.index', compact('daftar', 'q'));
    }

    public function show(int $id): View
    {
        $retur = $this->cari($id);

        abort_if($retur === null, 404);

        return view('retur.show', compact('retur'));
    }

    public function create(Request $request): View
    {
        $idpenerimaan = (int) $request->query('idpenerimaan', 0);

        $daftarPenerimaan = $this->penerimaanTersedia();
        $penerimaan = null;
        $rincian = [];

        if ($idpenerimaan > 0) {
            $penerimaan = $this->cariPenerimaan($idpenerimaan);

            if ($penerimaan !== null) {
                $rincian = $this->detailTersedia($idpenerimaan);
            }
        }

        return view('retur.create', compact('daftarPenerimaan', 'penerimaan', 'rincian', 'idpenerimaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'idpenerimaan' => ['required', 'integer'],
                'items' => ['required', 'array'],
                'items.*.iddetail_penerimaan' => ['required', 'integer'],
                'items.*.jumlah' => ['required', 'integer', 'min:0'],
                'items.*.alasan' => ['nullable', 'string', 'max:200'],
            ],
            [],
            [
                'idpenerimaan' => 'penerimaan',
                'items' => 'daftar barang',
                'items.*.iddetail_penerimaan' => 'detail penerimaan',
                'items.*.jumlah' => 'jumlah retur',
                'items.*.alasan' => 'alasan',
            ]
        );

        if ($this->cariPenerimaan((int) $data['idpenerimaan']) === null) {
            throw ValidationException::withMessages(['idpenerimaan' => 'Penerimaan yang dipilih tidak ada.']);
        }

        $items = array_values(array_filter(
            $data['items'],
            fn ($i) => (int) $i['jumlah'] > 0
        ));

        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Isi jumlah retur minimal untuk satu barang.']);
        }

        try {
            $id = $this->tambah((int) $data['idpenerimaan'], self::ID_USER_DEFAULT, $items);
        } catch (PDOException $e) {
            return back()->withInput()->with('gagal', $e->errorInfo[2] ?? 'Retur gagal disimpan.');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return redirect()->route('retur.show', $id)->with('sukses', 'Retur berhasil disimpan dan stok sudah dikoreksi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($this->cari($id) === null) {
            return redirect()->route('retur.index')->with('gagal', 'Retur tidak ditemukan.');
        }

        try {
            $this->hapus($id);
        } catch (PDOException $e) {
            return redirect()->route('retur.index')->with('gagal', $e->errorInfo[2] ?? 'Retur gagal dihapus.');
        } catch (RuntimeException $e) {
            return redirect()->route('retur.index')->with('gagal', $e->getMessage());
        }

        return redirect()->route('retur.index')->with('sukses', 'Retur dihapus dan stok dikoreksi.');
    }

    private function daftar(string $q, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT r.idretur, r.idpenerimaan,
                       DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       v.nama_vendor, u.username,
                       (SELECT COUNT(*) FROM detail_retur d WHERE d.idretur = r.idretur) AS detail_count
                FROM retur r
                LEFT JOIN penerimaan pn ON pn.idpenerimaan = r.idpenerimaan
                LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
                LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                LEFT JOIN `user` u ON u.iduser = r.iduser";
        $where = [];
        $bind = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where[] = '(r.idretur = ? OR r.idpenerimaan = ? OR v.nama_vendor LIKE ?)';
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

        $sql .= ' ORDER BY r.idretur';

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
            "SELECT r.idretur, r.idpenerimaan,
                    DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    v.nama_vendor, u.username
             FROM retur r
             LEFT JOIN penerimaan pn ON pn.idpenerimaan = r.idpenerimaan
             LEFT JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
             LEFT JOIN `user` u ON u.iduser = r.iduser
             WHERE r.idretur = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_retur, d.jumlah, d.alasan, d.iddetail_penerimaan,
                    dp.barang_idbarang, dp.jumlah_terima, b.nama, s.nama_satuan
             FROM detail_retur d
             LEFT JOIN detail_penerimaan dp ON dp.iddetail_penerimaan = d.iddetail_penerimaan
             LEFT JOIN barang b ON b.idbarang = dp.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.idretur = ?
             ORDER BY d.iddetail_retur',
            [$id]
        );

        return $header;
    }

    /**
     * Penerimaan yang masih punya barang bisa diretur.
     *
     * @return array<int, object>
     */
    private function penerimaanTersedia(): array
    {
        return DB::select(
            "SELECT pn.idpenerimaan, v.nama_vendor,
                    DATE_FORMAT(pn.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    SUM(GREATEST(dp.jumlah_terima - COALESCE(r.jumlah_retur, 0), 0)) AS sisa
             FROM penerimaan pn
             JOIN pengadaan p ON p.idpengadaan = pn.idpengadaan
             JOIN vendor v ON v.idvendor = p.vendor_idvendor
             JOIN detail_penerimaan dp ON dp.idpenerimaan = pn.idpenerimaan
             LEFT JOIN (
                 SELECT iddetail_penerimaan, SUM(jumlah) AS jumlah_retur
                 FROM detail_retur
                 GROUP BY iddetail_penerimaan
             ) r ON r.iddetail_penerimaan = dp.iddetail_penerimaan
             GROUP BY pn.idpenerimaan, pn.created_at, v.nama_vendor
             HAVING sisa > 0
             ORDER BY pn.idpenerimaan"
        );
    }

    /**
     * Rincian barang pada satu penerimaan yang masih bisa diretur.
     *
     * @return array<int, object>
     */
    private function detailTersedia(int $idpenerimaan): array
    {
        return DB::select(
            'SELECT dp.iddetail_penerimaan, dp.barang_idbarang AS idbarang,
                    dp.jumlah_terima, b.nama, s.nama_satuan,
                    COALESCE(r.jumlah_retur, 0) AS sudah_retur,
                    GREATEST(dp.jumlah_terima - COALESCE(r.jumlah_retur, 0), 0) AS sisa
             FROM detail_penerimaan dp
             JOIN barang b ON b.idbarang = dp.barang_idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             LEFT JOIN (
                 SELECT iddetail_penerimaan, SUM(jumlah) AS jumlah_retur
                 FROM detail_retur
                 GROUP BY iddetail_penerimaan
             ) r ON r.iddetail_penerimaan = dp.iddetail_penerimaan
             WHERE dp.idpenerimaan = ?
             ORDER BY dp.iddetail_penerimaan',
            [$idpenerimaan]
        );
    }

    /**
     * @param  array<int, array{iddetail_penerimaan:int, jumlah:int, alasan:?string}>  $items
     */
    private function tambah(int $idpenerimaan, int $iduser, array $items): int
    {
        return DB::transaction(function () use ($idpenerimaan, $iduser, $items) {
            $penerimaan = DB::selectOne(
                'SELECT idpenerimaan FROM penerimaan WHERE idpenerimaan = ?',
                [$idpenerimaan]
            );

            if ($penerimaan === null) {
                throw new RuntimeException('Penerimaan tidak ditemukan.');
            }

            DB::insert(
                'INSERT INTO retur (created_at, idpenerimaan, iduser)
                 VALUES (NOW(), ?, ?)',
                [$idpenerimaan, $iduser]
            );
            $id = (int) DB::getPdo()->lastInsertId();

            foreach ($items as $item) {
                $jumlah = (int) $item['jumlah'];
                $iddetail = (int) $item['iddetail_penerimaan'];

                if ($jumlah <= 0) {
                    throw new RuntimeException('Jumlah retur harus lebih dari 0.');
                }

                // Kunci baris detail agar pemeriksaan sisa retur berurutan.
                $detail = DB::selectOne(
                    'SELECT jumlah_terima, idpenerimaan
                     FROM detail_penerimaan
                     WHERE iddetail_penerimaan = ?
                     FOR UPDATE',
                    [$iddetail]
                );

                if ($detail === null) {
                    throw new RuntimeException('Detail penerimaan tidak ditemukan.');
                }

                if ((int) $detail->idpenerimaan !== $idpenerimaan) {
                    throw new RuntimeException('Detail penerimaan bukan milik penerimaan pada retur ini.');
                }

                $diretur = (int) DB::selectOne(
                    'SELECT COALESCE(SUM(jumlah), 0) AS total
                     FROM detail_retur
                     WHERE iddetail_penerimaan = ?',
                    [$iddetail]
                )->total;

                if ($diretur + $jumlah > (int) $detail->jumlah_terima) {
                    throw new RuntimeException('Jumlah retur melebihi jumlah yang diterima.');
                }

                DB::insert(
                    'INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan)
                     VALUES (?, ?, ?, ?)',
                    [$jumlah, $item['alasan'] ?? null, $id, $iddetail]
                );

                $idbarang = (int) DB::selectOne(
                    'SELECT barang_idbarang FROM detail_penerimaan WHERE iddetail_penerimaan = ?',
                    [$iddetail]
                )->barang_idbarang;

                $saldo = $this->saldoTerakhir($idbarang);

                $this->catatStok('R', 0, $jumlah, $saldo - $jumlah, $id, $idbarang);
            }

            return $id;
        });
    }

    private function hapus(int $id): void
    {
        DB::transaction(function () use ($id) {
            $baris = DB::select(
                'SELECT dp.barang_idbarang AS idbarang, d.jumlah
                 FROM detail_retur d
                 JOIN detail_penerimaan dp ON dp.iddetail_penerimaan = d.iddetail_penerimaan
                 WHERE d.idretur = ?
                 ORDER BY d.iddetail_retur',
                [$id]
            );

            foreach ($baris as $b) {
                $idbarang = $b->idbarang === null ? null : (int) $b->idbarang;
                $jumlah = (int) $b->jumlah;
                $saldo = $idbarang === null ? 0 : $this->saldoTerakhir($idbarang);

                $this->catatStok('M', $jumlah, 0, $saldo + $jumlah, $id, $idbarang);
            }

            DB::delete('DELETE FROM detail_retur WHERE idretur = ?', [$id]);
            DB::delete('DELETE FROM retur WHERE idretur = ?', [$id]);
        });
    }

    /**
     * Saldo stok terakhir satu barang. Baris barang dikunci lebih dulu agar
     * perhitungan saldo berurutan dan tidak saling menimpa.
     */
    private function saldoTerakhir(int $idbarang): int
    {
        DB::selectOne('SELECT idbarang FROM barang WHERE idbarang = ? FOR UPDATE', [$idbarang]);

        $baris = DB::selectOne(
            'SELECT stock FROM kartu_stok WHERE idbarang = ? ORDER BY idkartu_stok DESC LIMIT 1',
            [$idbarang]
        );

        return $baris === null ? 0 : (int) $baris->stock;
    }

    /**
     * Catat satu baris pergerakan stok pada kartu_stok.
     */
    private function catatStok(string $jenis, int $masuk, int $keluar, int $stock, int $idtransaksi, ?int $idbarang): void
    {
        DB::insert(
            'INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
             VALUES (?, ?, ?, ?, NOW(), ?, ?)',
            [$jenis, $masuk, $keluar, $stock, $idtransaksi, $idbarang]
        );
    }

    private function cariPenerimaan(int $id): ?object
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
}
