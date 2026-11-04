<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
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
