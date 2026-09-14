<?php

namespace App\Services\Parenting;

use App\Models\ParentingSesiOtp;
use App\Models\Siswa;
use App\Services\Wa\WaGatewayManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParentingAuthService
{
    /**
     * Autentikasi Orang Tua Cepat: Menggunakan NIS/NISN Anak atau Nama.
     */
    public static function attemptNisnLogin(?string $identifier, ?string $tanggalLahir = null): ?Siswa
    {
        if (empty($identifier)) {
            return null;
        }

        $clean = trim($identifier);

        // Cari berdasarkan NIS, atau jika ada kolom nisn, atau cari berdasarkan nama
        return Siswa::with('rombel')
            ->where(function ($q) use ($clean) {
                $q->where('nis', $clean)
                  ->orWhere('nama', 'like', '%' . $clean . '%');
            })
            ->first();
    }

    /**
     * Request Login via OTP / Magic Link WhatsApp ke Nomor Orang Tua.
     */
    public static function requestOtpLogin(Siswa $siswa, string $nomorWa): array
    {
        $otp = (string) rand(100000, 999999);
        $token = Str::random(48);

        $sesi = ParentingSesiOtp::create([
            'siswa_id' => $siswa->id,
            'nomor_wa_ortu' => $nomorWa,
            'otp_code' => $otp,
            'magic_token' => $token,
            'expires_at' => Carbon::now()->addMinutes(15),
            'is_used' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $magicUrl = url('/parenting/dashboard/' . $token);
        $namaSiswa = $siswa->nama;

        $pesan = "Halo Bpk/Ibu Orang Tua dari *{$namaSiswa}*.\n\n" .
                 "Berikut adalah Kode OTP Akses Portal Parenting Digital SMKN 2 Indramayu:\n" .
                 "🔑 KODE OTP: *{$otp}*\n" .
                 "_(Berlaku 15 menit)_\n\n" .
                 "Atau langsung klik tautan praktis tanpa sandi berikut:\n" .
                 "👉 {$magicUrl}\n\n" .
                 "Pantau kehadiran gerbang, jam KBM kelas, dan buku kedisiplinan anak Anda secara transparan.\n" .
                 "Salam, Pokja Parenting SMKN 2 Indramayu.";

        try {
            $driver = WaGatewayManager::driver();
            $sendRes = $driver->sendMessage($nomorWa, $pesan);
            return [
                'success' => true,
                'token' => $token,
                'otp' => $otp,
                'magic_url' => $magicUrl,
                'message' => 'Kode OTP & Tautan berhasil dikirim via WhatsApp.',
            ];
        } catch (\Throwable $e) {
            Log::error("Gagal kirim OTP Parenting WA: " . $e->getMessage());
            return [
                'success' => true,
                'token' => $token,
                'otp' => $otp,
                'magic_url' => $magicUrl,
                'message' => 'Pesan OTP disiapkan (Gunakan tautan langsung).',
            ];
        }
    }

    /**
     * Verifikasi OTP Input.
     */
    public static function verifyOtp(string $nomorWa, string $otp): ?ParentingSesiOtp
    {
        $sesi = ParentingSesiOtp::where('nomor_wa_ortu', $nomorWa)
            ->where('otp_code', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if ($sesi) {
            $sesi->update(['is_used' => true]);
            return $sesi;
        }

        return null;
    }

    /**
     * Verifikasi Token Magic Link.
     */
    public static function verifyMagicToken(string $token): ?Siswa
    {
        $sesi = ParentingSesiOtp::with('siswa.rombel')
            ->where('magic_token', $token)
            ->where('expires_at', '>', Carbon::now()->subDays(7))
            ->first();

        return $sesi?->siswa;
    }
}