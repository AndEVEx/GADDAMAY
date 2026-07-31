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
        $this->agenda->update([
            'waktu_selesai' => Carbon::now('Asia/Jakarta'),
            'prompter_custom' => $this->prompter,
        ]);

        return redirect()->route('guru.kktp', $this->agenda->id);
    }

    public function render()
    {
        return view('livewire.guru.stopwatch');
    }
}
