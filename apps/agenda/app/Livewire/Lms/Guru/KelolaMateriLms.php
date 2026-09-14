<?php

namespace App\Livewire\Lms\Guru;

use App\Models\LmsMateri;
use App\Models\LmsPenugasanSiswa;
use App\Models\LmsProyekLatihan;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use App\Services\Lms\LmsPersonalizationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class KelolaMateriLms extends Component
{
    use WithFileUploads;

    public $filterKategori = 'semua';
    public $search = '';

    // Form Tambah Materi
    public $showCreateModal = false;
    public $judul;
    public $kategori = 'ujikom_intensif'; // kbm_reguler / ujikom_intensif / lks_khusus
    public $bidang_keahlian = 'PPLG';
    public $mapel_id;
    public $tp_id;
    public $deskripsi;
    public $konten_materi;
    public $file_lampiran;
    public $link_video;

    // Modal Penugasan Diferensiasi
    public $showAssignModal = false;
    public $selectedMateriId;
    public $targetType = 'siswa'; // 'siswa' atau 'rombel'
    public $selectedSiswaId;
    public $selectedRombelId;
    public $tipeJalur = 'peserta_ujikom'; // reguler / pengayaan / remedial / peserta_ujikom / peserta_lks
    public $targetSelesai;

    public function mount()
    {
        $this->targetSelesai = Carbon::today()->addDays(7)->toDateString();
        $mapelFirst = MataPelajaran::first();
        $this->mapel_id = $mapelFirst?->id;

        $rombelFirst = Rombel::first();
        $this->selectedRombelId = $rombelFirst?->id;

        $siswaFirst = Siswa::first();
        $this->selectedSiswaId = $siswaFirst?->id;
    }

    public function simpanMateri()
    {
        $this->validate([
            'judul' => 'required|string|max:150',
            'kategori' => 'required|in:kbm_reguler,ujikom_intensif,lks_khusus',
            'konten_materi' => 'required|string',
        ]);

        $filePath = null;
        if ($this->file_lampiran) {
            $filePath = $this->file_lampiran->store('uploads/lms/modul', 'public');
        }

        $materi = LmsMateri::create([
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'bidang_keahlian' => $this->bidang_keahlian,
            'mapel_id' => $this->mapel_id,
            'tp_id' => $this->tp_id,
            'guru_id' => Auth::id() ?: User::first()->id,
            'deskripsi' => $this->deskripsi,
            'konten_materi' => $this->konten_materi,
            'file_lampiran' => $filePath,
            'link_video' => $this->link_video,
            'is_published' => true,
        ]);

        $this->showCreateModal = false;
        $this->reset(['judul', 'deskripsi', 'konten_materi', 'file_lampiran', 'link_video']);

        session()->flash('success', "Materi/Modul '{$materi->judul}' berhasil diterbitkan di LMS.");
    }

    public function openAssignModal($materiId)
    {
        $this->selectedMateriId = $materiId;
        $materi = LmsMateri::find($materiId);
        if ($materi) {
            if ($materi->kategori === 'ujikom_intensif') {
                $this->tipeJalur = 'peserta_ujikom';
            } elseif ($materi->kategori === 'lks_khusus') {
                $this->tipeJalur = 'peserta_lks';
            } else {
                $this->tipeJalur = 'reguler';
            }
        }
        $this->showAssignModal = true;
    }

    public function submitAssignment()
    {
        if (!$this->selectedMateriId) return;

        if ($this->targetType === 'siswa') {
            $this->validate(['selectedSiswaId' => 'required']);
            LmsPersonalizationService::assignToStudent(
                $this->selectedMateriId,
                $this->selectedSiswaId,
                $this->tipeJalur,
                $this->targetSelesai
            );
            session()->flash('success', 'Jalur pembelajaran personal berhasil ditugaskan khusus kepada siswa.');
        } else {
            $this->validate(['selectedRombelId' => 'required']);
            $count = LmsPersonalizationService::assignToRombel(
                $this->selectedMateriId,
                $this->selectedRombelId,
                $this->tipeJalur
            );
            session()->flash('success', "Materi berhasil ditugaskan ke seluruh {$count} siswa di rombel terpilih.");
        }

        $this->showAssignModal = false;
    }

    public function render()
    {
        $query = LmsMateri::with(['guru', 'penugasanSiswa.siswa', 'mataPelajaran', 'tujuanPembelajaran'])->latest();

        if ($this->filterKategori !== 'semua') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search) {
            $query->where('judul', 'like', '%' . $this->search . '%');
        }

        $materiList = $query->get();
        $mapelList = MataPelajaran::all();
        $tpList = TujuanPembelajaran::all();
        $rombelList = Rombel::all();
        $siswaList = Siswa::with('rombel')->orderBy('nama')->take(50)->get();

        return view('livewire.lms.guru.kelola-materi-lms', [
            'materiList' => $materiList,
            'mapelList' => $mapelList,
            'tpList' => $tpList,
            'rombelList' => $rombelList,
            'siswaList' => $siswaList,
        ])->layout('components.layouts.portal', ['title' => 'Kelola LMS & Modul Ujikom/LKS - SMKN 2 Indramayu']);
    }
}