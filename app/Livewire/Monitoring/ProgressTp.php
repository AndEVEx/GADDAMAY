<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\AgendaHarian;
use App\Models\Rombel;

#[Layout('components.layouts.app')]
#[Title('Progress TP')]
class ProgressTp extends Component
{
    public string $selectedMapel = '';
    public string $selectedRombel = '';

    public function render()
    {
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();
        $rombelList = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $progressData = collect();

        if ($this->selectedMapel) {
            $tps = TujuanPembelajaran::where('mapel_id', $this->selectedMapel)
                ->orderBy('order_sequence')
                ->get();

            $query = AgendaHarian::where('status', 'selesai')
                ->whereHas('jadwalPelajaran', function ($q) {
                    $q->where('mapel_id', $this->selectedMapel);
                    if ($this->selectedRombel) {
                        $q->where('rombel_id', $this->selectedRombel);
                    }
                });

            $taughtTpIds = $query->with('tujuanPembelajaran')
                ->get()
                ->flatMap(fn($a) => $a->tujuanPembelajaran->pluck('id'))
                ->unique();

            $progressData = $tps->map(function ($tp) use ($taughtTpIds) {
                return [
                    'tp' => $tp,
                    'taught' => $taughtTpIds->contains($tp->id),
                ];
            });
        }

        $totalTp = $progressData->count();
        $taughtCount = $progressData->where('taught', true)->count();
        $percentage = $totalTp > 0 ? round(($taughtCount / $totalTp) * 100) : 0;

        return view('livewire.monitoring.progress-tp', [
            'mapelList' => $mapelList,
            'rombelList' => $rombelList,
            'progressData' => $progressData,
            'totalTp' => $totalTp,
            'taughtCount' => $taughtCount,
            'percentage' => $percentage,
        ]);
    }
}
