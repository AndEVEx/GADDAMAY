<?php

namespace App\Livewire\Pkl\Admin;

use App\Models\PklDudi;
use App\Models\PklPenempatan;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class KelolaPenempatanPkl extends Component
{
    // Mode Form: 'penempatan' atau 'dudi'
    public $activeTab = 'penempatan';

    // Form DUDI Baru
    public $nama_instansi;
    public $bidang_usaha;
    public $alamat;
    public $kota = 'Indramayu';
    public $pembimbing_nama;
    public $pembimbing_kontak;
    public $latitude = -6.32650000; // Default Indramayu
    public $longitude = 108.32000000;
    public $radius_meter = 100;

    // Form Penempatan Baru
    public $siswa_id;
    public $dudi_id;
    public $guru_pembimbing_id;
    public $tahun_ajaran = '2025/2026';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $nama_pembimbing_dudi;
    public $nomor_wa_dudi;

    public function mount()
    {
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->addMonths(6)->toDateString();

        $dudiFirst = PklDudi::first();
        if ($dudiFirst) {
            $this->dudi_id = $dudiFirst->id;
            $this->nama_pembimbing_dudi = $dudiFirst->pembimbing_nama;
            $this->nomor_wa_dudi = $dudiFirst->pembimbing_kontak;
        }

        $guruFirst = User::whereIn('role', ['guru', 'admin'])->first();
        $this->guru_pembimbing_id = $guruFirst?->id;

        $siswaFirst = Siswa::first();
        $this->siswa_id = $siswaFirst?->id;
    }

    public function simpanDudi()
    {
        $this->validate([
            'nama_instansi' => 'required|string|max:150',
            'alamat' => 'required|string',
            'pembimbing_kontak' => 'required|string',
        ]);

        $dudi = PklDudi::create([
            'nama_instansi' => $this->nama_instansi,
            'bidang_usaha' => $this->bidang_usaha,
            'alamat' => $this->alamat,
            'kota' => $this->kota,
            'pembimbing_nama' => $this->pembimbing_nama,
            'pembimbing_kontak' => $this->pembimbing_kontak,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'radius_meter' => (int) $this->radius_meter,
        ]);

        $this->dudi_id = $dudi->id;
        $this->nama_pembimbing_dudi = $dudi->pembimbing_nama;
        $this->nomor_wa_dudi = $dudi->pembimbing_kontak;

        $this->reset(['nama_instansi', 'bidang_usaha', 'alamat', 'pembimbing_nama', 'pembimbing_kontak']);
        $this->activeTab = 'penempatan';

        session()->flash('success', 'Mitra DUDI baru berhasil didaftarkan.');
    }

    public function simpanPenempatan()
    {
        $this->validate([
            'siswa_id' => 'required',
            'dudi_id' => 'required',
            'guru_pembimbing_id' => 'required',
            'nomor_wa_dudi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        // Cek apakah siswa sudah punya penempatan aktif
        $exists = PklPenempatan::where('siswa_id', $this->siswa_id)
            ->where('status', 'aktif')
            ->exists();

        if ($exists) {
            session()->flash('error', 'Siswa tersebut sudah memiliki penempatan PKL aktif.');
            return;
        }

        PklPenempatan::create([
            'siswa_id' => $this->siswa_id,
            'dudi_id' => $this->dudi_id,
            'guru_pembimbing_id' => $this->guru_pembimbing_id,
            'tahun_ajaran' => $this->tahun_ajaran,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'nama_pembimbing_dudi' => $this->nama_pembimbing_dudi,
            'nomor_wa_dudi' => $this->nomor_wa_dudi,
            'token_magic_link_dudi' => Str::random(48),
            'status' => 'aktif',
        ]);

        session()->flash('success', 'Penempatan PKL siswa berhasil dibuat.');
    }

    public function render()
    {
        $dudiList = PklDudi::latest()->get();
        $siswaList = Siswa::with('rombel')->orderBy('nama')->take(50)->get();
        $guruList = User::whereIn('role', ['guru', 'admin'])->orderBy('name')->get();
        $penempatanList = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])->latest()->get();

        return view('livewire.pkl.admin.kelola-penempatan-pkl', [
            'dudiList' => $dudiList,
            'siswaList' => $siswaList,
            'guruList' => $guruList,
            'penempatanList' => $penempatanList,
        ])->layout('components.layouts.portal', ['title' => 'Kelola Penempatan DUDI PKL - SMKN 2 Indramayu']);
    }
}