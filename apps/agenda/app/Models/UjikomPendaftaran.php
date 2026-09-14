<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjikomPendaftaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ujikom_pendaftaran';

    protected $fillable = [
        'nomor_pendaftaran',
        'skema_id',
        'siswa_id',
        'tahun_ajaran',
        'status_verifikasi',
        'asesor_id',
        'jadwal_asesmen',
        'tempat_uji_kompetensi_tuk',
        'hasil_asesmen',
        'catatan_asesor',
    ];

    protected $casts = [
        'jadwal_asesmen' => 'date',
    ];

    public function skema()
    {
        return $this->belongsTo(UjikomSkema::class, 'skema_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function berkas()
    {
        return $this->hasMany(UjikomBerkasAsesmen::class, 'pendaftaran_id');
    }
}