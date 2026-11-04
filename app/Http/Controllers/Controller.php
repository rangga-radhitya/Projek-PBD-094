<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Belum ada fitur login pada tahap ini, sehingga transaksi dicatat
     * atas nama user id 1 (manajer01). Ubah di satu tempat ini bila login ditambahkan.
     */
    public const ID_USER_DEFAULT = 1;

    /** Tarif PPN (%) yang dipakai saat menghitung total pengadaan. */
    public const PPN_PERSEN = 11;
    /**
     * Ambil parameter tanggal (format Y-m-d) dari query string.
     * Mengembalikan string kosong bila tidak ada atau formatnya tidak valid.
     */
    protected function tanggal(Request $request, string $kunci): string
    {
        $nilai = (string) $request->query($kunci, '');

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $nilai) === 1 ? $nilai : '';
    }
}
