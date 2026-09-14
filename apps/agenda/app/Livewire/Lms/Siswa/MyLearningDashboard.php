<?php

namespace App\Livewire\Lms\Siswa;

use App\Models\LmsPenugasanSiswa;
use App\Models\LmsProyekLatihan;
use App\Models\Siswa;
use App\Services\Lms\LmsPersonalizationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MyLearningDashboard extends Component
{
    use WithFileUploads;

    public $siswaId;
    public $activeTab = 'ujikom'; // ujikom / kbm
    public $selectedPenugasan;

    // Form Pengumpulan Proyek Latihan
    public $showSubmitModal = false;
    public $penugasanIdToSubmit;
    public $linkRepoGit;
    public $fileProyek;
    public $deskripsiPekerjaan;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->role === 'siswa') {
            $siswa = Siswa::where('email', $user->email)->orWhere('nis', $user->username)->first();
            $this->siswaId = $siswa?->id;
        }

        if (!$this->siswaId) {
            $firstAssign = LmsPenugasanSiswa::first();
            $this->siswaId = $firstAssign?->siswa_id ?: Siswa::first()?->id;
        }
    }

    public function switchStudent($id)
    {
        $this->siswaId = $id;
        $this->selectedPenugasan = null;
    }

    public function selectMateri($penugasanId)
    {
        $this->selectedPenugasan = LmsPenugasanSiswa::with(['materi.guru', 'proyekLatihan'])->find($penugasanId);
    }

    public function tandaiSelesai($penugasanId)
    {
        $penugasan = LmsPenugasanSiswa::find($penugasanId);
        if ($penugasan) {
            $penugasan->update([
                'status_progres' => 'selesai',
                'selesai_at' => Carbon::now(),
            ]);
            $this->selectMateri($penugasanId);
            session()->flash('success', 'Modul berhasil ditandai selesai.');
        }
    }

    public function openSubmitModal($penugasanId)
    {
        $this->penugasanIdToSubmit = $penugasanId;
        $this->showSubmitModal = true;
    }

    public function submitProyekLatihan()
    {
        $this->validate([
            'deskripsiPekerjaan' => 'required|string|min:10',
        ]);

        $penugasan = LmsPenugasanSiswa::find($this->penugasanIdToSubmit);
        if (!$penugasan) return;

        $filePath = null;
        if ($this->fileProyek) {
            $filePath = $this->fileProyek->store('uploads/lms/proyek', 'public');
        }

        LmsProyekLatihan::updateOrCreate([
            'penugasan_id' => $penugasan->id,
        ], [
            'siswa_id' => $penugasan->siswa_id,
            'materi_id' => $penugasan->materi_id,
            'link_repository_git' => $this->linkRepoGit,
            'file_proyek' => $filePath,
            'deskripsi_pekerjaan' => $this->deskripsiPekerjaan,
            'status_review' => 'menunggu',
        ]);

        $penugasan->update(['status_progres' => 'sedang_belajar']);

        $this->showSubmitModal = false;
        $this->reset(['linkRepoGit', 'fileProyek', 'deskripsiPekerjaan']);
        $this->selectMateri($penugasan->id);

        session()->flash('success', 'Hasil proyek latihan berhasil dikumpulkan untuk direview oleh Guru Pembimbing.');
    }

    public function render()
    {
        $summary = $this->siswaId ? LmsPersonalizationService::getStudentLearningSummary($this->siswaId) : [];
        $demoSiswa = Siswa::take(4)->get();

        return view('livewire.lms.siswa.my-learning-dashboard', [
            'summary' => $summary,
            'demoSiswa' => $demoSiswa,
        ])->layout('components.layouts.portal', ['title' => 'Portal Belajar Vokasi Siswa - SMKN 2 Indramayu']);
    }
}