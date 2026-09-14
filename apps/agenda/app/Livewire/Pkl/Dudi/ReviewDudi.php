<?php

namespace App\Livewire\Pkl\Dudi;

use App\Models\PklJurnalHarian;
use App\Models\PklPenempatan;
use App\Models\PklPenilaian;
use App\Services\Pkl\PklAssessmentService;
use Carbon\Carbon;
use Livewire\Component;

class ReviewDudi extends Component
{
    public $token;
    public $penempatan;
    public $activeTab = 'jurnal'; // jurnal / presensi / nilai

    // Input catatan bimbingan untuk jurnal tertentu
    public $selectedJurnalId;
    public $catatanDudi;

    // Form input lembar penilaian DUDI
    public $nilai_soft_integritas = 85;
    public $nilai_soft_etos_kerja = 85;
    public $nilai_soft_gotong_royong = 85;
    public $nilai_soft_kemandirian = 80;
    public $nilai_soft_disiplin = 85;
    public $nilai_hard_tp1 = 80;
    public $nilai_hard_tp2 = 85;
    public $nilai_hard_tp3 = 85;
    public $nilai_hard_tp4 = 80;
    public $catatanNilaiDudi;

    public function mount($token)
    {
        $this->token = $token;
        $this->penempatan = PklPenempatan::with(['siswa', 'dudi', 'guruPembimbing', 'penilaian'])
            ->where('token_magic_link_dudi', $token)
            ->first();

        if ($this->penempatan && $this->penempatan->penilaian) {
            $p = $this->penempatan->penilaian;
            $this->nilai_soft_integritas = $p->nilai_soft_integritas ?: 85;
            $this->nilai_soft_etos_kerja = $p->nilai_soft_etos_kerja ?: 85;
            $this->nilai_soft_gotong_royong = $p->nilai_soft_gotong_royong ?: 85;
            $this->nilai_soft_kemandirian = $p->nilai_soft_kemandirian ?: 80;
            $this->nilai_soft_disiplin = $p->nilai_soft_disiplin ?: 85;
            $this->nilai_hard_tp1 = $p->nilai_hard_tp1 ?: 80;
            $this->nilai_hard_tp2 = $p->nilai_hard_tp2 ?: 85;
            $this->nilai_hard_tp3 = $p->nilai_hard_tp3 ?: 85;
            $this->nilai_hard_tp4 = $p->nilai_hard_tp4 ?: 80;
            $this->catatanNilaiDudi = $p->catatan_dudi;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function setujuiJurnal($jurnalId)
    {
        $jurnal = PklJurnalHarian::where('id', $jurnalId)
            ->where('penempatan_id', $this->penempatan->id)
            ->first();

        if ($jurnal) {
            $jurnal->update([
                'paraf_dudi_status' => 'disetujui',
                'paraf_dudi_at' => Carbon::now(),
                'catatan_dudi' => $this->selectedJurnalId === $jurnalId ? $this->catatanDudi : $jurnal->catatan_dudi,
            ]);

            $this->reset(['selectedJurnalId', 'catatanDudi']);
            session()->flash('success', 'Jurnal berhasil disetujui & diparaf secara digital.');
        }
    }

    public function setujuiSemuaJurnal()
    {
        if ($this->penempatan) {
            PklJurnalHarian::where('penempatan_id', $this->penempatan->id)
                ->where('paraf_dudi_status', 'pending')
                ->update([
                    'paraf_dudi_status' => 'disetujui',
                    'paraf_dudi_at' => Carbon::now(),
                ]);

            session()->flash('success', 'Semua jurnal pending berhasil disetujui sekaligus.');
        }
    }

    public function simpanPenilaianDudi()
    {
        if (!$this->penempatan) return;

        $penilaian = PklPenilaian::firstOrNew(['penempatan_id' => $this->penempatan->id]);

        $penilaian->nilai_soft_integritas = (float) $this->nilai_soft_integritas;
        $penilaian->nilai_soft_etos_kerja = (float) $this->nilai_soft_etos_kerja;
        $penilaian->nilai_soft_gotong_royong = (float) $this->nilai_soft_gotong_royong;
        $penilaian->nilai_soft_kemandirian = (float) $this->nilai_soft_kemandirian;
        $penilaian->nilai_soft_disiplin = (float) $this->nilai_soft_disiplin;

        $penilaian->nilai_hard_tp1 = (float) $this->nilai_hard_tp1;
        $penilaian->nilai_hard_tp2 = (float) $this->nilai_hard_tp2;
        $penilaian->nilai_hard_tp3 = (float) $this->nilai_hard_tp3;
        $penilaian->nilai_hard_tp4 = (float) $this->nilai_hard_tp4;

        $penilaian->catatan_dudi = $this->catatanNilaiDudi;
        $penilaian->dinilai_dudi_at = Carbon::now();

        // Hitung ulang dan simpan
        PklAssessmentService::saveAndRecalculate($penilaian);

        session()->flash('success', 'Penilaian DUDI berhasil disimpan. Nilai DUDI terakumulasi otomatis ke dalam Nilai Akhir (NA).');
    }

    public function render()
    {
        $jurnalList = collect();
        $presensiList = collect();
        $summary = [];

        if ($this->penempatan) {
            $jurnalList = PklJurnalHarian::where('penempatan_id', $this->penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $presensiList = $this->penempatan->presensi()->orderBy('tanggal', 'desc')->get();
            $summary = PklAssessmentService::getAttendanceSummary($this->penempatan);
        }

        return view('livewire.pkl.dudi.review-dudi', [
            'jurnalList' => $jurnalList,
            'presensiList' => $presensiList,
            'summary' => $summary,
        ])->layout('components.layouts.portal', ['title' => 'Review & Paraf DUDI PKL - SMKN 2 Indramayu']);
    }
}