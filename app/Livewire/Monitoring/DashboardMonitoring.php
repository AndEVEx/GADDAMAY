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
    public ?int $currentJam = null;
    public ?int $selectedJam = null;

    public function mount()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->hariIni = $now->dayOfWeekIso;
        $this->tanggal = $now->format('Y-m-d');

        // Detect current period
        $timeNow = $now->format('H:i:s');
        $jam = JamPelajaran::where('waktu_mulai', '<=', $timeNow)
            ->where('waktu_selesai', '>=', $timeNow)
            ->first();
        $this->currentJam = $jam?->jam_ke;
        $this->selectedJam = $this->currentJam;
    }

    public function setJam(?int $jam)
    {
        $this->selectedJam = $jam;
    }

    public function getMonitoringDataProperty()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return $rombels->map(function ($rombel) {
            // Find jadwal for this rombel at current time
            $jadwal = JadwalPelajaran::where('hari', $this->hariIni)
                ->where('rombel_id', $rombel->id)
                ->when($this->selectedJam, function ($q) {
                    $q->where('jam_ke_mulai', '<=', $this->selectedJam)
                      ->where('jam_ke_selesai', '>=', $this->selectedJam);
                })
                ->with(['mataPelajaran', 'jadwalGuru.guru'])
                ->first();

            if (is_null($this->selectedJam)) {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => 'Pilih jam pelajaran',
                    'jadwal' => null,
                    'agenda' => null,
                ];
            }

            if (!$jadwal) {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => 'Tidak ada jadwal',
                    'jadwal' => null,
                    'agenda' => null,
                ];
            }

            // Check if kegiatan khusus
            if ($jadwal->isKegiatanKhusus()) {
                return [
                    'rombel' => $rombel,
                    'status' => 'abu',
                    'label' => $jadwal->kegiatan_khusus,
                    'jadwal' => $jadwal,
                    'agenda' => null,
                ];
            }

            // Find agenda for today
            $agenda = AgendaHarian::where('jadwal_pelajaran_id', $jadwal->id)
                ->where('tanggal', $this->tanggal)
                ->with('guru')
                ->first();

            if (!$agenda) {
                return [
                    'rombel' => $rombel,
                    'status' => 'merah',
                    'label' => 'Guru belum handshake',
                    'jadwal' => $jadwal,
                    'agenda' => null,
                ];
            }

            // Check guru kehadiran
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

            // Check photo
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
