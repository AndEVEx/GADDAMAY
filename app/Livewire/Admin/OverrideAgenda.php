<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\User;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Override Agenda Harian')]
class OverrideAgenda extends Component
{
    public ?AgendaHarian $agenda = null;
    public string $agendaId = '';
    public string $materiDiajarkan = '';
    public string $status = 'berjalan';
    public string $statusKehadiranGuru = 'hadir';
    public ?string $guruPenggantiId = null;

    public function mount($agenda)
    {
        if ($agenda instanceof AgendaHarian) {
            $agendaModel = $agenda;
        } else {
            $agendaModel = AgendaHarian::with([
                'guru',
                'jadwalPelajaran.rombel',
                'jadwalPelajaran.mataPelajaran',
                'koreksiOleh',
                'guruPengganti'
            ])->findOrFail($agenda);
        }

        $this->agenda = $agendaModel;
        $this->agendaId = $agendaModel->id;
        $this->materiDiajarkan = $agendaModel->materi_diajarkan ?? '';
        $this->status = $agendaModel->status ?? 'berjalan';
        $this->statusKehadiranGuru = $agendaModel->status_kehadiran_guru ?? 'hadir';
        $this->guruPenggantiId = $agendaModel->guru_pengganti_id;
    }

    public function save()
    {
        $this->validate([
            'materiDiajarkan' => 'nullable|string',
            'status' => 'required|in:berjalan,selesai,dibatalkan',
            'statusKehadiranGuru' => 'required|in:hadir,izin,sakit,alpa',
            'guruPenggantiId' => 'nullable|exists:users,id',
        ]);

        $agendaModel = AgendaHarian::findOrFail($this->agendaId);
        $oldValues = $agendaModel->toArray();

        $agendaModel->update([
            'materi_diajarkan' => $this->materiDiajarkan,
            'status' => $this->status,
            'status_kehadiran_guru' => $this->statusKehadiranGuru,
            'guru_pengganti_id' => $this->guruPenggantiId ?: null,
            'koreksi_oleh_id' => auth()->id(),
        ]);

        $newValues = $agendaModel->fresh()->toArray();
        AuditLogService::logOverride($agendaModel, $oldValues, $newValues);

        $this->agenda = $agendaModel->fresh([
            'guru',
            'jadwalPelajaran.rombel',
            'jadwalPelajaran.mataPelajaran',
            'koreksiOleh',
            'guruPengganti'
        ]);

        $this->dispatch('show-toast', message: 'Override agenda harian berhasil disimpan!', type: 'success');
    }

    public function render()
    {
        $guruList = User::whereIn('role', ['guru', 'ketua_mgmp', 'waka', 'admin'])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.override-agenda', [
            'guruList' => $guruList,
        ]);
    }
}
