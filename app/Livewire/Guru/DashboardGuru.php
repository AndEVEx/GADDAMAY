<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\HariLibur;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Guru')]
class DashboardGuru extends Component
{
    public string $tanggal;
    public int $hariIni;
    public ?HariLibur $todayHoliday = null;

    public function mount()
    {
        $this->tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->hariIni = Carbon::now('Asia/Jakarta')->dayOfWeekIso; // 1=Senin
        $this->todayHoliday = HariLibur::isHariLibur($this->tanggal);
    }

    public function getJadwalHariIniProperty()
    {
        $user = auth()->user();

        // Auto-close any expired agendas for today
        AgendaHarian::autoCloseExpiredAgendas(null, $this->tanggal);

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

        // Auto-close any expired agendas whose periods have ended
        AgendaHarian::autoCloseExpiredAgendas(null, $this->tanggal);

        // 4. Batch load all agenda harian for today across all merged schedule IDs in 1 query
        $allScheduleIds = $allMerged->flatMap(fn($block) => $block['all_ids'])->toArray();

        $agendas = AgendaHarian::whereIn('jadwal_pelajaran_id', $allScheduleIds)
            ->where('tanggal', $this->tanggal)
            ->where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)
                  ->orWhere('guru_pengganti_id', $user->id);
            })
            ->get();

        // Batch load JamPelajaran mapping
        $jamMap = \App\Models\JamPelajaran::all()->keyBy('jam_ke');
        $now = Carbon::now('Asia/Jakarta');
        $timeNow = $now->format('H:i');

        // Official SMKN 2 Indramayu Schedule Timetable (Jam 0 to Jam 12)
        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'], // Jam 0: Apel Pagi / Upacara
            1  => ['mulai' => '06:45', 'selesai' => '07:30'], // Jam 1
            2  => ['mulai' => '07:30', 'selesai' => '08:15'], // Jam 2
            3  => ['mulai' => '08:15', 'selesai' => '09:00'], // Jam 3
            4  => ['mulai' => '09:00', 'selesai' => '09:45'], // Jam 4
            5  => ['mulai' => '09:45', 'selesai' => '10:00'], // Jam 5: Istirahat 1
            6  => ['mulai' => '10:00', 'selesai' => '10:45'], // Jam 6
            7  => ['mulai' => '10:45', 'selesai' => '11:30'], // Jam 7
            8  => ['mulai' => '11:30', 'selesai' => '12:15'], // Jam 8
            9  => ['mulai' => '12:15', 'selesai' => '12:45'], // Jam 9: Istirahat 2 / Ishoma
            10 => ['mulai' => '12:45', 'selesai' => '13:30'], // Jam 10
            11 => ['mulai' => '13:30', 'selesai' => '14:15'], // Jam 11
            12 => ['mulai' => '14:15', 'selesai' => '15:00'], // Jam 12
        ];

        // Attach agenda & status label for each block and generate teaching notifications
        return $allMerged->map(function ($block) use ($agendas, $user, $jamMap, $timeNow, $officialPeriods) {
            $blockAgendas = $agendas->filter(fn($a) => in_array($a->jadwal_pelajaran_id, $block['all_ids']));
            $agenda = $blockAgendas->sortBy(function ($a) {
                return match ($a->status) {
                    'token_terverifikasi' => 1,
                    'berjalan' => 2,
                    'menunggu_token' => 3,
                    'selesai' => 4,
                    'dibatalkan' => 5,
                    default => 6,
                };
            })->first();

            $startJamKey = (int) $block['jam_ke_mulai'];
            $endJamKey = (int) $block['jam_ke_selesai'];

            // Priority 1: Official SMKN 2 Indramayu period timetable mapping
            // Priority 2: Database JamPelajaran record fallback
            $waktuMulaiRaw = $officialPeriods[$startJamKey]['mulai'] 
                ?? ($jamMap->get($startJamKey)?->waktu_mulai ?? '06:45');
            $waktuSelesaiRaw = $officialPeriods[$endJamKey]['selesai'] 
                ?? ($jamMap->get($endJamKey)?->waktu_selesai ?? '15:00');

            $timeArrived = false;
            $isPeriodOver = false;
            $displayStartStr = '06:45';

            try {
                $mulaiCarbon = Carbon::parse($waktuMulaiRaw, 'Asia/Jakarta');
                $displayStartStr = $mulaiCarbon->format('H:i');

                $startTimeStr = $mulaiCarbon->format('H:i');

                if ($timeNow >= $startTimeStr) {
                    $timeArrived = true;
                }

                // Check if still within teaching period: handshake HARUS dilakukan selama jam pelajaran berlangsung
                $selesaiStr = $officialPeriods[$endJamKey]['selesai'] ?? ($jamMap->get($endJamKey)?->waktu_selesai ?? '15:00');
                if ($timeNow > substr($selesaiStr, 0, 5)) {
                    $isPeriodOver = true;
                }
            } catch (\Exception $e) {
                $timeArrived = true;
                $displayStartStr = substr($waktuMulaiRaw, 0, 5);
            }

            // Note: Do not prematurely auto-close active class today so teacher can complete materi, foto, presensi, and kktp

            $block['waktu_mulai_str'] = $displayStartStr;
            $block['time_arrived'] = $timeArrived;
            $block['is_period_over'] = $isPeriodOver;
            $block['agenda'] = $agenda;
            
            // Calculate handshake timing for card color (new rule)
            $block['handshake_on_time'] = null; // null = no handshake yet
            if ($agenda && in_array($agenda->status, ['berjalan', 'selesai', 'token_terverifikasi'])) {
                $scheduledStart = $officialPeriods[$startJamKey]['mulai'] ?? '06:45';
                $scheduledCarbon = Carbon::parse($scheduledStart, 'Asia/Jakarta');
                $actualStart = $agenda->waktu_mulai;
                if ($actualStart) {
                    $diffMinutes = $scheduledCarbon->diffInMinutes($actualStart, false);
                    $block['handshake_on_time'] = $diffMinutes <= 45; // true = ≤45min, false = >45min
                }
            }
            $block['has_foto'] = !empty($agenda?->foto_bukti_path);
            $block['otp_time'] = $agenda?->waktu_mulai?->setTimezone('Asia/Jakarta')?->format('H:i');

            $block['status_label'] = $this->getStatusLabel($agenda);
            $block['can_start'] = $this->canStart($block, $agenda, $timeArrived, $isPeriodOver);

            return (object) $block;
        });
    }

    private function getStatusLabel(?AgendaHarian $agenda): array
    {
        if ($this->todayHoliday && !$agenda) {
            return [
                'text' => 'Libur: ' . $this->todayHoliday->nama_hari_libur,
                'class' => 'status-kuning',
                'icon' => 'bi-brightness-alt-high-fill'
            ];
        }

        if (!$agenda) {
            return ['text' => 'Belum Mulai', 'class' => 'status-abu', 'icon' => 'bi-circle'];
        }

        if (in_array($agenda->status_kehadiran_guru, ['izin', 'cuti', 'sakit', 'dinas', 'tugas_luar'])) {
            return [
                'text' => 'Guru ' . ucfirst($agenda->status_kehadiran_guru),
                'class' => 'status-ungu',
                'icon' => 'bi-info-circle-fill'
            ];
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

    private function canStart(array $block, ?AgendaHarian $agenda, bool $timeArrived, bool $isPeriodOver): bool
    {
        $user = auth()->user();
        if ($this->todayHoliday && !$user->canOverride()) return false;
        if ($block['is_kegiatan_khusus']) return false;
        if ($agenda && in_array($agenda->status_kehadiran_guru, ['izin', 'cuti', 'sakit', 'dinas', 'tugas_luar'])) return false;

        // Handshake HARUS dilaksanakan selama jam pelajaran terkait berlangsung
        if ($isPeriodOver) return false;
        if (!$timeArrived && !$agenda) return false;
        if (!$agenda) return true;
        if ($agenda->status === 'dibatalkan' && !$isPeriodOver) return true;

        return false;
    }

    public function render()
    {
        return view('livewire.guru.dashboard-guru', [
            'jadwals' => $this->jadwalHariIni,
            'todayHoliday' => $this->todayHoliday,
        ]);
    }
}
