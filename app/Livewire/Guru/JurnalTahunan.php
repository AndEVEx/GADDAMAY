<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\Rombel;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Jurnal Tahunan')]
class JurnalTahunan extends Component
{
    public string $bulan;

    public function mount()
    {
        $this->bulan = Carbon::now('Asia/Jakarta')->format('Y-m');
    }

    public function render()
    {
        $user = auth()->user();
        $start = Carbon::parse($this->bulan)->startOfMonth();
        $end = Carbon::parse($this->bulan)->endOfMonth();

        // Get unique rombels where guru teaches
        $rombels = Rombel::whereHas('jadwalPelajaran.jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->withCount(['jadwalPelajaran as agenda_selesai_count' => function ($q) use ($user, $start, $end) {
                $q->whereHas('agendaHarian', fn($aq) => $aq->where('guru_id', $user->id)
                    ->where('status', 'selesai')
                    ->whereBetween('tanggal', [$start, $end]));
            }])
            ->withCount(['jadwalPelajaran as total_jadwal_count' => function ($q) use ($user) {
                $q->whereHas('jadwalGuru', fn($jq) => $jq->where('guru_id', $user->id));
            }])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('livewire.guru.jurnal-tahunan', ['rombels' => $rombels]);
    }
}
