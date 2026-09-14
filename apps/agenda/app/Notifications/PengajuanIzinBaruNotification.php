<?php

namespace App\Notifications;

use App\Models\IzinGuru;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanIzinBaruNotification extends Notification
{
    use Queueable;

    public function __construct(
        private IzinGuru $izin
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $guruName = $this->izin->guru?->name ?? 'Seorang Guru';
        $jenisLabel = $this->izin->jenis_izin_label;
        $waktu = $this->izin->waktu_display;
        $alasan = $this->izin->alasan;
        $actionUrl = $notifiable->role === 'admin' ? '/admin/verifikasi-izin' : '/waka/verifikasi-izin';
        $title = "📋 Pengajuan Izin Baru: {$guruName}";
        $message = "Guru {$guruName} mengajukan {$jenisLabel} ({$waktu}) hari ini. Alasan: {$alasan}";

        // Send WebPush
        try {
            WebPushService::sendToUser(
                $notifiable,
                $title,
                $message,
                $actionUrl,
                ['type' => 'izin_baru', 'izin_id' => $this->izin->id]
            );
        } catch (\Throwable $e) {
            // Silently ignore push error
        }

        return [
            'type' => 'izin_baru',
            'title' => $title,
            'message' => $message,
            'izin_id' => $this->izin->id,
            'guru_id' => $this->izin->guru_id,
            'guru_name' => $guruName,
            'jenis_izin' => $this->izin->jenis_izin,
            'waktu_display' => $waktu,
            'action_url' => $actionUrl,
            'icon' => 'bi-calendar-x-fill',
            'color' => 'text-warning',
        ];
    }
}
