<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaHarian extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'agenda_harian';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jadwal_pelajaran_id',
        'guru_id',
        'tanggal',
        'materi_diajarkan',
        'token_handshake',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'foto_bukti_path',
        'foto_guru_path',
        'prompter_custom',
        'refleksi',
        'status_kehadiran_guru',
        'guru_pengganti_id',
        'koreksi_oleh_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function jadwalPelajaran(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function guruPengganti(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_pengganti_id');
    }

    public function koreksiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koreksi_oleh_id');
    }

    public function tujuanPembelajaran(): BelongsToMany
    {
        return $this->belongsToMany(
            TujuanPembelajaran::class,
            'agenda_tp',
            'agenda_harian_id',
            'tp_id'
        );
    }

    public function kehadiranMurid(): HasMany
    {
        return $this->hasMany(KehadiranMurid::class);
    }

    public function kktpSiswa(): HasMany
    {
        return $this->hasMany(KktpSiswa::class);
    }

    // =========================================================================
    // Accessors & Helpers
    // =========================================================================

    /**
     * Get the duration (in minutes) between waktu_mulai and waktu_selesai.
     */
    public function getDurasiAttribute(): ?int
    {
        if ($this->waktu_mulai && $this->waktu_selesai) {
            return $this->waktu_mulai->diffInMinutes($this->waktu_selesai);
        }

        return null;
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isBerjalan(): bool
    {
        return $this->status === 'berjalan';
    }

    public function isTokenTerverifikasi(): bool
    {
        return $this->status === 'token_terverifikasi';
    }
}
