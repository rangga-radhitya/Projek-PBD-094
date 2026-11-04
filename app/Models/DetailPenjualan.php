<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel detail_penjualan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = 'iddetail_penjualan';
    public $incrementing = false;
    public $timestamps = false;

    public function barang(): BelongsTo { return $this->belongsTo(Barang::class, 'idbarang', 'idbarang'); }
    public function penjualan(): BelongsTo { return $this->belongsTo(Penjualan::class, 'penjualan_idpenjualan', 'idpenjualan'); }
}
