<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiterasiSoal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'literasi_soal';

    protected $fillable = [
        'sesi_id',
        'urutan',
        'jenis_soal', // pilihan_ganda, esai_pendek
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot_skor',
        'pembahasan',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'bobot_skor' => 'integer',
    ];

    public function sesi()
    {
        return $this->belongsTo(LiterasiSesi::class, 'sesi_id');
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(LiterasiJawabanSiswa::class, 'soal_id');
    }
}
