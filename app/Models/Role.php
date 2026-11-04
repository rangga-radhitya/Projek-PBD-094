<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_role.
 * Data tambah/ubah/hapus belum dibuat (tahap berikutnya).
 */
class Role extends Model
{
    protected $table = 'v_role';
    protected $primaryKey = 'idrole';
    public $incrementing = false;
    public $timestamps = false;
}
