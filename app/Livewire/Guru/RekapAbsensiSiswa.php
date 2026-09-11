<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
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
class RekapAbsensiSiswa extends Component
{
    public ?string $selectedRombelId = null;
    public ?string $selectedMapelId = null;
    public string $selectedBulan = 'all'; // 'all' or 'YYYY-MM'
    public string $search = '';

    public function mount(?string $rombel = null, ?string $mapel = null)
    {
        $pairs = $this->getAvailableClasses();

        if ($rombel && $mapel) {
            $this->selectedRombelId = $rombel;
            $this->selectedMapelId = $mapel;
        } elseif ($pairs->isNotEmpty()) {
            $first = $pairs->first();
            $this->selectedRombelId = $first->rombel_id;
            $this->selectedMapelId = $first->mapel_id;
        }
    }

    public function selectClass(string $rombelId, string $mapelId)
    {
        $this->selectedRombelId = $rombelId;
        $this->selectedMapelId = $mapelId;
    }

    public function getAvailableClasses()
    {
        $user = auth()->user();

        // 1. Ambil seluruh jadwal yang diampu guru
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('mapel_id')
            ->whereNotNull('rombel_id')
            ->with(['rombel', 'mataPelajaran'])
            ->get();

        $rombelMapelPairs = collect();
        foreach ($jadwals as $j) {
            if (!$j->rombel || !$j->mataPelajaran) continue;

            $k = $j->rombel_id . '_' . $j->mapel_id;
            if (!$rombelMapelPairs->has($k)) {
                $rombelMapelPairs->put($k, (object) [
                    'rombel_id' => $j->rombel_id,
                    'mapel_id' => $j->mapel_id,
                    'rombel_nama' => $j->rombel->nama_kelas,
                    'mapel_nama' => $j->mataPelajaran->nama_mapel,
                    'kode_mapel' => $j->mataPelajaran->kode_mapel,
                ]);
            }

            // Jika kelas blok vokasi (TP, RPL, APHP, NKPI), sertakan juga kelas pasangannya
            $partner = $j->rombel->getPartnerBlockRombel();
            if ($partner) {
                $pk = $partner->id . '_' . $j->mapel_id;
                if (!$rombelMapelPairs->has($pk)) {
                    $rombelMapelPairs->put($pk, (object) [
                        'rombel_id' => $partner->id,
                        'mapel_id' => $j->mapel_id,
                        'rombel_nama' => $partner->nama_kelas,
                        'mapel_nama' => $j->mataPelajaran->nama_mapel,
                        'kode_mapel' => $j->mataPelajaran->kode_mapel,
                    ]);
                }
            }
        }

        return $rombelMapelPairs->sortBy('rombel_nama')->values();
    }

