<?php

namespace App\Services;

use App\Support\Paginasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Penjualan: daftar/detail (SQL mentah) serta objek database
 * sp_laporan_penjualan_periode (Stored Procedure) dan fn_hitung_total_penjualan (Function).
 */
class PenjualanService
{
    private ?PDO $pdo = null;

    /** Untuk pengujian di luar Laravel: pakai koneksi PDO yang diberikan. */
    public static function denganPdo(PDO $pdo): static
    {
        $service = new static();
        $service->pdo = $pdo;

        return $service;
    }

    private function pdo(): PDO
    {
        return $this->pdo ??= DB::connection()->getPdo();
    }

    public function daftar(string $q, string $iduser, string $dari, string $sampai, int $perHalaman, Request $request): LengthAwarePaginator
    {
        $sql = "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai,
                       DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                       u.username, m.persen,
                       (SELECT COUNT(*) FROM detail_penjualan d WHERE d.penjualan_idpenjualan = p.idpenjualan) AS detail_count
                FROM penjualan p
                LEFT JOIN `user` u ON u.iduser = p.iduser
                LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan";
        $where = [];
        $bind = [];

        if ($q !== '' && ctype_digit($q)) {
            $where[] = 'p.idpenjualan = ?';
            $bind[] = (int) $q;
        }

        if ($iduser !== '' && ctype_digit($iduser)) {
            $where[] = 'p.iduser = ?';
            $bind[] = (int) $iduser;
        }

        if ($dari !== '') {
            $where[] = 'p.created_at >= ?';
            $bind[] = $dari.' 00:00:00';
        }

        if ($sampai !== '') {
            $where[] = 'p.created_at <= ?';
            $bind[] = $sampai.' 23:59:59';
        }

        if ($where !== []) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        return Paginasi::buat($sql.' ORDER BY p.idpenjualan DESC', $bind, $perHalaman, $request);
    }

    public function cari(int $id): ?object
    {
        $header = DB::selectOne(
            "SELECT p.idpenjualan, p.subtotal_nilai, p.ppn, p.total_nilai, p.idmargin_penjualan,
                    DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS tanggal,
                    u.username, m.persen
             FROM penjualan p
             LEFT JOIN `user` u ON u.iduser = p.iduser
             LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan
             WHERE p.idpenjualan = ?",
            [$id]
        );

        if ($header === null) {
            return null;
        }

        $header->detail = DB::select(
            'SELECT d.iddetail_penjualan, d.idbarang, d.harga_satuan, d.jumlah, d.subtotal,
                    b.nama, s.nama_satuan
             FROM detail_penjualan d
             LEFT JOIN barang b ON b.idbarang = d.idbarang
             LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
             WHERE d.penjualan_idpenjualan = ?
             ORDER BY d.iddetail_penjualan',
            [$id]
        );

        return $header;
    }

    /**
     * Rekap penjualan pada rentang tanggal (format Y-m-d, kedua ujung termasuk).
     *
     * @return array{rincian: array<int, array<string, mixed>>, rekap: array<string, mixed>}
     *
     * @throws InvalidArgumentException bila rentang ditolak oleh Stored Procedure (SQLSTATE 45000)
     * @throws PDOException             bila objek belum terpasang (errorInfo[1] = 1305) atau galat database lain
     */
    public function laporanPeriode(string $awal, string $akhir): array
    {
        $st = $this->pdo()->prepare('CALL sp_laporan_penjualan_periode(?, ?)');

        try {
            $st->execute([$awal, $akhir]);
            $rincian = $st->fetchAll(PDO::FETCH_ASSOC);

            $rekap = null;
            if ($st->nextRowset()) {
                $rekap = $st->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            return [
                'rincian' => $rincian,
                'rekap' => $rekap ?? [
                    'jumlah_transaksi' => 0,
                    'total_pendapatan' => '0.00',
                    'total_ppn' => 0,
                    'total_nilai_tercatat' => 0,
                ],
            ];
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === '45000') {
                throw new InvalidArgumentException((string) ($e->errorInfo[2] ?? 'Rentang tanggal tidak valid.'), 0, $e);
            }

            throw $e;
        } finally {
            $st->closeCursor();
        }
    }

    /**
     * Total satu transaksi penjualan dari fungsi database.
     * Mengembalikan string desimal (mis. "24000.00"), "0.00" bila tanpa detail, null bila transaksi tidak ada.
     *
     * @throws PDOException bila fungsi belum terpasang
     */
    public function totalPenjualan(int $idpenjualan): ?string
    {
        $st = $this->pdo()->prepare('SELECT fn_hitung_total_penjualan(?) AS total');
        $st->execute([$idpenjualan]);
        $nilai = $st->fetchColumn();
        $st->closeCursor();

        return $nilai === false || $nilai === null ? null : (string) $nilai;
    }

    /** True bila galat berarti objek database belum dibuat (MySQL 1305: routine does not exist). */
    public static function objekBelumTerpasang(PDOException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1305 || ($e->errorInfo[1] ?? null) === '1305';
    }
}
