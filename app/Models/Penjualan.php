<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel penjualan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'idpenjualan';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(Pengguna::class, 'iduser', 'iduser'); }
    public function margin(): BelongsTo { return $this->belongsTo(MarginPenjualan::class, 'idmargin_penjualan', 'idmargin_penjualan'); }
    public function detail(): HasMany { return $this->hasMany(DetailPenjualan::class, 'penjualan_idpenjualan', 'idpenjualan'); }
}
