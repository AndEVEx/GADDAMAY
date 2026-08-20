<?php

namespace App\Notifications;

use App\Models\IzinGuru;
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

        return [
            'type' => 'izin_baru',
            'title' => "📋 Pengajuan Izin Baru: {$guruName}",
            'message' => "Guru {$guruName} mengajukan {$jenisLabel} ({$waktu}) untuk hari ini. Alasan: {$alasan}",
            'izin_id' => $this->izin->id,
            'guru_id' => $this->izin->guru_id,
            'guru_name' => $guruName,
            'jenis_izin' => $this->izin->jenis_izin,
            'waktu_display' => $waktu,
            'action_url' => $notifiable->role === 'admin' ? '/admin/verifikasi-izin' : '/waka/verifikasi-izin',
            'icon' => 'bi-calendar-x-fill',
            'color' => 'text-warning',
        ];
    }
}
