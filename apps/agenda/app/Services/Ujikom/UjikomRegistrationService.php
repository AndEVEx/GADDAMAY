<?php

namespace App\Services\Ujikom;

use App\Models\UjikomPendaftaran;
use Carbon\Carbon;

class UjikomRegistrationService
{
    /**
     * Buat nomor registrasi asesi unik: UKK-YYYY-XXXX
     */
    public static function generateNomorPendaftaran(): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = 'UKK-' . $year . '-';
        $count = UjikomPendaftaran::where('nomor_pendaftaran', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Daftarkan siswa ke skema sertifikasi Ujikom.
     */
    public static function registerStudent(string $siswaId, string $skemaId, string $tahunAjaran = '2025/2026'): UjikomPendaftaran
    {
        return UjikomPendaftaran::firstOrCreate([
            'siswa_id' => $siswaId,
            'skema_id' => $skemaId,
            'tahun_ajaran' => $tahunAjaran,
        ], [
            'nomor_pendaftaran' => self::generateNomorPendaftaran(),
            'status_verifikasi' => 'menunggu_verifikasi',
            'hasil_asesmen' => 'belum_dinilai',
        ]);
    }

    /**
     * Ringkasan statistik peserta Ujikom.
     */
    public static function getSummaryStatistics(): array
    {
        $all = UjikomPendaftaran::all();
        $totalPeserta = $all->count();
        $lolosBerkas = $all->where('status_verifikasi', 'lolos_administrasi')->count();
        $menunggu = $all->where('status_verifikasi', 'menunggu_verifikasi')->count();
        $kompeten = $all->where('hasil_asesmen', 'kompeten')->count();

        return [
            'total_peserta' => $totalPeserta,
            'lolos_berkas' => $lolosBerkas,
            'menunggu' => $menunggu,
            'kompeten' => $kompeten,
        ];
    }
}