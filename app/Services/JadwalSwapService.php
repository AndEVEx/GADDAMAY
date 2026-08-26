<?php

namespace App\Services;

use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JadwalSwapService
{
    /**
     * Swap all schedules between two rombels.
     */
    public static function swapRombelSchedules(string $rombelIdA, string $rombelIdB): array
    {
        $rombelA = Rombel::find($rombelIdA);
        $rombelB = Rombel::find($rombelIdB);

        if (!$rombelA || !$rombelB || $rombelA->id === $rombelB->id) {
            return [
                'success' => false,
                'message' => 'Rombel tidak valid atau sama.',
                'swapped_count' => 0,
            ];
        }

        $countA = JadwalPelajaran::where('rombel_id', $rombelA->id)->count();
        $countB = JadwalPelajaran::where('rombel_id', $rombelB->id)->count();

        if ($countA === 0 && $countB === 0) {
            return [
                'success' => false,
                'message' => "Tidak ada jadwal pada rombel {$rombelA->nama_kelas} dan {$rombelB->nama_kelas}.",
                'swapped_count' => 0,
            ];
        }

        DB::transaction(function () use ($rombelA, $rombelB, $countA, $countB) {
            // Generate a safe unique placeholder UUID
            $tempId = (string) Str::uuid();

            // 1. Temporarily move A -> temp
            JadwalPelajaran::where('rombel_id', $rombelA->id)->update(['rombel_id' => $tempId]);

            // 2. Move B -> A
            JadwalPelajaran::where('rombel_id', $rombelB->id)->update(['rombel_id' => $rombelA->id]);

            // 3. Move temp -> B
            JadwalPelajaran::where('rombel_id', $tempId)->update(['rombel_id' => $rombelB->id]);

            // Clean unstarted / waiting handshake agendas today for these rombels so teachers can handshake fresh
            $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
            AgendaHarian::where('tanggal', $today)
                ->whereIn('status', ['menunggu_token'])
                ->whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', [$rombelA->id, $rombelB->id]))
                ->delete();

            // Log the swap in audit log
            if (auth()->check()) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'swap_jadwal',
                    'auditable_type' => Rombel::class,
                    'auditable_id' => $rombelA->id,
                    'new_values' => [
                        'rombel_a' => $rombelA->nama_kelas,
                        'rombel_b' => $rombelB->nama_kelas,
                        'total_jadwal_a' => $countA,
                        'total_jadwal_b' => $countB,
                        'timestamp' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ],
                    'ip_address' => request()->ip(),
                ]);
            }
        });

        return [
            'success' => true,
            'message' => "Berhasil menukar jadwal antara {$rombelA->nama_kelas} ({$countA} sesi) dan {$rombelB->nama_kelas} ({$countB} sesi).",
            'swapped_count' => $countA + $countB,
        ];
    }

    /**
     * Auto-detect and swap block schedules for TP, APHP/APHPi, and NKPI across all tingkat (10, 11, 12).
     */
    public static function swapAllVocationalBlockSchedules(): array
    {
        $jurusanPatterns = [
            'TP' => ['TP', 'TPM', 'Pemesinan'],
            'APHP' => ['APHP', 'APHPi', 'Pengolahan'],
            'NKPI' => ['NKPI', 'Nautika'],
        ];

        $tingkats = [10, 11, 12];
        $results = [];
        $totalPairsSwapped = 0;
        $totalSchedulesSwapped = 0;

        foreach ($jurusanPatterns as $key => $patterns) {
            foreach ($tingkats as $tingkat) {
                // Find Rombel 1 and Rombel 2 for this tingkat & jurusan
                $rombels = Rombel::where('tingkat', $tingkat)
                    ->where(function ($q) use ($patterns) {
                        foreach ($patterns as $p) {
                            $q->orWhere('nama_kelas', 'like', "%{$p}%");
                        }
                    })
                    ->get();

                // Find pair ending with 1 and 2
                $rombel1 = $rombels->first(fn($r) => preg_match('/(\b|_)1(\b|$)/', $r->nama_kelas) || str_ends_with(trim($r->nama_kelas), '1'));
                $rombel2 = $rombels->first(fn($r) => preg_match('/(\b|_)2(\b|$)/', $r->nama_kelas) || str_ends_with(trim($r->nama_kelas), '2'));

                if ($rombel1 && $rombel2 && $rombel1->id !== $rombel2->id) {
                    $res = self::swapRombelSchedules($rombel1->id, $rombel2->id);
                    if ($res['success']) {
                        $results[] = "{$rombel1->nama_kelas} <-> {$rombel2->nama_kelas}: {$res['swapped_count']} sesi ditukar";
                        $totalPairsSwapped++;
                        $totalSchedulesSwapped += $res['swapped_count'];
                    }
                }
            }
        }

        return [
            'success' => $totalPairsSwapped > 0,
            'pairs_count' => $totalPairsSwapped,
            'schedules_count' => $totalSchedulesSwapped,
            'details' => $results,
            'message' => $totalPairsSwapped > 0 
                ? "Berhasil menukar jadwal blok untuk {$totalPairsSwapped} pasang kelas ({$totalSchedulesSwapped} sesi total)."
                : "Tidak ditemukan pasangan kelas blok yang cocok untuk ditukar.",
        ];
    }
}
