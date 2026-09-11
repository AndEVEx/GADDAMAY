<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\HariLibur;
use App\Models\Siswa;
use App\Helpers\LogoHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class IkiExportController extends Controller
{
    /**
     * Export IKI Report to PDF with Kop Surat
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $includeJadwal = (bool) $request->get('jadwal', 1);
        $includeSiswa = (bool) $request->get('siswa', 1);
        $includeKehadiran = (bool) $request->get('kehadiran', 1);
        $bulan = $request->get('bulan', Carbon::now('Asia/Jakarta')->format('Y-m'));
        $stream = (int) $request->get('stream', 0);

        $startOfMonth = Carbon::parse($bulan)->startOfMonth();
        $endOfMonth = Carbon::parse($bulan)->endOfMonth();
        $namaBulan = Carbon::parse($bulan)->translatedFormat('F Y');

        $officialPeriods = JadwalPelajaran::$officialPeriods ?? [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'],
            1  => ['mulai' => '06:45', 'selesai' => '07:30'],
            2  => ['mulai' => '07:30', 'selesai' => '08:15'],
            3  => ['mulai' => '08:15', 'selesai' => '09:00'],
            4  => ['mulai' => '09:00', 'selesai' => '09:45'],
            5  => ['mulai' => '09:45', 'selesai' => '10:00'],
            6  => ['mulai' => '10:00', 'selesai' => '10:45'],
            7  => ['mulai' => '10:45', 'selesai' => '11:30'],
            8  => ['mulai' => '11:30', 'selesai' => '12:15'],
            9  => ['mulai' => '12:15', 'selesai' => '12:45'],
            10 => ['mulai' => '12:45', 'selesai' => '13:30'],
            11 => ['mulai' => '13:30', 'selesai' => '14:15'],
            12 => ['mulai' => '14:15', 'selesai' => '15:00'],
        ];

        // 1. DATA JADWAL MINGGUAN
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_ke_mulai')
            ->get();

        $jadwalList = $jadwals->map(function ($j) use ($officialPeriods) {
            $startKey = (int) $j->jam_ke_mulai;
            $endKey = (int) $j->jam_ke_selesai;
            $totalJp = max(1, $endKey - $startKey + 1);

            $waktuMulai = $officialPeriods[$startKey]['mulai'] ?? '06:45';
            $waktuSelesai = $officialPeriods[$endKey]['selesai'] ?? '15:00';

            return (object) [
                'hari_label' => $j->hari_label,
                'jam_range' => $startKey === $endKey ? "Jam ke-{$startKey}" : "Jam ke-{$startKey}-{$endKey}",
                'waktu_range' => "{$waktuMulai} - {$waktuSelesai}",
                'total_jp' => $totalJp,
                'rombel' => $j->rombel?->nama_kelas ?? ($j->kegiatan_khusus ?? 'Non-Kelas'),
                'mapel' => $j->mataPelajaran?->nama_mapel ?? ($j->kegiatan_khusus ?? 'Kegiatan Khusus'),
                'kode_mapel' => $j->mataPelajaran?->kode_mapel ?? '-',
            ];
        });
        $totalJpSeminggu = $jadwalList->sum('total_jp');

        // 2. DATA SISWA DIAJAR (Grouped by rombel + mapel, including block partners)
        $siswaTables = [];
        $uniqueSiswaIds = collect();

        $rombelMapelPairs = collect();
        foreach ($jadwals->whereNotNull('rombel_id')->whereNotNull('mapel_id') as $j) {
            if (!$j->rombel || !$j->mataPelajaran) continue;

            $k = $j->rombel_id . '_' . $j->mapel_id;
            if (!$rombelMapelPairs->has($k)) {
                $rombelMapelPairs->put($k, ['rombel' => $j->rombel, 'mapel' => $j->mataPelajaran]);
            }

            $partner = $j->rombel->getPartnerBlockRombel();
            if ($partner) {
                $pk = $partner->id . '_' . $j->mapel_id;
                if (!$rombelMapelPairs->has($pk)) {
                    $rombelMapelPairs->put($pk, ['rombel' => $partner, 'mapel' => $j->mataPelajaran]);
                }
            }
        }

        foreach ($rombelMapelPairs as $key => $pair) {
            $rombel = $pair['rombel'];
            $mapel = $pair['mapel'];

            $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();
            $uniqueSiswaIds = $uniqueSiswaIds->merge($siswaList->pluck('id'));

            $siswaTables[] = (object) [
                'rombel_nama' => $rombel->nama_kelas,
                'tingkat' => $rombel->tingkat,
                'mapel_nama' => $mapel->nama_mapel,
                'kode_mapel' => $mapel->kode_mapel,
                'siswa_list' => $siswaList,
                'total_siswa' => $siswaList->count(),
            ];
        }
        usort($siswaTables, fn($a, $b) => strcmp($a->rombel_nama . $a->mapel_nama, $b->rombel_nama . $b->mapel_nama));
        $totalSiswaUnik = $uniqueSiswaIds->unique()->count();

        // 3. DATA REALISASI KEHADIRAN BULANAN
        $targetJpTotal = 0;
        $targetPertemuanTotal = 0;
        $targetPerMapelKelas = [];

        foreach ($jadwals as $j) {
            $k = $j->rombel_id . '_' . $j->mapel_id;
            if (!isset($targetPerMapelKelas[$k])) {
                $targetPerMapelKelas[$k] = [
                    'rombel_nama' => $j->rombel?->nama_kelas ?? ($j->kegiatan_khusus ?? 'Non-Kelas'),
                    'mapel_nama' => $j->mataPelajaran?->nama_mapel ?? ($j->kegiatan_khusus ?? 'Kegiatan Khusus'),
                    'target_jp' => 0,
                    'realisasi_jp' => 0,
                    'izin_jp' => 0,
                    'sakit_jp' => 0,
                ];
            }
        }

        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dayOfWeek = $currentDate->dayOfWeekIso;
            if ($dayOfWeek <= 5) {
                if (!HariLibur::isHariLibur($currentDate)) {
                    $jadwalsToday = $jadwals->where('hari', $dayOfWeek);
                    foreach ($jadwalsToday as $jt) {
                        $jp = max(1, (int)$jt->jam_ke_selesai - (int)$jt->jam_ke_mulai + 1);
                        $targetJpTotal += $jp;
                        $targetPertemuanTotal++;

                        $k = $jt->rombel_id . '_' . $jt->mapel_id;
                        if (isset($targetPerMapelKelas[$k])) {
                            $targetPerMapelKelas[$k]['target_jp'] += $jp;
                        }
                    }
                }
            }
            $currentDate->addDay();
        }

        $agendas = AgendaHarian::where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)
                  ->orWhere('guru_pengganti_id', $user->id);
            })
            ->whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran'])
            ->orderBy('tanggal', 'asc')
            ->get();

        $realisasiJpTotal = 0;
        $totalIzinJp = 0;
        $totalSakitJp = 0;

        foreach ($agendas as $agenda) {
            $jp = 1;
            if ($agenda->jadwalPelajaran) {
                $jp = max(1, (int)$agenda->jadwalPelajaran->jam_ke_selesai - (int)$agenda->jadwalPelajaran->jam_ke_mulai + 1);
            }
            $agenda->durasi_jp = $jp;
            $k = ($agenda->jadwalPelajaran?->rombel_id ?? '') . '_' . ($agenda->jadwalPelajaran?->mapel_id ?? '');

            if (in_array($agenda->status, ['selesai', 'berjalan', 'token_terverifikasi']) && $agenda->status_kehadiran_guru === 'hadir') {
                $realisasiJpTotal += $jp;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['realisasi_jp'] += $jp;
                }
            } elseif (in_array($agenda->status_kehadiran_guru, ['izin', 'cuti', 'dinas', 'tugas_luar'])) {
                $totalIzinJp += $jp;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['izin_jp'] += $jp;
                }
            } elseif ($agenda->status_kehadiran_guru === 'sakit') {
                $totalSakitJp += $jp;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['sakit_jp'] += $jp;
                }
            }
        }

        $persentaseKehadiran = $targetJpTotal > 0 ? round(($realisasiJpTotal / $targetJpTotal) * 100, 1) : 0;
        $rekapKelas = collect($targetPerMapelKelas)->map(function ($item) {
            $target = $item['target_jp'];
            $real = $item['realisasi_jp'];
            $pct = $target > 0 ? round(($real / $target) * 100, 1) : 0;
            return (object) array_merge($item, [
                'persentase' => $pct,
                'selisih_jp' => $real - $target,
            ]);
        })->values();

        $data = [
            'guru' => $user,
            'includeJadwal' => $includeJadwal,
            'includeSiswa' => $includeSiswa,
            'includeKehadiran' => $includeKehadiran,
            'bulan' => $bulan,
            'namaBulan' => $namaBulan,
            'logoBase64' => LogoHelper::getBase64(),
            'tanggalCetak' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
            // Jadwal
            'jadwalList' => $jadwalList,
            'totalJpSeminggu' => $totalJpSeminggu,
            // Siswa
            'siswaTables' => $siswaTables,
            'totalSiswaUnik' => $totalSiswaUnik,
            // Kehadiran
            'targetJpTotal' => $targetJpTotal,
            'realisasiJpTotal' => $realisasiJpTotal,
            'totalIzinJp' => $totalIzinJp,
            'totalSakitJp' => $totalSakitJp,
            'persentaseKehadiran' => $persentaseKehadiran,
            'rekapKelas' => $rekapKelas,
            'agendas' => $agendas,
        ];

        $html = view('pdf.laporan-iki', $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeGuru = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
        $filename = "Laporan_IKI_{$safeGuru}_{$bulan}.pdf";

        if ($stream == 1) {
            return $dompdf->stream($filename, ['Attachment' => false]);
        }

        return $dompdf->stream($filename, ['Attachment' => true]);
    }
}