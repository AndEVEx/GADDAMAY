<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\TujuanPembelajaran;

#[Layout('components.layouts.app')]
#[Title('Isi Materi')]
class IsiMateri extends Component
{
    public AgendaHarian $agenda;
    public string $materi = '';
    public array $selectedTp = [];

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran']);
        $this->materi = $agenda->materi_diajarkan ?? '';
        $this->selectedTp = $agenda->tujuanPembelajaran->pluck('id')->toArray();
    }

    public function getTpListProperty()
    {
        $mapelId = $this->agenda->jadwalPelajaran?->mapel_id;
        $rombelId = $this->agenda->jadwalPelajaran?->rombel_id;

        if (!$mapelId) return collect();

        $allTp = TujuanPembelajaran::where('mapel_id', $mapelId)
            ->orderBy('order_sequence')
            ->get();

        // Get TP IDs already taught in this rombel for this mapel
        $taughtTpIds = AgendaHarian::where('guru_id', auth()->id())
            ->whereHas('jadwalPelajaran', function ($q) use ($mapelId, $rombelId) {
                $q->where('mapel_id', $mapelId);
                if ($rombelId) $q->where('rombel_id', $rombelId);
            })
            ->where('id', '!=', $this->agenda->id)
            ->with('tujuanPembelajaran')
            ->get()
            ->flatMap(fn($a) => $a->tujuanPembelajaran->pluck('id'))
            ->unique()
            ->toArray();

        // Sort: untaught first, then taught
        return $allTp->sortBy(function ($tp) use ($taughtTpIds) {
            return in_array($tp->id, $taughtTpIds) ? 1 : 0;
        })->map(function ($tp) use ($taughtTpIds) {
            $tp->is_taught = in_array($tp->id, $taughtTpIds);
            return $tp;
        });
    }

    public function simpan()
    {
        $this->validate([
            'materi' => 'required|min:3',
        ]);

        $this->agenda->update(['materi_diajarkan' => $this->materi]);
        $this->agenda->tujuanPembelajaran()->sync($this->selectedTp);

        $this->dispatch('show-toast', message: 'Materi dan TP berhasil disimpan!', type: 'success');

        return redirect()->route('guru.kehadiran', $this->agenda->id);
    }

    public function render()
    {
        return view('livewire.guru.isi-materi', [
            'tpList' => $this->tpList,
        ]);
    }
}
