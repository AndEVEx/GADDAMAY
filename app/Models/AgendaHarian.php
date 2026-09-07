<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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

    /**
     * Auto-close agendas whose scheduled period has ended.
     */
    public static function autoCloseExpiredAgendas(?string $rombelId = null, ?string $tanggal = null): int
    {
        $tanggal = $tanggal ?? Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $timeNow = Carbon::now('Asia/Jakarta')->format('H:i');

        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'],
            1  => ['mulai' => '06:45', 'selesai' => '07:30'],
            2  => ['mulai' => '07:30', 'selesai' => '08:15'],
            3  => ['mulai' => '08:15', 'selesai' => '09:00'],
            4  => ['mulai' => '09:00', 'selesai' => '09:45'],
            5  => ['mulai' => '09:45', 'selesai' => '10:00'],
            6  => ['mulai' => '10:00', 'selesai' => '10:45'],
            7  => ['mulai' => '10:45', 'selesai' => '11:30'],
            8  => ['mulai' => '11:30', 'selesai' => '12:15'],
            9  => ['mulai' => '12:15', 'selesai' => '12:45'],
            10 => ['mulai' => '12:45', 'selesai' => '13:30'],
            11 => ['mulai' => '13:30', 'selesai' => '14:15'],
            12 => ['mulai' => '14:15', 'selesai' => '15:00'],
        ];

        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $query = self::where(function ($q) use ($today) {
            // Sesi hari ini yang belum pernah dimulai sama sekali (hanya menunggu token)
            $q->where('tanggal', $today)
              ->where('status', 'menunggu_token');
        })->orWhere(function ($q) use ($today) {
            // Sesi dari hari-hari sebelumnya yang terlupakan
            $q->where('tanggal', '<', $today)
              ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan']);
        })->with('jadwalPelajaran');

        if ($rombelId) {
            $query->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $rombelId));
        }

        $activeAgendas = $query->get();
        $closedCount = 0;

        foreach ($activeAgendas as $agenda) {
            $jadwal = $agenda->jadwalPelajaran;
            if (!$jadwal) continue;

            // For today's unstarted tokens, only close if period has completely passed
            if ($agenda->tanggal === $today) {
                $endJamKey = (int) $jadwal->jam_ke_selesai;
                $endTimeStr = $officialPeriods[$endJamKey]['selesai'] ?? '15:00';
                if ($timeNow < $endTimeStr) {
                    continue; // Skip, still in progress
                }
            }

            $agenda->update([
                'status' => 'selesai',
                'waktu_selesai' => $agenda->waktu_selesai ?? Carbon::now('Asia/Jakarta'),
            ]);
            $closedCount++;
        }

        return $closedCount;
    }
}
