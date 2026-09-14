<?php

namespace App\Livewire\Pkl\Siswa;

use App\Models\PklPenempatan;
use App\Models\PklPresensi;
use App\Models\Siswa;
use App\Services\Pkl\PklGeoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PresensiPkl extends Component
{
    use WithFileUploads;

    public $penempatan;
    public $siswaId;
    public $todayPresensi;

    // Data GPS Geolocation
    public $latitude;
    public $longitude;
    public $jarakMeter = null;
    public $isInRadius = false;
    public $gpsLoaded = false;
    public $gpsError = null;

    // Form input
    public $fotoSelfie;
    public $keterangan;
    public $statusKehadiran = 'hadir'; // hadir / izin / sakit

    public function mount()
    {
        // Cari penempatan aktif untuk user siswa atau fallback siswa pertama
        $user = Auth::user();
        if ($user && $user->role === 'siswa') {
            $siswa = Siswa::where('email', $user->email)->orWhere('nisn', $user->username)->first();
            $this->siswaId = $siswa?->id;
        }

        if (!$this->siswaId) {
            $penempatanAktif = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])->latest()->first();
            $this->penempatan = $penempatanAktif;
            $this->siswaId = $penempatanAktif?->siswa_id;
        } else {
            $this->penempatan = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])
                ->where('siswa_id', $this->siswaId)
                ->where('status', 'aktif')
                ->latest()
                ->first();
        }

        $this->loadTodayPresensi();
    }

    public function loadTodayPresensi()
    {
        if ($this->penempatan) {
            $this->todayPresensi = PklPresensi::where('penempatan_id', $this->penempatan->id)
                ->where('tanggal', Carbon::today()->toDateString())
                ->first();
        }
    }

    public function updateCoordinates($lat, $lng)
    {
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;
        $this->gpsLoaded = true;
        $this->gpsError = null;

        if ($this->penempatan && $this->penempatan->dudi && $this->penempatan->dudi->latitude && $this->penempatan->dudi->longitude) {
            $res = PklGeoService::isWithinRadius(
                $this->latitude,
                $this->longitude,
                (float) $this->penempatan->dudi->latitude,
                (float) $this->penempatan->dudi->longitude,
                $this->penempatan->dudi->radius_meter ?? 100
            );
            $this->jarakMeter = $res['distance_meter'];
            $this->isInRadius = $res['is_valid'];
        } else {
            // Jika DUDI belum disetel koordinatnya, default izinkan
            $this->isInRadius = true;
            $this->jarakMeter = 0;
        }
    }

    public function setGpsError($errorMsg)
    {
        $this->gpsError = $errorMsg;
        $this->gpsLoaded = false;
    }

    public function presensiMasuk()
    {
        if (!$this->penempatan) {
            session()->flash('error', 'Data penempatan PKL tidak ditemukan.');
            return;
        }

        $fotoPath = null;
        if ($this->fotoSelfie) {
            $fotoPath = $this->fotoSelfie->store('uploads/pkl/presensi', 'public');
        }

        $presensi = PklPresensi::firstOrNew([
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => Carbon::today()->toDateString(),
        ]);

        $presensi->jam_masuk = Carbon::now()->toTimeString();
        $presensi->lat_masuk = $this->latitude;
        $presensi->long_masuk = $this->longitude;
        $presensi->jarak_masuk_meter = $this->jarakMeter;
        $presensi->is_in_radius = $this->isInRadius;
        $presensi->status_kehadiran = $this->statusKehadiran;
        $presensi->keterangan = $this->keterangan;

        if ($fotoPath) {
            $presensi->foto_masuk = $fotoPath;
        }

        $presensi->save();
        $this->fotoSelfie = null;
        $this->loadTodayPresensi();

        session()->flash('success', 'Presensi MASUK berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WIB.');
    }

    public function presensiPulang()
    {
        if (!$this->todayPresensi) {
            session()->flash('error', 'Anda belum melakukan presensi masuk hari ini.');
            return;
        }

        $fotoPath = null;
        if ($this->fotoSelfie) {
            $fotoPath = $this->fotoSelfie->store('uploads/pkl/presensi', 'public');
        }

        $this->todayPresensi->jam_pulang = Carbon::now()->toTimeString();
        $this->todayPresensi->lat_pulang = $this->latitude;
        $this->todayPresensi->long_pulang = $this->longitude;
        $this->todayPresensi->jarak_pulang_meter = $this->jarakMeter;

        if ($fotoPath) {
            $this->todayPresensi->foto_pulang = $fotoPath;
        }

        $this->todayPresensi->save();
        $this->fotoSelfie = null;
        $this->loadTodayPresensi();

        session()->flash('success', 'Presensi PULANG berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WIB.');
    }

    public function render()
    {
        $riwayatPresensi = collect();
        if ($this->penempatan) {
            $riwayatPresensi = PklPresensi::where('penempatan_id', $this->penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->take(10)
                ->get();
        }

        return view('livewire.pkl.siswa.presensi-pkl', [
            'riwayatPresensi' => $riwayatPresensi,
        ])->layout('components.layouts.portal', ['title' => 'Presensi PKL Geolocation - GADDAMAY']);
    }
}