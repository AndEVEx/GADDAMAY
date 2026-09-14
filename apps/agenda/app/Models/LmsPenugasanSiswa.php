<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsPenugasanSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_penugasan_siswa';

    protected $fillable = [
        'materi_id',
        'siswa_id',
        'tipe_jalur',
        'target_selesai',
        'status_progres',
        'skor_evaluasi',
        'catatan_guru',
        'selesai_at',
    ];

    protected $casts = [
        'target_selesai' => 'date',
        'selesai_at' => 'datetime',
        'skor_evaluasi' => 'float',
    ];

    public function materi()
    {
        return $this->belongsTo(LmsMateri::class, 'materi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function proyekLatihan()
    {
        return $this->hasOne(LmsProyekLatihan::class, 'penugasan_id');
    }
}