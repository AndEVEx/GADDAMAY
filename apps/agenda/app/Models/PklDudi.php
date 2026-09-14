<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PklDudi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pkl_dudi';

    protected $fillable = [
        'nama_instansi',
        'bidang_usaha',
        'alamat',
        'kota',
        'pimpinan_nama',
        'pimpinan_jabatan',
        'pembimbing_nama',
        'pembimbing_kontak',
        'latitude',
        'longitude',
        'radius_meter',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meter' => 'integer',
        'is_active' => 'boolean',
    ];

    public function penempatan()
    {
        return $this->hasMany(PklPenempatan::class, 'dudi_id');
    }
}