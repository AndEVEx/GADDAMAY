<?php

namespace App\Livewire\Literasi;

use App\Models\LiterasiJawabanSiswa;
use App\Models\LiterasiPresensiSiswa;
use App\Models\LiterasiSesi;
use App\Models\LiterasiSlide;
use App\Models\LiterasiSoal;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SiswaLiterasiLive extends Component
{
    public $sesiId;
    public $activeTab = 'materi'; // materi, kuis, riwayat
    public $fontSize = 'normal'; // small, normal, large, xlarge

    // State Jawaban Siswa
    public $jawabanPg = []; // [soalId => 'A']
    public $jawabanEsai = []; // [soalId => 'text']
    public $rangkumanUmum = '';
    public $hasSubmitted = false;
    public $totalSkorPg = 0;

    public function mount($id = null)
    {
        if ($id) {
            $this->sesiId = $id;
        } else {
            // Cari sesi yang sedang live hari ini, atau sesi terbaru
            $sesiAktif = LiterasiSesi::where('status', 'live')->latest('tanggal')->first();
            if (!$sesiAktif) {
                $sesiAktif = LiterasiSesi::latest('tanggal')->first();
            }
            $this->sesiId = $sesiAktif?->id;
        }

        $this->catatPresensiDanLoadJawaban();
    }

    public function catatPresensiDanLoadJawaban()
    {
        if (!$this->sesiId) return;

        $user = Auth::user();
        if (!$user) return;

        // Dapatkan siswa_id
        $siswa = Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nis', $user->username)
            ->first();

        // Fallback dummy jika user adalah ketua_kelas atau testing
        $siswaId = $siswa?->id;
        $rombelId = $siswa?->rombel_id ?? $user->rombel_id;

        if ($siswaId) {
            $presensi = LiterasiPresensiSiswa::firstOrCreate(
                [
                    'sesi_id' => $this->sesiId,
                    'siswa_id' => $siswaId,
                ],
                [
                    'rombel_id' => $rombelId,
                    'waktu_gabung' => now(),
                    'status_evaluasi' => 'mengikuti',
                ]
            );

            if ($presensi->waktu_submit) {
                $this->hasSubmitted = true;
                $this->totalSkorPg = $presensi->skor_pg;
                $this->rangkumanUmum = $presensi->rangkuman_siswa ?? '';
            }

            // Load existing jawaban jika ada
            $existing = LiterasiJawabanSiswa::where('sesi_id', $this->sesiId)
                ->where('siswa_id', $siswaId)
                ->get();

            foreach ($existing as $ans) {
                if ($ans->jawaban_pg) {
                    $this->jawabanPg[$ans->soal_id] = $ans->jawaban_pg;
                }
                if ($ans->jawaban_esai) {
                    $this->jawabanEsai[$ans->soal_id] = $ans->jawaban_esai;
                }
            }
        }
    }

    public function render()
    {
        $sesi = null;
        $currentSlide = null;
        $totalSlides = 0;
        $soalList = [];
        $daftarSesi = LiterasiSesi::orderBy('tanggal', 'desc')->take(5)->get();

        if ($this->sesiId) {
            $sesi = LiterasiSesi::with(['slides', 'soal'])->find($this->sesiId);
            if ($sesi) {
                $totalSlides = $sesi->slides->count();
                $currentSlide = $sesi->slides->where('urutan', $sesi->current_slide_index)->first();
                $soalList = $sesi->soal;
            }
        }

        return view('livewire.literasi.siswa-literasi-live', [
            'sesi' => $sesi,
            'currentSlide' => $currentSlide,
            'totalSlides' => $totalSlides,
            'soalList' => $soalList,
            'daftarSesi' => $daftarSesi,
        ])->layout('components.layouts.app');
    }

    public function switchSesi($newSesiId)
    {
        $this->sesiId = $newSesiId;
        $this->hasSubmitted = false;
        $this->jawabanPg = [];
        $this->jawabanEsai = [];
        $this->rangkumanUmum = '';
        $this->catatPresensiDanLoadJawaban();
    }

    public function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    public function submitEvaluasi()
    {
        $sesi = LiterasiSesi::with('soal')->findOrFail($this->sesiId);

        if (!$sesi->allow_quiz) {
            session()->flash('error', 'Evaluasi kuis saat ini belum dibuka oleh Pemateri.');
            return;
        }

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nis', $user->username)
            ->first();

        $siswaId = $siswa?->id;
        $rombelId = $siswa?->rombel_id ?? $user->rombel_id;

        if (!$siswaId) {
            session()->flash('error', 'Data profil siswa tidak ditemukan.');
            return;
        }

        // Kalkulasi skor PG
        $totalSkor = 0;
        $soalPgCount = 0;
        $benarCount = 0;

        foreach ($sesi->soal as $soal) {
            if ($soal->jenis_soal === 'pilihan_ganda') {
                $soalPgCount++;
                $jawabanUser = $this->jawabanPg[$soal->id] ?? null;
                $isBenar = ($jawabanUser && strtoupper($jawabanUser) === strtoupper($soal->kunci_jawaban));
                $skorButir = $isBenar ? $soal->bobot_skor : 0;

                if ($isBenar) $benarCount++;

                LiterasiJawabanSiswa::updateOrCreate(
                    [
                        'sesi_id' => $sesi->id,
                        'siswa_id' => $siswaId,
                        'soal_id' => $soal->id,
                    ],
                    [
                        'jawaban_pg' => $jawabanUser,
                        'is_benar' => $isBenar,
                        'skor' => $skorButir,
                    ]
                );
            } elseif ($soal->jenis_soal === 'esai_pendek') {
                $jawabanUser = $this->jawabanEsai[$soal->id] ?? null;
                LiterasiJawabanSiswa::updateOrCreate(
                    [
                        'sesi_id' => $sesi->id,
                        'siswa_id' => $siswaId,
                        'soal_id' => $soal->id,
                    ],
                    [
                        'jawaban_esai' => $jawabanUser,
                        'skor' => 0, // Akan dinilai pemateri/guru jika diperlukan
                    ]
                );
            }
        }

        // Nilai skala 0-100 untuk PG
        $skorAkhirPg = $soalPgCount > 0 ? round(($benarCount / $soalPgCount) * 100, 1) : 100;

        // Update Presensi Siswa
        LiterasiPresensiSiswa::updateOrCreate(
            [
                'sesi_id' => $sesi->id,
                'siswa_id' => $siswaId,
            ],
            [
                'rombel_id' => $rombelId,
                'waktu_submit' => now(),
                'skor_pg' => $skorAkhirPg,
                'status_evaluasi' => 'sudah_mengerjakan',
                'rangkuman_siswa' => $this->rangkumanUmum,
            ]
        );

        $this->hasSubmitted = true;
        $this->totalSkorPg = $skorAkhirPg;

        session()->flash('success', "Refleksi dan Kuis berhasil terkirim! Skor Kuis Anda: {$skorAkhirPg}");
    }
}
