<?php

namespace App\Services\Pkl;

use App\Models\PklPenempatan;
use App\Models\PklPenilaian;

class PklAssessmentService
{
    /**
     * Hitung ringkasan statistik kehadiran siswa selama masa PKL.
     */
    public static function getAttendanceSummary(PklPenempatan $penempatan): array
    {
        $presensi = $penempatan->presensi;
        $totalHadir = $presensi->where('status_kehadiran', 'hadir')->count();
        $totalTerlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
        $totalIzin = $presensi->where('status_kehadiran', 'izin')->count();
        $totalSakit = $presensi->where('status_kehadiran', 'sakit')->count();
        $totalAlpa = $presensi->where('status_kehadiran', 'alpa')->count();
        $totalHari = $presensi->count();

        $persenHadir = $totalHari > 0 ? round((($totalHadir + $totalTerlambat) / $totalHari) * 100, 1) : 0;

        return [
            'total_hari' => $totalHari,
            'hadir' => $totalHadir,
            'terlambat' => $totalTerlambat,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpa' => $totalAlpa,
            'persentase_kehadiran' => $persenHadir,
        ];
    }

    /**
     * Simpan dan sinkronkan nilai akhir.
     */
    public static function saveAndRecalculate(PklPenilaian $penilaian): PklPenilaian
    {
        $penilaian->hitungNilaiAkhir();
        $penilaian->save();
        return $penilaian;
    }
}