<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiterasiSesi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'literasi_sesi';

    protected $fillable = [
        'judul',
        'tema',
        'tanggal',
        'pemateri_id',
        'nama_pemateri',
        'jabatan_pemateri',
        'koordinator_id',
        'status', // draft, live, selesai
        'current_slide_index',
        'allow_quiz',
        'total_slides',
        'deskripsi',
        'kode_sesi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'current_slide_index' => 'integer',
        'allow_quiz' => 'boolean',
        'total_slides' => 'integer',
    ];

    public function pemateri()
    {
        return $this->belongsTo(User::class, 'pemateri_id');
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function slides()
    {
        return $this->hasMany(LiterasiSlide::class, 'sesi_id')->orderBy('urutan');
    }

    public function soal()
    {
        return $this->hasMany(LiterasiSoal::class, 'sesi_id')->orderBy('urutan');
    }

    public function presensiSiswa()
    {
        return $this->hasMany(LiterasiPresensiSiswa::class, 'sesi_id');
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(LiterasiJawabanSiswa::class, 'sesi_id');
    }

    public function getActiveSlideAttribute()
    {
        return $this->slides()->where('urutan', $this->current_slide_index)->first();
    }
}
