<?php

namespace App\Livewire\Pkl\Siswa;

use App\Models\PklJurnalHarian;
use App\Models\PklPenempatan;
use App\Models\PklPresensi;
use App\Models\Siswa;
use App\Services\Pkl\PklMagicLinkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class JurnalPkl extends Component
{
    use WithFileUploads;

    public $penempatan;
    public $siswaId;

    // Form inputs
    public $tanggal;
    public $jamMulai = '08:00';
    public $jamSelesai = '16:00';
    public $ringkasanPekerjaan;
    public $alatDanBahan;
    public $elemenCp;
    public $fotoDokumentasi;

    public function mount()
    {
        $this->tanggal = Carbon::today()->toDateString();
        $this->elemenCp = PklJurnalHarian::ELEMEN_CP[0] ?? '';

        $user = Auth::user();
        if ($user && $user->role === 'siswa') {
            $siswa = Siswa::where('email', $user->email)->orWhere('nisn', $user->username)->first();
            $this->siswaId = $siswa?->id;
        }

        if (!$this->siswaId) {
            $this->penempatan = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])->latest()->first();
            $this->siswaId = $this->penempatan?->siswa_id;
        } else {
            $this->penempatan = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])
                ->where('siswa_id', $this->siswaId)
                ->where('status', 'aktif')
                ->latest()
                ->first();
        }
    }

    public function simpanJurnal()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'ringkasanPekerjaan' => 'required|string|min:15',
            'elemenCp' => 'required|string',
        ]);

        if (!$this->penempatan) {
            session()->flash('error', 'Penempatan PKL tidak ditemukan.');
            return;
        }

        $fotoPath = null;
        if ($this->fotoDokumentasi) {
            $fotoPath = $this->fotoDokumentasi->store('uploads/pkl/jurnal', 'public');
        }

        // Cari presensi pada tanggal tersebut jika ada
        $presensi = PklPresensi::where('penempatan_id', $this->penempatan->id)
            ->where('tanggal', $this->tanggal)
            ->first();

        PklJurnalHarian::create([
            'penempatan_id' => $this->penempatan->id,
            'presensi_id' => $presensi?->id,
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jamMulai,
            'jam_selesai' => $this->jamSelesai,
            'ringkasan_pekerjaan' => $this->ringkasanPekerjaan,
            'alat_dan_bahan' => $this->alatDanBahan,
            'elemen_cp' => $this->elemenCp,
            'foto_dokumentasi' => $fotoPath,
            'paraf_dudi_status' => 'pending',
            'paraf_guru_status' => 'pending',
        ]);

        $this->reset(['ringkasanPekerjaan', 'alatDanBahan', 'fotoDokumentasi']);
        $this->tanggal = Carbon::today()->toDateString();

        // Notifikasi pengiriman otomatis via WhatsApp jika gateway aktif
        PklMagicLinkService::sendAutomatedNotification($this->penempatan, 'jurnal');

        session()->flash('success', 'Jurnal aktivitas harian berhasil disimpan. Tautan paraf telah dikirimkan ke Pembimbing DUDI via WhatsApp.');
    }

    public function render()
    {
        $daftarJurnal = collect();
        if ($this->penempatan) {
            $daftarJurnal = PklJurnalHarian::where('penempatan_id', $this->penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return view('livewire.pkl.siswa.jurnal-pkl', [
            'daftarJurnal' => $daftarJurnal,
            'elemenCpList' => PklJurnalHarian::ELEMEN_CP,
        ])->layout('components.layouts.portal', ['title' => 'Jurnal PKL Harian - GADDAMAY']);
    }
}