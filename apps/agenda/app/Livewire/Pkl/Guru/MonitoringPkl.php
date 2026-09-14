<?php

namespace App\Livewire\Pkl\Guru;

use App\Models\PklPenempatan;
use App\Models\PklPenilaian;
use App\Services\Pkl\PklAssessmentService;
use App\Services\Pkl\PklMagicLinkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MonitoringPkl extends Component
{
    public $filterTahun = '2025/2026';
    public $search = '';

    // Modal Kirim WA ke DUDI
    public $showWaModal = false;
    public $selectedPenempatanId;
    public $waMessage;
    public $waMeLink;
    public $magicLinkUrl;
    public $dudiPhone;
    public $dudiName;

    // Modal Input Nilai Sekolah (Sidang & Laporan)
    public $showNilaiModal = false;
    public $nilaiSidang = 80;
    public $nilaiLaporan = 85;
    public $catatanPenguji;
    public $currentPenilaian;

    public function mount()
    {
    }

    public function openWaModal($penempatanId, $context = 'jurnal')
    {
        $penempatan = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing'])->find($penempatanId);
        if (!$penempatan) return;

        $this->selectedPenempatanId = $penempatanId;
        $this->dudiName = $penempatan->dudi->nama_instansi ?? 'Mitra Industri';
        $this->dudiPhone = $penempatan->nomor_wa_dudi;
        $this->magicLinkUrl = PklMagicLinkService::getReviewUrl($penempatan);
        $this->waMessage = PklMagicLinkService::generateDudiNotificationMessage($penempatan, $context);
        $this->waMeLink = PklMagicLinkService::generateWaMeLink($this->dudiPhone, $this->waMessage);
        $this->showWaModal = true;
    }

    public function closeWaModal()
    {
        $this->showWaModal = false;
    }

    public function kirimWaOtomatis()
    {
        $penempatan = PklPenempatan::find($this->selectedPenempatanId);
        if ($penempatan) {
            $result = PklMagicLinkService::sendAutomatedNotification($penempatan);
            if ($result['success']) {
                session()->flash('success', 'Pesan WhatsApp otomatis berhasil dikirimkan ke Pembimbing DUDI via gateway.');
            } else {
                session()->flash('info', 'Gateway otomatis belum aktif atau offline. Silakan gunakan tombol failover [Buka WhatsApp] di bawah.');
            }
        }
    }

    public function openNilaiModal($penempatanId)
    {
        $penempatan = PklPenempatan::with(['penilaian', 'siswa'])->find($penempatanId);
        if (!$penempatan) return;

        $this->selectedPenempatanId = $penempatanId;
        $p = $penempatan->penilaian;
        if ($p) {
            $this->nilaiSidang = $p->nilai_sidang_sekolah ?: 80;
            $this->nilaiLaporan = $p->nilai_laporan_pkl ?: 85;
            $this->catatanPenguji = $p->catatan_penguji;
        } else {
            $this->nilaiSidang = 80;
            $this->nilaiLaporan = 85;
            $this->catatanPenguji = '';
        }

        $this->showNilaiModal = true;
    }

    public function closeNilaiModal()
    {
        $this->showNilaiModal = false;
    }

    public function simpanNilaiSekolah()
    {
        $penempatan = PklPenempatan::find($this->selectedPenempatanId);
        if (!$penempatan) return;

        $penilaian = PklPenilaian::firstOrNew(['penempatan_id' => $penempatan->id]);
        $penilaian->nilai_sidang_sekolah = (float) $this->nilaiSidang;
        $penilaian->nilai_laporan_pkl = (float) $this->nilaiLaporan;
        $penilaian->penguji_sekolah_id = Auth::id();
        $penilaian->catatan_penguji = $this->catatanPenguji;

        PklAssessmentService::saveAndRecalculate($penilaian);

        $this->showNilaiModal = false;
        session()->flash('success', 'Nilai Ujian Sidang & Laporan berhasil disimpan. Nilai Akhir (NA) telah dikalkulasi ulang.');
    }

    public function render()
    {
        $query = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing', 'penilaian', 'presensi', 'jurnalHarian']);

        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%');
            })->orWhereHas('dudi', function ($q) {
                $q->where('nama_instansi', 'like', '%' . $this->search . '%');
            });
        }

        $penempatanList = $query->latest()->get();

        return view('livewire.pkl.guru.monitoring-pkl', [
            'penempatanList' => $penempatanList,
        ])->layout('components.layouts.portal', ['title' => 'Monitoring Jurnal PKL Vokasi - SMKN 2 Indramayu']);
    }
}