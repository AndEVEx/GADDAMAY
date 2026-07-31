<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jadwal_pelajaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hari',
        'jam_ke_mulai',
        'jam_ke_selesai',
        'rombel_id',
        'mapel_id',
        'keterangan',
        'kegiatan_khusus',
        'asc_lesson_id',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function jadwalGuru(): HasMany
    {
        return $this->hasMany(JadwalGuru::class);
    }

    /**
     * The guru (users) assigned to this jadwal through the pivot table.
     */
    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jadwal_guru', 'jadwal_pelajaran_id', 'guru_id');
    }

    public function agendaHarian(): HasMany
    {
        return $this->hasMany(AgendaHarian::class);
    }

    // =========================================================================
    // Accessors & Helpers
    // =========================================================================

    /**
     * Get the Indonesian day name label for hari.
     */
    public function getHariLabelAttribute(): string
    {
        return match ((int) $this->hari) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            default => 'Hari ' . $this->hari,
        };
    }

    /**
     * Check if this jadwal is a kegiatan khusus (non-regular lesson).
     */
    public function isKegiatanKhusus(): bool
    {
        return $this->kegiatan_khusus !== null;
    }
}
