<?php

namespace App\Livewire\Parenting;

use App\Models\Siswa;
use App\Services\Parenting\ParentingAuthService;
use Illuminate\Support\Str;
use Livewire\Component;

class LoginParenting extends Component
{
    public $activeMethod = 'nisn'; // 'nisn' atau 'otp'

    // Form NISN
    public $nisn;
    public $tanggalLahir;

    // Form OTP
    public $nomorWa;
    public $otpInput;
    public $otpSent = false;
    public $selectedSiswaOtpId;
    public $simulatedOtpCode;
    public $simulatedMagicLink;

    public function loginWithNisn()
    {
        $this->validate([
            'nisn' => 'required|string',
        ]);

        $siswa = ParentingAuthService::attemptNisnLogin($this->nisn, $this->tanggalLahir);

        if (!$siswa) {
            session()->flash('error', 'Data NISN siswa tidak ditemukan pada database sekolah. Silakan periksa kembali nomor NISN anak Anda.');
            return;
        }

        // Simpan sesi dan generate token akses
        $token = Str::random(40);
        session(['parenting_siswa_id' => $siswa->id]);

        return redirect()->route('parenting.dashboard', ['token' => $token]);
    }

    public function quickLogin($siswaId)
    {
        $siswa = Siswa::find($siswaId);
        if ($siswa) {
            $token = Str::random(40);
            session(['parenting_siswa_id' => $siswa->id]);
            return redirect()->route('parenting.dashboard', ['token' => $token]);
        }
    }

    public function requestOtp()
    {
        $this->validate([
            'nomorWa' => 'required|string|min:9',
            'nisn' => 'required|string',
        ]);

        $siswa = ParentingAuthService::attemptNisnLogin($this->nisn);
        if (!$siswa) {
            session()->flash('error', 'NISN tidak valid.');
            return;
        }

        $res = ParentingAuthService::requestOtpLogin($siswa, $this->nomorWa);
        $this->otpSent = true;
        $this->selectedSiswaOtpId = $siswa->id;
        $this->simulatedOtpCode = $res['otp'];
        $this->simulatedMagicLink = $res['magic_url'];

        session()->flash('info', 'Kode OTP dan tautan masuk telah dipersiapkan.');
    }

    public function verifyOtpSubmit()
    {
        $this->validate([
            'otpInput' => 'required|string|size:6',
        ]);

        $sesi = ParentingAuthService::verifyOtp($this->nomorWa, $this->otpInput);
        if ($sesi) {
            session(['parenting_siswa_id' => $sesi->siswa_id]);
            return redirect()->route('parenting.dashboard', ['token' => $sesi->magic_token]);
        }

        session()->flash('error', 'Kode OTP salah atau telah kadaluarsa.');
    }

    public function render()
    {
        // Ambil beberapa siswa untuk demo quick-switch
        $demoSiswa = Siswa::with('rombel')->take(4)->get();

        return view('livewire.parenting.login-parenting', [
            'demoSiswa' => $demoSiswa,
        ])->layout('components.layouts.portal', ['title' => 'Portal Monitoring Parenting Digital - SMKN 2 Indramayu']);
    }
}