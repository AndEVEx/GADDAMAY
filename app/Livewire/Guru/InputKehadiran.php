<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use App\Models\Siswa;

#[Layout('components.layouts.app')]
#[Title('Input Kehadiran')]
class InputKehadiran extends Component
{
    public AgendaHarian $agenda;
    public array $kehadiran = []; // siswa_id => status

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran']);

        $rombelId = $agenda->jadwalPelajaran?->rombel_id;
        if (!$rombelId) return;

        $siswaList = Siswa::where('rombel_id', $rombelId)->orderBy('nama')->get();

        // Load existing kehadiran or default to 'hadir'
        $existingKehadiran = KehadiranMurid::where('agenda_harian_id', $agenda->id)
            ->pluck('status', 'siswa_id')
            ->toArray();

        foreach ($siswaList as $siswa) {
            $this->kehadiran[$siswa->id] = $existingKehadiran[$siswa->id] ?? 'hadir';
        }
    }

    public function setStatus(int|string $siswaId, string $status)
    {
        $this->kehadiran[$siswaId] = $status;
    }

    public function simpan()
    {
        foreach ($this->kehadiran as $siswaId => $status) {
            KehadiranMurid::updateOrCreate(
                ['agenda_harian_id' => $this->agenda->id, 'siswa_id' => $siswaId],
                ['status' => $status]
            );
        }

        $this->dispatch('show-toast', message: 'Kehadiran berhasil disimpan!', type: 'success');

        return redirect()->route('guru.stopwatch', $this->agenda->id);
    }

    public function render()
    {
        $rombelId = $this->agenda->jadwalPelajaran?->rombel_id;
        $siswaList = $rombelId ? Siswa::where('rombel_id', $rombelId)->orderBy('nama')->get() : collect();

        $summary = collect($this->kehadiran)->countBy();

        return view('livewire.guru.input-kehadiran', [
            'siswaList' => $siswaList,
            'summary' => $summary,
        ]);
    }
}
