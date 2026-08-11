<?php

namespace App\Console\Commands;

use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\MotivasiPantun;
use App\Notifications\KelasAkanDimulaiNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyTeachersCommand extends Command
{
    protected $signature = 'agenda:notify-teachers';
    protected $description = 'Kirim notifikasi ke guru 5 menit sebelum jadwal mengajar';

    public function handle(): int
    {
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->dayOfWeekIso; // 1=Senin

        if ($hariIni > 5) {
            $this->info('Weekend - no notifications.');
            return self::SUCCESS;
        }

        // Find period that starts in ~15 minutes
        $targetTime = $now->copy()->addMinutes(15)->format('H:i');
        $jam = JamPelajaran::where('waktu_mulai', '<=', $targetTime . ':59')
            ->where('waktu_mulai', '>=', $targetTime . ':00')
            ->first();

        if (!$jam) {
            return self::SUCCESS;
        }

        // Find jadwal for this period today
        $jadwals = JadwalPelajaran::where('hari', $hariIni)
            ->where('jam_ke_mulai', $jam->jam_ke)
            ->whereNotNull('mapel_id') // Skip kegiatan khusus
            ->with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->get();

        $motivasi = MotivasiPantun::sebelumMengajar()->inRandomOrder()->first();
        $notifiedCount = 0;

        foreach ($jadwals as $jadwal) {
            foreach ($jadwal->jadwalGuru as $jg) {
                $guru = $jg->guru;
                if (!$guru) continue;

                // Check if already notified today for this jadwal using robust string matching
                $todayNotifications = $guru->notifications()
                    ->whereDate('created_at', $now->toDateString())
                    ->get();

                $alreadyNotified = $todayNotifications->contains(function ($n) use ($jadwal) {
                    return isset($n->data['jadwal_id']) && (string)$n->data['jadwal_id'] === (string)$jadwal->id;
                });

                if ($alreadyNotified) continue;

                $guru->notify(new KelasAkanDimulaiNotification(
                    $jadwal,
                    $motivasi?->isi ?? 'Semangat mengajar!'
                ));
                $notifiedCount++;
            }
        }

        $this->info("Notified {$notifiedCount} teachers for period {$jam->jam_ke}.");
        return self::SUCCESS;
    }
}
