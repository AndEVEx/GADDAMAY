<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

#[Layout('components.layouts.app')]
#[Title('Rekap Absensi Siswa')]
class RekapAbsensiAdmin extends Component
{
    public ?string $selectedRombelId = null;
    public string $modeRekap = 'bulanan'; // 'bulanan' | 'harian'
    public string $selectedTanggal = '';
    public string $selectedBulan = '';
    public string $filterTingkat = 'all'; // 'all', '10', '11', '12'
    public string $search = '';

    public function mount()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->selectedTanggal = $now->format('Y-m-d');
        $this->selectedBulan = $now->format('Y-m');

        $rombels = $this->getRombels();
        if ($rombels->isNotEmpty()) {
            $this->selectedRombelId = $rombels->first()->id;
        }
    }

    public function selectRombel(string $rombelId)
    {
        $this->selectedRombelId = $rombelId;
    }

    public function getRombels()
    {
        $query = Rombel::query();

        if ($this->filterTingkat !== 'all') {
            $query->where('tingkat', $this->filterTingkat);
        }

        return $query->orderBy('tingkat')->orderBy('nama_kelas')->get();
    }

    public function exportExcel()
    {
        if (!$this->selectedRombelId) {
            $this->dispatch('show-toast', message: 'Pilih kelas terlebih dahulu.', type: 'danger');
            return;
        }

        $rombel = Rombel::find($this->selectedRombelId);
        if (!$rombel) return;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheetTitle = substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '_', $rombel->nama_kelas), 0, 30);
        $sheet->setTitle($sheetTitle);

        $this->populateSheetData($sheet, $rombel);

        $filename = 'Rekap_Absensi_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $rombel->nama_kelas) . '_' . date('Ymd_His') . '.xlsx';
        $tempPath = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportExcelAll()
    {
        $rombels = $this->getRombels();
        if ($rombels->isEmpty()) {
            $this->dispatch('show-toast', message: 'Tidak ada data kelas untuk diexport.', type: 'warning');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default empty sheet

        $usedTitles = [];

        foreach ($rombels as $rombel) {
            $rawTitle = preg_replace('/[\\\\\/\?\*\[\]:]/', '_', $rombel->nama_kelas);
            $cleanTitle = substr($rawTitle, 0, 28);
            if (isset($usedTitles[$cleanTitle])) {
                $usedTitles[$cleanTitle]++;
                $cleanTitle = substr($cleanTitle, 0, 26) . '_' . $usedTitles[$cleanTitle];
            } else {
                $usedTitles[$cleanTitle] = 1;
            }

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($cleanTitle);

            $this->populateSheetData($sheet, $rombel);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Rekap_Semua_Kelas_' . ($this->modeRekap === 'harian' ? $this->selectedTanggal : $this->selectedBulan) . '_' . date('Ymd_His') . '.xlsx';
        $tempPath = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    private function populateSheetData($sheet, Rombel $rombel): void
    {
        // Target rombel IDs (include block partner if exists)
        $targetRombelIds = [$rombel->id];
        $partner = $rombel->getPartnerBlockRombel();
        if ($partner) {
            $targetRombelIds[] = $partner->id;
        }

        // Ambil agenda sesuai mode
        $agendaQuery = AgendaHarian::whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', $targetRombelIds))
            ->with(['jadwalPelajaran.mataPelajaran', 'guru', 'kehadiranMurid']);

        if ($this->modeRekap === 'harian') {
            $agendaQuery->whereDate('tanggal', $this->selectedTanggal)->orderBy('waktu_mulai');
            $periodLabel = 'Tanggal: ' . Carbon::parse($this->selectedTanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
        } else {
            if ($this->selectedBulan !== 'all' && !empty($this->selectedBulan)) {
                $agendaQuery->where('tanggal', 'like', $this->selectedBulan . '%');
                $periodLabel = 'Bulan: ' . Carbon::createFromFormat('Y-m', $this->selectedBulan)->locale('id')->isoFormat('MMMM Y');
            } else {
                $periodLabel = 'Semua Bulan / Semester';
            }
            $agendaQuery->orderBy('tanggal')->orderBy('waktu_mulai');
        }

        $agendas = $agendaQuery->get();
        $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();

        // Header Dokumen Excel
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI SISWA — SMKN 2 INDRAMAYU');
        $sheet->setCellValue('A2', 'Kelas: ' . $rombel->nama_kelas . ' | ' . $periodLabel);
        $sheet->setCellValue('A3', 'Dicetak pada: ' . Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') . ' WIB');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('666666');

        // Table Header
        $rowHeader1 = 5;
        $sheet->setCellValue('A' . $rowHeader1, 'No');
        $sheet->setCellValue('B' . $rowHeader1, 'NIS');
        $sheet->setCellValue('C' . $rowHeader1, 'Nama Siswa');

        $colIdx = 4; // Column D
        foreach ($agendas as $i => $agenda) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $mapelCode = $agenda->jadwalPelajaran?->mataPelajaran?->kode_mapel ?? 'Mapel';
            
            if ($this->modeRekap === 'harian') {
                $label = $mapelCode . "\n(Sesi " . ($i + 1) . ")";
            } else {
                $tgl = Carbon::parse($agenda->tanggal)->format('d/m');
                $label = "P-" . ($i + 1) . "\n" . $tgl;
            }

            $sheet->setCellValue($colLetter . $rowHeader1, $label);
            $sheet->getStyle($colLetter . $rowHeader1)->getAlignment()->setWrapText(true);
            $colIdx++;
        }

        // Kolom Total
        $colH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colA = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colPersen = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);

        $sheet->setCellValue($colH . $rowHeader1, 'H');
        $sheet->setCellValue($colS . $rowHeader1, 'S');
        $sheet->setCellValue($colI . $rowHeader1, 'I');
        $sheet->setCellValue($colA . $rowHeader1, 'A');
        $sheet->setCellValue($colPersen . $rowHeader1, '% Hadir');

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx - 1);

        // Styling Table Header
        $sheet->getStyle("A{$rowHeader1}:{$lastColLetter}{$rowHeader1}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1A56DB'],
            ],
        ]);
        $sheet->getRowDimension($rowHeader1)->setRowHeight(32);

        // Isi Data Siswa
        $currentRow = 6;
        foreach ($siswaList as $no => $siswa) {
            $sheet->setCellValue('A' . $currentRow, $no + 1);
            $sheet->setCellValue('B' . $currentRow, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $currentRow, $siswa->nama);

            $cIdx = 4;
            $hCount = 0; $sCount = 0; $iCount = 0; $aCount = 0;

            foreach ($agendas as $agenda) {
                $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                $rec = $agenda->kehadiranMurid->firstWhere('siswa_id', $siswa->id);
                $st = $rec ? $rec->status : null;

                $badge = '-';
                if ($st === 'hadir') {
                    $badge = 'H'; $hCount++;
                    $sheet->getStyle($cLetter . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1E7DD');
                } elseif ($st === 'sakit') {
                    $badge = 'S'; $sCount++;
                    $sheet->getStyle($cLetter . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CFF4FC');
                } elseif ($st === 'izin') {
                    $badge = 'I'; $iCount++;
                    $sheet->getStyle($cLetter . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3CD');
                } elseif ($st === 'alpa') {
                    $badge = 'A'; $aCount++;
                    $sheet->getStyle($cLetter . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');
                }

                $sheet->setCellValue($cLetter . $currentRow, $badge);
                $sheet->getStyle($cLetter . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $cIdx++;
            }

            $totalSesi = count($agendas);
            $persen = $totalSesi > 0 ? round(($hCount / $totalSesi) * 100) : 0;

            $sheet->setCellValue($colH . $currentRow, $hCount);
            $sheet->setCellValue($colS . $currentRow, $sCount);
            $sheet->setCellValue($colI . $currentRow, $iCount);
            $sheet->setCellValue($colA . $currentRow, $aCount);
            $sheet->setCellValue($colPersen . $currentRow, $persen . '%');

            // Alignment
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$colH}{$currentRow}:{$colPersen}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $currentRow++;
        }

        // Border Table
        $lastRow = max(6, $currentRow - 1);
        $sheet->getStyle("A{$rowHeader1}:{$lastColLetter}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D7DE'],
                ],
            ],
        ]);

        // AutoSize
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(30);
        for ($c = 4; $c < $colIdx; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(9);
        }
    }

    public function render()
    {
        $allRombels = $this->getRombels();

        if ($this->selectedRombelId && !$allRombels->contains('id', $this->selectedRombelId)) {
            $this->selectedRombelId = $allRombels->first()?->id;
        }

        $selectedRombel = $this->selectedRombelId ? Rombel::find($this->selectedRombelId) : null;

        // Ambil Agenda Sesuai Mode
        $agendas = collect();
        if ($selectedRombel) {
            $targetRombelIds = [$selectedRombel->id];
            $partner = $selectedRombel->getPartnerBlockRombel();
            if ($partner) {
                $targetRombelIds[] = $partner->id;
            }

            $agendaQuery = AgendaHarian::whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', $targetRombelIds))
                ->with(['jadwalPelajaran.mataPelajaran', 'guru', 'guruPengganti', 'kehadiranMurid']);

            if ($this->modeRekap === 'harian') {
                $agendaQuery->whereDate('tanggal', $this->selectedTanggal)->orderBy('waktu_mulai');
            } else {
                if ($this->selectedBulan !== 'all' && !empty($this->selectedBulan)) {
                    $agendaQuery->where('tanggal', 'like', $this->selectedBulan . '%');
                }
                $agendaQuery->orderBy('tanggal')->orderBy('waktu_mulai');
            }

            $agendas = $agendaQuery->get();
        }

        // Ambil Siswa
        $siswaList = collect();
        if ($selectedRombel) {
            $siswaQuery = Siswa::where('rombel_id', $selectedRombel->id);
            if (!empty($this->search)) {
                $s = '%' . trim($this->search) . '%';
                $siswaQuery->where(function ($q) use ($s) {
                    $q->where('nama', 'like', $s)->orWhere('nis', 'like', $s);
                });
            }
            $siswaList = $siswaQuery->orderBy('nama')->get();
        }

        // Bangun Matrix
        $siswaMatrix = $siswaList->map(function ($siswa) use ($agendas) {
            $statuses = [];
            $h = 0; $s = 0; $i = 0; $a = 0;

            foreach ($agendas as $agenda) {
                $rec = $agenda->kehadiranMurid->firstWhere('siswa_id', $siswa->id);
                $st = $rec ? $rec->status : null;
                $statuses[$agenda->id] = $st;

                if ($st === 'hadir') $h++;
                elseif ($st === 'sakit') $s++;
                elseif ($st === 'izin') $i++;
                elseif ($st === 'alpa') $a++;
            }

            $totalSesi = count($agendas);
            $persenHadir = $totalSesi > 0 ? round(($h / $totalSesi) * 100) : 0;

            return (object) [
                'siswa' => $siswa,
                'statuses' => $statuses,
                'h' => $h,
                's' => $s,
                'i' => $i,
                'a' => $a,
                'persenHadir' => $persenHadir,
            ];
        });

        return view('livewire.admin.rekap-absensi-admin', [
            'allRombels' => $allRombels,
            'selectedRombel' => $selectedRombel,
            'agendas' => $agendas,
            'siswaMatrix' => $siswaMatrix,
        ]);
    }
}
