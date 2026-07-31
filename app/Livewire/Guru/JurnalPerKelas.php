<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\Rombel;

#[Layout('components.layouts.app')]
#[Title('Jurnal Per Kelas')]
class JurnalPerKelas extends Component
{
    public Rombel $rombel;

    public function mount(Rombel $rombel)
    {
        $this->rombel = $rombel;
    }

    public function render()
    {
        $agendas = AgendaHarian::where('guru_id', auth()->id())
            ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $this->rombel->id))
            ->with(['jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('livewire.guru.jurnal-per-kelas', ['agendas' => $agendas]);
    }
}
