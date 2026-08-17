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

    public function mount()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->hariIni = $now->dayOfWeekIso;
        $this->tanggal = $now->format('Y-m-d');

        // Dynamically get maximum jam_ke from database (defaults to 12)
        $this->maxJam = JamPelajaran::max('jam_ke') ?? 12;

        $this->detectLiveJam();
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

        return $rombels->map(function ($rombel) use ($allJadwals, $allAgendas) {
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
                    'status' => 'oranye',
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

            if ($handshakeDone && $hasFoto) {
                return [
                    'rombel' => $rombel,
                    'status' => 'hijau',
                    'label' => 'Lengkap',
                    'jadwal' => $jadwal,
                    'agenda' => $agenda,
                ];
            }

            if ($handshakeDone && !$hasFoto) {
                return [
                    'rombel' => $rombel,
                    'status' => 'kuning',
                    'label' => 'Belum foto',
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
        ]);
    }
}
