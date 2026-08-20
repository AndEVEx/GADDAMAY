<?php

namespace App\Notifications;

use App\Models\JadwalPelajaran;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KelasAkanDimulaiNotification extends Notification
{
    use Queueable;

    public function __construct(
        private JadwalPelajaran $jadwal,
        private string $motivasi
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $mapel = $this->jadwal->mataPelajaran?->nama_mapel ?? 'Mengajar';
        $kelas = $this->jadwal->rombel?->nama_kelas ?? '';
        $title = "⏰ 15 Menit Lagi Mengajar!";
        $message = "{$notifiable->name}, 15 menit lagi Anda mengajar {$mapel} di kelas {$kelas}! \"{$this->motivasi}\"";

        // Also trigger WebPush directly to user's devices
        try {
            WebPushService::sendToUser(
                $notifiable,
                $title,
                "{$mapel} — {$kelas} segera dimulai. \"{$this->motivasi}\"",
                "/guru/dashboard",
                ['jadwal_id' => $this->jadwal->id, 'type' => 'kelas_mulai']
            );
        } catch (\Throwable $e) {
            // Silently log or ignore push failures to keep database notification reliable
        }

        return [
            'type' => 'kelas_mulai',
            'title' => $title,
            'message' => $message,
            'jadwal_id' => $this->jadwal->id,
            'mapel' => $mapel,
            'kelas' => $kelas,
            'motivasi' => $this->motivasi,
            'action_url' => '/guru/dashboard',
            'icon' => 'bi-clock-fill',
            'color' => 'text-warning',
        ];
    }
}
