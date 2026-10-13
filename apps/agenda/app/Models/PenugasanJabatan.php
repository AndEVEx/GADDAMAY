<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanJabatan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'penugasan_jabatan';

    protected $fillable = [
        'master_jabatan_id',
        'tipe_subjek',
        'user_id',
        'siswa_id',
        'rombel_id',
        'tahun_ajaran',
        'nomor_sk',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function masterJabatan(): BelongsTo
    {
        return $this->belongsTo(MasterJabatan::class, 'master_jabatan_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function getNamaPejabatAttribute(): string
    {
        if ($this->tipe_subjek === 'guru') {
            return $this->guru->name ?? '-';
        }
        return $this->siswa->nama ?? '-';
    }

    public function getIdentitasPejabatAttribute(): string
    {
        if ($this->tipe_subjek === 'guru') {
            return 'NIP/NUPTK: ' . ($this->guru->email ?? '-');
        }
        return 'NIS: ' . ($this->siswa->nis ?? '-') . ' (' . ($this->siswa->rombel->nama_rombel ?? '-') . ')';
    }
}
