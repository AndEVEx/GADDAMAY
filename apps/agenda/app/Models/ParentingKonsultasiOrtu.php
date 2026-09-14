<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentingKonsultasiOrtu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'parenting_konsultasi_ortu';

    protected $fillable = [
        'siswa_id',
        'nama_ortu',
        'nomor_wa_ortu',
        'topik_konsultasi',
        'pesan',
        'tujuan',
        'status',
        'tanggapan_sekolah',
        'ditanggapi_oleh_id',
        'ditanggapi_at',
    ];

    protected $casts = [
        'ditanggapi_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function penanggap()
    {
        return $this->belongsTo(User::class, 'ditanggapi_oleh_id');
    }
}