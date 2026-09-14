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
     * Official timetable periods for SMKN 2 Indramayu.
     */
    public static array $officialPeriods = [
        0  => ['mulai' => '06:25', 'selesai' => '06:45'], // Jam 0: Apel Pagi / Upacara
        1  => ['mulai' => '06:45', 'selesai' => '07:30'], // Jam 1
        2  => ['mulai' => '07:30', 'selesai' => '08:15'], // Jam 2
        3  => ['mulai' => '08:15', 'selesai' => '09:00'], // Jam 3
        4  => ['mulai' => '09:00', 'selesai' => '09:45'], // Jam 4
        5  => ['mulai' => '09:45', 'selesai' => '10:00'], // Jam 5: Istirahat 1
        6  => ['mulai' => '10:00', 'selesai' => '10:45'], // Jam 6
        7  => ['mulai' => '10:45', 'selesai' => '11:30'], // Jam 7
        8  => ['mulai' => '11:30', 'selesai' => '12:15'], // Jam 8
        9  => ['mulai' => '12:15', 'selesai' => '12:45'], // Jam 9: Istirahat 2 / Ishoma
        10 => ['mulai' => '12:45', 'selesai' => '13:30'], // Jam 10
        11 => ['mulai' => '13:30', 'selesai' => '14:15'], // Jam 11
        12 => ['mulai' => '14:15', 'selesai' => '15:00'], // Jam 12
    ];

    /**
     * Dapatkan rentang jam dan waktu untuk jadwal ini, termasuk jika merupakan bagian dari blok mengajar gabungan.
     */
    public function getEffectiveTimeRange(): array
    {
        $startJam = (int) $this->jam_ke_mulai;
        $endJam = (int) $this->jam_ke_selesai;

        // Jika mapel dan rombel ada, cek apakah ada jadwal bersambung di hari & rombel yang sama
        if ($this->mapel_id && $this->rombel_id) {
            $guruIds = $this->jadwalGuru()->pluck('guru_id')->toArray();

            $siblings = self::where('hari', $this->hari)
                ->where('rombel_id', $this->rombel_id)
                ->where('mapel_id', $this->mapel_id)
                ->where('id', '!=', $this->id)
                ->whereHas('jadwalGuru', fn($q) => $q->whereIn('guru_id', $guruIds))
                ->get();

            foreach ($siblings as $sibling) {
                $sStart = (int) $sibling->jam_ke_mulai;
                $sEnd = (int) $sibling->jam_ke_selesai;

                // Jika bersambung langsung atau hanya dipisahkan jam istirahat (jam 5 atau jam 9)
                $gap = $sStart > $endJam ? ($sStart - $endJam - 1) : ($startJam - $sEnd - 1);
                if ($gap <= 1) {
                    $startJam = min($startJam, $sStart);
                    $endJam = max($endJam, $sEnd);
                }
            }
        }

        $waktuMulai = self::$officialPeriods[$startJam]['mulai'] ?? '06:45';
        $waktuSelesai = self::$officialPeriods[$endJam]['selesai'] ?? '15:00';

        return [
            'jam_ke_mulai' => $startJam,
            'jam_ke_selesai' => $endJam,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
        ];
    }

    /**
     * Cek apakah saat ini berada dalam rentang jam pelajaran terkait berlangsung.
     */
    public function isWithinTeachingPeriod(?string $timeString = null): bool
    {
        $timeString = $timeString ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
        $range = $this->getEffectiveTimeRange();

        return $timeString >= $range['waktu_mulai'] && $timeString <= $range['waktu_selesai'];
    }

    /**
     * Cek apakah jam pelajaran terkait sudah berakhir.
     */
    public function hasPeriodEnded(?string $timeString = null): bool
    {
        $timeString = $timeString ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
        $range = $this->getEffectiveTimeRange();

        return $timeString > $range['waktu_selesai'];
    }

    /**
     * Check if this jadwal is a kegiatan khusus (non-regular lesson).
     */
    public function isKegiatanKhusus(): bool
    {
        return $this->kegiatan_khusus !== null;
    }
}
