<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rombel';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'asc_id',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Get the Roman numeral label for the tingkat.
     */
    public function getTingkatLabelAttribute(): string
    {
        return match ((int) $this->tingkat) {
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => (string) $this->tingkat,
        };
    }
}
