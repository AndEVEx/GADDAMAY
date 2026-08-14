<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GuruScheduleController extends Controller
{
    /**
     * Return today's teaching schedule for the authenticated guru,
     * with official SMKN 2 Indramayu period times.
     */
    public function todaySchedule(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([], 401);
        }

        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->dayOfWeekIso; // 1=Monday

        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'],
            1  => ['mulai' => '06:45', 'selesai' => '07:30'],
            2  => ['mulai' => '07:30', 'selesai' => '08:15'],
            3  => ['mulai' => '08:15', 'selesai' => '09:00'],
            4  => ['mulai' => '09:00', 'selesai' => '09:45'],
            5  => ['mulai' => '09:45', 'selesai' => '10:00'],
            6  => ['mulai' => '10:00', 'selesai' => '10:45'],
            7  => ['mulai' => '10:45', 'selesai' => '11:30'],
            8  => ['mulai' => '11:30', 'selesai' => '12:15'],
            9  => ['mulai' => '12:15', 'selesai' => '12:45'],
            10 => ['mulai' => '12:45', 'selesai' => '13:30'],
            11 => ['mulai' => '13:30', 'selesai' => '14:15'],
            12 => ['mulai' => '14:15', 'selesai' => '15:00'],
        ];

        $jadwals = JadwalPelajaran::where('hari', $hariIni)
            ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran'])
            ->orderBy('jam_ke_mulai')
            ->get();

        // Merge adjacent sessions split by break (same mapel + rombel)
        $grouped = $jadwals->groupBy(fn($j) => $j->rombel_id . '_' . $j->mapel_id);
        $blocks = [];

        foreach ($grouped as $items) {
            $sorted = $items->sortBy('jam_ke_mulai')->values();
            $currentBlock = null;

            foreach ($sorted as $item) {
                if (!$currentBlock) {
                    $currentBlock = [
                        'jam_ke_mulai' => $item->jam_ke_mulai,
                        'jam_ke_selesai' => $item->jam_ke_selesai,
                        'kelas' => $item->rombel?->nama_kelas ?? '-',
                        'mapel' => $item->mataPelajaran?->nama_mapel ?? '-',
                    ];
                } else {
                    $currentBlock['jam_ke_selesai'] = max($currentBlock['jam_ke_selesai'], $item->jam_ke_selesai);
                }
            }

            if ($currentBlock) {
                $startJam = (int) $currentBlock['jam_ke_mulai'];
                $endJam = (int) $currentBlock['jam_ke_selesai'];

                $currentBlock['waktu_mulai'] = $officialPeriods[$startJam]['mulai'] ?? '06:45';
                $currentBlock['waktu_selesai'] = $officialPeriods[$endJam]['selesai'] ?? '15:00';

                $blocks[] = $currentBlock;
            }
        }

        // Sort by start time
        usort($blocks, fn($a, $b) => strcmp($a['waktu_mulai'], $b['waktu_mulai']));

        return response()->json([
            'tanggal' => $now->format('Y-m-d'),
            'server_time' => $now->format('H:i'),
            'jadwal' => $blocks,
        ]);
    }
}
