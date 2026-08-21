<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\IzinGuru;
use App\Models\Rombel;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\JamPelajaran;
use App\Services\PdfReportService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Monitoring Harian')]
class MonitoringHarian extends Component
{
    public string $tanggal = '';
    public string $filterStatus = 'semua'; // 'semua', 'selesai', 'berjalan', 'izin', 'kosong'
    public string $filterTingkat = 'all';  // 'all', '10', '11', '12'
    public string $filterRombel = '';
    public string $filterGuru = '';
    public string $filterJam = '';
    public string $search = '';
    public string $sortBy = 'jam_ke';      // 'jam_ke', 'kelas', 'guru', 'status'
    public string $sortDirection = 'asc';

    public function mount()
    {
        if (empty($this->tanggal)) {
            $this->tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        }
    }

    public function setTanggal(string $date)
    {
        $this->tanggal = $date;
    }

    public function setHariIni()
    {
        $this->tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');
    }

    public function prevDay()
    {
        $this->tanggal = Carbon::parse($this->tanggal)->subDay()->format('Y-m-d');
    }

    public function nextDay()
    {
        $this->tanggal = Carbon::parse($this->tanggal)->addDay()->format('Y-m-d');
    }

    public function setFilterStatus(string $status)
    {
        $this->filterStatus = $status;
    }

