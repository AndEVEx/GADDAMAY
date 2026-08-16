<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use App\Models\NilaiKktp;
use App\Models\TujuanPembelajaran;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;
use App\Helpers\LogoHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class JurnalExportController extends Controller
{
    /**
     * Export Jurnal Per Kelas to PDF (Gabungan, Absensi, atau KKTP)
     */
    public function exportPdf(Request $request, Rombel $rombel)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', Carbon::now('Asia/Jakarta')->format('Y-m'));
        $tipe = $request->get('tipe', 'gabungan'); // 'gabungan', 'absensi', 'kktp'
        $stream = $request->get('stream', 0); // 1 = view in browser, 0 = force download

        $start = Carbon::parse($bulan)->startOfMonth()->format('Y-m-d');
        $end = Carbon::parse($bulan)->endOfMonth()->format('Y-m-d');
        $namaBulan = Carbon::parse($bulan)->translatedFormat('F Y');

        // 1. Fetch all completed/processed agendas for this teacher & class in the month
        $agendas = AgendaHarian::where('guru_id', $user->id)
            ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $rombel->id))
            ->whereBetween('tanggal', [$start, $end])
            ->with(['jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran', 'kehadiranMurid.siswa'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Assign pertemuan numbers
        $pertemuanCounter = 1;
        $seenDayMapel = [];
        foreach ($agendas as $agenda) {
            $dateStr = $agenda->tanggal?->format('Y-m-d') ?? $agenda->created_at?->format('Y-m-d');
            $mapelId = $agenda->jadwalPelajaran?->mapel_id ?? 0;
            $key = $dateStr . '_' . $mapelId;

            if (!isset($seenDayMapel[$key])) {
                $seenDayMapel[$key] = $pertemuanCounter++;
            }
            $agenda->pertemuan_ke = $seenDayMapel[$key];
            $agenda->week_of_month = Carbon::parse($agenda->tanggal)->weekOfMonth;
        }

        // 2. Fetch Absence records (Siswa Tidak Masuk: Sakit, Izin, Alpha)
        $agendaIds = $agendas->pluck('id');
        $absensiRecords = KehadiranMurid::whereIn('agenda_harian_id', $agendaIds)
            ->whereIn('status', ['sakit', 'izin', 'alpha'])
            ->with(['siswa', 'agendaHarian.jadwalPelajaran.mataPelajaran'])
            ->get()
            ->sortBy(fn($k) => $k->agendaHarian?->tanggal?->format('Y-m-d') . '_' . $k->siswa?->nama);

        // Summary statistics of absences
        $absenStats = [
            'sakit' => $absensiRecords->where('status', 'sakit')->count(),
            'izin' => $absensiRecords->where('status', 'izin')->count(),
            'alpha' => $absensiRecords->where('status', 'alpha')->count(),
            'total' => $absensiRecords->count(),
        ];

        // 3. Fetch Mapel list taught by this teacher for this rombel
        $mapelIds = $agendas->pluck('jadwalPelajaran.mapel_id')->filter()->unique();
        if ($mapelIds->isEmpty()) {
            $mapelIds = JadwalPelajaran::where('rombel_id', $rombel->id)
                ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
                ->pluck('mapel_id')->filter()->unique();
        }

        // 4. Fetch Students with Incomplete KKTP ('belum_tercapai')
        $allTps = TujuanPembelajaran::whereIn('mapel_id', $mapelIds)->orderBy('order_sequence')->get();
        $tpIds = $allTps->pluck('id');

        $belumTuntasKktp = NilaiKktp::where('rombel_id', $rombel->id)
            ->whereIn('tp_id', $tpIds)
            ->where('status', 'belum_tercapai')
            ->with(['siswa', 'tujuanPembelajaran.mataPelajaran'])
            ->get()
            ->groupBy('siswa_id');

        $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();

        $rekapSiswaKktp = $siswaList->map(function ($siswa) use ($belumTuntasKktp, $allTps) {
            $uncompleted = $belumTuntasKktp->get($siswa->id, collect());
            $totalTp = $allTps->count();
            $uncompletedCount = $uncompleted->count();
            $completedCount = max(0, $totalTp - $uncompletedCount);
            $pct = $totalTp > 0 ? round(($completedCount / $totalTp) * 100) : 0;

            return (object) [
                'siswa' => $siswa,
                'uncompleted_tps' => $uncompleted->pluck('tujuanPembelajaran'),
                'uncompleted_count' => $uncompletedCount,
                'completed_count' => $completedCount,
                'total_tp' => $totalTp,
                'percentage' => $pct,
                'status_tuntas' => $uncompletedCount === 0,
            ];
        });

        // Determine view template based on export type
        $viewName = match ($tipe) {
            'absensi' => 'pdf.rekap-absensi',
            'kktp' => 'pdf.rekap-kktp-belum-tuntas',
            default => 'pdf.jurnal-gabungan',
        };

        $data = [
            'rombel' => $rombel,
            'guru' => $user,
            'bulan' => $bulan,
            'namaBulan' => $namaBulan,
            'agendas' => $agendas,
            'absensiRecords' => $absensiRecords,
            'absenStats' => $absenStats,
            'allTps' => $allTps,
            'rekapSiswaKktp' => $rekapSiswaKktp,
            'siswaBelumTuntasOnly' => $rekapSiswaKktp->where('status_tuntas', false)->values(),
            'logoBase64' => LogoHelper::getBase64(),
            'tanggalCetak' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
        ];

        $html = view($viewName, $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeRombel = str_replace(' ', '_', $rombel->nama_kelas);
        $filename = "Jurnal_{$tipe}_{$safeRombel}_{$bulan}.pdf";

        if ($stream == 1) {
            return $dompdf->stream($filename, ['Attachment' => false]);
        }

        return $dompdf->stream($filename, ['Attachment' => true]);
    }
}
