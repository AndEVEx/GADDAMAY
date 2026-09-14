<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjikomSkema extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ujikom_skema';

    protected $fillable = [
        'kode_skema',
        'nama_skema',
        'jurusan',
        'deskripsi',
        'jumlah_unit_kompetensi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jumlah_unit_kompetensi' => 'integer',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(UjikomPendaftaran::class, 'skema_id');
    }
}