    public function exportExcel()
    {
        if (!$this->selectedRombelId || !$this->selectedMapelId) {
            $this->dispatch('show-toast', message: 'Pilih kelas dan mata pelajaran terlebih dahulu.', type: 'danger');
            return;
        }

        $rombel = Rombel::find($this->selectedRombelId);
        $mapel = MataPelajaran::find($this->selectedMapelId);
        $user = auth()->user();

        if (!$rombel || !$mapel) return;

        // Ambil data pertemuan (agenda)
        $agendas = AgendaHarian::where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)->orWhere('guru_pengganti_id', $user->id);
            })
            ->whereHas('jadwalPelajaran', function ($q) {
                $q->where('rombel_id', $this->selectedRombelId)
                  ->where('mapel_id', $this->selectedMapelId);
            })
            ->when($this->selectedBulan !== 'all', fn($q) => $q->where('tanggal', 'like', $this->selectedBulan . '%'))
            ->with('kehadiranMurid')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr("Absen " . $rombel->nama_kelas, 0, 31));

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI SISWA');
        $sheet->setCellValue('A2', "Mata Pelajaran: {$mapel->nama_mapel} | Kelas: {$rombel->nama_kelas} | Guru: {$user->name}");
        $sheet->setCellValue('A3', 'Periode: ' . ($this->selectedBulan === 'all' ? 'Semua Pertemuan' : Carbon::parse($this->selectedBulan . '-01')->translatedFormat('F Y')));
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->setItalic(true);

        // Header Row 5
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'Nama Siswa');
        $sheet->setCellValue('D5', 'H');
        $sheet->setCellValue('E5', 'S');
        $sheet->setCellValue('F5', 'I');
        $sheet->setCellValue('G5', 'A');
        $sheet->setCellValue('H5', '% Hadir');

        $colIdx = 9; // Column I is index 9
        foreach ($agendas as $index => $ag) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $tgl = Carbon::parse($ag->tanggal)->format('d/m');
            $sheet->setCellValue($colLetter . '5', "P-" . ($index + 1) . "\n" . $tgl);
            $colIdx++;
        }

        // Style Table Header
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(8, $colIdx - 1));
        $sheet->getStyle("A5:{$lastColLetter}5")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A5:{$lastColLetter}5")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle("A5:{$lastColLetter}5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

        // Data Rows
        $row = 6;
        foreach ($siswaList as $idx => $siswa) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $row, $siswa->nama);

            $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;
            $currentCol = 9;

            foreach ($agendas as $ag) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
                $record = $ag->kehadiranMurid->firstWhere('siswa_id', $siswa->id);
                $status = $record ? strtolower($record->status) : '-';

                $code = '-';
                $color = 'FFFFFF';
                if ($status === 'hadir') {
                    $code = 'H';
                    $hadir++;
                    $color = 'D1E7DD'; // light green
                } elseif ($status === 'sakit') {
                    $code = 'S';
                    $sakit++;
                    $color = 'CFF4FC'; // light blue
                } elseif ($status === 'izin') {
                    $code = 'I';
                    $izin++;
                    $color = 'FFF3CD'; // light yellow
                } elseif (in_array($status, ['alpa', 'tanpa_keterangan'])) {
                    $code = 'A';
                    $alpa++;
                    $color = 'F8D7DA'; // light red
                }

                $sheet->setCellValue($colLetter . $row, $code);
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                if ($code !== '-') {
                    $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                    $sheet->getStyle($colLetter . $row)->getFont()->setBold(true);
                }

                $currentCol++;
            }

            $totalSesi = count($agendas);
            $pct = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;

            $sheet->setCellValue('D' . $row, $hadir);
            $sheet->setCellValue('E' . $row, $sakit);
            $sheet->setCellValue('F' . $row, $izin);
            $sheet->setCellValue('G' . $row, $alpa);
            $sheet->setCellValue('H' . $row, "{$pct}%");

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Borders
        $lastRow = $row - 1;
        $sheet->getStyle("A5:{$lastColLetter}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (['A', 'B', 'D', 'E', 'F', 'G', 'H'] as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(30);

        $filename = 'Rekap_Absensi_' . str_replace(' ', '_', $rombel->nama_kelas) . '_' . str_replace(' ', '_', $mapel->kode_mapel ?? $mapel->nama_mapel) . '.xlsx';
        $path = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $user = auth()->user();
        $availableClasses = $this->getAvailableClasses();

        $selectedRombel = $this->selectedRombelId ? Rombel::find($this->selectedRombelId) : null;
        $selectedMapel = $this->selectedMapelId ? MataPelajaran::find($this->selectedMapelId) : null;

        // Ambil daftar pertemuan (agenda harian)
        $agendas = collect();
        $matrixData = collect();
        $availableMonths = collect();

        if ($selectedRombel && $selectedMapel) {
            // Ambil semua bulan unik dari agenda guru untuk rombel+mapel ini
            $allAgendasQuery = AgendaHarian::where(function ($q) use ($user) {
                    $q->where('guru_id', $user->id)->orWhere('guru_pengganti_id', $user->id);
                })
                ->whereHas('jadwalPelajaran', function ($q) {
                    $q->where('rombel_id', $this->selectedRombelId)
                      ->where('mapel_id', $this->selectedMapelId);
                })
                ->orderBy('tanggal', 'asc');

            $allAgendas = $allAgendasQuery->get();
            $availableMonths = $allAgendas->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('Y-m'))->unique()->values();

            // Filter agendas by selectedBulan
            $agendas = $allAgendas->when($this->selectedBulan !== 'all', function ($collection) {
                return $collection->filter(fn($ag) => Carbon::parse($ag->tanggal)->format('Y-m') === $this->selectedBulan);
            })->values();

            // Ambil daftar siswa
            $siswaQuery = Siswa::where('rombel_id', $selectedRombel->id)->orderBy('nama');
            if (!empty($this->search)) {
                $s = '%' . trim($this->search) . '%';
                $siswaQuery->where(function ($q) use ($s) {
                    $q->where('nama', 'like', $s)->orWhere('nis', 'like', $s);
                });
            }
            $siswaList = $siswaQuery->get();

            // Load kehadiran records for these agendas
            $kehadirans = KehadiranMurid::whereIn('agenda_harian_id', $agendas->pluck('id'))->get();
            $kehadiranKeyed = $kehadirans->groupBy('agenda_harian_id')->map(fn($group) => $group->keyBy('siswa_id'));

            // Build Matrix Row for each student
            $matrixData = $siswaList->map(function ($siswa) use ($agendas, $kehadiranKeyed) {
                $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;
                $pertemuanStatuses = [];

                foreach ($agendas as $index => $ag) {
                    $record = $kehadiranKeyed->get($ag->id)?->get($siswa->id);
                    $rawStatus = $record ? strtolower($record->status) : null;

                    $status = '-';
                    if ($rawStatus === 'hadir') {
                        $status = 'H';
                        $hadir++;
                    } elseif ($rawStatus === 'sakit') {
                        $status = 'S';
                        $sakit++;
                    } elseif ($rawStatus === 'izin') {
                        $status = 'I';
                        $izin++;
                    } elseif (in_array($rawStatus, ['alpa', 'tanpa_keterangan'])) {
                        $status = 'A';
                        $alpa++;
                    }

                    $pertemuanStatuses[$ag->id] = $status;
                }

                $totalSesi = count($agendas);
                $persentase = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;

                return (object) [
                    'siswa' => $siswa,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'persentase' => $persentase,
                    'statuses' => $pertemuanStatuses,
                ];
            });
        }

        // Summary Stats
        $totalPertemuan = $agendas->count();
        $totalSiswa = $matrixData->count();
        $avgKehadiran = $totalSiswa > 0 ? round($matrixData->avg('persentase'), 1) : 0;

        return view('livewire.guru.rekap-absensi-siswa', [
            'availableClasses' => $availableClasses,
            'selectedRombel' => $selectedRombel,
            'selectedMapel' => $selectedMapel,
            'agendas' => $agendas,
            'matrixData' => $matrixData,
            'availableMonths' => $availableMonths,
            'totalPertemuan' => $totalPertemuan,
            'totalSiswa' => $totalSiswa,
            'avgKehadiran' => $avgKehadiran,
        ]);
    }
}
