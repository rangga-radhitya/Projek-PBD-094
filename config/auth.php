<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Menentukan guard autentikasi default dan broker reset password.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Setiap guard punya driver dan provider sendiri. Driver "session" menyimpan
    | status login di session, sedangkan provider menentukan dari mana data
    | pengguna diambil.
    |
    | Driver yang dipakai: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider menentukan cara pengguna diambil dari database. Aplikasi ini
    | memakai driver "database" sehingga data dibaca langsung dari tabel `user`
    | tanpa kelas model.
    |
    | Driver yang dipakai: "database"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'database',
            'table' => 'user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Pengaturan reset password: tabel penyimpan token, masa berlaku token,
    | dan jeda minimum antar permintaan token.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Batas waktu (detik) sebelum pengguna diminta memasukkan ulang password
    | pada layar konfirmasi. Nilai default tiga jam.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
