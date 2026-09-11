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

    /**
     * Get the paired block rombel (e.g. TEORI <-> PRODUKTIF or Rombel 1 <-> Rombel 2 for vocational majors).
     */
    public function getPartnerBlockRombel(): ?Rombel
    {
        $name = trim($this->nama_kelas);
        $tingkat = $this->tingkat;

        $jurusanKeywords = ['TP', 'TPM', 'APHP', 'APHPi', 'NKPI', 'RPL'];
        $isBlock = false;
        $matchedJurusan = null;
        foreach ($jurusanKeywords as $kw) {
            if (stripos($name, $kw) !== false) {
                $isBlock = true;
                $matchedJurusan = $kw;
                break;
            }
        }

        if (!$isBlock) return null;

        $candidates = self::where('tingkat', $tingkat)
            ->where('id', '!=', $this->id)
            ->where('nama_kelas', 'like', "%{$matchedJurusan}%")
            ->get();

        if (stripos($name, 'TEORI') !== false) {
            return $candidates->first(fn($c) => stripos($c->nama_kelas, 'PRODUKTIF') !== false || preg_match('/(\b|_)2(\b|$)/', $c->nama_kelas));
        }
        if (stripos($name, 'PRODUKTIF') !== false) {
            return $candidates->first(fn($c) => stripos($c->nama_kelas, 'TEORI') !== false || preg_match('/(\b|_)1(\b|$)/', $c->nama_kelas));
        }

        if (preg_match('/(\b|_)1(\b|$)/', $name) || str_ends_with($name, '1')) {
            return $candidates->first(fn($c) => preg_match('/(\b|_)2(\b|$)/', $c->nama_kelas) || str_ends_with(trim($c->nama_kelas), '2') || stripos($c->nama_kelas, 'PRODUKTIF') !== false);
        }
        if (preg_match('/(\b|_)2(\b|$)/', $name) || str_ends_with($name, '2')) {
            return $candidates->first(fn($c) => preg_match('/(\b|_)1(\b|$)/', $c->nama_kelas) || str_ends_with(trim($c->nama_kelas), '1') || stripos($c->nama_kelas, 'TEORI') !== false);
        }

        return $candidates->first();
    }
}

