<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsMateri extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_materi';

    protected $fillable = [
        'judul',
        'kategori',
        'bidang_keahlian',
        'mapel_id',
        'tp_id',
        'guru_id',
        'deskripsi',
        'konten_materi',
        'file_lampiran',
        'link_video',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tp_id');
    }

    public function penugasanSiswa()
    {
        return $this->hasMany(LmsPenugasanSiswa::class, 'materi_id');
    }

    public function proyekLatihan()
    {
        return $this->hasMany(LmsProyekLatihan::class, 'materi_id');
    }
}