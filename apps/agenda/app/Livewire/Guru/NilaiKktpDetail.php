<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\Siswa;
use App\Models\NilaiKktp;
use App\Models\KehadiranMurid;
use App\Models\AgendaHarian;

#[Layout('components.layouts.app')]
#[Title('Input Nilai KKTP')]
class NilaiKktpDetail extends Component
{
    public Rombel $rombel;
    public MataPelajaran $mapel;
    public array $nilaiData = []; // [siswa_id][tp_id] => 'tercapai' | 'belum_tercapai'

    public function getTpsProperty()
    {
        $userId = auth()->id();
        $query = TujuanPembelajaran::where('mapel_id', $this->mapel->id);

        // Filter by rombel tingkat
        $tingkat = $this->rombel->tingkat;
        if ($tingkat) {
            $query->where(function ($q) use ($tingkat) {
                $q->where('tingkat', $tingkat)
                  ->orWhereNull('tingkat'); // backward compat
            });
        }

        $hasTeacherTps = TujuanPembelajaran::where('mapel_id', $this->mapel->id)
            ->where('ketua_mgmp_id', $userId)
            ->exists();

        if ($hasTeacherTps) {
            $query->where(function ($q) use ($userId) {
                $q->where('ketua_mgmp_id', $userId)
                  ->orWhereNull('ketua_mgmp_id');
            });
        }

        $tps = $query->orderBy('order_sequence')->get();

        return $tps->unique(function ($tp) {
            return strtolower(trim(preg_replace('/\s+/', ' ', $tp->deskripsi_tp)));
        })->values();
    }

    public function mount(Rombel $rombel, MataPelajaran $mapel)
    {
        $this->rombel = $rombel;
        $this->mapel = $mapel;

        // Load existing NilaiKktp records
        $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();
        $tps = $this->tps;

        $existingNilai = NilaiKktp::where('rombel_id', $rombel->id)
            ->whereIn('tp_id', $tps->pluck('id'))
            ->get()
            ->keyBy(fn($n) => $n->siswa_id . '_' . $n->tp_id);

        // Get latest agenda for this rombel+mapel to check attendance
        $latestAgenda = AgendaHarian::whereHas('jadwalPelajaran', fn($q) => 
            $q->where('rombel_id', $rombel->id)->where('mapel_id', $mapel->id)
        )->latest('tanggal')->first();

        $absentSiswaIds = collect();
        if ($latestAgenda) {
            $absentSiswaIds = KehadiranMurid::where('agenda_harian_id', $latestAgenda->id)
                ->where('status', '!=', 'hadir')
                ->pluck('siswa_id');
        }

        foreach ($siswaList as $siswa) {
            foreach ($tps as $tp) {
                $key = $siswa->id . '_' . $tp->id;
                if (isset($existingNilai[$key])) {
                    $this->nilaiData[$siswa->id][$tp->id] = $existingNilai[$key]->status;
                } elseif ($absentSiswaIds->contains($siswa->id)) {
                    // Auto-mark absent students as belum_tercapai
                    $this->nilaiData[$siswa->id][$tp->id] = 'belum_tercapai';
                } else {
                    // Default: tercapai
                    $this->nilaiData[$siswa->id][$tp->id] = 'tercapai';
                }
            }
        }
    }

    public function toggleNilai(string $siswaId, string $tpId)
    {
        $current = $this->nilaiData[$siswaId][$tpId] ?? 'tercapai';
        $this->nilaiData[$siswaId][$tpId] = $current === 'tercapai' ? 'belum_tercapai' : 'tercapai';
    }

    public function setAllTp(string $tpId, string $status)
    {
        foreach ($this->nilaiData as $siswaId => $tps) {
            if (isset($this->nilaiData[$siswaId][$tpId])) {
                $this->nilaiData[$siswaId][$tpId] = $status;
            }
        }
    }

    public function simpanNilai()
    {
        $user = auth()->user();

        foreach ($this->nilaiData as $siswaId => $tps) {
            foreach ($tps as $tpId => $status) {
                NilaiKktp::updateOrCreate(
                    ['siswa_id' => $siswaId, 'tp_id' => $tpId, 'rombel_id' => $this->rombel->id],
                    ['status' => $status, 'guru_id' => $user->id]
                );
            }
        }

        $this->dispatch('show-toast', message: 'Nilai KKTP berhasil disimpan!', type: 'success');
    }

