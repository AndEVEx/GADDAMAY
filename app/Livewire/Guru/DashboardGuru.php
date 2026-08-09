<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Guru')]
class DashboardGuru extends Component
{
    public string $tanggal;
    public int $hariIni;

    public function mount()
    {
        $this->tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->hariIni = Carbon::now('Asia/Jakarta')->dayOfWeekIso; // 1=Senin
    }

    public function getJadwalHariIniProperty()
    {
        $user = auth()->user();

        $jadwals = JadwalPelajaran::where('hari', $this->hariIni)
            ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->orderBy('jam_ke_mulai')
            ->get();

        // Also get kegiatan khusus for this day (no guru assignment needed)
        $kegiatanKhusus = JadwalPelajaran::where('hari', $this->hariIni)
            ->whereNotNull('kegiatan_khusus')
            ->whereNull('mapel_id')
            ->orderBy('jam_ke_mulai')
            ->get();

        // Merge and sort
        $all = $jadwals->merge($kegiatanKhusus)->sortBy('jam_ke_mulai');

        if ($all->isEmpty()) {
            return collect();
        }

        // Batch load all agenda harian for today in 1 single query (Eliminates N+1 query lag)
        $agendas = AgendaHarian::whereIn('jadwal_pelajaran_id', $all->pluck('id'))
            ->where('tanggal', $this->tanggal)
            ->where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)
                  ->orWhere('guru_pengganti_id', $user->id);
            })
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        // Attach agenda status for each jadwal
        return $all->map(function ($jadwal) use ($agendas) {
            $agenda = $agendas->get($jadwal->id);
            $jadwal->agenda = $agenda;
            $jadwal->status_label = $this->getStatusLabel($agenda);
            $jadwal->can_start = $this->canStart($jadwal, $agenda);

            return $jadwal;
        });
    }

    private function getStatusLabel(?AgendaHarian $agenda): array
    {
        if (!$agenda) {
            return ['text' => 'Belum Mulai', 'class' => 'status-abu', 'icon' => 'bi-circle'];
        }

        return match ($agenda->status) {
            'menunggu_token' => ['text' => 'Menunggu Token', 'class' => 'status-kuning', 'icon' => 'bi-hourglass-split'],
            'token_terverifikasi' => ['text' => 'Token Verified', 'class' => 'status-kuning', 'icon' => 'bi-shield-check'],
            'berjalan' => ['text' => 'Berjalan', 'class' => 'status-hijau', 'icon' => 'bi-play-circle-fill'],
            'selesai' => ['text' => 'Selesai', 'class' => 'status-hijau', 'icon' => 'bi-check-circle-fill'],
            'dibatalkan' => ['text' => 'Dibatalkan', 'class' => 'status-merah', 'icon' => 'bi-x-circle'],
            default => ['text' => 'Unknown', 'class' => 'status-abu', 'icon' => 'bi-question-circle'],
        };
    }

    private function canStart(JadwalPelajaran $jadwal, ?AgendaHarian $agenda): bool
    {
        // Can't start kegiatan khusus
        if ($jadwal->isKegiatanKhusus()) return false;

        // Can start if no agenda yet or agenda was cancelled
        if (!$agenda) return true;
        if ($agenda->status === 'dibatalkan') return true;
        if ($agenda->status === 'token_terverifikasi') return false;

        return false;
    }

    public function render()
    {
        return view('livewire.guru.dashboard-guru', [
            'jadwals' => $this->jadwalHariIni,
        ]);
    }
}
