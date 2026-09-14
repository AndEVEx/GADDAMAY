<?php

namespace App\Services;

use App\Models\PerizinanSiswa;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use App\Models\JadwalPelajaran;
use App\Services\Wa\WaGatewayManager;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class PerizinanSyncService
{
    protected WaGatewayManager $waManager;

    public function __construct(WaGatewayManager $waManager)
    {
        $this->waManager = $waManager;
    }

    /**
     * Sinkronisasi status izin siswa ke buku agenda KBM kelas
     */
    public function syncToAgendaKBM(PerizinanSiswa $izin): int
    {
        if (!$izin->isDisetujui()) {
            return 0;
        }

        $siswa = $izin->siswa;
        if (!$siswa || !$siswa->rombel_id) {
            return 0;
        }

        $startDate = Carbon::parse($izin->tanggal_mulai);
        $endDate = Carbon::parse($izin->tanggal_selesai);
        $period = CarbonPeriod::create($startDate, $endDate);

        $statusKehadiran = ($izin->kategori === 'sakit') ? 'sakit' : 'izin';
        $verifikatorName = $izin->verifikator?->name ?? 'Petugas Sekolah';
        $keterangan = "[Izin Resmi] {$izin->kategori_label} - Diverifikasi: {$verifikatorName}";

        $affectedCount = 0;

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');

            // Cari seluruh agenda harian yang sudah dibuka untuk rombel siswa pada tanggal tersebut
            $agendas = AgendaHarian::where('rombel_id', $siswa->rombel_id)
                ->whereDate('tanggal', $formattedDate)
                ->get();

            foreach ($agendas as $agenda) {
                KehadiranMurid::updateOrCreate(
                    [
                        'agenda_harian_id' => $agenda->id,
                        'siswa_id' => $siswa->id,
                    ],
                    [
                        'status' => $statusKehadiran,
                        'keterangan' => $keterangan,
                    ]
                );
                $affectedCount++;
            }
        }

        $izin->update(['auto_locked_agenda' => true]);

        return $affectedCount;
    }

    /**
     * Kirim notifikasi WhatsApp resmi via Multi-Driver Gateway
     */
    public function dispatchWaNotification(PerizinanSiswa $izin): array
    {
        $phone = $izin->nomor_wa_pemohon;
        if (empty($phone)) {
            $izin->update([
                'wa_notif_status' => 'skipped',
                'wa_notif_text' => 'Nomor WhatsApp pemohon tidak tercantum.',
            ]);
            return ['success' => false, 'reason' => 'no_phone'];
        }

        $message = $izin->generateWaMessage();

        try {
            $result = $this->waManager->sendMessage($phone, $message);

            if ($result['success']) {
                $izin->update([
                    'wa_notif_status' => 'sent_auto',
                    'wa_notif_text' => $message,
                    'wa_sent_at' => now(),
                ]);
            } else {
                $izin->update([
                    'wa_notif_status' => 'failed',
                    'wa_notif_text' => $message,
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('PerizinanSyncService WA Error: ' . $e->getMessage());

            $izin->update([
                'wa_notif_status' => 'failed',
                'wa_notif_text' => $message,
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tandai notifikasi WhatsApp terkirim secara manual oleh guru / piket
     */
    public function markAsSentManually(PerizinanSiswa $izin): void
    {
        $izin->update([
            'wa_notif_status' => 'sent_manual',
            'wa_sent_at' => now(),
            'wa_notif_text' => $izin->generateWaMessage(),
        ]);
    }
}