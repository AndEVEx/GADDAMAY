<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiterasiJawabanSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'literasi_jawaban_siswa';

    protected $fillable = [
        'sesi_id',
        'siswa_id',
        'soal_id',
        'jawaban_pg',
        'jawaban_esai',
        'is_benar',
        'skor',
        'catatan_koreksi',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
        'skor' => 'decimal:2',
    ];

    public function sesi()
    {
        return $this->belongsTo(LiterasiSesi::class, 'sesi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function soal()
    {
        return $this->belongsTo(LiterasiSoal::class, 'soal_id');
    }
}
