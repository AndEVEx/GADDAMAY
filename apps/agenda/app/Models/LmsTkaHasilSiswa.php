<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsTkaHasilSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_tka_hasil_siswa';

    protected $fillable = [
        'paket_id',
        'siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_detik',
        'jumlah_benar',
        'jumlah_salah',
        'nilai_skor',
        'lembar_jawaban',
        'status',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'durasi_detik' => 'integer',
        'jumlah_benar' => 'integer',
        'jumlah_salah' => 'integer',
        'nilai_skor' => 'float',
        'lembar_jawaban' => 'array',
    ];

    public function paket()
    {
        return $this->belongsTo(LmsTkaPaket::class, 'paket_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
