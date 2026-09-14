<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Jurnal Tahunan')]
class JurnalTahunan extends Component
{
    public string $bulan;

    public function mount()
    {
        $this->bulan = request('bulan', Carbon::now('Asia/Jakarta')->format('Y-m'));
    }

    public function render()
    {
        $user = auth()->user();
        $start = Carbon::parse($this->bulan)->startOfMonth()->format('Y-m-d');
        $end = Carbon::parse($this->bulan)->endOfMonth()->format('Y-m-d');

        // Get unique rombels where guru teaches
        $rombels = Rombel::whereHas('jadwalPelajaran.jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        foreach ($rombels as $rombel) {
            // Count completed agendas for this rombel in the selected month
            $rombel->agenda_selesai_count = AgendaHarian::where('guru_id', $user->id)
                ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $rombel->id))
                ->where('status', 'selesai')
                ->whereBetween('tanggal', [$start, $end])
                ->count();

            // Calculate merged sessions per week (sessions cut off by break = 1 session)
            $rombel->merged_sesi_minggu = $this->calculateMergedSesiMinggu($user->id, $rombel->id);
        }

        return view('livewire.guru.jurnal-tahunan', [
            'rombels' => $rombels,
            'namaBulan' => Carbon::parse($this->bulan)->translatedFormat('F Y'),
        ]);
    }

    private function calculateMergedSesiMinggu(int|string $userId, int|string $rombelId): int
    {
        $jadwals = JadwalPelajaran::where('rombel_id', $rombelId)
            ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $userId))
            ->get();

        if ($jadwals->isEmpty()) {
            return 0;
        }

        $totalMergedBlocks = 0;
        $byDay = $jadwals->groupBy('hari');

        foreach ($byDay as $hari => $dayJadwals) {
            $byMapel = $dayJadwals->groupBy('mapel_id');

            foreach ($byMapel as $mapelId => $items) {
                $sorted = $items->sortBy('jam_ke_mulai')->values();
                $currentBlock = null;

                foreach ($sorted as $item) {
                    if (!$currentBlock) {
                        $currentBlock = [
                            'all_ids' => [$item->id],
                            'jam_ke_selesai' => $item->jam_ke_selesai,
                        ];
                    } else {
                        $previousEnd = $currentBlock['jam_ke_selesai'];
                        $nextStart = $item->jam_ke_mulai;

                        // Check if split only by break (no other mapel in between)
                        $hasOtherInBetween = $dayJadwals->contains(function ($other) use ($item, $currentBlock, $previousEnd, $nextStart) {
                            return $other->id !== $item->id 
                                && !in_array($other->id, $currentBlock['all_ids'])
                                && $other->jam_ke_mulai > $previousEnd 
                                && $other->jam_ke_selesai < $nextStart;
                        });

                        if (!$hasOtherInBetween) {
                            $currentBlock['all_ids'][] = $item->id;
                            $currentBlock['jam_ke_selesai'] = max($currentBlock['jam_ke_selesai'], $item->jam_ke_selesai);
                        } else {
                            $totalMergedBlocks++;
                            $currentBlock = [
                                'all_ids' => [$item->id],
                                'jam_ke_selesai' => $item->jam_ke_selesai,
                            ];
                        }
                    }
                }

                if ($currentBlock) {
                    $totalMergedBlocks++;
                }
            }
        }

        return $totalMergedBlocks;
    }
}
