<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiterasiSlide extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'literasi_slides';

    protected $fillable = [
        'sesi_id',
        'urutan',
        'judul_slide',
        'konten',
        'poin_kunci',
        'gambar_url',
        'catatan_pemateri',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function sesi()
    {
        return $this->belongsTo(LiterasiSesi::class, 'sesi_id');
    }
}
