<?php

namespace App\Livewire\Lms\Tka;

use App\Models\LmsTkaHasilSiswa;
use App\Models\LmsTkaPaket;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Simulasi Latihan Soal TKA - LMS SMKN 2 Indramayu')]
class SimulasiTkaSiswa extends Component
{
    public $siswaId;
    public $siswa;
    public $activePaket = null;
    public $mode = 'daftar'; // daftar / ujian / hasil

    // Sesi Ujian
    public $currentSoalIndex = 0;
    public $jawabanSiswa = []; // [soalId => 'A']
    public $waktuMulai;
    public $sisaDetik = 0;
    public $hasilAkhir = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->role === 'siswa') {
            $this->siswa = Siswa::where('email', $user->email)->orWhere('nis', $user->username)->first();
            $this->siswaId = $this->siswa?->id;
        }

        if (!$this->siswaId) {
            $this->siswa = Siswa::with('rombel')->first();
            $this->siswaId = $this->siswa?->id;
        }
    }

    public function mulaiUjian($paketId)
    {
        $this->activePaket = LmsTkaPaket::with('soal')->findOrFail($paketId);
        if ($this->activePaket->soal->isEmpty()) {
            session()->flash('error', 'Paket soal ini belum memiliki butir soal.');
            return;
        }

        $this->currentSoalIndex = 0;
        $this->jawabanSiswa = [];
        $this->waktuMulai = Carbon::now();
        $this->sisaDetik = $this->activePaket->durasi_menit * 60;
        $this->mode = 'ujian';
    }

    public function pilihJawaban($soalId, $pilihan)
    {
        $this->jawabanSiswa[$soalId] = $pilihan;
    }

    public function nextSoal()
    {
        if ($this->currentSoalIndex < $this->activePaket->soal->count() - 1) {
            $this->currentSoalIndex++;
        }
    }

    public function prevSoal()
    {
        if ($this->currentSoalIndex > 0) {
            $this->currentSoalIndex--;
        }
    }

    public function jumpSoal($index)
    {
        $this->currentSoalIndex = $index;
    }

    public function selesaikanUjian()
    {
        if (!$this->activePaket) return;

        $totalSoal = $this->activePaket->soal->count();
        $benar = 0;
        $salah = 0;

        foreach ($this->activePaket->soal as $soal) {
            $jawaban = $this->jawabanSiswa[$soal->id] ?? null;
            if ($jawaban && $jawaban === $soal->kunci_jawaban) {
                $benar++;
            } else {
                $salah++;
            }
        }

        $nilai = $totalSoal > 0 ? round(($benar / $totalSoal) * 100, 1) : 0;
        $durasi = Carbon::now()->diffInSeconds($this->waktuMulai);

        $this->hasilAkhir = LmsTkaHasilSiswa::create([
            'paket_id' => $this->activePaket->id,
            'siswa_id' => $this->siswaId,
            'waktu_mulai' => $this->waktuMulai,
            'waktu_selesai' => Carbon::now(),
            'durasi_detik' => $durasi,
            'jumlah_benar' => $benar,
            'jumlah_salah' => $salah,
            'nilai_skor' => $nilai,
            'lembar_jawaban' => $this->jawabanSiswa,
            'status' => 'selesai',
        ]);

        $this->mode = 'hasil';
    }

    public function kembaliKeDaftar()
    {
        $this->mode = 'daftar';
        $this->activePaket = null;
        $this->hasilAkhir = null;
    }

    public function render()
    {
        $paketList = LmsTkaPaket::with('guruPembuat')
            ->withCount('soal')
            ->where('is_active', true)
            ->get();

        $riwayatHasil = LmsTkaHasilSiswa::with('paket')
            ->where('siswa_id', $this->siswaId)
            ->latest()
            ->get();

        return view('livewire.lms.tka.simulasi-tka-siswa', [
            'paketList' => $paketList,
            'riwayatHasil' => $riwayatHasil,
        ]);
    }
}
