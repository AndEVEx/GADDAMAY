<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsTkaPaket extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_tka_paket';

    protected $fillable = [
        'judul_paket',
        'mata_uji',
        'guru_pembuat_id',
        'durasi_menit',
        'jumlah_soal',
        'target_tingkat',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
        'jumlah_soal' => 'integer',
        'is_active' => 'boolean',
    ];

    public function guruPembuat()
    {
        return $this->belongsTo(User::class, 'guru_pembuat_id');
    }

    public function soal()
    {
        return $this->hasMany(LmsTkaSoal::class, 'paket_id')->orderBy('nomor_urut');
    }

    public function hasilSiswa()
    {
        return $this->hasMany(LmsTkaHasilSiswa::class, 'paket_id');
    }
}
