<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Stopwatch')]
class Stopwatch extends Component
{
    public AgendaHarian $agenda;
    public string $prompter = '';

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran']);
        $this->prompter = $agenda->prompter_custom ?? '';
    }

    public function savePrompter()
    {
        $this->agenda->update(['prompter_custom' => $this->prompter]);
    }

    public function akhiriPembelajaran()
    {
        try {
            $this->agenda->update([
                'prompter_custom' => $this->prompter,
                'waktu_selesai' => $this->agenda->waktu_selesai ?? Carbon::now('Asia/Jakarta'),
            ]);

            $this->dispatch('show-toast', message: 'Lanjut ke pengisian Refleksi Pembelajaran & KKTP!', type: 'info');
            return redirect()->route('guru.kktp', $this->agenda->id);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengakhiri: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function batalkanAgenda()
    {
        if ($this->agenda) {
            $this->agenda->delete();
            $this->dispatch('show-toast', message: 'Sesi agenda berhasil dibatalkan!', type: 'info');
        }
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        return view('livewire.guru.stopwatch');
    }
}
