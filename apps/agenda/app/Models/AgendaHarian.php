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
        'alasan_terlambat',
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

    public function agendaTp(): HasMany
    {
        return $this->hasMany(AgendaTp::class, 'agenda_harian_id');
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
     * Handshake HARUS dilaksanakan selama jam pelajaran terkait berlangsung.
     * Jika jam pelajaran telah usai, agenda yang masih menunggu token otomatis dibatalkan/kadaluarsa.
     */
    public static function autoCloseExpiredAgendas(?string $rombelId = null, ?string $tanggal = null): int
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $now = Carbon::now('Asia/Jakarta');
        $timeNow = $now->format('H:i');

        $query = self::where(function ($q) use ($today) {
            // Sesi dari hari-hari sebelumnya yang terlupakan
            $q->where('tanggal', '<', $today)
              ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan']);
        })->orWhere(function ($q) use ($today) {
            // Sesi hari ini yang masih aktif
            $q->where('tanggal', $today)
              ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan']);
        });

        if ($rombelId) {
            $query->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $rombelId));
        }

        $activeAgendas = $query->with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.rombel', 'jadwalPelajaran.jadwalGuru'])->get();
        $closedCount = 0;

        foreach ($activeAgendas as $agenda) {
            $isPastDay = $agenda->tanggal < $today;
            $periodEnded = false;

            if ($isPastDay) {
                $periodEnded = true;
            } elseif ($agenda->jadwalPelajaran) {
                $periodEnded = $agenda->jadwalPelajaran->hasPeriodEnded($timeNow);
            }

            if ($periodEnded) {
                // If it was never verified during the teaching period, it expired -> dibatalkan
                $finalStatus = $agenda->status === 'menunggu_token' ? 'dibatalkan' : 'selesai';

                $agenda->update([
                    'status' => $finalStatus,
                    'waktu_selesai' => $agenda->waktu_selesai ?? Carbon::now('Asia/Jakarta'),
                ]);
                $closedCount++;
            }
        }

        return $closedCount;
    }
}
