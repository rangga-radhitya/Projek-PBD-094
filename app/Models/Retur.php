<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel retur (tahap frontend: belum ada simpan/ubah/hapus).
 */
class Retur extends Model
{
    protected $table = 'retur';
    protected $primaryKey = 'idretur';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = ['created_at' => 'datetime'];

    public function penerimaan(): BelongsTo { return $this->belongsTo(Penerimaan::class, 'idpenerimaan', 'idpenerimaan'); }
    public function user(): BelongsTo { return $this->belongsTo(Pengguna::class, 'iduser', 'iduser'); }
    public function detail(): HasMany { return $this->hasMany(DetailRetur::class, 'idretur', 'idretur'); }
}
