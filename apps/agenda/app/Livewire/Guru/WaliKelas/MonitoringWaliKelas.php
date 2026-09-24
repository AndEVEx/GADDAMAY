<?php

namespace App\Livewire\Guru\WaliKelas;

use App\Models\LmsTesFisikSiswa;
use App\Models\LmsTkaHasilSiswa;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TugasTambahanGuru;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Monitoring Kelas Binaan - Wali Kelas')]
class MonitoringWaliKelas extends Component
{
    public $rombelId;
    public $rombel;
    public $activeTab = 'fisik'; // fisik / tka / presensi
    public string $search = '';

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $tugas = TugasTambahanGuru::where('guru_id', $user->id)
                ->where('jenis_tugas', 'wali_kelas')
                ->where('is_active', true)
                ->first();

            $this->rombelId = $tugas?->rombel_id;
        }

        if (!$this->rombelId) {
            $this->rombelId = Rombel::first()?->id;
        }

        $this->rombel = Rombel::with('siswa')->find($this->rombelId);
    }

    public function switchRombel($id)
    {
        $this->rombelId = $id;
        $this->rombel = Rombel::with('siswa')->find($this->rombelId);
    }

    public function render()
    {
        $allRombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $siswaList = Siswa::where('rombel_id', $this->rombelId)
            ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%")->orWhere('nis', 'like', "%{$this->search}%"))
            ->orderBy('nama')
            ->get();

        $siswaIds = $siswaList->pluck('id');

        // Rekap Fisik Siswa Kelas Ini
        $tesFisik = LmsTesFisikSiswa::with('guruOlahraga')
            ->whereIn('siswa_id', $siswaIds)
            ->latest('tanggal_tes')
            ->get()
            ->groupBy('siswa_id');

        // Rekap Nilai TKA Siswa Kelas Ini
        $tkaHasil = LmsTkaHasilSiswa::with('paket')
            ->whereIn('siswa_id', $siswaIds)
            ->latest()
            ->get()
            ->groupBy('siswa_id');

        return view('livewire.guru.wali-kelas.monitoring-wali-kelas', [
            'allRombels' => $allRombels,
            'siswaList' => $siswaList,
            'tesFisik' => $tesFisik,
            'tkaHasil' => $tkaHasil,
        ]);
    }
}
