<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\Rombel;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Jurnal Per Kelas')]
class JurnalPerKelas extends Component
{
    public Rombel $rombel;
    public string $bulan;
    public string $minggu = 'semua';

    public function mount(Rombel $rombel)
    {
        $this->rombel = $rombel;
        $this->bulan = request('bulan', Carbon::now('Asia/Jakarta')->format('Y-m'));
        $this->minggu = request('minggu', 'semua');
    }

    public function setMinggu(string $val)
    {
        $this->minggu = $val;
    }

    public function render()
    {
        $user = auth()->user();
        $start = Carbon::parse($this->bulan)->startOfMonth()->format('Y-m-d');
        $end = Carbon::parse($this->bulan)->endOfMonth()->format('Y-m-d');

        $allAgendasMonth = AgendaHarian::where('guru_id', $user->id)
            ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $this->rombel->id))
            ->whereBetween('tanggal', [$start, $end])
            ->with(['jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran', 'guru'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Assign sequence number (Pertemuan Ke-1, Pertemuan Ke-2, ...) for the month
        $pertemuanCounter = 1;
        foreach ($allAgendasMonth as $agenda) {
            $agenda->pertemuan_ke = $pertemuanCounter++;
            $agenda->week_of_month = Carbon::parse($agenda->tanggal)->weekOfMonth;
        }

        // Apply week filter if selected
        $filteredAgendas = $allAgendasMonth;
        if ($this->minggu !== 'semua') {
            $filteredAgendas = $allAgendasMonth->filter(function ($a) {
                return (string) $a->week_of_month === (string) $this->minggu;
            });
        }

        return view('livewire.guru.jurnal-per-kelas', [
            'agendas' => $filteredAgendas->values(),
            'totalMonth' => $allAgendasMonth->count(),
            'namaBulan' => Carbon::parse($this->bulan)->translatedFormat('F Y'),
        ]);
    }
}
