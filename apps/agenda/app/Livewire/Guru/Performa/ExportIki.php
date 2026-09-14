<?php

namespace App\Livewire\Guru\Performa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Export IKI (Indikator Kinerja Individu)')]
class ExportIki extends Component
{
    public bool $includeJadwal = true;
    public bool $includeSiswa = true;
    public bool $includeKehadiran = true;
    public string $bulan;

    public function mount()
    {
        $this->bulan = request()->get('bulan', Carbon::now('Asia/Jakarta')->format('Y-m'));
    }

    public function downloadPdf()
    {
        return redirect()->route('guru.performa.export-iki.pdf', [
            'jadwal' => $this->includeJadwal ? 1 : 0,
            'siswa' => $this->includeSiswa ? 1 : 0,
            'kehadiran' => $this->includeKehadiran ? 1 : 0,
            'bulan' => $this->bulan,
            'stream' => 0,
        ]);
    }

    public function previewPdf()
    {
        return redirect()->route('guru.performa.export-iki.pdf', [
            'jadwal' => $this->includeJadwal ? 1 : 0,
            'siswa' => $this->includeSiswa ? 1 : 0,
            'kehadiran' => $this->includeKehadiran ? 1 : 0,
            'bulan' => $this->bulan,
            'stream' => 1,
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $namaBulan = Carbon::parse($this->bulan)->translatedFormat('F Y');

        $totalJadwal = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))->count();

        return view('livewire.guru.performa.export-iki', [
            'namaBulan' => $namaBulan,
            'totalJadwal' => $totalJadwal,
        ]);
    }
}