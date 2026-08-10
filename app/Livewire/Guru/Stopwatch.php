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
                'waktu_selesai' => Carbon::now('Asia/Jakarta'),
                'prompter_custom' => $this->prompter,
                'status' => 'selesai',
            ]);

            $this->dispatch('show-toast', message: 'Pembelajaran berhasil diakhiri!', type: 'success');
            return redirect()->route('guru.dashboard');
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
