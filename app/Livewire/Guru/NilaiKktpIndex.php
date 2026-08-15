<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiKktp;
use App\Models\Siswa;

#[Layout('components.layouts.app')]
#[Title('Nilai KKTP Murid')]
class NilaiKktpIndex extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Get unique rombel+mapel combinations the guru teaches
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('mapel_id')
            ->whereNotNull('rombel_id')
            ->with(['rombel', 'mataPelajaran'])
            ->get()
            ->unique(fn($j) => $j->rombel_id . '_' . $j->mapel_id);

        // Build card data with stats
        $kelasData = $jadwals->map(function ($jadwal) {
            $rombel = $jadwal->rombel;
            $mapel = $jadwal->mataPelajaran;
            if (!$rombel || !$mapel) return null;

            $tpCount = TujuanPembelajaran::where('mapel_id', $mapel->id)->count();
            $siswaCount = Siswa::where('rombel_id', $rombel->id)->count();
            $totalCells = $tpCount * $siswaCount;

            $tercapaiCount = 0;
            if ($totalCells > 0) {
                $tercapaiCount = NilaiKktp::where('rombel_id', $rombel->id)
                    ->whereHas('tujuanPembelajaran', fn($q) => $q->where('mapel_id', $mapel->id))
                    ->where('status', 'tercapai')
                    ->count();
            }

            $percentage = $totalCells > 0 ? round(($tercapaiCount / $totalCells) * 100) : 0;

            return (object) [
                'rombel' => $rombel,
                'mapel' => $mapel,
                'tp_count' => $tpCount,
                'siswa_count' => $siswaCount,
                'tercapai_count' => $tercapaiCount,
                'total_cells' => $totalCells,
                'percentage' => $percentage,
            ];
        })->filter()->sortBy('rombel.nama_kelas')->values();

        return view('livewire.guru.nilai-kktp-index', [
            'kelasData' => $kelasData,
        ]);
    }
}
