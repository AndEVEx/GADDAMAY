<?php

namespace App\Services;

use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\AuditLog;
use App\Models\JadwalSwapLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JadwalSwapService
{
    /**
     * Jurusan patterns for vocational block rotation.
     */
    public const VOCATIONAL_PATTERNS = [
        'TP' => ['TP', 'TPM', 'Pemesinan'],
        'APHP' => ['APHP', 'APHPi', 'Pengolahan'],
        'NKPI' => ['NKPI', 'Nautika'],
        'RPL' => ['RPL', 'PPLG', 'Rekayasa Perangkat Lunak'],
    ];

    /**
     * Preview custom swap between two specific rombels.
     */
    public static function previewSwapCustom(string $rombelIdA, string $rombelIdB): array
    {
        $rombelA = Rombel::find($rombelIdA);
        $rombelB = Rombel::find($rombelIdB);

        if (!$rombelA || !$rombelB || $rombelA->id === $rombelB->id) {
            return [
                'can_swap' => false,
                'message' => 'Pilih dua kelas yang berbeda dan valid.',
                'pair' => null,
            ];
        }

        $schedulesA = JadwalPelajaran::where('rombel_id', $rombelA->id)
            ->with(['mataPelajaran', 'jadwalGuru.guru'])
            ->get();

        $schedulesB = JadwalPelajaran::where('rombel_id', $rombelB->id)
            ->with(['mataPelajaran', 'jadwalGuru.guru'])
            ->get();

        $mapelsA = $schedulesA->map(fn($s) => $s->mataPelajaran?->nama_mapel ?? $s->kegiatan_khusus ?? 'Tanpa Mapel')->unique()->values()->all();
        $mapelsB = $schedulesB->map(fn($s) => $s->mataPelajaran?->nama_mapel ?? $s->kegiatan_khusus ?? 'Tanpa Mapel')->unique()->values()->all();

        $pairData = [
            'rombel_a_id' => $rombelA->id,
            'rombel_a_nama' => $rombelA->nama_kelas,
            'count_a' => $schedulesA->count(),
            'mapels_a' => $mapelsA,
            'rombel_b_id' => $rombelB->id,
            'rombel_b_nama' => $rombelB->nama_kelas,
            'count_b' => $schedulesB->count(),
            'mapels_b' => $mapelsB,
        ];

        return [
            'can_swap' => ($schedulesA->count() > 0 || $schedulesB->count() > 0),
            'message' => ($schedulesA->count() === 0 && $schedulesB->count() === 0)
                ? "Kedua kelas ({$rombelA->nama_kelas} & {$rombelB->nama_kelas}) tidak memiliki jadwal aktif."
                : "Siap menukar jadwal antara {$rombelA->nama_kelas} dan {$rombelB->nama_kelas}.",
            'pair' => $pairData,
        ];
    }

    /**
     * Preview all vocational block swap pairs across all tingkat.
     */
    public static function previewSwapAllVocational(): array
    {
        $tingkats = [10, 11, 12];
        $pairs = [];
        $totalSchedules = 0;

        foreach (self::VOCATIONAL_PATTERNS as $jurusanKey => $patterns) {
            foreach ($tingkats as $tingkat) {
                $rombels = Rombel::where('tingkat', $tingkat)
                    ->where(function ($q) use ($patterns) {
                        foreach ($patterns as $p) {
                            $q->orWhere('nama_kelas', 'like', "%{$p}%");
                        }
                    })
                    ->get();

                $rombel1 = $rombels->first(fn($r) => preg_match('/(\b|_)1(\b|$)/', $r->nama_kelas) || str_ends_with(trim($r->nama_kelas), '1'));
                $rombel2 = $rombels->first(fn($r) => preg_match('/(\b|_)2(\b|$)/', $r->nama_kelas) || str_ends_with(trim($r->nama_kelas), '2'));

                if ($rombel1 && $rombel2 && $rombel1->id !== $rombel2->id) {
                    $preview = self::previewSwapCustom($rombel1->id, $rombel2->id);
                    if ($preview['pair']) {
                        $pair = $preview['pair'];
                        $pair['jurusan'] = $jurusanKey;
                        $pair['tingkat'] = $tingkat;
                        $pairs[] = $pair;
                        $totalSchedules += ($pair['count_a'] + $pair['count_b']);
                    }
                }
            }
        }

        return [
            'can_swap' => count($pairs) > 0,
            'pairs' => $pairs,
            'total_pairs' => count($pairs),
            'total_schedules' => $totalSchedules,
            'message' => count($pairs) > 0
                ? "Ditemukan " . count($pairs) . " pasang kelas blok vokasi ({$totalSchedules} sesi jadwal total) siap ditukar."
                : "Tidak ditemukan pasangan kelas blok vokasi yang cocok.",
        ];
    }

    /**
     * Swap all schedules between two rombels with atomic logging.
     */
    public static function swapRombelSchedules(
        string $rombelIdA,
        string $rombelIdB,
        ?string $batchId = null,
        string $swapType = 'custom_pair'
    ): array {
        $rombelA = Rombel::find($rombelIdA);
        $rombelB = Rombel::find($rombelIdB);

        if (!$rombelA || !$rombelB || $rombelA->id === $rombelB->id) {
            return [
                'success' => false,
                'message' => 'Rombel tidak valid atau sama.',
                'swapped_count' => 0,
            ];
        }

        $schedulesA = JadwalPelajaran::where('rombel_id', $rombelA->id)->with(['mataPelajaran'])->get();
        $schedulesB = JadwalPelajaran::where('rombel_id', $rombelB->id)->with(['mataPelajaran'])->get();
        $countA = $schedulesA->count();
        $countB = $schedulesB->count();

        if ($countA === 0 && $countB === 0) {
            return [
                'success' => false,
                'message' => "Tidak ada jadwal pada rombel {$rombelA->nama_kelas} dan {$rombelB->nama_kelas}.",
                'swapped_count' => 0,
            ];
        }

        $batchId = $batchId ?? ('SWAP_' . date('Ymd_His') . '_' . strtoupper(Str::random(4)));

        DB::transaction(function () use ($rombelA, $rombelB, $countA, $countB, $schedulesA, $schedulesB, $batchId, $swapType) {
            // Atomic swap using SQL CASE without violating foreign key constraints
            DB::statement("
                UPDATE jadwal_pelajaran 
                SET rombel_id = CASE 
                    WHEN rombel_id = ? THEN ? 
                    WHEN rombel_id = ? THEN ? 
                    ELSE rombel_id 
                END,
                updated_at = NOW()
                WHERE rombel_id IN (?, ?)
            ", [
                $rombelA->id, $rombelB->id,
                $rombelB->id, $rombelA->id,
                $rombelA->id, $rombelB->id
            ]);

            // Clean unstarted / waiting handshake agendas today for these rombels so teachers can handshake fresh
            $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
            AgendaHarian::where('tanggal', $today)
                ->whereIn('status', ['menunggu_token'])
                ->whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', [$rombelA->id, $rombelB->id]))
                ->delete();

            // Record to jadwal_swap_logs table if exists
            if (Schema::hasTable('jadwal_swap_logs')) {
                JadwalSwapLog::create([
                    'batch_id' => $batchId,
                    'user_id' => auth()->id(),
                    'swap_type' => $swapType,
                    'rombel_a_id' => $rombelA->id,
                    'rombel_b_id' => $rombelB->id,
                    'rombel_a_nama' => $rombelA->nama_kelas,
                    'rombel_b_nama' => $rombelB->nama_kelas,
                    'schedules_count_a' => $countA,
                    'schedules_count_b' => $countB,
                    'details' => [
                        'mapels_a_before' => $schedulesA->map(fn($s) => $s->mataPelajaran?->nama_mapel ?? $s->kegiatan_khusus)->unique()->values()->all(),
                        'mapels_b_before' => $schedulesB->map(fn($s) => $s->mataPelajaran?->nama_mapel ?? $s->kegiatan_khusus)->unique()->values()->all(),
                    ],
                    'status' => 'active',
                ]);
            }

            // Log the swap in audit log
            if (auth()->check()) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'swap_jadwal',
                    'auditable_type' => Rombel::class,
                    'auditable_id' => $rombelA->id,
                    'new_values' => [
                        'batch_id' => $batchId,
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
            'batch_id' => $batchId,
            'message' => "Berhasil menukar jadwal antara {$rombelA->nama_kelas} ({$countA} sesi) dan {$rombelB->nama_kelas} ({$countB} sesi).",
            'swapped_count' => $countA + $countB,
        ];
    }

    /**
     * Auto-detect and swap block schedules for TP, APHP/APHPi, NKPI, and RPL across all tingkat (10, 11, 12).
     */
    public static function swapAllVocationalBlockSchedules(): array
    {
        $preview = self::previewSwapAllVocational();

        if (!$preview['can_swap'] || empty($preview['pairs'])) {
            return [
                'success' => false,
                'message' => "Tidak ditemukan pasangan kelas blok yang cocok untuk ditukar.",
                'pairs_count' => 0,
                'schedules_count' => 0,
            ];
        }

        $batchId = 'BATCH_' . date('Ymd_His') . '_' . strtoupper(Str::random(4));
        $totalPairs = 0;
        $totalSchedules = 0;
        $results = [];

        foreach ($preview['pairs'] as $pair) {
            $res = self::swapRombelSchedules($pair['rombel_a_id'], $pair['rombel_b_id'], $batchId, 'all_vocational');
            if ($res['success']) {
                $results[] = "{$pair['rombel_a_nama']} <-> {$pair['rombel_b_nama']}: {$res['swapped_count']} sesi ditukar";
                $totalPairs++;
                $totalSchedules += $res['swapped_count'];
            }
        }

        return [
            'success' => $totalPairs > 0,
            'batch_id' => $batchId,
            'pairs_count' => $totalPairs,
            'schedules_count' => $totalSchedules,
            'details' => $results,
            'message' => $totalPairs > 0
                ? "Berhasil menukar jadwal blok untuk {$totalPairs} pasang kelas ({$totalSchedules} sesi total). Batch ID: {$batchId}"
                : "Gagal menukar jadwal blok.",
        ];
    }

    /**
     * Undo a specific swap log record.
     */
    public static function undoSwap(string $swapLogId): array
    {
        if (!Schema::hasTable('jadwal_swap_logs')) {
            return ['success' => false, 'message' => 'Tabel log pertukaran belum tersedia.'];
        }

        $log = JadwalSwapLog::find($swapLogId);
        if (!$log) {
            return ['success' => false, 'message' => 'Catatan riwayat penukaran tidak ditemukan.'];
        }

        if ($log->status === 'undone') {
            return ['success' => false, 'message' => 'Penukaran jadwal ini sudah dibatalkan sebelumnya.'];
        }

        $rombelA = Rombel::find($log->rombel_a_id);
        $rombelB = Rombel::find($log->rombel_b_id);

        if (!$rombelA || !$rombelB) {
            return ['success' => false, 'message' => 'Rombel terkait tidak ditemukan di database.'];
        }

        DB::transaction(function () use ($log, $rombelA, $rombelB) {
            // Swap back atomically
            DB::statement("
                UPDATE jadwal_pelajaran 
                SET rombel_id = CASE 
                    WHEN rombel_id = ? THEN ? 
                    WHEN rombel_id = ? THEN ? 
                    ELSE rombel_id 
                END,
                updated_at = NOW()
                WHERE rombel_id IN (?, ?)
            ", [
                $rombelA->id, $rombelB->id,
                $rombelB->id, $rombelA->id,
                $rombelA->id, $rombelB->id
            ]);

            // Clean unstarted agendas today
            $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
            AgendaHarian::where('tanggal', $today)
                ->whereIn('status', ['menunggu_token'])
                ->whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', [$rombelA->id, $rombelB->id]))
                ->delete();

            // Mark log as undone
            $log->update([
                'status' => 'undone',
                'undone_at' => Carbon::now('Asia/Jakarta'),
                'undone_by_id' => auth()->id(),
            ]);

            // Audit log
            if (auth()->check()) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'undo_swap_jadwal',
                    'auditable_type' => Rombel::class,
                    'auditable_id' => $rombelA->id,
                    'new_values' => [
                        'swap_log_id' => $log->id,
                        'batch_id' => $log->batch_id,
                        'rombel_a' => $rombelA->nama_kelas,
                        'rombel_b' => $rombelB->nama_kelas,
                        'undone_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ],
                    'ip_address' => request()->ip(),
                ]);
            }
        });

        return [
            'success' => true,
            'message' => "Berhasil membatalkan (Undo) penukaran jadwal antara {$log->rombel_a_nama} dan {$log->rombel_b_nama}. Jadwal dikembalikan ke posisi semula.",
        ];
    }

    /**
     * Undo all swaps in a batch.
     */
    public static function undoBatch(string $batchId): array
    {
        if (!Schema::hasTable('jadwal_swap_logs')) {
            return ['success' => false, 'message' => 'Tabel log pertukaran belum tersedia.'];
        }

        $logs = JadwalSwapLog::where('batch_id', $batchId)->where('status', 'active')->get();

        if ($logs->isEmpty()) {
            return ['success' => false, 'message' => 'Tidak ada penukaran aktif yang dapat di-undo pada batch ini.'];
        }

        $undoneCount = 0;
        foreach ($logs as $log) {
            $res = self::undoSwap($log->id);
            if ($res['success']) {
                $undoneCount++;
            }
        }

        return [
            'success' => $undoneCount > 0,
            'undone_count' => $undoneCount,
            'message' => "Berhasil membatalkan (Undo) {$undoneCount} pasang jadwal pada Batch {$batchId}.",
        ];
    }
}
