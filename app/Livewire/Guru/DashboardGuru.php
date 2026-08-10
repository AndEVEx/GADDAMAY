<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Guru')]
class DashboardGuru extends Component
{
    public string $tanggal;
    public int $hariIni;

    public function mount()
    {
        $this->tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->hariIni = Carbon::now('Asia/Jakarta')->dayOfWeekIso; // 1=Senin
    }

    public function getJadwalHariIniProperty()
    {
        $user = auth()->user();

        // 1. Fetch teacher's teaching schedules for today
        $jadwals = JadwalPelajaran::where('hari', $this->hariIni)
            ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->orderBy('jam_ke_mulai')
            ->get();

        // 2. Fetch unique kegiatan khusus (Upacara, Istirahat, Sholat)
        $kegiatanKhusus = JadwalPelajaran::where('hari', $this->hariIni)
            ->whereNotNull('kegiatan_khusus')
            ->whereNull('mapel_id')
            ->orderBy('jam_ke_mulai')
            ->get()
            ->unique(fn($item) => trim($item->kegiatan_khusus) . '-' . $item->jam_ke_mulai);

        if ($jadwals->isEmpty() && $kegiatanKhusus->isEmpty()) {
            return collect();
        }

        // 3. Merge adjacent/split teaching sessions for the SAME mapel & SAME rombel split ONLY by breaks
        $mergedJadwals = collect();
        $groupedMapelRombel = $jadwals->groupBy(fn($item) => $item->rombel_id . '_' . $item->mapel_id);

        foreach ($groupedMapelRombel as $key => $items) {
            $sorted = $items->sortBy('jam_ke_mulai')->values();
            
            $currentBlock = null;

            foreach ($sorted as $item) {
                if (!$currentBlock) {
                    $currentBlock = [
                        'id' => $item->id,
                        'primary_id' => $item->id,
                        'all_ids' => [$item->id],
                        'jam_ke_mulai' => $item->jam_ke_mulai,
                        'jam_ke_selesai' => $item->jam_ke_selesai,
                        'is_split_by_break' => false,
                        'rombel' => $item->rombel,
                        'mataPelajaran' => $item->mataPelajaran,
                        'keterangan' => $item->keterangan,
                        'jadwalGuru' => $item->jadwalGuru,
                        'is_kegiatan_khusus' => false,
                    ];
                } else {
                    $previousEnd = $currentBlock['jam_ke_selesai'];
                    $nextStart = $item->jam_ke_mulai;

                    // Verify no other subject/class exists in between for this teacher
                    $hasOtherClassBetween = $jadwals->contains(function ($other) use ($item, $currentBlock, $previousEnd, $nextStart) {
                        return $other->id !== $item->id 
                            && !in_array($other->id, $currentBlock['all_ids'])
                            && $other->jam_ke_mulai > $previousEnd 
                            && $other->jam_ke_selesai < $nextStart;
                    });

                    if (!$hasOtherClassBetween) {
                        // Merge into continuous teaching block!
                        $currentBlock['all_ids'][] = $item->id;
                        $currentBlock['jam_ke_selesai'] = max($currentBlock['jam_ke_selesai'], $item->jam_ke_selesai);
                        if ($nextStart > $previousEnd + 1) {
                            $currentBlock['is_split_by_break'] = true;
                        }
                    } else {
                        $mergedJadwals->push($currentBlock);
                        $currentBlock = [
                            'id' => $item->id,
                            'primary_id' => $item->id,
                            'all_ids' => [$item->id],
                            'jam_ke_mulai' => $item->jam_ke_mulai,
                            'jam_ke_selesai' => $item->jam_ke_selesai,
                            'is_split_by_break' => false,
                            'rombel' => $item->rombel,
                            'mataPelajaran' => $item->mataPelajaran,
                            'keterangan' => $item->keterangan,
                            'jadwalGuru' => $item->jadwalGuru,
                            'is_kegiatan_khusus' => false,
                        ];
                    }
                }
            }

            if ($currentBlock) {
                $mergedJadwals->push($currentBlock);
            }
        }

        // Add kegiatan khusus as standalone blocks
        foreach ($kegiatanKhusus as $kk) {
            $mergedJadwals->push([
                'id' => $kk->id,
                'primary_id' => $kk->id,
                'all_ids' => [$kk->id],
                'jam_ke_mulai' => $kk->jam_ke_mulai,
                'jam_ke_selesai' => $kk->jam_ke_selesai,
                'is_split_by_break' => false,
                'rombel' => null,
                'mataPelajaran' => null,
                'kegiatan_khusus' => $kk->kegiatan_khusus,
                'keterangan' => $kk->keterangan,
                'jadwalGuru' => collect(),
                'is_kegiatan_khusus' => true,
            ]);
        }

        // Sort all merged blocks by jam_ke_mulai
        $allMerged = $mergedJadwals->sortBy('jam_ke_mulai')->values();

        // 4. Batch load all agenda harian for today across all merged schedule IDs in 1 query
        $allScheduleIds = $allMerged->flatMap(fn($block) => $block['all_ids'])->toArray();

        $agendas = AgendaHarian::whereIn('jadwal_pelajaran_id', $allScheduleIds)
            ->where('tanggal', $this->tanggal)
            ->where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)
                  ->orWhere('guru_pengganti_id', $user->id);
            })
            ->get();

        // Attach agenda & status label for each block
        return $allMerged->map(function ($block) use ($agendas) {
            $agenda = $agendas->first(fn($a) => in_array($a->jadwal_pelajaran_id, $block['all_ids']));

            $block['agenda'] = $agenda;
            $block['status_label'] = $this->getStatusLabel($agenda);
            $block['can_start'] = $this->canStart($block, $agenda);

            return (object) $block;
        });
    }

    private function getStatusLabel(?AgendaHarian $agenda): array
    {
        if (!$agenda) {
            return ['text' => 'Belum Mulai', 'class' => 'status-abu', 'icon' => 'bi-circle'];
        }

        return match ($agenda->status) {
            'menunggu_token' => ['text' => 'Menunggu Token', 'class' => 'status-kuning', 'icon' => 'bi-hourglass-split'],
            'token_terverifikasi' => ['text' => 'Token Verified', 'class' => 'status-kuning', 'icon' => 'bi-shield-check'],
            'berjalan' => ['text' => 'Berjalan', 'class' => 'status-hijau', 'icon' => 'bi-play-circle-fill'],
            'selesai' => ['text' => 'Selesai', 'class' => 'status-hijau', 'icon' => 'bi-check-circle-fill'],
            'dibatalkan' => ['text' => 'Dibatalkan', 'class' => 'status-merah', 'icon' => 'bi-x-circle'],
            default => ['text' => 'Unknown', 'class' => 'status-abu', 'icon' => 'bi-question-circle'],
        };
    }

    private function canStart(array $block, ?AgendaHarian $agenda): bool
    {
        if ($block['is_kegiatan_khusus']) return false;
        if (!$agenda) return true;
        if ($agenda->status === 'dibatalkan') return true;

        return false;
    }

    public function render()
    {
        return view('livewire.guru.dashboard-guru', [
            'jadwals' => $this->jadwalHariIni,
        ]);
    }
}
