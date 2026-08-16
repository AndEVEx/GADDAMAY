<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\AgendaHarian;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\NilaiKktp;
use App\Models\KktpSiswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Analytic Dashboard Progress KKTP')]
class ProgressTp extends Component
{
    public string $viewMode = 'overview'; // 'overview', 'mapel', 'kelas', 'siswa'
    public string $selectedMapel = '';
    public string $selectedRombel = '';
    public string $selectedTingkat = 'all'; // 'all', '10', '11', '12'
    public string $search = '';

    public function setViewMode(string $mode)
    {
        $this->viewMode = $mode;
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analisis KKTP');

        // Headers
        $sheet->setCellValue('A1', 'REKAPITULASI ANALITIK PROGRESS KKTP - SMKN 2 INDRAMAYU');
        $sheet->setCellValue('A2', 'Dicetak pada: ' . date('d F Y H:i') . ' WIB');

        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Mata Pelajaran');
        $sheet->setCellValue('C4', 'Kelas / Rombel');
        $sheet->setCellValue('D4', 'Total TP');
        $sheet->setCellValue('E4', 'Total Siswa');
        $sheet->setCellValue('F4', 'Nilai Tercapai');
        $sheet->setCellValue('G4', 'Nilai Belum');
        $sheet->setCellValue('H4', '% Ketercapaian');

        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setRGB('FFFFFF');

        $mapels = MataPelajaran::with(['tujuanPembelajaran', 'jadwalPelajaran.rombel'])->orderBy('nama_mapel')->get();
        $row = 5;
        $no = 1;

        foreach ($mapels as $mapel) {
            $tpCount = $mapel->tujuanPembelajaran->count();
            $rombels = $mapel->jadwalPelajaran->pluck('rombel')->filter()->unique('id');

            if ($rombels->isEmpty()) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $mapel->nama_mapel);
                $sheet->setCellValue('C' . $row, '-');
                $sheet->setCellValue('D' . $row, $tpCount);
                $sheet->setCellValue('E' . $row, 0);
                $sheet->setCellValue('F' . $row, 0);
                $sheet->setCellValue('G' . $row, 0);
                $sheet->setCellValue('H' . $row, '0%');
                $row++;
                continue;
            }

