<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_user.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class Pengguna extends Model
{
    protected $table = 'v_user';
    protected $primaryKey = 'iduser';
    public $incrementing = false;
    public $timestamps = false;
}
