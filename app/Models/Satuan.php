<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_satuan.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class Satuan extends Model
{
    protected $table = 'v_satuan';
    protected $primaryKey = 'idsatuan';
    public $incrementing = false;
    public $timestamps = false;
}
