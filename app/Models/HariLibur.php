<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class HariLibur extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'hari_libur';

    protected $fillable = [
        'nama_hari_libur',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_libur',
        'keterangan',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // =========================================================================
    // Static Query Helpers
    // =========================================================================

    /**
     * Check if a given date is a holiday.
     * Returns the HariLibur model instance if true, or null if regular school day.
     */
    public static function isHariLibur(Carbon|string|null $date = null): ?self
    {
        if (!$date) {
            $date = Carbon::now('Asia/Jakarta');
        } elseif (is_string($date)) {
            $date = Carbon::parse($date, 'Asia/Jakarta');
        }

        $dateStr = $date->format('Y-m-d');

        return static::where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->first();
    }

    /**
     * Get all holidays in a given date range.
     */
    public static function getLiburInRange(Carbon|string $start, Carbon|string $end)
    {
        $startStr = is_string($start) ? $start : $start->format('Y-m-d');
        $endStr = is_string($end) ? $end : $end->format('Y-m-d');

        return static::where(function ($q) use ($startStr, $endStr) {
            $q->whereBetween('tanggal_mulai', [$startStr, $endStr])
              ->orWhereBetween('tanggal_selesai', [$startStr, $endStr])
              ->orWhere(function ($sub) use ($startStr, $endStr) {
                  $sub->where('tanggal_mulai', '<=', $startStr)
                      ->where('tanggal_selesai', '>=', $endStr);
              });
        })->orderBy('tanggal_mulai')->get();
    }

    /**
     * Get upcoming holidays starting from today.
     */
    public static function getUpcomingLibur(int $limit = 10)
    {
        $todayStr = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        return static::where('tanggal_selesai', '>=', $todayStr)
            ->orderBy('tanggal_mulai')
            ->take($limit)
            ->get();
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_libur) {
            'nasional' => 'Libur Nasional',
            'sekolah' => 'Libur Sekolah / Semester',
            'cuti_bersama' => 'Cuti Bersama',
            'khusus' => 'Kegiatan Khusus / Non-KBM',
            default => ucfirst(str_replace('_', ' ', $this->tipe_libur)),
        };
    }

    public function getTipeBadgeClassAttribute(): string
    {
        return match ($this->tipe_libur) {
            'nasional' => 'bg-danger text-white',
            'sekolah' => 'bg-primary text-white',
            'cuti_bersama' => 'bg-warning text-dark',
            'khusus' => 'bg-info text-dark',
            default => 'bg-secondary text-white',
        };
    }

    public function getDurasiHariAttribute(): int
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
        }
        return 1;
    }
}