    public function exportExcel()
    {
        $siswaList = Siswa::where('rombel_id', $this->rombel->id)->orderBy('nama')->get();
        $tps = $this->tps;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai KKTP ' . substr($this->rombel->nama_kelas, 0, 20));

        // Header info
        $sheet->setCellValue('A1', 'DAFTAR NILAI KKTP — ' . $this->rombel->nama_kelas);
        $sheet->setCellValue('A2', 'Mata Pelajaran: ' . $this->mapel->nama_mapel . ' | Guru: ' . auth()->user()->name);

        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'NIS');
        $sheet->setCellValue('C4', 'Nama Siswa');

        $col = 'D';
        foreach ($tps as $tp) {
            $sheet->setCellValue($col . '4', $tp->kode_tp);
            $col++;
        }
        $sheet->setCellValue($col . '4', 'Tuntas');
        $col++;
        $sheet->setCellValue($col . '4', '%');

        $lastCol = $col;
        $sheet->getStyle('A4:' . $lastCol . '4')->getFont()->setBold(true);
        $sheet->getStyle('A4:' . $lastCol . '4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:' . $lastCol . '4')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 5;
        $no = 1;
        $tpCount = $tps->count();

        foreach ($siswaList as $siswa) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $row, $siswa->nama);

            $col = 'D';
            $tercapaiCount = 0;
            foreach ($tps as $tp) {
                $status = $this->nilaiData[$siswa->id][$tp->id] ?? 'tercapai';
                $sheet->setCellValue($col . $row, $status === 'tercapai' ? 'V' : 'X');
                if ($status === 'tercapai') $tercapaiCount++;
                $col++;
            }

            $sheet->setCellValue($col . $row, $tercapaiCount . '/' . $tpCount);
            $col++;
            $pct = $tpCount > 0 ? round(($tercapaiCount / $tpCount) * 100) : 0;
            $sheet->setCellValue($col . $row, $pct . '%');
            $row++;
        }

        foreach (range('A', $lastCol) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'Nilai_KKTP_' . str_replace(' ', '_', $this->rombel->nama_kelas) . '_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $siswaList = Siswa::where('rombel_id', $this->rombel->id)->orderBy('nama')->get();
        $tps = $this->tps;

        $headers = [
            ['name' => 'No', 'width' => '5%', 'align' => 'center'],
            ['name' => 'NIS', 'width' => '12%', 'align' => 'center'],
            ['name' => 'Nama Lengkap Siswa', 'width' => '30%', 'align' => 'left'],
        ];

        foreach ($tps as $tp) {
            $headers[] = ['name' => $tp->kode_tp, 'width' => '7%', 'align' => 'center'];
        }

        $headers[] = ['name' => 'Tuntas', 'width' => '10%', 'align' => 'center'];
        $headers[] = ['name' => '%', 'width' => '8%', 'align' => 'center'];

        $rows = [];
        $no = 1;
        $tpCount = $tps->count();

        foreach ($siswaList as $siswa) {
            $row = [
                $no++,
                e($siswa->nis ?? '-'),
                '<strong>' . e($siswa->nama) . '</strong>',
            ];

            $tercapaiCount = 0;
            foreach ($tps as $tp) {
                $status = $this->nilaiData[$siswa->id][$tp->id] ?? 'tercapai';
                if ($status === 'tercapai') {
                    $row[] = '<span style="color: green; font-weight: bold;">&#10004;</span>';
                    $tercapaiCount++;
                } else {
                    $row[] = '<span style="color: red; font-weight: bold;">&#10008;</span>';
                }
            }

            $pct = $tpCount > 0 ? round(($tercapaiCount / $tpCount) * 100) : 0;
            $row[] = $tercapaiCount . ' / ' . $tpCount;
            $row[] = '<strong>' . $pct . '%</strong>';

            $rows[] = $row;
        }

        $filename = 'Laporan_Nilai_KKTP_' . str_replace(' ', '_', $this->rombel->nama_kelas) . '_' . date('Y-m-d') . '.pdf';
        return \App\Services\PdfReportService::download(
            'DAFTAR NILAI KETERCAPAIAN KKTP SISWA',
            'Kelas: ' . $this->rombel->nama_kelas . ' | Mapel: ' . $this->mapel->nama_mapel,
            $headers,
            $rows,
            $filename,
            'A4',
            count($tps) > 5 ? 'landscape' : 'portrait',
            [
                'Kelas / Rombel' => $this->rombel->nama_kelas,
                'Mata Pelajaran' => $this->mapel->nama_mapel,
                'Guru Pengajar' => auth()->user()->name,
            ],
            auth()->user()->name,
            'Guru Mata Pelajaran'
        );
    }

    public function render()
    {
        $siswaList = Siswa::where('rombel_id', $this->rombel->id)->orderBy('nama')->get();
        $tps = $this->tps;

        return view('livewire.guru.nilai-kktp-detail', [
            'siswaList' => $siswaList,
            'tps' => $tps,
        ]);
    }
}
