<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJabatan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_jabatan';

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'kategori',
        'level_hierarki',
        'deskripsi_tugas',
        'delegasi_modul',
        'hak_akses',
        'is_aktif',
    ];

    protected $casts = [
        'level_hierarki' => 'integer',
        'is_aktif' => 'boolean',
    ];

    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanJabatan::class, 'master_jabatan_id');
    }
}
