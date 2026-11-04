<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel kartu_stok (tahap frontend: belum ada simpan/ubah/hapus).
 */
class KartuStok extends Model
{
    protected $table = 'kartu_stok';
    protected $primaryKey = 'idkartu_stok';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = ['created_at' => 'datetime'];

    public function barang(): BelongsTo { return $this->belongsTo(Barang::class, 'idbarang', 'idbarang'); }
}
