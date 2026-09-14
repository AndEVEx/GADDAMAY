<?php

namespace App\Notifications;

use App\Models\IzinGuru;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusIzinGuruNotification extends Notification
{
    use Queueable;

    public function __construct(
        private IzinGuru $izin,
        private string $statusDecision, // 'disetujui' or 'ditolak'
        private ?string $catatan = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isDisetujui = $this->statusDecision === 'disetujui';
        $waktu = $this->izin->waktu_display;
        $jenisLabel = $this->izin->jenis_izin_label;

        if ($isDisetujui) {
            $title = "✅ Pengajuan Izin Disetujui";
            $message = "Pengajuan {$jenisLabel} Anda ({$waktu}) untuk hari ini telah DISETUJUI oleh Waka Kurikulum.";
            if (!empty($this->catatan)) {
                $message .= " Catatan: {$this->catatan}";
            }
            $icon = 'bi-check-circle-fill';
            $color = 'text-success';
        } else {
            $title = "❌ Pengajuan Izin Ditolak";
            $message = "Pengajuan {$jenisLabel} Anda ({$waktu}) untuk hari ini DITOLAK oleh Waka Kurikulum.";
            if (!empty($this->catatan)) {
                $message .= " Alasan penolakan: {$this->catatan}";
            }
            $icon = 'bi-x-circle-fill';
            $color = 'text-danger';
        }

        // Trigger WebPush
        try {
            WebPushService::sendToUser(
                $notifiable,
                $title,
                $message,
                '/guru/izin',
                ['type' => 'status_izin', 'status' => $this->statusDecision, 'izin_id' => $this->izin->id]
            );
        } catch (\Throwable $e) {
            // Silently ignore push error
        }

        return [
            'type' => 'status_izin',
            'title' => $title,
            'message' => $message,
            'izin_id' => $this->izin->id,
            'status' => $this->statusDecision,
            'waktu_display' => $waktu,
            'action_url' => '/guru/izin',
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
