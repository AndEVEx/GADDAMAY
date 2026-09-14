<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklPenilaian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pkl_penilaian';

    protected $fillable = [
        'penempatan_id',
        'nilai_soft_integritas',
        'nilai_soft_etos_kerja',
        'nilai_soft_gotong_royong',
        'nilai_soft_kemandirian',
        'nilai_soft_disiplin',
        'nilai_hard_tp1',
        'nilai_hard_tp2',
        'nilai_hard_tp3',
        'nilai_hard_tp4',
        'nilai_total_dudi',
        'catatan_dudi',
        'dinilai_dudi_at',
        'nilai_sidang_sekolah',
        'penguji_sekolah_id',
        'catatan_penguji',
        'nilai_laporan_pkl',
        'nilai_akhir_angka',
        'predikat_huruf',
        'status_kelulusan',
    ];

    protected $casts = [
        'nilai_soft_integritas' => 'float',
        'nilai_soft_etos_kerja' => 'float',
        'nilai_soft_gotong_royong' => 'float',
        'nilai_soft_kemandirian' => 'float',
        'nilai_soft_disiplin' => 'float',
        'nilai_hard_tp1' => 'float',
        'nilai_hard_tp2' => 'float',
        'nilai_hard_tp3' => 'float',
        'nilai_hard_tp4' => 'float',
        'nilai_total_dudi' => 'float',
        'nilai_sidang_sekolah' => 'float',
        'nilai_laporan_pkl' => 'float',
        'nilai_akhir_angka' => 'float',
        'dinilai_dudi_at' => 'datetime',
    ];

    public function penempatan()
    {
        return $this->belongsTo(PklPenempatan::class, 'penempatan_id');
    }

    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_sekolah_id');
    }

    /**
     * Hitung otomatis Nilai Akhir (NA) berdasarkan rumus resmi SMKN 2 Indramayu:
     * NA = (5 * Nilai DUDI + 3 * Nilai Sidang Penguji + 2 * Nilai Laporan) / 10
     */
    public function hitungNilaiAkhir(): void
    {
        // 1. Hitung total DUDI jika soft/hard skill diisi
        $soft = ($this->nilai_soft_integritas + $this->nilai_soft_etos_kerja + $this->nilai_soft_gotong_royong + $this->nilai_soft_kemandirian + $this->nilai_soft_disiplin) / 5;
        $hard = ($this->nilai_hard_tp1 + $this->nilai_hard_tp2 + $this->nilai_hard_tp3 + $this->nilai_hard_tp4) / 4;
        
        $this->nilai_total_dudi = round(($soft + $hard) / 2, 2);

        // 2. Rumus resmi SMKN 2 Indramayu Bobot 5 : 3 : 2
        $na = ( (5 * $this->nilai_total_dudi) + (3 * $this->nilai_sidang_sekolah) + (2 * $this->nilai_laporan_pkl) ) / 10;
        $this->nilai_akhir_angka = round($na, 2);

        // 3. Predikat Huruf Mutu
        if ($this->nilai_akhir_angka >= 85) {
            $this->predikat_huruf = 'A';
            $this->status_kelulusan = 'lulus';
        } elseif ($this->nilai_akhir_angka >= 75) {
            $this->predikat_huruf = 'B';
            $this->status_kelulusan = 'lulus';
        } elseif ($this->nilai_akhir_angka >= 65) {
            $this->predikat_huruf = 'C';
            $this->status_kelulusan = 'tidak_lulus';
        } else {
            $this->predikat_huruf = 'D';
            $this->status_kelulusan = 'tidak_lulus';
        }
    }
}