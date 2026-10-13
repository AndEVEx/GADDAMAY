<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EkskulAnggota extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ekskul_anggota';

    protected $fillable = [
        'ekskul_id',
        'siswa_id',
        'jabatan_anggota',
        'tahun_ajaran',
        'nomor_anggota',
        'status_keanggotaan',
        'nilai_predikat',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class, 'ekskul_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(EkskulPresensi::class, 'ekskul_anggota_id');
    }

    public function getPersentaseKehadiranAttribute(): int
    {
        $totalPertemuan = $this->ekskul->pertemuan()->count();
        if ($totalPertemuan === 0) return 100;

        $totalHadir = $this->presensi()->where('status', 'H')->count();
        return (int) round(($totalHadir / $totalPertemuan) * 100);
    }
}
