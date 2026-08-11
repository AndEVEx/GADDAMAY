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
        $timeNow = $now->format('H:i:s');

        $jam = JamPelajaran::where('waktu_mulai', '<=', $timeNow)
            ->where('waktu_selesai', '>=', $timeNow)
            ->first();

        $this->currentJam = $jam?->jam_ke;
        if (is_null($this->selectedJam)) {
            $this->selectedJam = $this->currentJam ?? 1;
        }
    }

    public function resetToLive()
    {
        $now = Carbon::now('Asia/Jakarta');
        $timeNow = $now->format('H:i:s');

        $jam = JamPelajaran::where('waktu_mulai', '<=', $timeNow)
            ->where('waktu_selesai', '>=', $timeNow)
            ->first();

        $this->currentJam = $jam?->jam_ke;
        $this->selectedJam = $this->currentJam ?? 1;

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

        return view('livewire.monitoring.dashboard-monitoring', [
            'monitoringData' => $data,
            'summary' => $summary,
        ]);
    }
}
