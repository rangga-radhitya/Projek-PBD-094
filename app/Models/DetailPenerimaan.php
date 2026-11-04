<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel detail_penerimaan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class DetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'iddetail_penerimaan';
    public $incrementing = false;
    public $timestamps = false;

    public function barang(): BelongsTo { return $this->belongsTo(Barang::class, 'barang_idbarang', 'idbarang'); }
    public function penerimaan(): BelongsTo { return $this->belongsTo(Penerimaan::class, 'idpenerimaan', 'idpenerimaan'); }
}
