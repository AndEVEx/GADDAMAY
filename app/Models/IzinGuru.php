<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IzinGuru extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'izin_guru';

    protected $fillable = [
        'guru_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'file_lampiran',
        'guru_pengganti_id',
        'status',
        'diverifikasi_oleh_id',
        'catatan_waka',
        'waktu_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'waktu_verifikasi' => 'datetime',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function guruPengganti(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_pengganti_id');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh_id');
    }

    // =========================================================================
    // Helpers & Accessors
    // =========================================================================

    public function getJenisIzinLabelAttribute(): string
    {
        return match ($this->jenis_izin) {
            'izin' => 'Izin Pribadi',
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            'dinas' => 'Perjalanan Dinas',
            'tugas_luar' => 'Tugas Luar / Pelatihan',
            default => ucfirst($this->jenis_izin),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'menunggu' => ['text' => 'Menunggu Verifikasi', 'class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
            'disetujui' => ['text' => 'Disetujui Waka', 'class' => 'bg-success text-white', 'icon' => 'bi-check-circle-fill'],
            'ditolak' => ['text' => 'Ditolak', 'class' => 'bg-danger text-white', 'icon' => 'bi-x-circle-fill'],
            default => ['text' => 'Unknown', 'class' => 'bg-secondary text-white', 'icon' => 'bi-question-circle'],
        };
    }
}
