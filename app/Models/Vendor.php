<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_vendor.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class Vendor extends Model
{
    protected $table = 'v_vendor';
    protected $primaryKey = 'idvendor';
    public $incrementing = false;
    public $timestamps = false;
}
