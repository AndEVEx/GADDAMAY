<?php

namespace App\Services\Pkl;

class PklGeoService
{
    /**
     * Hitung jarak antara dua koordinat GPS menggunakan formula Haversine (hasil dalam meter).
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Periksa apakah koordinat siswa masih dalam batas radius toleransi DUDI.
     */
    public static function isWithinRadius(float $studentLat, float $studentLon, float $dudiLat, float $dudiLon, int $radiusToleranceMeter = 100): array
    {
        $distance = self::calculateDistance($studentLat, $studentLon, $dudiLat, $dudiLon);
        $isValid = $distance <= $radiusToleranceMeter;

        return [
            'is_valid' => $isValid,
            'distance_meter' => $distance,
            'tolerance_meter' => $radiusToleranceMeter,
            'difference_meter' => max(0, $distance - $radiusToleranceMeter),
        ];
    }
}