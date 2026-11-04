<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model hanya-baca untuk tabel detail_pengadaan (tahap frontend: belum ada simpan/ubah/hapus).
 */
class DetailPengadaan extends Model
{
    protected $table = 'detail_pengadaan';
    protected $primaryKey = 'iddetail_pengadaan';
    public $incrementing = false;
    public $timestamps = false;

    public function barang(): BelongsTo { return $this->belongsTo(Barang::class, 'idbarang', 'idbarang'); }
    public function pengadaan(): BelongsTo { return $this->belongsTo(Pengadaan::class, 'idpengadaan', 'idpengadaan'); }
}
