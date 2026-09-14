<?php

namespace App\Services\Lms;

use App\Models\LmsMateri;
use App\Models\LmsPenugasanSiswa;
use App\Models\Siswa;
use Carbon\Carbon;

class LmsPersonalizationService
{
    /**
     * Tugaskan materi/modul secara spesifik per siswa (Diferensiasi Belajar / Pengayaan / Remedial / Ujikom / LKS).
     */
    public static function assignToStudent(string $materiId, string $siswaId, string $tipeJalur = 'reguler', ?string $targetSelesai = null): LmsPenugasanSiswa
    {
        return LmsPenugasanSiswa::updateOrCreate([
            'materi_id' => $materiId,
            'siswa_id' => $siswaId,
        ], [
            'tipe_jalur' => $tipeJalur,
            'target_selesai' => $targetSelesai ?: Carbon::today()->addDays(7)->toDateString(),
            'status_progres' => 'belum_mulai',
        ]);
    }

    /**
     * Tugaskan materi ke seluruh siswa dalam satu rombel kelas.
     */
    public static function assignToRombel(string $materiId, string $rombelId, string $tipeJalur = 'reguler'): int
    {
        $siswaList = Siswa::where('rombel_id', $rombelId)->get();
        $count = 0;

        foreach ($siswaList as $s) {
            self::assignToStudent($materiId, $s->id, $tipeJalur);
            $count++;
        }

        return $count;
    }

    /**
     * Hitung ringkasan progres belajar siswa di LMS.
     */
    public static function getStudentLearningSummary(string $siswaId): array
    {
        $penugasan = LmsPenugasanSiswa::with(['materi.guru', 'proyekLatihan'])
            ->where('siswa_id', $siswaId)
            ->get();

        $totalMateri = $penugasan->count();
        $selesai = $penugasan->where('status_progres', 'selesai')->count();
        $sedangBelajar = $penugasan->where('status_progres', 'sedang_belajar')->count();
        $belumMulai = $penugasan->where('status_progres', 'belum_mulai')->count();

        $modulUjikom = $penugasan->filter(fn($p) => $p->materi->kategori === 'ujikom_intensif')->count();
        $modulLks = $penugasan->filter(fn($p) => $p->materi->kategori === 'lks_khusus')->count();

        $persen = $totalMateri > 0 ? round(($selesai / $totalMateri) * 100, 1) : 0;

        return [
            'total_materi' => $totalMateri,
            'selesai' => $selesai,
            'sedang_belajar' => $sedangBelajar,
            'belum_mulai' => $belumMulai,
            'modul_ujikom' => $modulUjikom,
            'modul_lks' => $modulLks,
            'persentase_selesai' => $persen,
            'daftar_penugasan' => $penugasan,
        ];
    }
}