<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EkskulPresensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ekskul_presensi';

    protected $fillable = [
        'ekskul_pertemuan_id',
        'ekskul_anggota_id',
        'siswa_id',
        'status',
        'metode_presensi',
        'waktu_scan',
        'catatan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(EkskulPertemuan::class, 'ekskul_pertemuan_id');
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(EkskulAnggota::class, 'ekskul_anggota_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'H' => 'Hadir',
            'S' => 'Sakit',
            'I' => 'Izin',
            'A' => 'Alpa',
            default => '-',
        };
    }
}
