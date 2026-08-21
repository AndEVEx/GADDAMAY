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
use App\Services\PdfReportService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Monitoring Mingguan')]
class MonitoringMingguan extends Component
{
    public string $currentDate = '';
    public string $filterStatus = 'semua'; // 'semua', 'selesai', 'izin', 'kosong'
    public string $filterHari = 'semua';   // 'semua', '1', '2', '3', '4', '5'
    public string $filterTingkat = 'all';
    public string $filterRombel = '';
    public string $filterGuru = '';
    public string $search = '';
    public string $sortBy = 'tanggal';     // 'tanggal', 'jam_ke', 'kelas', 'guru', 'status'
    public string $sortDirection = 'asc';

    public function mount()
    {
        if (empty($this->currentDate)) {
            $this->currentDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        }
    }

    public function setMingguIni()
    {
        $this->currentDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
    }

    public function prevWeek()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->subWeek()->format('Y-m-d');
    }

    public function nextWeek()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->addWeek()->format('Y-m-d');
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

    public function getWeeklyData()
    {
        $refDate = Carbon::parse($this->currentDate, 'Asia/Jakarta');
        $startOfWeek = $refDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $refDate->copy()->endOfWeek(Carbon::FRIDAY);

        // Fetch all agendas in this week range
        $agendas = AgendaHarian::with(['guru', 'guruPengganti', 'jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran'])
            ->whereBetween('tanggal', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d') . '_' . $item->jadwal_pelajaran_id);

        // Fetch approved leave in this week range
        $izins = IzinGuru::with(['guru', 'guruPengganti'])
            ->where('status', 'disetujui')
            ->whereBetween('created_at', [$startOfWeek->copy()->startOfDay(), $endOfWeek->copy()->endOfDay()])
            ->get();

        // Fetch all schedules (Senin to Jumat)
        $jadwals = JadwalPelajaran::with(['rombel', 'mataPelajaran', 'guru'])
            ->whereBetween('hari', [1, 5])
            ->when($this->filterTingkat !== 'all', function ($q) {
                $q->whereHas('rombel', fn($r) => $r->where('tingkat', $this->filterTingkat));
            })
            ->when(!empty($this->filterRombel), function ($q) {
                $q->where('rombel_id', $this->filterRombel);
            })
            ->when(!empty($this->filterGuru), function ($q) {
                $q->whereHas('guru', fn($g) => $g->where('users.id', $this->filterGuru));
            })
            ->orderBy('hari')
            ->orderBy('jam_ke_mulai')
            ->get();

        $items = collect();

        // Loop each day of the week (Senin to Jumat)
        for ($day = 1; $day <= 5; $day++) {
            if ($this->filterHari !== 'semua' && (int)$this->filterHari !== $day) {
                continue;
            }

            $currentDayDate = $startOfWeek->copy()->addDays($day - 1)->format('Y-m-d');
            $dayJadwals = $jadwals->where('hari', $day);

            foreach ($dayJadwals as $j) {
                $key = $currentDayDate . '_' . $j->id;
                $agenda = $agendas->get($key)?->first();
                $guruIds = $j->guru->pluck('id')->toArray();

                $matchedIzin = $izins->first(function ($izin) use ($guruIds, $currentDayDate, $j) {
                    if (!in_array($izin->guru_id, $guruIds)) return false;
                    if ($izin->created_at->format('Y-m-d') !== $currentDayDate) return false;
                    if ($izin->is_seharian) return true;
                    if (!empty($izin->jam_terpilih) && is_array($izin->jam_terpilih)) {
                        for ($k = $j->jam_ke_mulai; $k <= $j->jam_ke_selesai; $k++) {
                            if (in_array($k, $izin->jam_terpilih)) return true;
                        }
                    }
                    return false;
                });

                $status = 'kosong';
                $statusLabel = 'Belum Ada Guru / Kosong';
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
                        $status = 'selesai'; // Count as active/berjalan
                        $statusLabel = 'Sedang KBM (Aktif)';
                        $statusBadge = 'bg-primary bg-opacity-15 text-primary border border-primary';
                        $statusIcon = 'bi-broadcast';
                    }

                    if ($agenda->waktu_mulai) {
                        $waktuAktual = $agenda->waktu_mulai->format('H:i') . ($agenda->waktu_selesai ? ' - ' . $agenda->waktu_selesai->format('H:i') : ' WIB');
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
                    'tanggal' => $currentDayDate,
                    'tanggal_formatted' => Carbon::parse($currentDayDate)->translatedFormat('l, d M Y'),
                    'hari_nama' => $j->hari_label,
                    'hari_num' => $day,
                    'jam_ke_mulai' => $j->jam_ke_mulai,
                    'jam_ke_selesai' => $j->jam_ke_selesai,
                    'jam_display' => 'Jam ' . $j->jam_ke_mulai . ($j->jam_ke_mulai != $j->jam_ke_selesai ? ' - ' . $j->jam_ke_selesai : ''),
                    'rombel_id' => $j->rombel_id,
                    'rombel_nama' => $rombelName,
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
                ];

                // Search Filter
                if (!empty($this->search)) {
                    $term = strtolower($this->search);
                    $matches = str_contains(strtolower($rombelName), $term)
                        || str_contains(strtolower($mapelName), $term)
                        || str_contains(strtolower($guruNames), $term)
                        || str_contains(strtolower($materi), $term);
                    if (!$matches) continue;
                }

                // Status Filter
                if ($this->filterStatus !== 'semua' && $item->status !== $this->filterStatus) {
                    continue;
                }

                $items->push($item);
            }
        }

        // Sorting
        if ($this->sortBy === 'kelas') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('rombel_nama') : $items->sortByDesc('rombel_nama');
        } elseif ($this->sortBy === 'guru') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('guru_nama') : $items->sortByDesc('guru_nama');
        } elseif ($this->sortBy === 'status') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('status') : $items->sortByDesc('status');
        } elseif ($this->sortBy === 'jam_ke') {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('jam_ke_mulai') : $items->sortByDesc('jam_ke_mulai');
        } else {
            $items = $this->sortDirection === 'asc' ? $items->sortBy('tanggal') : $items->sortByDesc('tanggal');
        }

        return $items;
    }

    public function exportExcel()
    {
        $items = $this->getWeeklyData();
        $refDate = Carbon::parse($this->currentDate, 'Asia/Jakarta');
        $startOfWeek = $refDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $refDate->copy()->endOfWeek(Carbon::FRIDAY);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monitoring Mingguan');

        // Headers
        $sheet->setCellValue('A1', 'LAPORAN MONITORING MINGGUAN KBM — SMKN 2 INDRAMAYU');
        $sheet->setCellValue('A2', 'Rentang: ' . $startOfWeek->translatedFormat('d M Y') . ' s.d. ' . $endOfWeek->translatedFormat('d M Y') . ' | Filter Status: ' . strtoupper($this->filterStatus));

        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Hari & Tanggal');
        $sheet->setCellValue('C4', 'Jam Ke-');
        $sheet->setCellValue('D4', 'Kelas / Rombel');
        $sheet->setCellValue('E4', 'Mata Pelajaran / Kegiatan');
        $sheet->setCellValue('F4', 'Guru Pengajar');
        $sheet->setCellValue('G4', 'Guru Pengganti');
        $sheet->setCellValue('H4', 'Status KBM');
        $sheet->setCellValue('I4', 'Waktu Aktual');
        $sheet->setCellValue('J4', 'Materi Diajarkan');

        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:J4')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 5;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->tanggal_formatted);
            $sheet->setCellValue('C' . $row, $item->jam_display);
            $sheet->setCellValue('D' . $row, $item->rombel_nama);
            $sheet->setCellValue('E' . $row, $item->mapel_nama);
            $sheet->setCellValue('F' . $row, $item->guru_nama);
            $sheet->setCellValue('G' . $row, $item->guru_pengganti ?? '-');
            $sheet->setCellValue('H' . $row, $item->status_label);
            $sheet->setCellValue('I' . $row, $item->waktu_aktual);
            $sheet->setCellValue('J' . $row, $item->materi);
            $row++;
        }

        foreach (range('A', 'J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'Monitoring_Mingguan_' . $startOfWeek->format('Y-m-d') . '_' . $this->filterStatus . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $items = $this->getWeeklyData();
        $refDate = Carbon::parse($this->currentDate, 'Asia/Jakarta');
        $startOfWeek = $refDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $refDate->copy()->endOfWeek(Carbon::FRIDAY);

        $headers = [
            ['name' => 'No', 'width' => '4%', 'align' => 'center'],
            ['name' => 'Hari & Tgl', 'width' => '13%', 'align' => 'center'],
            ['name' => 'Jam', 'width' => '9%', 'align' => 'center'],
            ['name' => 'Kelas', 'width' => '10%', 'align' => 'center'],
            ['name' => 'Mata Pelajaran', 'width' => '20%', 'align' => 'left'],
            ['name' => 'Guru Pengajar', 'width' => '18%', 'align' => 'left'],
            ['name' => 'Status KBM', 'width' => '14%', 'align' => 'center'],
            ['name' => 'Waktu', 'width' => '12%', 'align' => 'center'],
        ];

        $rows = [];
        $no = 1;
        foreach ($items as $item) {
            $statusBadgeHtml = match ($item->status) {
                'selesai' => '<span style="color: #16a34a; font-weight: bold;">Selesai (Hadir)</span>',
                'izin' => '<span style="color: #d97706; font-weight: bold;">Izin/Sakit</span>',
                default => '<span style="color: #64748b;">Belum Mulai</span>',
            };

            $guruText = '<strong>' . e($item->guru_nama) . '</strong>';
            if ($item->guru_pengganti) {
                $guruText .= '<br><small style="color: #d97706;">Pengganti: ' . e($item->guru_pengganti) . '</small>';
            }

            $rows[] = [
                $no++,
                e($item->tanggal_formatted),
                e($item->jam_display),
                '<strong>' . e($item->rombel_nama) . '</strong>',
                e($item->mapel_nama),
                $guruText,
                $statusBadgeHtml,
                e($item->waktu_aktual),
            ];
        }

        $filename = 'Laporan_Monitoring_Mingguan_' . $startOfWeek->format('Y-m-d') . '.pdf';
        return PdfReportService::download(
            'LAPORAN MONITORING MINGGUAN PROSES BELAJAR MENGAJAR',
            'Periode: ' . $startOfWeek->translatedFormat('d F Y') . ' s.d. ' . $endOfWeek->translatedFormat('d F Y') . ' — SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'landscape',
            [
                'Rentang Minggu' => $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y'),
                'Filter Status' => ucfirst($this->filterStatus),
                'Filter Hari' => $this->filterHari !== 'semua' ? 'Hari ' . $this->filterHari : 'Semua Hari (Senin-Jumat)',
                'Total Sesi Terpantau' => count($rows) . ' Sesi',
            ],
            auth()->user()?->name,
            auth()->user()?->role === 'kepsek' ? 'Kepala Sekolah' : 'Waka Kurikulum / Verifikator'
        );
    }

    public function render()
    {
        $refDate = Carbon::parse($this->currentDate, 'Asia/Jakarta');
        $startOfWeek = $refDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $refDate->copy()->endOfWeek(Carbon::FRIDAY);

        $items = $this->getWeeklyData();

        // Total Weekly Stats
        $totalWeekly = $items->count();
        $selesaiWeekly = $items->where('status', 'selesai')->count();
        $izinWeekly = $items->where('status', 'izin')->count();
        $kosongWeekly = $items->where('status', 'kosong')->count();

        // 4 Percentage KPIs for Weekly Page
        // KPI 1: % KBM Terlaksana Minggu Ini
        $pctKbmMingguan = $totalWeekly > 0 ? round(($selesaiWeekly / $totalWeekly) * 100, 1) : 0;

        // KPI 2: % Rata-rata Guru Hadir Mingguan
        $distinctGurusSelesai = AgendaHarian::whereBetween('tanggal', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->where('status', 'selesai')->pluck('guru_id')->unique()->count();
        $distinctGurusTotal = User::whereIn('role', ['guru', 'ketua_mgmp'])->count();
        $pctGuruMingguan = $distinctGurusTotal > 0 ? round(($distinctGurusSelesai / $distinctGurusTotal) * 100, 1) : 0;

        // KPI 3: % KBM Terlaksana Hari Ini (Realtime Context)
        $todayStr = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $todayTotal = JadwalPelajaran::where('hari', Carbon::now('Asia/Jakarta')->dayOfWeekIso)->count();
        $todaySelesai = AgendaHarian::where('tanggal', $todayStr)->where('status', 'selesai')->count();
        $pctKbmHariIni = $todayTotal > 0 ? round(($todaySelesai / $todayTotal) * 100, 1) : 0;

        // KPI 4: % Tingkat Izin Mingguan
        $pctIzinMingguan = $totalWeekly > 0 ? round(($izinWeekly / $totalWeekly) * 100, 1) : 0;

        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $gurus = User::whereIn('role', ['guru', 'ketua_mgmp'])->orderBy('name')->get();

        return view('livewire.monitoring.monitoring-mingguan', [
            'items' => $items,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'totalWeekly' => $totalWeekly,
            'selesaiWeekly' => $selesaiWeekly,
            'izinWeekly' => $izinWeekly,
            'kosongWeekly' => $kosongWeekly,
            'pctKbmMingguan' => $pctKbmMingguan,
            'pctGuruMingguan' => $pctGuruMingguan,
            'pctKbmHariIni' => $pctKbmHariIni,
            'pctIzinMingguan' => $pctIzinMingguan,
            'rombels' => $rombels,
            'gurus' => $gurus,
        ]);
    }
}
