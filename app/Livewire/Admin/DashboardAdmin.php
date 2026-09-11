<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\HariLibur;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiKktp;
use App\Models\KehadiranMurid;
use Carbon\Carbon;
use Livewire\WithPagination;
use App\Models\AuditLog;
use App\Services\JadwalSwapService;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

#[Layout('components.layouts.app')]
#[Title('Dashboard Admin')]
class DashboardAdmin extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $searchLogin = '';
    public string $filterRole = 'guru'; // Default to guru as requested
    public int $loginLogsLimit = 15;
    public string $exportBulan = '';

    public function mount()
    {
        $this->exportBulan = Carbon::now('Asia/Jakarta')->format('Y-m');
    }

    public function updatingSearchLogin()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function tukarJadwalBlok()
    {
        $res = JadwalSwapService::swapAllVocationalBlockSchedules();

        if ($res['success']) {
            $this->dispatch('show-toast', message: $res['message'], type: 'success');
        } else {
            $this->dispatch('show-toast', message: $res['message'], type: 'warning');
        }
    }

    public function getOnlineUsersProperty(): array
    {
        $onlineUsers = [];
        $onlineIds = Cache::get('online_user_ids', []);

        foreach ($onlineIds as $id) {
            $data = Cache::get('user_online_' . $id);
            if ($data) {
                $onlineUsers[] = (object) $data;
            }
        }

        // If current auth user is not in list, add them
        if (auth()->check()) {
            $myId = auth()->id();
            $exists = collect($onlineUsers)->contains('id', $myId);
            if (!$exists) {
                $onlineUsers[] = (object) [
                    'id' => $myId,
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'role' => auth()->user()->role,
                    'last_seen_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ];
            }
        }

        // Sort by last_seen_at descending
        usort($onlineUsers, fn($a, $b) => strcmp($b->last_seen_at ?? '', $a->last_seen_at ?? ''));

        return $onlineUsers;
    }

    // ============================================================
    // FITUR EXPORT GLOBAL ADMIN (Item 4)
    // ============================================================

    public function exportRekapGuruBulanan()
    {
        $bulanStr = !empty($this->exportBulan) ? $this->exportBulan : Carbon::now('Asia/Jakarta')->format('Y-m');
        $startOfMonth = Carbon::parse($bulanStr)->startOfMonth();
        $endOfMonth = Carbon::parse($bulanStr)->endOfMonth();
        $namaBulan = Carbon::parse($bulanStr)->translatedFormat('F Y');

        $gurus = User::whereIn('role', ['guru', 'ketua_mgmp'])->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kehadiran Guru');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI KEHADIRAN & JAM MENGAJAR GURU');
        $sheet->setCellValue('A2', "SMKN 2 INDRAMAYU — PERIODE {$namaBulan}");
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = ['No', 'Nama Guru', 'Email / Akun', 'Role', 'Target JP', 'Realisasi JP Hadir', 'Izin (JP)', 'Sakit (JP)', '% Kehadiran'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '4', $h);
        }
        $sheet->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        $no = 1;

        foreach ($gurus as $guru) {
            // Calculate target JP in the month
            $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $guru->id))->get();
            $targetJp = 0;

            $curDate = $startOfMonth->copy();
            while ($curDate->lte($endOfMonth)) {
                $dayOfWeek = $curDate->dayOfWeekIso;
                if ($dayOfWeek <= 5 && !HariLibur::isHariLibur($curDate)) {
                    $jadwalsToday = $jadwals->where('hari', $dayOfWeek);
                    foreach ($jadwalsToday as $jt) {
                        $targetJp += max(1, (int)$jt->jam_ke_selesai - (int)$jt->jam_ke_mulai + 1);
                    }
                }
                $curDate->addDay();
            }

            // Calculate realisasi JP
            $agendas = AgendaHarian::where(fn($q) => $q->where('guru_id', $guru->id)->orWhere('guru_pengganti_id', $guru->id))
                ->whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->with('jadwalPelajaran')
                ->get();

            $realisasiJp = 0;
            $izinJp = 0;
            $sakitJp = 0;

            foreach ($agendas as $ag) {
                $jp = $ag->jadwalPelajaran ? max(1, (int)$ag->jadwalPelajaran->jam_ke_selesai - (int)$ag->jadwalPelajaran->jam_ke_mulai + 1) : 1;
                if (in_array($ag->status, ['selesai', 'berjalan', 'token_terverifikasi']) && $ag->status_kehadiran_guru === 'hadir') {
                    $realisasiJp += $jp;
                } elseif (in_array($ag->status_kehadiran_guru, ['izin', 'cuti', 'dinas', 'tugas_luar'])) {
                    $izinJp += $jp;
                } elseif ($ag->status_kehadiran_guru === 'sakit') {
                    $sakitJp += $jp;
                }
            }

            $persentase = $targetJp > 0 ? round(($realisasiJp / $targetJp) * 100, 1) : ($realisasiJp > 0 ? 100 : 0);

            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $guru->name);
            $sheet->setCellValue('C' . $rowNum, $guru->email);
            $sheet->setCellValue('D' . $rowNum, strtoupper($guru->role));
            $sheet->setCellValue('E' . $rowNum, $targetJp);
            $sheet->setCellValue('F' . $rowNum, $realisasiJp);
            $sheet->setCellValue('G' . $rowNum, $izinJp);
            $sheet->setCellValue('H' . $rowNum, $sakitJp);
            $sheet->setCellValue('I' . $rowNum, $persentase . '%');

            $rowNum++;
        }

        // Apply borders & auto size
        $sheet->getStyle('A4:I' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Rekap_Kehadiran_Guru_{$bulanStr}.xlsx";
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportRekapPresensiSiswa()
    {
        $bulanStr = !empty($this->exportBulan) ? $this->exportBulan : Carbon::now('Asia/Jakarta')->format('Y-m');
        $startOfMonth = Carbon::parse($bulanStr)->startOfMonth();
        $endOfMonth = Carbon::parse($bulanStr)->endOfMonth();
        $namaBulan = Carbon::parse($bulanStr)->translatedFormat('F Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Presensi Siswa');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI SISWA PER MATA PELAJARAN');
        $sheet->setCellValue('A2', "SMKN 2 INDRAMAYU — PERIODE {$namaBulan}");
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Total Sesi', 'Hadir', 'Sakit', 'Izin', 'Alpa / Belum'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '4', $h);
        }
        $sheet->getStyle('A4:J4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0891B2');
        $sheet->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        $no = 1;

        // Fetch students with attendance data
        $siswas = Siswa::with('rombel')->orderBy('rombel_id')->orderBy('nama')->get();
        $agendasInMonth = AgendaHarian::whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->whereNotNull('jadwal_pelajaran_id')
            ->pluck('id');

        $kehadiranRecords = KehadiranMurid::whereIn('agenda_harian_id', $agendasInMonth)
            ->with(['agendaHarian.jadwalPelajaran.mataPelajaran'])
            ->get()
            ->groupBy('siswa_id');

        foreach ($siswas as $siswa) {
            $records = $kehadiranRecords->get($siswa->id, collect());
            $byMapel = $records->groupBy(fn($r) => $r->agendaHarian?->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? 'Umum');

            if ($byMapel->isEmpty()) {
                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValue('B' . $rowNum, $siswa->nis ?? '-');
                $sheet->setCellValue('C' . $rowNum, $siswa->nama);
                $sheet->setCellValue('D' . $rowNum, $siswa->rombel?->nama_kelas ?? '-');
                $sheet->setCellValue('E' . $rowNum, 'Semua Mapel');
                $sheet->setCellValue('F' . $rowNum, 0);
                $sheet->setCellValue('G' . $rowNum, 0);
                $sheet->setCellValue('H' . $rowNum, 0);
                $sheet->setCellValue('I' . $rowNum, 0);
                $sheet->setCellValue('J' . $rowNum, 0);
                $rowNum++;
            } else {
                foreach ($byMapel as $mapelName => $mRecords) {
                    $total = $mRecords->count();
                    $hadir = $mRecords->where('status', 'hadir')->count();
                    $sakit = $mRecords->where('status', 'sakit')->count();
                    $izin = $mRecords->where('status', 'izin')->count();
                    $alpa = $mRecords->whereIn('status', ['alpa', 'tanpa_keterangan', 'belum_hadir'])->count();

                    $sheet->setCellValue('A' . $rowNum, $no++);
                    $sheet->setCellValue('B' . $rowNum, $siswa->nis ?? '-');
                    $sheet->setCellValue('C' . $rowNum, $siswa->nama);
                    $sheet->setCellValue('D' . $rowNum, $siswa->rombel?->nama_kelas ?? '-');
                    $sheet->setCellValue('E' . $rowNum, $mapelName);
                    $sheet->setCellValue('F' . $rowNum, $total);
                    $sheet->setCellValue('G' . $rowNum, $hadir);
                    $sheet->setCellValue('H' . $rowNum, $sakit);
                    $sheet->setCellValue('I' . $rowNum, $izin);
                    $sheet->setCellValue('J' . $rowNum, $alpa);
                    $rowNum++;
                }
            }
        }

        $sheet->getStyle('A4:J' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Rekap_Presensi_Siswa_{$bulanStr}.xlsx";
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportRekapKktpGlobal()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Capaian KKTP');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI KETERCAPAIAN TUJUAN PEMBELAJARAN (KKTP) GLOBAL');
        $sheet->setCellValue('A2', 'SMKN 2 INDRAMAYU');
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = ['No', 'Mata Pelajaran', 'Kode TP', 'Deskripsi Tujuan Pembelajaran (TP)', 'Kelas / Rombel', 'Total Siswa', 'Tercapai', 'Belum Tercapai', '% Ketercapaian'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '4', $h);
        }
        $sheet->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('059669');
        $sheet->getStyle('A4:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        $no = 1;

        $tps = TujuanPembelajaran::with('mataPelajaran')->orderBy('mapel_id')->orderBy('order_sequence')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        foreach ($tps as $tp) {
            $mapelName = $tp->mataPelajaran?->nama_mapel ?? '-';

            // Check which rombels take this mapel
            $activeRombelIds = JadwalPelajaran::where('mapel_id', $tp->mapel_id)->pluck('rombel_id')->unique();
            $targetRombels = $rombels->whereIn('id', $activeRombelIds);

            if ($targetRombels->isEmpty()) {
                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValue('B' . $rowNum, $mapelName);
                $sheet->setCellValue('C' . $rowNum, $tp->kode_tp);
                $sheet->setCellValue('D' . $rowNum, $tp->deskripsi_tp);
                $sheet->setCellValue('E' . $rowNum, 'Semua Kelas');
                $sheet->setCellValue('F' . $rowNum, 0);
                $sheet->setCellValue('G' . $rowNum, 0);
                $sheet->setCellValue('H' . $rowNum, 0);
                $sheet->setCellValue('I' . $rowNum, '0%');
                $rowNum++;
            } else {
                foreach ($targetRombels as $r) {
                    $totalSiswa = Siswa::where('rombel_id', $r->id)->count();
                    $tercapai = NilaiKktp::where('tp_id', $tp->id)->where('rombel_id', $r->id)->where('status', 'tercapai')->count();
                    $belum = NilaiKktp::where('tp_id', $tp->id)->where('rombel_id', $r->id)->where('status', 'belum_tercapai')->count();
                    $pct = $totalSiswa > 0 ? round(($tercapai / $totalSiswa) * 100, 1) : 0;

                    $sheet->setCellValue('A' . $rowNum, $no++);
                    $sheet->setCellValue('B' . $rowNum, $mapelName);
                    $sheet->setCellValue('C' . $rowNum, $tp->kode_tp);
                    $sheet->setCellValue('D' . $rowNum, $tp->deskripsi_tp);
                    $sheet->setCellValue('E' . $rowNum, $r->nama_kelas);
                    $sheet->setCellValue('F' . $rowNum, $totalSiswa);
                    $sheet->setCellValue('G' . $rowNum, $tercapai);
                    $sheet->setCellValue('H' . $rowNum, $belum);
                    $sheet->setCellValue('I' . $rowNum, $pct . '%');
                    $rowNum++;
                }
            }
        }

        $sheet->getStyle('A4:I' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Rekap_Capaian_KKTP_Global.xlsx";
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportSemuaSiswa()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Header Title
        $sheet->setCellValue('A1', 'DATA SELURUH PESERTA DIDIK SMKN 2 INDRAMAYU');
        $sheet->setCellValue('A2', 'Tanggal Export: ' . Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i'));
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = ['No', 'NIS', 'Nama Peserta Didik', 'Tingkat', 'Kelas / Rombel'];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '4', $h);
        }
        $sheet->getStyle('A4:E4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:E4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $siswas = Siswa::with('rombel')
            ->join('rombel', 'siswa.rombel_id', '=', 'rombel.id')
            ->orderBy('rombel.tingkat')
            ->orderBy('rombel.nama_kelas')
            ->orderBy('siswa.nama')
            ->select('siswa.*')
            ->get();

        $rowNum = 5;
        $no = 1;
        foreach ($siswas as $siswa) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $rowNum, $siswa->nama);
            $sheet->setCellValue('D' . $rowNum, $siswa->rombel?->tingkat_label ?? ($siswa->rombel?->tingkat ?? '-'));
            $sheet->setCellValue('E' . $rowNum, $siswa->rombel?->nama_kelas ?? '-');
            $rowNum++;
        }

        $lastRow = max(5, $rowNum - 1);
        $sheet->getStyle('A4:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A5:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(35);

        $filename = "Export_Seluruh_Siswa_" . date('Y-m-d') . ".xlsx";
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        // Fetch user login records (1 row per user / teacher as requested)
        $userLogins = User::query()
            ->when($this->searchLogin, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', "%{$this->searchLogin}%")
                       ->orWhere('email', 'like', "%{$this->searchLogin}%")
                       ->orWhere('last_login_ip', 'like', "%{$this->searchLogin}%");
                });
            })
            ->when($this->filterRole, function ($q) {
                $q->where('role', $this->filterRole);
            })
            ->orderByRaw('last_login_at IS NULL, last_login_at DESC')
            ->paginate($this->loginLogsLimit);

        return view('livewire.admin.dashboard-admin', [
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalKelas' => Rombel::count(),
            'totalMapel' => MataPelajaran::count(),
            'totalJadwal' => JadwalPelajaran::count(),
            'agendaHariIni' => AgendaHarian::where('tanggal', $today)->count(),
            'agendaSelesai' => AgendaHarian::where('tanggal', $today)->where('status', 'selesai')->count(),
            'agendaBerjalan' => AgendaHarian::where('tanggal', $today)->where('status', 'berjalan')->count(),
            'onlineUsers' => $this->onlineUsers,
            'userLogins' => $userLogins,
        ]);
    }
}

