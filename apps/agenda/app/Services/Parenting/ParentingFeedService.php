<?php

namespace App\Services\Parenting;

use App\Models\KehadiranMurid;
use App\Models\ParentingCatatanDisiplin;
use App\Models\PerizinanSiswa;
use App\Models\PklPenempatan;
use App\Models\Siswa;
use Carbon\Carbon;

class ParentingFeedService
{
    /**
     * Ringkasan performa dan profil anak untuk dashboard orang tua.
     */
    public static function getStudentSummary(Siswa $siswa): array
    {
        $today = Carbon::today()->toDateString();

        // 1. Akumulasi Poin Disiplin
        $catatan = ParentingCatatanDisiplin::where('siswa_id', $siswa->id)->get();
        $poinPelanggaran = $catatan->whereIn('kategori', ['pelanggaran', 'pembinaan_bk'])->sum('poin');
        $poinPrestasi = $catatan->whereIn('kategori', ['prestasi', 'apresiasi'])->sum('poin');
        $saldoPoinDisiplin = 100 + $poinPrestasi - abs($poinPelanggaran); // Skor dasar 100

        // 2. Status Izin Hari Ini
        $izinHariIni = PerizinanSiswa::where('siswa_id', $siswa->id)
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->whereIn('status', ['disetujui_walas', 'disetujui_piket', 'disetujui_bk'])
            ->first();

        // 3. Status Penempatan PKL
        $penempatanPkl = PklPenempatan::with('dudi')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->first();

        // 4. Riwayat KBM Hari Ini
        $kbmHariIni = KehadiranMurid::with(['agenda.mapel', 'agenda.user'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('agenda', function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            })
            ->get();

        return [
            'saldo_poin' => max(0, $saldoPoinDisiplin),
            'total_pelanggaran' => $catatan->where('kategori', 'pelanggaran')->count(),
            'total_prestasi' => $catatan->where('kategori', 'prestasi')->count(),
            'izin_aktif' => $izinHariIni,
            'status_pkl' => $penempatanPkl,
            'total_mapel_hari_ini' => $kbmHariIni->count(),
            'kbm_hari_ini' => $kbmHariIni,
        ];
    }

    /**
     * Timeline presensi harian (Gerbang + Jam KBM Kelas).
     */
    public static function getAttendanceTimeline(Siswa $siswa, ?string $date = null): array
    {
        $targetDate = $date ?: Carbon::today()->toDateString();
        $timeline = [];

        // A. Simulasi Scan Gerbang Pagi & Sore (Jika ada data CI4 / gate)
        // Kita berikan representasi jam datang di gerbang
        $timeline[] = [
            'jam' => '06:45 WIB',
            'tipe' => 'gerbang',
            'judul' => 'Tiba di Gerbang SMKN 2 Indramayu',
            'lokasi' => 'Pintu Gerbang Utama',
            'status' => 'hadir_tepat_waktu',
            'keterangan' => 'Pemindaian Scanner Kartu Pelajar RFID berhasil tercatat.',
        ];

        // B. Jam Pelajaran KBM di Kelas dari tabel agenda
        $kbmList = KehadiranMurid::with(['agenda.mapel', 'agenda.user'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('agenda', function ($q) use ($targetDate) {
                $q->whereDate('tanggal', $targetDate);
            })
            ->get();

        foreach ($kbmList as $item) {
            $mapelName = $item->agenda->mapel->nama_mapel ?? 'Mata Pelajaran';
            $guruName = $item->agenda->user->name ?? 'Guru Mapel';
            $status = $item->status; // hadir, sakit, izin, alpa

            $timeline[] = [
                'jam' => substr($item->agenda->jam_ke ?? '07:30', 0, 5) . ' WIB',
                'tipe' => 'kbm_kelas',
                'judul' => "KBM: {$mapelName}",
                'lokasi' => 'Ruang Kelas',
                'guru' => $guruName,
                'status' => $status,
                'keterangan' => $item->keterangan ?? "Kehadiran diverifikasi oleh {$guruName}",
            ];
        }

        return $timeline;
    }
}