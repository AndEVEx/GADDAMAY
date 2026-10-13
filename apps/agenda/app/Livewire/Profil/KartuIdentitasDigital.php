<?php

namespace App\Livewire\Profil;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kartu Identitas Digital (2 Muka QR) - SMKN 2 Indramayu')]
class KartuIdentitasDigital extends Component
{
    public $isStudentCard = true;
    public $selectedSiswaId;
    public $siswa;
    public $user;
    public $nomorInduk;
    public $namaLengkap;
    public $jabatanAtauKelas;
    public $ekskulAktif = '';
    public $qrCodeValue;
    public $viewMode = 'side-by-side'; // side-by-side or 3d-flip

    public function mount()
    {
        $this->user = Auth::user();
        $reqSiswaId = request()->query('siswa_id');

        if ($this->user->role === 'siswa' || $this->user->role === 'ketua_kelas' || $reqSiswaId) {
            $this->isStudentCard = true;
            if ($reqSiswaId) {
                $this->siswa = Siswa::with('rombel')->find($reqSiswaId);
            } else {
                $this->siswa = Siswa::where('email', $this->user->email)
                    ->orWhere('nis', $this->user->username ?? '')
                    ->orWhere('rombel_id', $this->user->rombel_id)
                    ->first() ?: Siswa::with('rombel')->first();
            }
            $this->selectedSiswaId = $this->siswa?->id;
            $this->setupStudentCardData();
        } else {
            // Guru atau Pegawai
            $this->isStudentCard = false;
            $this->setupEmployeeCardData();
        }
    }

    public function toggleCardType($type)
    {
        if ($type === 'siswa') {
            $this->isStudentCard = true;
            if (!$this->siswa) {
                $this->siswa = Siswa::with('rombel')->first();
                $this->selectedSiswaId = $this->siswa?->id;
            }
            $this->setupStudentCardData();
        } else {
            $this->isStudentCard = false;
            $this->setupEmployeeCardData();
        }
    }

    public function switchSiswa($id)
    {
        $this->selectedSiswaId = $id;
        $this->siswa = Siswa::with('rombel')->find($id);
        $this->isStudentCard = true;
        $this->setupStudentCardData();
        $this->dispatch('qrCodeRegenerate', ['code' => $this->qrCodeValue]);
    }

    protected function setupStudentCardData()
    {
        $this->nomorInduk = $this->siswa->nis ?? '001001';
        $this->namaLengkap = $this->siswa->nama ?? 'Siswa SMKN 2';
        $this->jabatanAtauKelas = $this->siswa->rombel->nama_rombel ?? 'X Vokasi PPLG';
        $this->ekskulAktif = $this->siswa ? $this->siswa->daftar_ekskul_aktif : 'Paskibraka Korps Taruna';
        // Encoding QR Code: nilai NIS siswa
        $this->qrCodeValue = (string)$this->nomorInduk;
    }

    protected function setupEmployeeCardData()
    {
        $this->nomorInduk = '19850314 201001 1 008';
        $this->namaLengkap = $this->user->name ?? 'Guru SMKN 2';
        $roleLabel = match ($this->user->role) {
            'admin' => 'Administrator Sistem',
            'kepsek' => 'Kepala Sekolah',
            'waka' => 'Wakil Kepala Sekolah',
            'guru' => 'Guru Mata Pelajaran',
            'guru_piket' => 'Guru Piket Sekolah',
            'ketua_mgmp' => 'Ketua MGMP',
            default => 'Tenaga Pendidik',
        };
        $this->jabatanAtauKelas = $roleLabel;
        $this->ekskulAktif = 'Pembina / Pembimbing Ketarunaan';
        // Encoding QR Code: nilai NIP pegawai
        $this->qrCodeValue = str_replace(' ', '', $this->nomorInduk);
    }

    public function render()
    {
        $siswaList = [];
        if (in_array($this->user->role, ['admin', 'guru', 'waka', 'kepsek', 'guru_piket'])) {
            $siswaList = Siswa::with('rombel')->orderBy('nama')->take(50)->get();
        }

        return view('livewire.profil.kartu-identitas-digital', [
            'siswaList' => $siswaList,
        ]);
    }
}
