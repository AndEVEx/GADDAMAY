<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\JadwalPelajaran;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Koreksi Agenda')]
class KoreksiAgenda extends Component
{
    public string $tanggal;
    public string $selectedGuru = '';
    public string $selectedAction = '';
    public string $guruPenggantiId = '';
    public string $selectedAgendaId = '';

    public function mount()
    {
        $this->tanggal = Carbon::today('Asia/Jakarta')->format('Y-m-d');
    }

    public function selectAgenda(string $id)
    {
        $this->selectedAgendaId = $id;
        $this->selectedAction = '';
        $this->guruPenggantiId = '';
    }

    public function tandaiIzin(string $status)
    {
        $agenda = AgendaHarian::findOrFail($this->selectedAgendaId);
        $old = $agenda->toArray();

        $agenda->update([
            'status_kehadiran_guru' => $status,
            'koreksi_oleh_id' => auth()->id(),
        ]);

        AuditLogService::logOverride($agenda, $old, $agenda->fresh()->toArray());
        $this->dispatch('show-toast', message: "Guru ditandai {$status}.", type: 'success');
        $this->selectedAgendaId = '';
    }

    public function assignPengganti()
    {
        $this->validate(['guruPenggantiId' => 'required|exists:users,id']);

        $agenda = AgendaHarian::findOrFail($this->selectedAgendaId);
        $old = $agenda->toArray();

        $agenda->update([
            'guru_pengganti_id' => $this->guruPenggantiId,
            'koreksi_oleh_id' => auth()->id(),
        ]);

        AuditLogService::logOverride($agenda, $old, $agenda->fresh()->toArray());
        $this->dispatch('show-toast', message: 'Guru pengganti berhasil ditetapkan.', type: 'success');
        $this->selectedAgendaId = '';
    }

    public function batalkanJadwal()
    {
        $agenda = AgendaHarian::findOrFail($this->selectedAgendaId);
        $old = $agenda->toArray();

        $agenda->update([
            'status' => 'dibatalkan',
            'koreksi_oleh_id' => auth()->id(),
        ]);

        AuditLogService::logOverride($agenda, $old, $agenda->fresh()->toArray());
        $this->dispatch('show-toast', message: 'Jadwal dibatalkan.', type: 'success');
        $this->selectedAgendaId = '';
    }

    public function render()
    {
        $agendas = AgendaHarian::where('tanggal', $this->tanggal)
            ->with(['guru', 'jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'koreksiOleh'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Also get jadwal without agenda for today (guru belum mulai)
        $hariIni = Carbon::parse($this->tanggal)->dayOfWeekIso;
        $jadwalTanpaAgenda = JadwalPelajaran::where('hari', $hariIni)
            ->whereNotNull('mapel_id')
            ->whereDoesntHave('agendaHarian', fn($q) => $q->where('tanggal', $this->tanggal))
            ->with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->get();

        $guruList = User::where('role', 'guru')->orderBy('name')->get();

        return view('livewire.admin.koreksi-agenda', [
            'agendas' => $agendas,
            'jadwalTanpaAgenda' => $jadwalTanpaAgenda,
            'guruList' => $guruList,
        ]);
    }
}
