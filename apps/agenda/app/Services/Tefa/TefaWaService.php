<?php

namespace App\Services\Tefa;

use App\Models\TefaOrder;
use App\Services\Wa\WaGatewayManager;
use Illuminate\Support\Facades\Log;

class TefaWaService
{
    /**
     * Susun pesan resmi WhatsApp untuk Konsumen / Pemesan Order TEFA.
     */
    public static function generateOrderNotification(TefaOrder $order, string $stage = 'konfirmasi'): string
    {
        $namaPemesan = $order->nama_pemesan;
        $instruktur = $order->instruktur->name ?? 'Instruktur TEFA';
        $judulProyek = $order->judul_proyek;
        $kodeOrder = $order->kode_order;

        if ($stage === 'selesai') {
            return "🎉 *PRODUK / LAYANAN TEFA SELESAI & LOLOS QC*\n\n" .
                   "Yth. Bpk/Ibu *{$namaPemesan}*.\n\n" .
                   "Kabar gembira dari Unit Produksi Teaching Factory (TEFA) SMKN 2 Indramayu:\n" .
                   "📌 Kode Order: *{$kodeOrder}*\n" .
                   "📦 Pekerjaan: *{$judulProyek}*\n\n" .
                   "Pengerjaan proyek oleh tim siswa pelaksana telah *SELESAI* dan dinyatakan *LOLOS UJI KUALITAS (Quality Control)* oleh instruktur.\n\n" .
                   "Pesanan Bapak/Ibu saat ini *SIAP DISERAHKAN / DIAMBIL* di bengkel/studio TEFA SMKN 2 Indramayu.\n\n" .
                   "Terima kasih atas kepercayaan dan kemitraan Bpk/Ibu bersama generasi vokasi kami.\n" .
                   "Salam hormat,\n{$instruktur} (Unit Produksi TEFA SMKN 2 Indramayu)";
        }

        if ($stage === 'progres') {
            $totalJam = $order->total_jam_produksi;
            return "⚙️ *UPDATE PROGRES PRODUKSI TEFA SMKN 2 INDRAMAYU*\n\n" .
                   "Yth. Bpk/Ibu *{$namaPemesan}*.\n\n" .
                   "Berikut update pengerjaan pesanan Bapak/Ibu:\n" .
                   "📌 Kode Order: *{$kodeOrder}*\n" .
                   "📦 Proyek: *{$judulProyek}*\n" .
                   "⏱️ Jam Produksi Berjalan: *{$totalJam} Jam Kerja*\n" .
                   "📅 Target Selesai: " . \Carbon\Carbon::parse($order->target_selesai)->format('d/m/Y') . "\n\n" .
                   "Tim siswa di bawah supervisi instruktur sedang menyelesaikan tahapan produksi sesuai spesifikasi.\n" .
                   "Salam, Pokja TEFA SMKN 2 Indramayu.";
        }

        // Default: Konfirmasi Penerimaan Order Baru
        $biayaFormatted = 'Rp ' . number_format($order->biaya_proyek, 0, ',', '.');
        return "📋 *SURAT PERINTAH KERJA (SPK) ORDER TEFA DITERIMA*\n\n" .
               "Yth. Bpk/Ibu *{$namaPemesan}*.\n\n" .
               "Terima kasih telah mempercayakan pengerjaan proyek kepada Unit Teaching Factory (TEFA) SMKN 2 Indramayu:\n" .
               "📌 Kode Order: *{$kodeOrder}*\n" .
               "📦 Pekerjaan: *{$judulProyek}*\n" .
               "💰 Estimasi Nilai: {$biayaFormatted}\n" .
               "📅 Estimasi Selesai: " . \Carbon\Carbon::parse($order->target_selesai)->format('d/m/Y') . "\n" .
               "👨‍🏫 Instruktur Penanggung Jawab: {$instruktur}\n\n" .
               "Pekerjaan telah didistribusikan ke tim kerja siswa dan segera masuk antrean produksi.\n" .
               "Salam hormat,\nUnit Produksi TEFA SMKN 2 Indramayu.";
    }

    /**
     * Hasilkan tautan failover manual wa.me untuk guru/instruktur TEFA.
     */
    public static function generateWaMeLink(string $phone, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($message);
    }

    /**
     * Kirim pesan otomatis via gateway.
     */
    public static function sendAutomated(TefaOrder $order, string $stage = 'konfirmasi'): array
    {
        $pesan = self::generateOrderNotification($order, $stage);

        try {
            $driver = WaGatewayManager::driver();
            $result = $driver->sendMessage($order->nomor_wa_pemesan, $pesan);
            return [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'Pesan diproses',
            ];
        } catch (\Throwable $e) {
            Log::error("Gagal kirim WA TEFA Konsumen: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}