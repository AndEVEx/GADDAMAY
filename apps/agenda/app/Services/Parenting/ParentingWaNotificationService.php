<?php

namespace App\Services\Parenting;

use App\Models\ParentingCatatanDisiplin;
use App\Models\Siswa;
use App\Services\Wa\WaGatewayManager;
use Illuminate\Support\Facades\Log;

class ParentingWaNotificationService
{
    /**
     * Buat format pesan notifikasi catatan kedisiplinan / prestasi untuk orang tua.
     */
    public static function generateDisciplineMessage(ParentingCatatanDisiplin $catatan): string
    {
        $namaSiswa = $catatan->siswa->nama ?? 'Siswa';
        $kelas = $catatan->siswa->rombel->nama_rombel ?? '-';
        $petugas = $catatan->petugas->name ?? 'Guru BK / Wali Kelas';
        $kategori = strtoupper($catatan->kategori);
        $portalUrl = url('/parenting');

        if (in_array($catatan->kategori, ['prestasi', 'apresiasi'])) {
            return "🎉 *APRESIASI PRESTASI SISWA SMKN 2 INDRAMAYU*\n\n" .
                   "Yth. Bpk/Ibu Orang Tua dari *{$namaSiswa}* ({$kelas}).\n\n" .
                   "Kami dengan bangga menginformasikan bahwa ananda telah mencatatkan prestasi/sikap positif:\n" .
                   "🏆 *{$catatan->jenis_tindakan}*\n" .
                   "📝 Keterangan: {$catatan->deskripsi}\n" .
                   "⭐ Tambahan Poin Disiplin: +{$catatan->poin} Poin\n" .
                   "👨‍🏫 Dicatat oleh: {$petugas}\n\n" .
                   "Pantau rekam jejak ananda melalui Buku Parenting Digital:\n" .
                   "👉 {$portalUrl}\n\n" .
                   "_Terima kasih atas bimbingan dan dukungan Bpk/Ibu di rumah._";
        }

        return "⚠️ *PEMBERITAHUAN KEDISIPLINAN SISWA SMKN 2 INDRAMAYU*\n\n" .
               "Yth. Bpk/Ibu Orang Tua dari *{$namaSiswa}* ({$kelas}).\n\n" .
               "Melalui pesan ini kami menginformasikan catatan pembinaan ananda di sekolah hari ini:\n" .
               "📌 Tindakan: *{$catatan->jenis_tindakan}*\n" .
               "📝 Catatan Pembinaan: {$catatan->deskripsi}\n" .
               "🔻 Poin Pelanggaran: -{$catatan->poin} Poin\n" .
               "👨‍🏫 Guru Pembimbing: {$petugas}\n\n" .
               "Bpk/Ibu dapat memantau detail catatan serta riwayat presensi melalui portal berikut:\n" .
               "👉 {$portalUrl}\n\n" .
               "Jika membutuhkan konsultasi lebih lanjut, silakan hubungi Wali Kelas atau Guru BK.\n" .
               "Salam hormat,\nTim Kesiswaan & BK SMKN 2 Indramayu.";
    }

    /**
     * Hasilkan link failover manual wa.me.
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
     * Kirim otomatis via Multi-Driver Gateway.
     */
    public static function sendAutomated(ParentingCatatanDisiplin $catatan, string $nomorWaOrtu): array
    {
        $pesan = self::generateDisciplineMessage($catatan);

        try {
            $driver = WaGatewayManager::driver();
            $result = $driver->sendMessage($nomorWaOrtu, $pesan);
            return [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'Pesan diproses',
            ];
        } catch (\Throwable $e) {
            Log::error("Gagal kirim WA Catatan Disiplin Ortu: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}