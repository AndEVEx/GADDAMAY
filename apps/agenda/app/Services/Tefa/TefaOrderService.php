<?php

namespace App\Services\Tefa;

use App\Models\TefaOrder;
use Carbon\Carbon;

class TefaOrderService
{
    /**
     * Buat kode SPK order baru unik: TEFA-YYYYMM-XXXX
     */
    public static function generateKodeOrder(): string
    {
        $prefix = 'TEFA-' . Carbon::now()->format('Ym') . '-';
        $count = TefaOrder::where('kode_order', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung durasi kerja produksi dalam menit.
     */
    public static function calculateDurationMinutes(string $jamMulai, string $jamSelesai): int
    {
        try {
            $start = Carbon::parse($jamMulai);
            $end = Carbon::parse($jamSelesai);
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            return max(1, (int) $start->diffInMinutes($end));
        } catch (\Throwable $e) {
            return 60; // default 1 jam
        }
    }
}