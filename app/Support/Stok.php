<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Penulisan kartu_stok dari aplikasi.
 *
 * Dipakai untuk transaksi yang tidak lagi ditangani stored procedure/trigger:
 * retur (keluar), pembatalan retur (masuk kembali), dan penghapusan penerimaan
 * (koreksi keluar). Saldo dihitung dari baris kartu_stok terakhir per barang.
 */
class Stok
{
    /**
     * Saldo stok terakhir satu barang. Baris barang dikunci lebih dulu agar
     * perhitungan saldo berurutan dan tidak saling menimpa.
     */
    public static function saldoTerakhir(int $idbarang): int
    {
        DB::selectOne('SELECT idbarang FROM barang WHERE idbarang = ? FOR UPDATE', [$idbarang]);

        $baris = DB::selectOne(
            'SELECT stock FROM kartu_stok WHERE idbarang = ? ORDER BY idkartu_stok DESC LIMIT 1',
            [$idbarang]
        );

        return $baris === null ? 0 : (int) $baris->stock;
    }

    /**
     * Catat satu baris pergerakan stok.
     */
    public static function catat(string $jenis, int $masuk, int $keluar, int $stock, int $idtransaksi, ?int $idbarang): void
    {
        DB::insert(
            'INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
             VALUES (?, ?, ?, ?, NOW(), ?, ?)',
            [$jenis, $masuk, $keluar, $stock, $idtransaksi, $idbarang]
        );
    }
}
