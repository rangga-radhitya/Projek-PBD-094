<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel detail_retur (tahap frontend: belum ada simpan/ubah/hapus).
 */
class DetailRetur extends Model
{
    protected $table = 'detail_retur';
    protected $primaryKey = 'iddetail_retur';
    public $incrementing = false;
    public $timestamps = false;

    public function retur(): BelongsTo { return $this->belongsTo(Retur::class, 'idretur', 'idretur'); }
    public function detailPenerimaan(): BelongsTo { return $this->belongsTo(DetailPenerimaan::class, 'iddetail_penerimaan', 'iddetail_penerimaan'); }
}