            foreach ($rombels as $rombel) {
                $siswaCount = Siswa::where('rombel_id', $rombel->id)->count();
                $tercapai = NilaiKktp::where('rombel_id', $rombel->id)
                    ->whereHas('tujuanPembelajaran', fn($q) => $q->where('mapel_id', $mapel->id))
                    ->where('status', 'tercapai')
                    ->count();
                $belum = NilaiKktp::where('rombel_id', $rombel->id)
                    ->whereHas('tujuanPembelajaran', fn($q) => $q->where('mapel_id', $mapel->id))
                    ->where('status', 'belum_tercapai')
                    ->count();

                $totalEntries = $tercapai + $belum;
                $pct = $totalEntries > 0 ? round(($tercapai / $totalEntries) * 100, 1) : 0;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $mapel->nama_mapel);
                $sheet->setCellValue('C' . $row, $rombel->nama_kelas);
                $sheet->setCellValue('D' . $row, $tpCount);
                $sheet->setCellValue('E' . $row, $siswaCount);
                $sheet->setCellValue('F' . $row, $tercapai);
                $sheet->setCellValue('G' . $row, $belum);
                $sheet->setCellValue('H' . $row, $pct . '%');
                $row++;
            }
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'rekap_analitik_kktp_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();
        $rombelList = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // 1. Overall System KPI Metrics
        $totalMapel = $mapelList->count();
        $totalTp = TujuanPembelajaran::count();
        $totalRombel = $rombelList->count();
        $totalSiswa = Siswa::count();

        $totalTercapai = NilaiKktp::where('status', 'tercapai')->count();
        $totalBelum = NilaiKktp::where('status', 'belum_tercapai')->count();
        $totalNilaiEntries = $totalTercapai + $totalBelum;
        $globalPercentage = $totalNilaiEntries > 0 ? round(($totalTercapai / $totalNilaiEntries) * 100, 1) : 0;

        // 2. Tingkat Analytics (X, XI, XII)
        $tingkatStats = [];
        foreach (['10' => 'Kelas X', '11' => 'Kelas XI', '12' => 'Kelas XII'] as $key => $label) {
            $rombelsInTingkat = Rombel::where('tingkat', $key)->pluck('id');
            $siswaInTingkat = Siswa::whereIn('rombel_id', $rombelsInTingkat)->count();
            
            $tercapaiTingkat = NilaiKktp::whereIn('rombel_id', $rombelsInTingkat)->where('status', 'tercapai')->count();
            $belumTingkat = NilaiKktp::whereIn('rombel_id', $rombelsInTingkat)->where('status', 'belum_tercapai')->count();
            $sumTingkat = $tercapaiTingkat + $belumTingkat;
            $pctTingkat = $sumTingkat > 0 ? round(($tercapaiTingkat / $sumTingkat) * 100, 1) : 0;

            $tingkatStats[$key] = [
                'label' => $label,
                'kelas_count' => $rombelsInTingkat->count(),
                'siswa_count' => $siswaInTingkat,
                'tercapai' => $tercapaiTingkat,
                'belum' => $belumTingkat,
                'percentage' => $pctTingkat,
            ];
        }

        // 3. Top & Bottom Mapel Performance
        $mapelAnalytics = $mapelList->map(function ($mapel) {
            $tpCount = TujuanPembelajaran::where('mapel_id', $mapel->id)->count();
            $tercapai = NilaiKktp::whereHas('tujuanPembelajaran', fn($q) => $q->where('mapel_id', $mapel->id))
                ->where('status', 'tercapai')
                ->count();
            $belum = NilaiKktp::whereHas('tujuanPembelajaran', fn($q) => $q->where('mapel_id', $mapel->id))
                ->where('status', 'belum_tercapai')
                ->count();
            $total = $tercapai + $belum;
            $pct = $total > 0 ? round(($tercapai / $total) * 100, 1) : 0;

            return (object) [
                'id' => $mapel->id,
                'nama_mapel' => $mapel->nama_mapel,
                'tp_count' => $tpCount,
                'tercapai' => $tercapai,
                'belum' => $belum,
                'total_entry' => $total,
                'percentage' => $pct,
            ];
        });

        $topMapel = $mapelAnalytics->where('total_entry', '>', 0)->sortByDesc('percentage')->take(5);
        $bottomMapel = $mapelAnalytics->where('total_entry', '>', 0)->sortBy('percentage')->take(5);

        // 4. Class (Rombel) Analytics
        $rombelAnalytics = $rombelList->when($this->selectedTingkat !== 'all', fn($q) => $q->where('tingkat', $this->selectedTingkat))
            ->map(function ($rombel) {
                $siswaCount = Siswa::where('rombel_id', $rombel->id)->count();
                $tercapai = NilaiKktp::where('rombel_id', $rombel->id)->where('status', 'tercapai')->count();
                $belum = NilaiKktp::where('rombel_id', $rombel->id)->where('status', 'belum_tercapai')->count();
                $total = $tercapai + $belum;
                $pct = $total > 0 ? round(($tercapai / $total) * 100, 1) : 0;

                return (object) [
                    'id' => $rombel->id,
                    'nama_kelas' => $rombel->nama_kelas,
                    'tingkat' => $rombel->tingkat,
                    'siswa_count' => $siswaCount,
                    'tercapai' => $tercapai,
                    'belum' => $belum,
                    'total' => $total,
                    'percentage' => $pct,
                ];
            });

        // 5. Specific Mapel / TP Details (When Mapel Tab Active)
        $detailTpList = collect();
        if ($this->selectedMapel) {
            $tps = TujuanPembelajaran::where('mapel_id', $this->selectedMapel)->orderBy('order_sequence')->get();
            $detailTpList = $tps->map(function ($tp) {
                $tercapai = NilaiKktp::where('tp_id', $tp->id)
                    ->when($this->selectedRombel, fn($q) => $q->where('rombel_id', $this->selectedRombel))
                    ->where('status', 'tercapai')
                    ->count();
                $belum = NilaiKktp::where('tp_id', $tp->id)
                    ->when($this->selectedRombel, fn($q) => $q->where('rombel_id', $this->selectedRombel))
                    ->where('status', 'belum_tercapai')
                    ->count();
                $total = $tercapai + $belum;
                $pct = $total > 0 ? round(($tercapai / $total) * 100, 1) : 0;

                return (object) [
                    'tp' => $tp,
                    'tercapai' => $tercapai,
                    'belum' => $belum,
                    'total' => $total,
                    'percentage' => $pct,
                ];
            });
        }

        // 6. Specific Siswa Matrix / List (When Siswa Tab Active)
        $siswaData = collect();
        if ($this->selectedRombel && $this->selectedMapel) {
            $siswaQuery = Siswa::where('rombel_id', $this->selectedRombel)
                ->when($this->search, fn($q) => $q->where('nama', 'like', '%' . $this->search . '%'))
                ->orderBy('nama')
                ->get();

            $mapelTps = TujuanPembelajaran::where('mapel_id', $this->selectedMapel)->get();
            $tpCount = $mapelTps->count();

            $siswaData = $siswaQuery->map(function ($siswa) use ($mapelTps, $tpCount) {
                $tercapai = NilaiKktp::where('siswa_id', $siswa->id)
                    ->whereIn('tp_id', $mapelTps->pluck('id'))
                    ->where('status', 'tercapai')
                    ->count();

                $pct = $tpCount > 0 ? round(($tercapai / $tpCount) * 100, 1) : 0;

                return (object) [
                    'siswa' => $siswa,
                    'total_tp' => $tpCount,
                    'tercapai' => $tercapai,
                    'belum' => $tpCount - $tercapai,
                    'percentage' => $pct,
                ];
            });
        }

        return view('livewire.monitoring.progress-tp', [
            'mapelList' => $mapelList,
            'rombelList' => $rombelList,
            'totalMapel' => $totalMapel,
            'totalTp' => $totalTp,
            'totalRombel' => $totalRombel,
            'totalSiswa' => $totalSiswa,
            'globalPercentage' => $globalPercentage,
            'totalTercapai' => $totalTercapai,
            'totalBelum' => $totalBelum,
            'tingkatStats' => $tingkatStats,
            'topMapel' => $topMapel,
            'bottomMapel' => $bottomMapel,
            'rombelAnalytics' => $rombelAnalytics,
            'detailTpList' => $detailTpList,
            'siswaData' => $siswaData,
        ]);
    }
}
