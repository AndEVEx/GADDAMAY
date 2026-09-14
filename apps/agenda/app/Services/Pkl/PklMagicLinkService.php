<?php

namespace App\Services\Pkl;

use App\Models\PklPenempatan;
use App\Services\Wa\WaGatewayManager;
use Illuminate\Support\Facades\Log;

class PklMagicLinkService
{
    /**
     * Buat URL Magic Link untuk Pembimbing DUDI.
     */
    public static function getReviewUrl(PklPenempatan $penempatan): string
    {
        return url('/pkl/review-dudi/' . $penempatan->token_magic_link_dudi);
    }

    /**
     * Susun pesan resmi WhatsApp untuk Pembimbing Industri (DUDI).
     */
    public static function generateDudiNotificationMessage(PklPenempatan $penempatan, string $context = 'jurnal'): string
    {
        $namaDudi = $penempatan->dudi->nama_instansi ?? 'Mitra Industri';
        $namaSiswa = $penempatan->siswa->nama ?? 'Siswa PKL';
        $namaGuru = $penempatan->guruPembimbing->name ?? 'Guru Pembimbing';
        $link = self::getReviewUrl($penempatan);

        if ($context === 'penilaian') {
            return "Yth. Bpk/Ibu Pembimbing DUDI di *{$namaDudi}*.\n\n" .
                   "Kami dari Pokja PKL SMKN 2 Indramayu menyampaikan terima kasih atas bimbingan yang telah diberikan kepada siswa kami:\n" .
                   "👤 *{$namaSiswa}*\n" .
                   "👨‍🏫 Pembimbing Sekolah: {$namaGuru}\n\n" .
                   "Masa pelaksanaan PKL telah mendekati akhir periode. Mohon kesediaan Bapak/Ibu untuk mengisi *Lembar Penilaian Akhir PKL* melalui tautan praktis berikut (tanpa perlu login/password):\n" .
                   "👉 {$link}\n\n" .
                   "_Terima kasih atas kemitraan dan dedikasi Bapak/Ibu dalam membimbing generasi vokasi terampil SMKN 2 Indramayu._";
        }

        return "Yth. Bpk/Ibu Pembimbing DUDI di *{$namaDudi}*.\n\n" .
               "Siswa bimbingan PKL kami:\n" .
               "👤 *{$namaSiswa}*\n" .
               "telah memperbarui Presensi & Jurnal Aktivitas Harian di sistem GADDAMAY SMKN 2 Indramayu.\n\n" .
               "Mohon kesediaan Bapak/Ibu untuk memeriksa dan memberikan paraf digital melalui tautan langsung berikut (tanpa password):\n" .
               "👉 {$link}\n\n" .
               "Salam hormat,\n" .
               "{$namaGuru} (Pembimbing PKL SMKN 2 Indramayu)";
    }

    /**
     * Hasilkan tautan failover manual wa.me untuk guru sekolah ke pembimbing DUDI.
     */
    public static function generateWaMeLink(string $phoneNumber, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($message);
    }

    /**
     * Kirim pesan otomatis via Multi-Driver WhatsApp Gateway.
     */
    public static function sendAutomatedNotification(PklPenempatan $penempatan, string $context = 'jurnal'): array
    {
        $message = self::generateDudiNotificationMessage($penempatan, $context);
        $phone = $penempatan->nomor_wa_dudi;

        try {
            $driver = WaGatewayManager::driver();
            $result = $driver->sendMessage($phone, $message);
            return [
                'success' => $result['success'] ?? false,
                'driver' => get_class($driver),
                'message' => $result['message'] ?? 'Pesan diproses',
            ];
        } catch (\Throwable $e) {
            Log::error("Gagal kirim WA ke DUDI PKL: " . $e->getMessage());
            return [
                'success' => false,
                'driver' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}