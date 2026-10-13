<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siswa';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'rombel_id',
        'nis',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function kehadiranMurid(): HasMany
    {
        return $this->hasMany(KehadiranMurid::class);
    }

    public function tesFisik(): HasMany
    {
        return $this->hasMany(LmsTesFisikSiswa::class, 'siswa_id');
    }

    public function tkaHasil(): HasMany
    {
        return $this->hasMany(LmsTkaHasilSiswa::class, 'siswa_id');
    }

    public function literasiPresensi(): HasMany
    {
        return $this->hasMany(LiterasiPresensiSiswa::class, 'siswa_id');
    }

    public function riwayatBacaPerpus(): HasMany
    {
        return $this->hasMany(PerpusRiwayatBaca::class, 'siswa_id');
    }

    public function perduptarPoin(): HasMany
    {
        return $this->hasMany(PerduptarPoinSiswa::class, 'siswa_id');
    }

    public function penugasanJabatan(): HasMany
    {
        return $this->hasMany(PenugasanJabatan::class, 'siswa_id');
    }

    public function ekskulAnggota(): HasMany
    {
        return $this->hasMany(EkskulAnggota::class, 'siswa_id');
    }

    public function ekskulPresensi(): HasMany
    {
        return $this->hasMany(EkskulPresensi::class, 'siswa_id');
    }

    public function getDaftarEkskulAktifAttribute(): string
    {
        $ekskulNames = $this->ekskulAnggota()
            ->where('status_keanggotaan', 'aktif')
            ->with('ekskul')
            ->get()
            ->pluck('ekskul.nama_ekskul')
            ->filter()
            ->toArray();

        return !empty($ekskulNames) ? implode(', ', $ekskulNames) : 'Pramuka Wajib / Paskibra Taruna';
    }

    /**
     * Total Poin Prestasi Kumulatif (Npr)
     */
    public function getTotalPoinPrestasi(): int
    {
        return (int) $this->perduptarPoin()->where('tipe', 'prestasi')->sum('bobot_poin');
    }

    /**
     * Total Poin Pelanggaran Kumulatif (Npl)
     */
    public function getTotalPoinPelanggaran(): int
    {
        return (int) abs($this->perduptarPoin()->where('tipe', 'pelanggaran')->sum('bobot_poin'));
    }

    /**
     * Nilai Kedisiplinan Taruna (Nk = Npr - Npl) Sesuai Pasal 32 Ayat 5 PERDUPTAR 2026
     */
    public function getNilaiKedisiplinan(): int
    {
        return $this->getTotalPoinPrestasi() - $this->getTotalPoinPelanggaran();
    }

    /**
     * Kategori Nilai Kedisiplinan Sesuai Pasal 32 Ayat 6 PERDUPTAR 2026
     */
    public function getKategoriKedisiplinan(): array
    {
        $nk = $this->getNilaiKedisiplinan();

        if ($nk > 20) {
            return ['grade' => 'A', 'label' => 'Baik Sekali', 'badge' => 'bg-success', 'status' => 'Aman'];
        } elseif ($nk >= -5) {
            return ['grade' => 'B', 'label' => 'Baik', 'badge' => 'bg-primary', 'status' => 'Standar Minimal'];
        } elseif ($nk >= -20) {
            return ['grade' => 'C', 'label' => 'Cukup', 'badge' => 'bg-warning text-dark', 'status' => 'Peringatan'];
        } else {
            return ['grade' => 'D', 'label' => 'Kurang', 'badge' => 'bg-danger', 'status' => 'Terancam Sanksi Berat'];
        }
    }

    public function getNamaSiswaAttribute(): string
    {
        return $this->nama ?? '';
    }
}
