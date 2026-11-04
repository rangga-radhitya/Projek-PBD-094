<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_margin_penjualan.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class MarginPenjualan extends Model
{
    protected $table = 'v_margin_penjualan';
    protected $primaryKey = 'idmargin_penjualan';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'persen'     => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
