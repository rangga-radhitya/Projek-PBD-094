<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    public const ID_USER_DEFAULT = 1;

    public const PPN_PERSEN = 11;

    protected function tanggal(Request $request, string $kunci): string
    {
        $nilai = (string) $request->query($kunci, '');

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $nilai) === 1 ? $nilai : '';
    }
}
