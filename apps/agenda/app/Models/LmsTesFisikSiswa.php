<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsTesFisikSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_tes_fisik_siswa';

    protected $fillable = [
        'siswa_id',
        'guru_olahraga_id',
        'tanggal_tes',
        'semester',
        'tahun_ajaran',
        'tinggi_badan_cm',
        'berat_badan_kg',
        'bmi',
        'kategori_bmi',
        'lari_1200m_detik',
        'push_up_1min',
        'sit_up_1min',
        'shuttle_run_detik',
        'sit_and_reach_cm',
        'skor_kebugaran',
        'predikat',
        'catatan_guru_olahraga',
    ];

    protected $casts = [
        'tanggal_tes' => 'date',
        'tinggi_badan_cm' => 'float',
        'berat_badan_kg' => 'float',
        'bmi' => 'float',
        'lari_1200m_detik' => 'integer',
        'push_up_1min' => 'integer',
        'sit_up_1min' => 'integer',
        'shuttle_run_detik' => 'float',
        'sit_and_reach_cm' => 'float',
        'skor_kebugaran' => 'float',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guruOlahraga()
    {
        return $this->belongsTo(User::class, 'guru_olahraga_id');
    }

    /**
     * Hitung BMI dan Kategori otomatis
     */
    public static function calculateBmi(?float $tbCm, ?float $bbKg): array
    {
        if (!$tbCm || !$bbKg || $tbCm <= 0 || $bbKg <= 0) {
            return ['bmi' => null, 'kategori' => null];
        }

        $tbMeter = $tbCm / 100;
        $bmi = round($bbKg / ($tbMeter * $tbMeter), 1);

        $kategori = match (true) {
            $bmi < 18.5 => 'Kurus (Underweight)',
            $bmi <= 25.0 => 'Ideal / Normal',
            $bmi <= 29.9 => 'Kelebihan Berat (Overweight)',
            default => 'Obesitas',
        };

        return ['bmi' => $bmi, 'kategori' => $kategori];
    }

    /**
     * Hitung Skor Kebugaran Rata-rata dan Predikat
     */
    public static function evaluateKebugaran(array $data): array
    {
        $skorList = [];

        // Evaluasi Push-up (1 min)
        if (isset($data['push_up_1min']) && is_numeric($data['push_up_1min'])) {
            $val = (int)$data['push_up_1min'];
            $skorList[] = match (true) {
                $val >= 35 => 100,
                $val >= 25 => 85,
                $val >= 18 => 75,
                $val >= 10 => 65,
                default => 50,
            };
        }

        // Evaluasi Sit-up (1 min)
        if (isset($data['sit_up_1min']) && is_numeric($data['sit_up_1min'])) {
            $val = (int)$data['sit_up_1min'];
            $skorList[] = match (true) {
                $val >= 38 => 100,
                $val >= 28 => 85,
                $val >= 20 => 75,
                $val >= 12 => 65,
                default => 50,
            };
        }

        // Evaluasi Lari 1200m (detik)
        if (isset($data['lari_1200m_detik']) && is_numeric($data['lari_1200m_detik']) && $data['lari_1200m_detik'] > 0) {
            $detik = (int)$data['lari_1200m_detik'];
            $skorList[] = match (true) {
                $detik <= 270 => 100, // < 4m 30s
                $detik <= 330 => 85,  // < 5m 30s
                $detik <= 390 => 75,  // < 6m 30s
                $detik <= 450 => 65,  // < 7m 30s
                default => 50,
            };
        }

        $skorRata = count($skorList) > 0 ? round(array_sum($skorList) / count($skorList), 1) : 70;

        $predikat = match (true) {
            $skorRata >= 90 => 'Sangat Baik (Prima)',
            $skorRata >= 80 => 'Baik',
            $skorRata >= 70 => 'Cukup',
            default => 'Kurang (Perlu Pembinaan)',
        };

        return ['skor' => $skorRata, 'predikat' => $predikat];
    }
}
