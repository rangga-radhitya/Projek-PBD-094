<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel pengadaan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'idpengadaan';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = ['timestamp' => 'datetime'];

    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class, 'vendor_idvendor', 'idvendor'); }
    public function user(): BelongsTo { return $this->belongsTo(Pengguna::class, 'user_iduser', 'iduser'); }
    public function detail(): HasMany { return $this->hasMany(DetailPengadaan::class, 'idpengadaan', 'idpengadaan'); }
    public function penerimaan(): HasMany { return $this->hasMany(Penerimaan::class, 'idpengadaan', 'idpengadaan'); }
}
