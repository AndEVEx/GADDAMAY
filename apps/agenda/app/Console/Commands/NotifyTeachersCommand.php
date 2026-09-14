<?php

namespace App\Console\Commands;

use App\Models\JadwalPelajaran;
use App\Models\MotivasiPantun;
use App\Models\HariLibur;
use App\Notifications\KelasAkanDimulaiNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyTeachersCommand extends Command
{
    protected $signature = 'agenda:notify-teachers';
    protected $description = 'Kirim notifikasi ke guru 15 menit sebelum jadwal mengajar';

    public function handle(): int
    {
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->dayOfWeekIso; // 1=Senin..7=Minggu

        if ($hariIni > 5) {
            $this->info('Weekend - no notifications.');
            return self::SUCCESS;
        }

        // Check if today is a school holiday
        if (HariLibur::isHariLibur($now)) {
            $this->info('Hari libur sekolah - notifikasi dinonaktifkan.');
            return self::SUCCESS;
        }

        // Official SMKN 2 Indramayu periods
        $officialPeriods = [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'],
            1  => ['mulai' => '06:45', 'selesai' => '07:30'],
            2  => ['mulai' => '07:30', 'selesai' => '08:15'],
            3  => ['mulai' => '08:15', 'selesai' => '09:00'],
            4  => ['mulai' => '09:00', 'selesai' => '09:45'],
            6  => ['mulai' => '10:00', 'selesai' => '10:45'],
            7  => ['mulai' => '10:45', 'selesai' => '11:30'],
            8  => ['mulai' => '11:30', 'selesai' => '12:15'],
            10 => ['mulai' => '12:45', 'selesai' => '13:30'],
            11 => ['mulai' => '13:30', 'selesai' => '14:15'],
            12 => ['mulai' => '14:15', 'selesai' => '15:00'],
        ];

        $nowMinutes = $now->hour * 60 + $now->minute;
        $matchingPeriods = [];

        foreach ($officialPeriods as $periodNum => $time) {
            $parts = explode(':', $time['mulai']);
            $startMinutes = (int)$parts[0] * 60 + (int)$parts[1];
            $diff = $startMinutes - $nowMinutes;

            // Target periods that start in 10 to 20 minutes (centered on 15 minutes)
            if ($diff >= 10 && $diff <= 20) {
                $matchingPeriods[] = $periodNum;
            }
        }

        if (empty($matchingPeriods)) {
            $this->info('No periods starting in ~15 minutes at this time (' . $now->format('H:i') . ').');
            return self::SUCCESS;
        }

        // Find schedules starting in these periods today
        $jadwals = JadwalPelajaran::where('hari', $hariIni)
            ->whereIn('jam_ke_mulai', $matchingPeriods)
            ->whereNotNull('mapel_id')
            ->with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->get();

        $motivasi = MotivasiPantun::sebelumMengajar()->inRandomOrder()->first();
        $notifiedCount = 0;

        foreach ($jadwals as $jadwal) {
            foreach ($jadwal->jadwalGuru as $jg) {
                $guru = $jg->guru;
                if (!$guru) continue;

                // Check if already notified today for this jadwal
                $todayNotifications = $guru->notifications()
                    ->whereDate('created_at', $now->toDateString())
                    ->get();

                $alreadyNotified = $todayNotifications->contains(function ($n) use ($jadwal) {
                    return isset($n->data['jadwal_id']) && (string)$n->data['jadwal_id'] === (string)$jadwal->id;
                });

                if ($alreadyNotified) continue;

                $guru->notify(new KelasAkanDimulaiNotification(
                    $jadwal,
                    $motivasi?->isi ?? 'Semangat mencerdaskan anak bangsa!'
                ));
                $notifiedCount++;
            }
        }

        $this->info("Notified {$notifiedCount} teachers for upcoming periods: " . implode(', ', $matchingPeriods));
        return self::SUCCESS;
    }
}
