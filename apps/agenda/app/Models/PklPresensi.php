<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklPresensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pkl_presensi';

    protected $fillable = [
        'penempatan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'long_masuk',
        'jarak_masuk_meter',
        'foto_masuk',
        'lat_pulang',
        'long_pulang',
        'jarak_pulang_meter',
        'foto_pulang',
        'status_kehadiran',
        'is_in_radius',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'lat_masuk' => 'float',
        'long_masuk' => 'float',
        'lat_pulang' => 'float',
        'long_pulang' => 'float',
        'jarak_masuk_meter' => 'integer',
        'jarak_pulang_meter' => 'integer',
        'is_in_radius' => 'boolean',
    ];

    public function penempatan()
    {
        return $this->belongsTo(PklPenempatan::class, 'penempatan_id');
    }

    public function jurnal()
    {
        return $this->hasOne(PklJurnalHarian::class, 'presensi_id');
    }
}