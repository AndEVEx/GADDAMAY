<?php

namespace App\Notifications;

use App\Models\JadwalPelajaran;
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

        return [
            'title' => "⏰ Bersiap Mengajar!",
            'message' => "{$notifiable->name}, bersiap mengajar {$mapel} di {$kelas}! {$this->motivasi}",
            'jadwal_id' => $this->jadwal->id,
            'mapel' => $mapel,
            'kelas' => $kelas,
        ];
    }
}
