<?php

namespace App\Livewire\Ujikom\Siswa;

use App\Models\Siswa;
use App\Models\UjikomBerkasAsesmen;
use App\Models\UjikomPendaftaran;
use App\Models\UjikomSkema;
use App\Services\Ujikom\UjikomRegistrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PendaftaranUjikomSiswa extends Component
{
    use WithFileUploads;

    public $siswaId;
    public $siswa;
    public $pendaftaranAktif;

    // Form Pendaftaran
    public $skema_id;
    public $tahun_ajaran = '2025/2026';

    // Form Upload Berkas
    public $jenis_berkas = 'apl_01_permohonan';
    public $nama_berkas;
    public $fileBerkas;

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

        $firstSkema = UjikomSkema::first();
        if (!$firstSkema) {
            $firstSkema = UjikomSkema::create([
                'kode_skema' => 'SKM-PPLG-2026',
                'nama_skema' => 'Pemrograman Web & Rekayasa Perangkat Bergerak (Level II)',
                'jurusan' => 'PPLG',
                'deskripsi' => 'Skema Sertifikasi Nasional LSP-P1 SMKN 2 Indramayu mencakup analisis sistem, perancangan UI/UX, basis data, dan coding API.',
                'jumlah_unit_kompetensi' => 5,
                'is_active' => true,
            ]);
        }
        $this->skema_id = $firstSkema->id;

        $this->loadPendaftaran();
    }

    public function loadPendaftaran()
    {
        if ($this->siswaId) {
            $this->pendaftaranAktif = UjikomPendaftaran::with(['skema', 'asesor', 'berkas'])
                ->where('siswa_id', $this->siswaId)
                ->latest()
                ->first();
        }
    }

    public function daftarUjikom()
    {
        $this->validate([
            'skema_id' => 'required',
        ]);

        $this->pendaftaranAktif = UjikomRegistrationService::registerStudent(
            $this->siswaId,
            $this->skema_id,
            $this->tahun_ajaran
        );

        $this->loadPendaftaran();
        session()->flash('success', "Pendaftaran berhasil! Nomor Peserta Asesi Anda: {$this->pendaftaranAktif->nomor_pendaftaran}.");
    }

    public function uploadBerkas()
    {
        $this->validate([
            'fileBerkas' => 'required|file|max:10240',
            'nama_berkas' => 'required|string',
        ]);

        if (!$this->pendaftaranAktif) return;

        $filePath = $this->fileBerkas->store('uploads/ujikom/berkas', 'public');

        UjikomBerkasAsesmen::create([
            'pendaftaran_id' => $this->pendaftaranAktif->id,
            'jenis_berkas' => $this->jenis_berkas,
            'nama_file' => $this->nama_berkas,
            'file_path' => $filePath,
            'is_valid' => true,
        ]);

        $this->reset(['nama_berkas', 'fileBerkas']);
        $this->loadPendaftaran();

        session()->flash('success', 'Berkas pra-asesmen berhasil diunggah.');
    }

    public function render()
    {
        $skemaList = UjikomSkema::where('is_active', true)->get();

        return view('livewire.ujikom.siswa.pendaftaran-ujikom-siswa', [
            'skemaList' => $skemaList,
        ])->layout('components.layouts.portal', ['title' => 'Pendaftaran Ujikom LSP-P1 - SMKN 2 Indramayu']);
    }
}