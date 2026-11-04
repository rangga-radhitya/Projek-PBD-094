<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel penerimaan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'idpenerimaan';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = ['created_at' => 'datetime'];

    public function pengadaan(): BelongsTo { return $this->belongsTo(Pengadaan::class, 'idpengadaan', 'idpengadaan'); }
    public function user(): BelongsTo { return $this->belongsTo(Pengguna::class, 'iduser', 'iduser'); }
    public function detail(): HasMany { return $this->hasMany(DetailPenerimaan::class, 'idpenerimaan', 'idpenerimaan'); }
    public function retur(): HasMany { return $this->hasMany(Retur::class, 'idpenerimaan', 'idpenerimaan'); }
}
