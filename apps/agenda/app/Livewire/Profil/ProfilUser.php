<?php

namespace App\Livewire\Profil;

use App\Models\LmsTesFisikSiswa;
use App\Models\LmsTkaHasilSiswa;
use App\Models\PerpusRiwayatBaca;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Profil Komprehensif & Analisis Statistik - SMKN 2 Indramayu')]
class ProfilUser extends Component
{
    public $activeTab = 'hexagonal'; // hexagonal, fisik, numerasi_literasi, biodata
    public $selectedSiswaId;
    public $siswa;
    public $user;
    public $isViewingStudent = false;

    public function mount()
    {
        $this->user = Auth::user();

        // Jika user adalah siswa atau ketua_kelas
        if ($this->user->role === 'siswa' || $this->user->role === 'ketua_kelas') {
            $this->siswa = Siswa::where('email', $this->user->email)
                ->orWhere('nis', $this->user->username ?? '')
                ->orWhere('rombel_id', $this->user->rombel_id)
                ->first();

            if (!$this->siswa && $this->user->rombel_id) {
                $this->siswa = Siswa::where('rombel_id', $this->user->rombel_id)->first();
            }

            if (!$this->siswa) {
                $this->siswa = Siswa::with('rombel')->first();
            }

            $this->selectedSiswaId = $this->siswa?->id;
            $this->isViewingStudent = true;
        } else {
            // Jika guru / admin / waka / kepsek
            // Cek apakah ada query parameter siswa_id
            $reqSiswaId = request()->query('siswa_id');
            if ($reqSiswaId) {
                $this->siswa = Siswa::with('rombel')->find($reqSiswaId);
                $this->selectedSiswaId = $this->siswa?->id;
                $this->isViewingStudent = true;
            } else {
                // Default: jika guru ingin melihat siswa pertamanya atau profil pegawai
                $this->selectedSiswaId = Siswa::first()?->id;
                $this->siswa = Siswa::with('rombel')->find($this->selectedSiswaId);
                // Boleh switch antara profil pegawai dan siswa
                $this->isViewingStudent = request()->boolean('mode_siswa', false);
            }
        }
    }

    public function switchSiswa($id)
    {
        $this->selectedSiswaId = $id;
        $this->siswa = Siswa::with('rombel')->find($id);
        $this->isViewingStudent = true;
        $this->dispatch('hexagonalDataUpdated', $this->getHexagonalStats());
    }

    public function setMode($modeSiswa)
    {
        $this->isViewingStudent = (bool)$modeSiswa;
        if ($this->isViewingStudent && !$this->siswa) {
            $this->siswa = Siswa::with('rombel')->find($this->selectedSiswaId) ?: Siswa::with('rombel')->first();
        }
    }

    public function getHexagonalStats()
    {
        if (!$this->siswa) {
            return [
                'fisik' => 85,
                'numerasi' => 82,
                'literasi' => 88,
                'disiplin' => 92,
                'vokasi' => 86,
                'karakter' => 90,
            ];
        }

        // 1. Fisik
        $tesFisik = LmsTesFisikSiswa::where('siswa_id', $this->siswa->id)->latest('tanggal_tes')->first();
        $skorFisik = $tesFisik ? (int)$tesFisik->skor_kebugaran : 85;

        // 2. Numerasi
        $tka = LmsTkaHasilSiswa::where('siswa_id', $this->siswa->id)->avg('nilai_skor');
        $skorNumerasi = $tka ? (int)round($tka) : 82;

        // 3. Literasi
        $bacaPerpusCount = PerpusRiwayatBaca::where('siswa_id', $this->siswa->id)->count();
        $skorLiterasi = min(100, 75 + ($bacaPerpusCount * 5));

        // 4. Disiplin & Presensi
        $totalHadir = $this->siswa->kehadiranMurid()->where('status', 'H')->count();
        $totalSesi = max(1, $this->siswa->kehadiranMurid()->count());
        $persenHadir = (int)round(($totalHadir / $totalSesi) * 100);
        $skorDisiplin = $totalSesi > 5 ? $persenHadir : 92;

        // 5. Vokasi
        $skorVokasi = 86;

        // 6. Karakter
        $skorKarakter = 90;

        return [
            'fisik' => max(40, min(100, $skorFisik)),
            'numerasi' => max(40, min(100, $skorNumerasi)),
            'literasi' => max(40, min(100, $skorLiterasi)),
            'disiplin' => max(40, min(100, $skorDisiplin)),
            'vokasi' => max(40, min(100, $skorVokasi)),
            'karakter' => max(40, min(100, $skorKarakter)),
        ];
    }

    public function render()
    {
        $allSiswaList = [];
        if (in_array($this->user->role, ['admin', 'guru', 'waka', 'kepsek', 'guru_piket', 'ketua_mgmp'])) {
            $allSiswaList = Siswa::with('rombel')->orderBy('nama')->take(50)->get();
        }

        $hexagonal = $this->getHexagonalStats();

        // Data Fisik Lengkap
        $tesFisikTerakhir = null;
        if ($this->siswa) {
            $tesFisikTerakhir = LmsTesFisikSiswa::where('siswa_id', $this->siswa->id)->latest('tanggal_tes')->first();
        }

        // Data TKA Numerasi
        $tkaHasilList = [];
        if ($this->siswa) {
            $tkaHasilList = LmsTkaHasilSiswa::with('paket')->where('siswa_id', $this->siswa->id)->get();
        }

        // Data Perpus Literasi
        $riwayatBacaList = [];
        if ($this->siswa) {
            $riwayatBacaList = PerpusRiwayatBaca::with('buku.kategori')->where('siswa_id', $this->siswa->id)->get();
        }

        return view('livewire.profil.profil-user', [
            'hexagonal' => $hexagonal,
            'tesFisikTerakhir' => $tesFisikTerakhir,
            'tkaHasilList' => $tkaHasilList,
            'riwayatBacaList' => $riwayatBacaList,
            'allSiswaList' => $allSiswaList,
        ]);
    }
}