    public function toggleSort(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getDailyData()
    {
        $date = Carbon::parse($this->tanggal, 'Asia/Jakarta');
        $dayOfWeek = $date->dayOfWeekIso; // 1=Senin ... 7=Minggu

        // 1. Get all scheduled lessons for this day
        $jadwals = JadwalPelajaran::with(['rombel', 'mataPelajaran', 'guru'])
            ->where('hari', $dayOfWeek)
            ->when($this->filterTingkat !== 'all', function ($q) {
                $q->whereHas('rombel', fn($r) => $r->where('tingkat', $this->filterTingkat));
            })
            ->when(!empty($this->filterRombel), function ($q) {
                $q->where('rombel_id', $this->filterRombel);
            })
            ->when(!empty($this->filterGuru), function ($q) {
                $q->whereHas('guru', fn($g) => $g->where('users.id', $this->filterGuru));
            })
            ->when(!empty($this->filterJam), function ($q) {
                $jam = (int) $this->filterJam;
                $q->where('jam_ke_mulai', '<=', $jam)->where('jam_ke_selesai', '>=', $jam);
            })
            ->orderBy('jam_ke_mulai')
            ->orderBy('rombel_id')
            ->get();

        // 2. Fetch existing AgendaHarian on this date
        $agendas = AgendaHarian::with(['guru', 'guruPengganti', 'jadwalPelajaran'])
            ->where('tanggal', $this->tanggal)
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        // 3. Fetch approved IzinGuru on this date
        $izins = IzinGuru::with(['guru', 'guruPengganti'])
            ->where('status', 'disetujui')
            ->whereDate('created_at', $this->tanggal)
            ->get();

        // Build composite row items
        $items = collect();

        foreach ($jadwals as $j) {
            $agenda = $agendas->get($j->id);
            $primaryGuru = $j->guru->first();
            $guruIds = $j->guru->pluck('id')->toArray();

            // Check if any assigned teacher has an approved leave
            $matchedIzin = $izins->first(function ($izin) use ($guruIds, $j) {
                if (!in_array($izin->guru_id, $guruIds)) return false;
                if ($izin->is_seharian) return true;
                if (!empty($izin->jam_terpilih) && is_array($izin->jam_terpilih)) {
                    for ($k = $j->jam_ke_mulai; $k <= $j->jam_ke_selesai; $k++) {
                        if (in_array($k, $izin->jam_terpilih)) return true;
                    }
                }
                return false;
            });

            // Determine status
            $status = 'kosong'; // Default: Kosong / Belum Mulai
            $statusLabel = 'Belum Dimulai / Kosong';
            $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
            $statusIcon = 'bi-circle';
            $waktuAktual = '-';
            $materi = '-';
            $guruPenggantiName = null;
            $fotoBukti = null;

            if ($agenda) {
                $fotoBukti = $agenda->foto_bukti_path ? asset('storage/' . $agenda->foto_bukti_path) : ($agenda->foto_guru_path ? asset('storage/' . $agenda->foto_guru_path) : null);
                $materi = $agenda->materi_diajarkan ?: '-';
                $guruPenggantiName = $agenda->guruPengganti?->name;

                if ($agenda->status === 'selesai') {
                    if (in_array($agenda->status_kehadiran_guru, ['izin', 'sakit', 'cuti'])) {
                        $status = 'izin';
                        $statusLabel = 'Izin (' . ucfirst($agenda->status_kehadiran_guru) . ')';
                        $statusBadge = 'bg-warning bg-opacity-20 text-warning-emphasis border border-warning';
                        $statusIcon = 'bi-exclamation-circle-fill';
                    } else {
                        $status = 'selesai';
                        $statusLabel = 'Selesai (Hadir)';
                        $statusBadge = 'bg-success bg-opacity-15 text-success border border-success';
                        $statusIcon = 'bi-check-circle-fill';
                    }
                } elseif ($agenda->status === 'berjalan' || $agenda->status === 'token_terverifikasi') {
                    $status = 'berjalan';
                    $statusLabel = 'Sedang KBM (Aktif)';
                    $statusBadge = 'bg-primary bg-opacity-15 text-primary border border-primary';
                    $statusIcon = 'bi-broadcast';
                }

                if ($agenda->waktu_mulai) {
                    $waktuAktual = $agenda->waktu_mulai->format('H:i') . ($agenda->waktu_selesai ? ' - ' . $agenda->waktu_selesai->format('H:i') : ' WIB (Berjalan)');
                }
            } elseif ($matchedIzin) {
                $status = 'izin';
                $statusLabel = 'Izin: ' . $matchedIzin->jenis_izin_label;
                $statusBadge = 'bg-warning bg-opacity-20 text-warning-emphasis border border-warning';
                $statusIcon = 'bi-clock-history';
                $guruPenggantiName = $matchedIzin->guruPengganti?->name;
                $materi = 'Alasan: ' . $matchedIzin->alasan;
            }

            $guruNames = $j->guru->pluck('name')->join(', ') ?: 'Belum ditentukan';
            $mapelName = $j->kegiatan_khusus ?: ($j->mataPelajaran->nama_mapel ?? 'Tanpa Mapel');
            $rombelName = $j->rombel->nama_kelas ?? '-';

            $item = (object) [
                'jadwal_id' => $j->id,
                'hari' => $j->hari_label,
                'jam_ke_mulai' => $j->jam_ke_mulai,
                'jam_ke_selesai' => $j->jam_ke_selesai,
                'jam_display' => 'Jam ' . $j->jam_ke_mulai . ($j->jam_ke_mulai != $j->jam_ke_selesai ? ' - ' . $j->jam_ke_selesai : ''),
                'rombel_id' => $j->rombel_id,
                'rombel_nama' => $rombelName,
                'tingkat' => $j->rombel?->tingkat,
                'mapel_nama' => $mapelName,
                'guru_nama' => $guruNames,
                'guru_pengganti' => $guruPenggantiName,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'status_icon' => $statusIcon,
                'waktu_aktual' => $waktuAktual,
                'materi' => $materi,
                'foto_bukti' => $fotoBukti,
                'agenda_id' => $agenda?->id,
            ];

            // Search filter
            if (!empty($this->search)) {
                $term = strtolower($this->search);
                $matches = str_contains(strtolower($rombelName), $term)
                    || str_contains(strtolower($mapelName), $term)
                    || str_contains(strtolower($guruNames), $term)
                    || str_contains(strtolower($materi), $term);
                if (!$matches) continue;
            }

            // Status filter
            if ($this->filterStatus !== 'semua' && $item->status !== $this->filterStatus) {
                continue;
            }

            $items->push($item);
        }

        // Apply Sorting
        if ($this->sortBy === 'kelas') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('rombel_nama') : $items->sortByDesc('rombel_nama');
        } elseif ($this->sortBy === 'guru') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('guru_nama') : $items->sortByDesc('guru_nama');
        } elseif ($this->sortBy === 'status') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('status') : $items->sortByDesc('status');
        } else {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('jam_ke_mulai') : $items->sortByDesc('jam_ke_mulai');
        }

        return $items;
    }

    public function exportExcel()
    {
        $items = $this->getDailyData();
        $date = Carbon::parse($this->tanggal, 'Asia/Jakarta');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monitoring ' . $date->format('d M'));

        // Header info
        $sheet->setCellValue('A1', 'LAPORAN MONITORING HARIAN KBM — SMKN 2 INDRAMAYU');
        $sheet->setCellValue('A2', 'Hari/Tanggal: ' . $date->translatedFormat('l, d F Y') . ' | Filter Status: ' . strtoupper($this->filterStatus));

        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Jam Ke-');
        $sheet->setCellValue('C4', 'Kelas / Rombel');
        $sheet->setCellValue('D4', 'Mata Pelajaran / Kegiatan');
        $sheet->setCellValue('E4', 'Guru Pengajar');
        $sheet->setCellValue('F4', 'Guru Pengganti');
        $sheet->setCellValue('G4', 'Status Sesi KBM');
        $sheet->setCellValue('H4', 'Waktu Aktual');
        $sheet->setCellValue('I4', 'Materi Diajarkan');

        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:I4')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 5;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->jam_display);
            $sheet->setCellValue('C' . $row, $item->rombel_nama);
            $sheet->setCellValue('D' . $row, $item->mapel_nama);
            $sheet->setCellValue('E' . $row, $item->guru_nama);
            $sheet->setCellValue('F' . $row, $item->guru_pengganti ?? '-');
            $sheet->setCellValue('G' . $row, $item->status_label);
            $sheet->setCellValue('H' . $row, $item->waktu_aktual);
            $sheet->setCellValue('I' . $row, $item->materi);
            $row++;
        }

        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'Monitoring_Harian_' . $date->format('Y-m-d') . '_' . $this->filterStatus . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $items = $this->getDailyData();
        $date = Carbon::parse($this->tanggal, 'Asia/Jakarta');

        $headers = [
            ['name' => 'No', 'width' => '4%', 'align' => 'center'],
            ['name' => 'Jam Ke-', 'width' => '10%', 'align' => 'center'],
            ['name' => 'Kelas', 'width' => '12%', 'align' => 'center'],
            ['name' => 'Mata Pelajaran', 'width' => '22%', 'align' => 'left'],
            ['name' => 'Guru Pengajar', 'width' => '20%', 'align' => 'left'],
            ['name' => 'Status KBM', 'width' => '14%', 'align' => 'center'],
            ['name' => 'Jam Aktual', 'width' => '10%', 'align' => 'center'],
            ['name' => 'Materi / Keterangan', 'width' => '8%', 'align' => 'left'],
        ];

        $rows = [];
        $no = 1;
        foreach ($items as $item) {
            $statusBadgeHtml = match ($item->status) {
                'selesai' => '<span style="color: #16a34a; font-weight: bold;">Selesai (Hadir)</span>',
                'berjalan' => '<span style="color: #2563eb; font-weight: bold;">Sedang KBM</span>',
                'izin' => '<span style="color: #d97706; font-weight: bold;">Izin/Sakit</span>',
                default => '<span style="color: #64748b;">Belum Mulai</span>',
            };

            $guruText = '<strong>' . e($item->guru_nama) . '</strong>';
            if ($item->guru_pengganti) {
                $guruText .= '<br><small style="color: #d97706;">Pengganti: ' . e($item->guru_pengganti) . '</small>';
            }

            $rows[] = [
                $no++,
                e($item->jam_display),
                '<strong>' . e($item->rombel_nama) . '</strong>',
                e($item->mapel_nama),
                $guruText,
                $statusBadgeHtml,
                e($item->waktu_aktual),
                e(mb_strimwidth($item->materi, 0, 45, '...')),
            ];
        }

        $filename = 'Laporan_Monitoring_Harian_' . $date->format('Y-m-d') . '.pdf';
        return PdfReportService::download(
            'LAPORAN MONITORING HARIAN PROSES BELAJAR MENGAJAR',
            'Hari/Tanggal: ' . $date->translatedFormat('l, d F Y') . ' — SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'landscape',
            [
                'Hari & Tanggal' => $date->translatedFormat('l, d F Y'),
                'Filter Status' => ucfirst($this->filterStatus),
                'Total Sesi Terpantau' => count($rows) . ' Sesi',
            ],
            auth()->user()?->name,
            auth()->user()?->role === 'kepsek' ? 'Kepala Sekolah' : 'Waka Kurikulum / Verifikator'
        );
    }

    public function render()
    {
        $date = Carbon::parse($this->tanggal, 'Asia/Jakarta');
        $items = $this->getDailyData();

        // 1. Daily Statistics
        $totalDaily = $items->count();
        $selesaiDaily = $items->where('status', 'selesai')->count();
        $berjalanDaily = $items->where('status', 'berjalan')->count();
        $izinDaily = $items->where('status', 'izin')->count();
        $kosongDaily = $items->where('status', 'kosong')->count();

        // 2. Calculate the 4 requested Percentage KPIs
        // KPI 1: % KBM Terlaksana Hari Ini (selesai / total * 100)
        $pctKbmHariIni = $totalDaily > 0 ? round(($selesaiDaily / $totalDaily) * 100, 1) : 0;

        // KPI 2: % Guru Mengajar Hari Ini (distinct active teachers / scheduled teachers * 100)
        $activeGuruIds = AgendaHarian::where('tanggal', $this->tanggal)
            ->whereIn('status', ['berjalan', 'selesai'])
            ->pluck('guru_id')->unique()->filter();
        
        $scheduledGuruIds = JadwalPelajaran::where('hari', $date->dayOfWeekIso)
            ->with('guru')->get()->pluck('guru')->flatten()->pluck('id')->unique();
        
        $pctGuruMengajar = $scheduledGuruIds->count() > 0 
            ? round(($activeGuruIds->count() / $scheduledGuruIds->count()) * 100, 1) 
            : 0;

        // KPI 3: % KBM Terlaksana Minggu Ini
        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $endOfWeek = $date->copy()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');

        $totalSesiMinggu = JadwalPelajaran::whereBetween('hari', [1, 5])->count();
        $selesaiMinggu = AgendaHarian::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->where('status', 'selesai')
            ->count();
        $pctKbmMingguIni = $totalSesiMinggu > 0 ? round(($selesaiMinggu / $totalSesiMinggu) * 100, 1) : 0;

        // KPI 4: % Tingkat Izin & Dispensasi (izin / total * 100)
        $pctIzinHariIni = $totalDaily > 0 ? round(($izinDaily / $totalDaily) * 100, 1) : 0;

        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $gurus = User::whereIn('role', ['guru', 'ketua_mgmp'])->orderBy('name')->get();

        return view('livewire.monitoring.monitoring-harian', [
            'items' => $items,
            'date' => $date,
            'totalDaily' => $totalDaily,
            'selesaiDaily' => $selesaiDaily,
            'berjalanDaily' => $berjalanDaily,
            'izinDaily' => $izinDaily,
            'kosongDaily' => $kosongDaily,
            'pctKbmHariIni' => $pctKbmHariIni,
            'pctGuruMengajar' => $pctGuruMengajar,
            'pctKbmMingguIni' => $pctKbmMingguIni,
            'pctIzinHariIni' => $pctIzinHariIni,
            'rombels' => $rombels,
            'gurus' => $gurus,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
        ]);
    }
}
