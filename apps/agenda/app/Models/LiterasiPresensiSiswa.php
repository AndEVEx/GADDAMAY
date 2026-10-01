<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiterasiPresensiSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'literasi_presensi_siswa';

    protected $fillable = [
        'sesi_id',
        'siswa_id',
        'rombel_id',
        'waktu_gabung',
        'waktu_submit',
        'skor_pg',
        'status_evaluasi', // mengikuti, sudah_mengerjakan, dinilai
        'rangkuman_siswa',
    ];

    protected $casts = [
        'waktu_gabung' => 'datetime',
        'waktu_submit' => 'datetime',
        'skor_pg' => 'decimal:2',
    ];

    public function sesi()
    {
        return $this->belongsTo(LiterasiSesi::class, 'sesi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function jawaban()
    {
        return $this->hasMany(LiterasiJawabanSiswa::class, 'sesi_id', 'sesi_id')
                    ->where('siswa_id', $this->siswa_id);
    }
}
