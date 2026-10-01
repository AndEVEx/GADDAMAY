<?php

namespace App\Livewire\Literasi;

use App\Models\LiterasiJawabanSiswa;
use App\Models\LiterasiPresensiSiswa;
use App\Models\LiterasiSesi;
use App\Models\LiterasiSlide;
use App\Models\LiterasiSoal;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class KelolaLiterasiSesi extends Component
{
    use WithFileUploads;

    public $search = '';
    public $filterStatus = '';
    
    // Modal Form Sesi
    public $showModalSesi = false;
    public $isEditSesi = false;
    public $sesiId;
    public $judul = '';
    public $tema = 'Budaya Kerja & Karakter';
    public $tanggal = '';
    public $pemateri_id = '';
    public $nama_pemateri = '';
    public $jabatan_pemateri = '';
    public $deskripsi = '';
    public $kode_sesi = '';

    // Modal Kelola Slides
    public $showModalSlide = false;
    public $activeSesiForSlide;
    public $slideId;
    public $isEditSlide = false;
    public $slideJudul = '';
    public $slideKonten = '';
    public $slidePoinKunci = '';
    public $slideCatatanPemateri = '';

    // Modal Kelola Soal Evaluasi
    public $showModalSoal = false;
    public $activeSesiForSoal;
    public $soalId;
    public $isEditSoal = false;
    public $jenisSoal = 'pilihan_ganda';
    public $pertanyaan = '';
    public $opsi_a = '';
    public $opsi_b = '';
    public $opsi_c = '';
    public $opsi_d = '';
    public $opsi_e = '';
    public $kunci_jawaban = 'A';
    public $bobot_skor = 10;
    public $pembahasan = '';

    // Modal Rekap Siswa
    public $showModalRekap = false;
    public $activeSesiForRekap;
    public $filterRombel = '';

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        if (Auth::user()?->isGuru()) {
            $this->pemateri_id = Auth::id();
            $this->nama_pemateri = Auth::user()->name;
        }
    }

    public function render()
    {
        $query = LiterasiSesi::with(['pemateri', 'slides', 'soal'])
            ->withCount(['slides', 'soal', 'presensiSiswa']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('tema', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_pemateri', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $sesiList = $query->orderBy('tanggal', 'desc')->paginate(10);
        $guruList = User::whereIn('role', ['guru', 'ketua_mgmp', 'guru_piket', 'admin'])->orderBy('name')->get();
        $rombelList = Rombel::orderBy('nama_rombel')->get();

        $rekapSiswa = [];
        if ($this->activeSesiForRekap) {
            $rekapQuery = LiterasiPresensiSiswa::where('sesi_id', $this->activeSesiForRekap->id)
                ->with(['siswa', 'rombel', 'jawaban.soal']);

            if ($this->filterRombel) {
                $rekapQuery->where('rombel_id', $this->filterRombel);
            }

            $rekapSiswa = $rekapQuery->orderBy('waktu_submit', 'desc')->get();
        }

        return view('livewire.literasi.kelola-literasi-sesi', [
            'sesiList' => $sesiList,
            'guruList' => $guruList,
            'rombelList' => $rombelList,
            'rekapSiswa' => $rekapSiswa,
        ])->layout('components.layouts.app');
    }

    // =========================================================================
    // CRUD Sesi Literasi
    // =========================================================================

    public function openCreateSesi()
    {
        $this->reset(['sesiId', 'isEditSesi', 'judul', 'deskripsi']);
        $this->tema = 'Budaya Kerja & Karakter';
        $this->tanggal = date('Y-m-d');
        if (Auth::user()?->isGuru()) {
            $this->pemateri_id = Auth::id();
            $this->nama_pemateri = Auth::user()->name;
        } else {
            $this->pemateri_id = '';
            $this->nama_pemateri = '';
        }
        $this->jabatan_pemateri = 'Guru Pemateri';
        $this->kode_sesi = strtoupper(Str::random(6));
        $this->showModalSesi = true;
    }

    public function editSesi($id)
    {
        $sesi = LiterasiSesi::findOrFail($id);
        $this->sesiId = $sesi->id;
        $this->isEditSesi = true;
        $this->judul = $sesi->judul;
        $this->tema = $sesi->tema;
        $this->tanggal = $sesi->tanggal->format('Y-m-d');
        $this->pemateri_id = $sesi->pemateri_id ?? '';
        $this->nama_pemateri = $sesi->nama_pemateri;
        $this->jabatan_pemateri = $sesi->jabatan_pemateri ?? '';
        $this->deskripsi = $sesi->deskripsi ?? '';
        $this->kode_sesi = $sesi->kode_sesi ?? strtoupper(Str::random(6));
        $this->showModalSesi = true;
    }

    public function saveSesi()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'tema' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'nama_pemateri' => 'required|string|max:150',
        ]);

        if ($this->isEditSesi && $this->sesiId) {
            $sesi = LiterasiSesi::findOrFail($this->sesiId);
            $sesi->update([
                'judul' => $this->judul,
                'tema' => $this->tema,
                'tanggal' => $this->tanggal,
                'pemateri_id' => $this->pemateri_id ?: null,
                'nama_pemateri' => $this->nama_pemateri,
                'jabatan_pemateri' => $this->jabatan_pemateri,
                'deskripsi' => $this->deskripsi,
                'kode_sesi' => $this->kode_sesi,
            ]);
            session()->flash('success', 'Sesi literasi berhasil diperbarui.');
        } else {
            LiterasiSesi::create([
                'judul' => $this->judul,
                'tema' => $this->tema,
                'tanggal' => $this->tanggal,
                'pemateri_id' => $this->pemateri_id ?: null,
                'nama_pemateri' => $this->nama_pemateri,
                'jabatan_pemateri' => $this->jabatan_pemateri,
                'koordinator_id' => Auth::id(),
                'status' => 'draft',
                'current_slide_index' => 1,
                'allow_quiz' => false,
                'total_slides' => 0,
                'deskripsi' => $this->deskripsi,
                'kode_sesi' => $this->kode_sesi ?: strtoupper(Str::random(6)),
            ]);
            session()->flash('success', 'Sesi literasi baru berhasil dibuat.');
        }

        $this->showModalSesi = false;
    }

    public function deleteSesi($id)
    {
        $sesi = LiterasiSesi::findOrFail($id);
        $sesi->delete();
        session()->flash('success', 'Sesi literasi berhasil dihapus.');
    }

    public function toggleLiveStatus($id)
    {
        $sesi = LiterasiSesi::findOrFail($id);
        if ($sesi->status === 'draft') {
            $sesi->status = 'live';
            if ($sesi->current_slide_index < 1 && $sesi->slides()->count() > 0) {
                $sesi->current_slide_index = 1;
            }
        } elseif ($sesi->status === 'live') {
            $sesi->status = 'selesai';
        } else {
            $sesi->status = 'live';
        }
        $sesi->save();
        session()->flash('success', "Status sesi diubah menjadi: " . strtoupper($sesi->status));
    }

    // =========================================================================
    // Kelola Slides
    // =========================================================================

    public function openSlidesModal($sesiId)
    {
        $this->activeSesiForSlide = LiterasiSesi::with('slides')->findOrFail($sesiId);
        $this->resetSlideForm();
        $this->showModalSlide = true;
    }

    public function resetSlideForm()
    {
        $this->reset(['slideId', 'isEditSlide', 'slideJudul', 'slideKonten', 'slidePoinKunci', 'slideCatatanPemateri']);
    }

    public function editSlide($slideId)
    {
        $slide = LiterasiSlide::findOrFail($slideId);
        $this->slideId = $slide->id;
        $this->isEditSlide = true;
        $this->slideJudul = $slide->judul_slide;
        $this->slideKonten = $slide->konten;
        $this->slidePoinKunci = $slide->poin_kunci ?? '';
        $this->slideCatatanPemateri = $slide->catatan_pemateri ?? '';
    }

    public function saveSlide()
    {
        $this->validate([
            'slideJudul' => 'required|string|max:200',
            'slideKonten' => 'required|string',
        ]);

        if ($this->isEditSlide && $this->slideId) {
            $slide = LiterasiSlide::findOrFail($this->slideId);
            $slide->update([
                'judul_slide' => $this->slideJudul,
                'konten' => $this->slideKonten,
                'poin_kunci' => $this->slidePoinKunci,
                'catatan_pemateri' => $this->slideCatatanPemateri,
            ]);
            session()->flash('success', 'Slide berhasil diperbarui.');
        } else {
            $nextUrutan = $this->activeSesiForSlide->slides()->max('urutan') + 1;
            LiterasiSlide::create([
                'sesi_id' => $this->activeSesiForSlide->id,
                'urutan' => $nextUrutan,
                'judul_slide' => $this->slideJudul,
                'konten' => $this->slideKonten,
                'poin_kunci' => $this->slidePoinKunci,
                'catatan_pemateri' => $this->slideCatatanPemateri,
            ]);
            
            // update total slides di sesi
            $this->activeSesiForSlide->total_slides = $this->activeSesiForSlide->slides()->count();
            if ($this->activeSesiForSlide->current_slide_index === 0) {
                $this->activeSesiForSlide->current_slide_index = 1;
            }
            $this->activeSesiForSlide->save();

            session()->flash('success', 'Slide baru berhasil ditambahkan.');
        }

        $this->activeSesiForSlide = LiterasiSesi::with('slides')->findOrFail($this->activeSesiForSlide->id);
        $this->resetSlideForm();
    }

    public function deleteSlide($slideId)
    {
        $slide = LiterasiSlide::findOrFail($slideId);
        $sesiId = $slide->sesi_id;
        $slide->delete();

        // re-index urutan
        $slides = LiterasiSlide::where('sesi_id', $sesiId)->orderBy('urutan')->get();
        $i = 1;
        foreach ($slides as $s) {
            $s->urutan = $i++;
            $s->save();
        }

        $sesi = LiterasiSesi::findOrFail($sesiId);
        $sesi->total_slides = $slides->count();
        if ($sesi->current_slide_index > $sesi->total_slides) {
            $sesi->current_slide_index = max(1, $sesi->total_slides);
        }
        $sesi->save();

        $this->activeSesiForSlide = LiterasiSesi::with('slides')->findOrFail($sesiId);
        session()->flash('success', 'Slide berhasil dihapus.');
    }

    // =========================================================================
    // Kelola Soal Evaluasi
    // =========================================================================

    public function openSoalModal($sesiId)
    {
        $this->activeSesiForSoal = LiterasiSesi::with('soal')->findOrFail($sesiId);
        $this->resetSoalForm();
        $this->showModalSoal = true;
    }

    public function resetSoalForm()
    {
        $this->reset([
            'soalId', 'isEditSoal', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'pembahasan'
        ]);
        $this->jenisSoal = 'pilihan_ganda';
        $this->kunci_jawaban = 'A';
        $this->bobot_skor = 10;
    }

    public function editSoal($soalId)
    {
        $soal = LiterasiSoal::findOrFail($soalId);
        $this->soalId = $soal->id;
        $this->isEditSoal = true;
        $this->jenisSoal = $soal->jenis_soal;
        $this->pertanyaan = $soal->pertanyaan;
        $this->opsi_a = $soal->opsi_a ?? '';
        $this->opsi_b = $soal->opsi_b ?? '';
        $this->opsi_c = $soal->opsi_c ?? '';
        $this->opsi_d = $soal->opsi_d ?? '';
        $this->opsi_e = $soal->opsi_e ?? '';
        $this->kunci_jawaban = $soal->kunci_jawaban ?? 'A';
        $this->bobot_skor = $soal->bobot_skor;
        $this->pembahasan = $soal->pembahasan ?? '';
    }

    public function saveSoal()
    {
        $this->validate([
            'pertanyaan' => 'required|string',
            'jenisSoal' => 'required|in:pilihan_ganda,esai_pendek',
        ]);

        if ($this->jenisSoal === 'pilihan_ganda') {
            $this->validate([
                'opsi_a' => 'required|string',
                'opsi_b' => 'required|string',
                'kunci_jawaban' => 'required|in:A,B,C,D,E',
            ]);
        }

        if ($this->isEditSoal && $this->soalId) {
            $soal = LiterasiSoal::findOrFail($this->soalId);
            $soal->update([
                'jenis_soal' => $this->jenisSoal,
                'pertanyaan' => $this->pertanyaan,
                'opsi_a' => $this->opsi_a,
                'opsi_b' => $this->opsi_b,
                'opsi_c' => $this->opsi_c,
                'opsi_d' => $this->opsi_d,
                'opsi_e' => $this->opsi_e,
                'kunci_jawaban' => $this->jenisSoal === 'pilihan_ganda' ? $this->kunci_jawaban : null,
                'bobot_skor' => $this->bobot_skor,
                'pembahasan' => $this->pembahasan,
            ]);
            session()->flash('success', 'Butir soal berhasil diperbarui.');
        } else {
            $nextUrutan = $this->activeSesiForSoal->soal()->max('urutan') + 1;
            LiterasiSoal::create([
                'sesi_id' => $this->activeSesiForSoal->id,
                'urutan' => $nextUrutan,
                'jenis_soal' => $this->jenisSoal,
                'pertanyaan' => $this->pertanyaan,
                'opsi_a' => $this->opsi_a,
                'opsi_b' => $this->opsi_b,
                'opsi_c' => $this->opsi_c,
                'opsi_d' => $this->opsi_d,
                'opsi_e' => $this->opsi_e,
                'kunci_jawaban' => $this->jenisSoal === 'pilihan_ganda' ? $this->kunci_jawaban : null,
                'bobot_skor' => $this->bobot_skor,
                'pembahasan' => $this->pembahasan,
            ]);
            session()->flash('success', 'Butir soal evaluasi berhasil ditambahkan.');
        }

        $this->activeSesiForSoal = LiterasiSesi::with('soal')->findOrFail($this->activeSesiForSoal->id);
        $this->resetSoalForm();
    }

    public function deleteSoal($soalId)
    {
        $soal = LiterasiSoal::findOrFail($soalId);
        $sesiId = $soal->sesi_id;
        $soal->delete();

        // re-index
        $soals = LiterasiSoal::where('sesi_id', $sesiId)->orderBy('urutan')->get();
        $i = 1;
        foreach ($soals as $s) {
            $s->urutan = $i++;
            $s->save();
        }

        $this->activeSesiForSoal = LiterasiSesi::with('soal')->findOrFail($sesiId);
        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function toggleAllowQuiz($sesiId)
    {
        $sesi = LiterasiSesi::findOrFail($sesiId);
        $sesi->allow_quiz = !$sesi->allow_quiz;
        $sesi->save();
        session()->flash('success', $sesi->allow_quiz ? 'Evaluasi / Kuis telah DIBUKA untuk siswa.' : 'Evaluasi / Kuis DITUTUP sementara.');
    }

    // =========================================================================
    // Rekap & Ekspor Hasil Siswa
    // =========================================================================

    public function openRekapModal($sesiId)
    {
        $this->activeSesiForRekap = LiterasiSesi::with(['slides', 'soal'])->findOrFail($sesiId);
        $this->filterRombel = '';
        $this->showModalRekap = true;
    }

    public function exportExcelRekap()
    {
        if (!$this->activeSesiForRekap) return;

        $sesi = $this->activeSesiForRekap;
        $query = LiterasiPresensiSiswa::where('sesi_id', $sesi->id)
            ->with(['siswa', 'rombel', 'jawaban.soal']);

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        $data = $query->orderBy('rombel_id')->orderBy('waktu_submit')->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Rekap_Literasi_" . Str::slug($sesi->judul) . "_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data, $sesi) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8

            // Judul
            fputcsv($file, ["REKAP KETERCAPAIAN LITERASI RABU PAGI - SMKN 2 INDRAMAYU"]);
            fputcsv($file, ["Judul Sesi", $sesi->judul]);
            fputcsv($file, ["Tema", $sesi->tema]);
            fputcsv($file, ["Tanggal", $sesi->tanggal->format('d/m/Y')]);
            fputcsv($file, ["Pemateri", $sesi->nama_pemateri . " (" . $sesi->jabatan_pemateri . ")"]);
            fputcsv($file, []);

            // Header Tabel
            fputcsv($file, [
                'No', 'NISN', 'Nama Siswa', 'Kelas / Rombel', 'Waktu Hadir', 'Waktu Kirim Jawaban',
                'Status Evaluasi', 'Skor PG', 'Rangkuman Refleksi Siswa'
            ]);

            $no = 1;
            foreach ($data as $item) {
                fputcsv($file, [
                    $no++,
                    $item->siswa->nisn ?? $item->siswa->nis ?? '-',
                    $item->siswa->nama ?? '-',
                    $item->rombel->nama_rombel ?? '-',
                    $item->waktu_gabung ? $item->waktu_gabung->format('H:i:s') : '-',
                    $item->waktu_submit ? $item->waktu_submit->format('H:i:s') : '-',
                    ucfirst(str_replace('_', ' ', $item->status_evaluasi)),
                    $item->skor_pg,
                    $item->rangkuman_siswa ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
