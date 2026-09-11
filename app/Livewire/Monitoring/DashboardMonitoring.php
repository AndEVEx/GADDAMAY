<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\JamPelajaran;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Monitoring')]
class DashboardMonitoring extends Component
{
    public int $hariIni;
    public string $tanggal;
    public int $maxJam = 12;
    public ?int $currentJam = null;
    public ?int $selectedJam = null;
    public string $search = '';

    public ?array $modalDetail = null;
    public bool $showDetailModal = false;
    public string $rekapBulan = '';
    public string $searchGuruRekap = '';

    public function mount()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->hariIni = $now->dayOfWeekIso;
        $this->tanggal = $now->format('Y-m-d');
        $this->rekapBulan = $now->format('Y-m');

        // Dynamically get maximum jam_ke from database (defaults to 12)
        $this->maxJam = JamPelajaran::max('jam_ke') ?? 12;

        $this->detectLiveJam();
    }

    public function openDetailModal($rombelId, $jadwalId = null, $agendaId = null)
    {
        $rombel = Rombel::find($rombelId);
        $jadwal = $jadwalId ? JadwalPelajaran::with(['mataPelajaran', 'jadwalGuru.guru'])->find($jadwalId) : null;
        $agenda = $agendaId ? AgendaHarian::with(['guru', 'kehadiranMurid', 'agendaTp.tujuanPembelajaran'])->find($agendaId) : null;

        if (!$agenda && $jadwal) {
            $agenda = AgendaHarian::where('jadwal_pelajaran_id', $jadwal->id)
                ->where('tanggal', $this->tanggal)
                ->with(['guru', 'kehadiranMurid', 'agendaTp.tujuanPembelajaran'])
                ->first();
        }

        // TP list
        $tps = collect();
        if ($jadwal && $jadwal->mapel_id) {
            $tps = \App\Models\TujuanPembelajaran::where('mapel_id', $jadwal->mapel_id)
                ->orderBy('order_sequence')
                ->get();
        }

        // Attendance stats
        $presensiStats = [
            'total' => 0,
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
        ];

        if ($agenda) {
            $murids = $agenda->kehadiranMurid;
            $presensiStats['total'] = $murids->count();
            $presensiStats['hadir'] = $murids->where('status', 'hadir')->count();
            $presensiStats['sakit'] = $murids->where('status', 'sakit')->count();
            $presensiStats['izin'] = $murids->where('status', 'izin')->count();
            $presensiStats['alpa'] = $murids->whereIn('status', ['alpa', 'tanpa_keterangan', 'belum_hadir'])->count();
        } elseif ($rombel) {
            $presensiStats['total'] = \App\Models\Siswa::where('rombel_id', $rombel->id)->count();
        }

        $this->modalDetail = [
            'rombel' => $rombel,
            'jadwal' => $jadwal,
            'agenda' => $agenda,
            'tps' => $tps,
            'presensiStats' => $presensiStats,
            'jamSelected' => $this->selectedJam,
        ];

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->modalDetail = null;
    }

    public function getRekapGuruKehadiranProperty()
    {
        $bulanStr = !empty($this->rekapBulan) ? $this->rekapBulan : Carbon::now('Asia/Jakarta')->format('Y-m');
        $startOfMonth = Carbon::parse($bulanStr)->startOfMonth();
        $endOfMonth = Carbon::parse($bulanStr)->endOfMonth();

        $gurus = \App\Models\User::whereIn('role', ['guru', 'ketua_mgmp'])
            ->when($this->searchGuruRekap, function ($q) {
                $q->where('name', 'like', "%{$this->searchGuruRekap}%")
                  ->orWhere('email', 'like', "%{$this->searchGuruRekap}%");
            })
            ->orderBy('name')
            ->get();

        return $gurus->map(function ($guru) use ($startOfMonth, $endOfMonth) {
            // Target JP
            $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $guru->id))->get();
            $targetJp = 0;

            $curDate = $startOfMonth->copy();
            while ($curDate->lte($endOfMonth)) {
                $dayOfWeek = $curDate->dayOfWeekIso;
                if ($dayOfWeek <= 5 && !\App\Models\HariLibur::isHariLibur($curDate)) {
                    $jadwalsToday = $jadwals->where('hari', $dayOfWeek);
                    foreach ($jadwalsToday as $jt) {
                        $targetJp += max(1, (int)$jt->jam_ke_selesai - (int)$jt->jam_ke_mulai + 1);
                    }
                }
                $curDate->addDay();
            }

            // Realisasi JP
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

            $pct = $targetJp > 0 ? round(($realisasiJp / $targetJp) * 100, 1) : ($realisasiJp > 0 ? 100 : 0);

            return (object) [
                'guru' => $guru,
                'target_jp' => $targetJp,
                'realisasi_jp' => $realisasiJp,
                'izin_jp' => $izinJp,
                'sakit_jp' => $sakitJp,
                'persentase' => $pct,
            ];
        });
    }

    public function render()
    {
        $data = $this->monitoringData;
        $summary = $data->countBy('status');

        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45', 'label' => 'Apel Pagi / Upacara', 'is_break' => false],
            1  => ['mulai' => '06:45', 'selesai' => '07:30', 'label' => 'KBM 1', 'is_break' => false],
            2  => ['mulai' => '07:30', 'selesai' => '08:15', 'label' => 'KBM 2', 'is_break' => false],
            3  => ['mulai' => '08:15', 'selesai' => '09:00', 'label' => 'KBM 3', 'is_break' => false],
            4  => ['mulai' => '09:00', 'selesai' => '09:45', 'label' => 'KBM 4', 'is_break' => false],
            5  => ['mulai' => '09:45', 'selesai' => '10:00', 'label' => 'Istirahat 1', 'is_break' => true],
            6  => ['mulai' => '10:00', 'selesai' => '10:45', 'label' => 'KBM 5', 'is_break' => false],
            7  => ['mulai' => '10:45', 'selesai' => '11:30', 'label' => 'KBM 6', 'is_break' => false],
            8  => ['mulai' => '11:30', 'selesai' => '12:15', 'label' => 'KBM 7', 'is_break' => false],
            9  => ['mulai' => '12:15', 'selesai' => '12:45', 'label' => 'Istirahat 2 / Ishoma', 'is_break' => true],
            10 => ['mulai' => '12:45', 'selesai' => '13:30', 'label' => 'KBM 8', 'is_break' => false],
            11 => ['mulai' => '13:30', 'selesai' => '14:15', 'label' => 'KBM 9', 'is_break' => false],
            12 => ['mulai' => '14:15', 'selesai' => '15:00', 'label' => 'KBM 10', 'is_break' => false],
        ];

        $jamPelajaranList = collect($officialPeriods)->map(function ($slot, $jamKe) {
            return (object) [
                'jam_ke' => $jamKe,
                'waktu_mulai' => $slot['mulai'],
                'waktu_selesai' => $slot['selesai'],
                'label' => $slot['label'],
                'is_break' => $slot['is_break'],
            ];
        });

        $currentJamObj = $jamPelajaranList->firstWhere('jam_ke', $this->currentJam);
        $todayHoliday = \App\Models\HariLibur::isHariLibur($this->tanggal);

        return view('livewire.monitoring.dashboard-monitoring', [
            'monitoringData' => $data,
            'summary' => $summary,
            'jamPelajaranList' => $jamPelajaranList,
            'currentJamObj' => $currentJamObj,
            'todayHoliday' => $todayHoliday,
            'rekapGuruKehadiran' => $this->rekapGuruKehadiran,
        ]);
    }

    public function detectLiveJam()
    {
        $now = Carbon::now('Asia/Jakarta');
        $timeNow = $now->format('H:i');

        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'], // Jam 0: Apel Pagi / Upacara
            1  => ['mulai' => '06:45', 'selesai' => '07:30'], // Jam 1
            2  => ['mulai' => '07:30', 'selesai' => '08:15'], // Jam 2
            3  => ['mulai' => '08:15', 'selesai' => '09:00'], // Jam 3
            4  => ['mulai' => '09:00', 'selesai' => '09:45'], // Jam 4
            5  => ['mulai' => '09:45', 'selesai' => '10:00'], // Jam 5: Istirahat 1
            6  => ['mulai' => '10:00', 'selesai' => '10:45'], // Jam 6
            7  => ['mulai' => '10:45', 'selesai' => '11:30'], // Jam 7
            8  => ['mulai' => '11:30', 'selesai' => '12:15'], // Jam 8
            9  => ['mulai' => '12:15', 'selesai' => '12:45'], // Jam 9: Istirahat 2 / Ishoma
            10 => ['mulai' => '12:45', 'selesai' => '13:30'], // Jam 10
            11 => ['mulai' => '13:30', 'selesai' => '14:15'], // Jam 11
            12 => ['mulai' => '14:15', 'selesai' => '15:00'], // Jam 12
        ];

        $foundJam = null;
        foreach ($officialPeriods as $jamKe => $slot) {
            if ($timeNow >= $slot['mulai'] && $timeNow <= $slot['selesai']) {
                $foundJam = $jamKe;
                break;
            }
        }

        if ($foundJam === null) {
            foreach ($officialPeriods as $jamKe => $slot) {
                if ($slot['mulai'] > $timeNow) {
                    $foundJam = $jamKe;
                    break;
                }
            }
        }

        if ($foundJam === null) {
            $foundJam = 12;
        }

        $this->currentJam = $foundJam;
        if (is_null($this->selectedJam)) {
            $this->selectedJam = $this->currentJam;
        }
    }

    public function resetToLive()
    {
        $this->detectLiveJam();
        $this->selectedJam = $this->currentJam;
        $this->dispatch('show-toast', message: "Monitoring dikembalikan ke Jam Live saat ini (Jam ke-{$this->selectedJam})", type: 'info');
    }

    public function setJam(?int $jam)
    {
        $this->selectedJam = $jam;
    }

    public function getMonitoringDataProperty()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        if (is_null($this->selectedJam)) {
            return $rombels->map(fn($rombel) => [
                'rombel' => $rombel,
                'status' => 'abu',
                'label' => 'Pilih jam pelajaran',
                'jadwal' => null,
                'agenda' => null,
            ]);
        }

        // Batch fetch all jadwals for selected jam in 1 single query (Eliminates 35+ queries)
        $allJadwals = JadwalPelajaran::where('hari', $this->hariIni)
            ->where('jam_ke_mulai', '<=', $this->selectedJam)
            ->where('jam_ke_selesai', '>=', $this->selectedJam)
            ->with(['mataPelajaran', 'jadwalGuru.guru'])
            ->get()
            ->keyBy('rombel_id');

        // Batch fetch all agendas for today in 1 single query (Eliminates 35+ queries)
        $allAgendas = AgendaHarian::whereIn('jadwal_pelajaran_id', $allJadwals->pluck('id'))
            ->where('tanggal', $this->tanggal)
            ->with('guru')
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        $data = $rombels->map(function ($rombel) use ($allJadwals, $allAgendas) {
            $jadwal = $allJadwals->get($rombel->id);

            if (!$jadwal) {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => 'Tidak ada jadwal',
                    'jadwal' => null,
                    'agenda' => null,
                ];
            }

            if ($jadwal->isKegiatanKhusus()) {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => $jadwal->kegiatan_khusus,
                    'jadwal' => $jadwal,
                    'agenda' => null,
                ];
            }

            $agenda = $allAgendas->get($jadwal->id);

            if (!$agenda) {
                return [
                    'rombel' => $rombel,
                    'status' => 'merah',
                    'label' => 'Guru belum handshake',
                    'jadwal' => $jadwal,
                    'agenda' => null,
                ];
            }

            if (in_array($agenda->status_kehadiran_guru, ['izin', 'cuti', 'sakit'])) {
                return [
                    'rombel' => $rombel,
                    'status' => 'ungu',
                    'label' => 'Guru ' . $agenda->status_kehadiran_guru,
                    'jadwal' => $jadwal,
                    'agenda' => $agenda,
                ];
            }

            if ($agenda->status === 'dibatalkan') {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => 'Dibatalkan',
                    'jadwal' => $jadwal,
                    'agenda' => $agenda,
                ];
            }

            $hasFoto = !empty($agenda->foto_bukti_path);
            $handshakeDone = in_array($agenda->status, ['berjalan', 'selesai']);

            if ($handshakeDone) {
                // Calculate if handshake was on time (≤45 min from scheduled start)
                $officialPeriods = [
                    0  => ['mulai' => '06:25', 'selesai' => '06:45'], // Jam 0: Apel Pagi / Upacara
                    1  => ['mulai' => '06:45', 'selesai' => '07:30'], // Jam 1
                    2  => ['mulai' => '07:30', 'selesai' => '08:15'], // Jam 2
                    3  => ['mulai' => '08:15', 'selesai' => '09:00'], // Jam 3
                    4  => ['mulai' => '09:00', 'selesai' => '09:45'], // Jam 4
                    5  => ['mulai' => '09:45', 'selesai' => '10:00'], // Jam 5: Istirahat 1
                    6  => ['mulai' => '10:00', 'selesai' => '10:45'], // Jam 6
                    7  => ['mulai' => '10:45', 'selesai' => '11:30'], // Jam 7
                    8  => ['mulai' => '11:30', 'selesai' => '12:15'], // Jam 8
                    9  => ['mulai' => '12:15', 'selesai' => '12:45'], // Jam 9: Istirahat 2 / Ishoma
                    10 => ['mulai' => '12:45', 'selesai' => '13:30'], // Jam 10
                    11 => ['mulai' => '13:30', 'selesai' => '14:15'], // Jam 11
                    12 => ['mulai' => '14:15', 'selesai' => '15:00'], // Jam 12
                ];
                $scheduledStart = $officialPeriods[$this->selectedJam]['mulai'] ?? '06:45';
                $onTime = true;
                if ($agenda->waktu_mulai) {
                    $scheduledCarbon = Carbon::parse($scheduledStart, 'Asia/Jakarta');
                    $diffMinutes = $scheduledCarbon->diffInMinutes($agenda->waktu_mulai, false);
                    $onTime = $diffMinutes <= 45;
                }

                if ($onTime && $hasFoto) {
                    return [
                        'rombel' => $rombel,
                        'status' => 'hijau',
                        'label' => 'Lengkap',
                        'jadwal' => $jadwal,
                        'agenda' => $agenda,
                    ];
                }

                $label = !$hasFoto ? 'Belum foto' : 'Terlambat';
                return [
                    'rombel' => $rombel,
                    'status' => 'kuning',
                    'label' => $label,
                    'jadwal' => $jadwal,
                    'agenda' => $agenda,
                ];
            }

            return [
                'rombel' => $rombel,
                'status' => 'merah',
                'label' => 'Menunggu',
                'jadwal' => $jadwal,
                'agenda' => $agenda,
            ];
        });

        // Filter by search query (nama kelas or nama guru)
        if (!empty($this->search)) {
            $searchLower = strtolower($this->search);
            $data = $data->filter(function ($item) use ($searchLower) {
                $matchKelas = str_contains(strtolower($item['rombel']->nama_kelas ?? ''), $searchLower);
                $matchGuru = false;
                if (!empty($item['agenda'])) {
                    $matchGuru = str_contains(strtolower($item['agenda']->guru?->name ?? ''), $searchLower);
                } elseif (!empty($item['jadwal'])) {
                    $guruNames = $item['jadwal']->jadwalGuru?->pluck('guru.name')->join(', ') ?? '';
                    $matchGuru = str_contains(strtolower($guruNames), $searchLower);
                }
                $matchMapel = !empty($item['jadwal']) && str_contains(strtolower($item['jadwal']->mataPelajaran?->nama_mapel ?? ''), $searchLower);
                return $matchKelas || $matchGuru || $matchMapel;
            })->values();
        }

        return $data;
    }
}

