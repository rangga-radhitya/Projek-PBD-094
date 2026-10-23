<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_barang.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class Barang extends Model
{
    protected $table = 'v_barang';
    protected $primaryKey = 'idbarang';
    public $incrementing = false;
    public $timestamps = false;
}
