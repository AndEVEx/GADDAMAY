<?php

namespace App\Livewire\Literasi;

use App\Models\LiterasiPresensiSiswa;
use App\Models\LiterasiSesi;
use App\Models\LiterasiSlide;
use Livewire\Component;

class PresenterLiterasiLive extends Component
{
    public $sesiId;
    public $fullscreenMode = false;

    public function mount($id)
    {
        $this->sesiId = $id;
        $sesi = LiterasiSesi::findOrFail($id);
        if ($sesi->current_slide_index === 0 && $sesi->slides()->count() > 0) {
            $sesi->current_slide_index = 1;
            $sesi->save();
        }
    }

    public function render()
    {
        $sesi = LiterasiSesi::with(['slides', 'soal'])->findOrFail($this->sesiId);
        $totalSlides = $sesi->slides->count();
        $currentSlide = $sesi->slides->where('urutan', $sesi->current_slide_index)->first();

        // Statistik Realtime Siswa di Lapangan
        $siswaConnectedCount = LiterasiPresensiSiswa::where('sesi_id', $this->sesiId)->count();
        $siswaSubmittedCount = LiterasiPresensiSiswa::where('sesi_id', $this->sesiId)->whereNotNull('waktu_submit')->count();

        return view('livewire.literasi.presenter-literasi-live', [
            'sesi' => $sesi,
            'totalSlides' => $totalSlides,
            'currentSlide' => $currentSlide,
            'siswaConnectedCount' => $siswaConnectedCount,
            'siswaSubmittedCount' => $siswaSubmittedCount,
        ])->layout('components.layouts.app');
    }

    public function nextSlide()
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        $totalSlides = $sesi->slides()->count();
        if ($sesi->current_slide_index < $totalSlides) {
            $sesi->current_slide_index++;
            $sesi->save();
        }
    }

    public function prevSlide()
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        if ($sesi->current_slide_index > 1) {
            $sesi->current_slide_index--;
            $sesi->save();
        }
    }

    public function goToSlide($index)
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        $totalSlides = $sesi->slides()->count();
        if ($index >= 1 && $index <= $totalSlides) {
            $sesi->current_slide_index = $index;
            $sesi->save();
        }
    }

    public function startLive()
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        $sesi->status = 'live';
        if ($sesi->current_slide_index === 0 && $sesi->slides()->count() > 0) {
            $sesi->current_slide_index = 1;
        }
        $sesi->save();
        session()->flash('success', 'Siaran Literasi telah DIMULAI! Siswa di lapangan sekarang tersinkronisasi.');
    }

    public function toggleQuiz()
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        $sesi->allow_quiz = !$sesi->allow_quiz;
        $sesi->save();
        session()->flash('success', $sesi->allow_quiz ? 'Kuis / Evaluasi DIBUKA! Siswa sekarang dapat menjawab di ponsel.' : 'Kuis DITUTUP sementara.');
    }

    public function finishSession()
    {
        $sesi = LiterasiSesi::findOrFail($this->sesiId);
        $sesi->status = 'selesai';
        $sesi->save();
        session()->flash('success', 'Sesi Literasi Rabu Pagi dinyatakan SELESAI.');
    }

    public function toggleFullscreen()
    {
        $this->fullscreenMode = !$this->fullscreenMode;
    }
}
