<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsProyekLatihan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_proyek_latihan';

    protected $fillable = [
        'penugasan_id',
        'siswa_id',
        'materi_id',
        'link_repository_git',
        'file_proyek',
        'deskripsi_pekerjaan',
        'status_review',
        'catatan_pembimbing',
        'dinilai_at',
    ];

    protected $casts = [
        'dinilai_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(LmsPenugasanSiswa::class, 'penugasan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function materi()
    {
        return $this->belongsTo(LmsMateri::class, 'materi_id');
    }
}