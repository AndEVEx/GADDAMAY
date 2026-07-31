<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mata_pelajaran';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
        'asc_id',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function tujuanPembelajaran(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class, 'mapel_id')
                    ->orderBy('order_sequence');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'mapel_id');
    }
}
