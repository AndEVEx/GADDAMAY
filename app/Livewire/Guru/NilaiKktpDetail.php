<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\Siswa;
use App\Models\NilaiKktp;
use App\Models\KehadiranMurid;
use App\Models\AgendaHarian;

#[Layout('components.layouts.app')]
#[Title('Input Nilai KKTP')]
class NilaiKktpDetail extends Component
{
    public Rombel $rombel;
    public MataPelajaran $mapel;
    public array $nilaiData = []; // [siswa_id][tp_id] => 'tercapai' | 'belum_tercapai'

    public function mount(Rombel $rombel, MataPelajaran $mapel)
    {
        $this->rombel = $rombel;
        $this->mapel = $mapel;

        // Load existing NilaiKktp records
        $siswaList = Siswa::where('rombel_id', $rombel->id)->orderBy('nama')->get();
        $tps = TujuanPembelajaran::where('mapel_id', $mapel->id)->orderBy('order_sequence')->get();

        $existingNilai = NilaiKktp::where('rombel_id', $rombel->id)
            ->whereIn('tp_id', $tps->pluck('id'))
            ->get()
            ->keyBy(fn($n) => $n->siswa_id . '_' . $n->tp_id);

        // Get latest agenda for this rombel+mapel to check attendance
        $latestAgenda = AgendaHarian::whereHas('jadwalPelajaran', fn($q) => 
            $q->where('rombel_id', $rombel->id)->where('mapel_id', $mapel->id)
        )->latest('tanggal')->first();

        $absentSiswaIds = collect();
        if ($latestAgenda) {
            $absentSiswaIds = KehadiranMurid::where('agenda_harian_id', $latestAgenda->id)
                ->where('status', '!=', 'hadir')
                ->pluck('siswa_id');
        }

        foreach ($siswaList as $siswa) {
            foreach ($tps as $tp) {
                $key = $siswa->id . '_' . $tp->id;
                if (isset($existingNilai[$key])) {
                    $this->nilaiData[$siswa->id][$tp->id] = $existingNilai[$key]->status;
                } elseif ($absentSiswaIds->contains($siswa->id)) {
                    // Auto-mark absent students as belum_tercapai
                    $this->nilaiData[$siswa->id][$tp->id] = 'belum_tercapai';
                } else {
                    // Default: tercapai
                    $this->nilaiData[$siswa->id][$tp->id] = 'tercapai';
                }
            }
        }
    }

    public function toggleNilai(string $siswaId, string $tpId)
    {
        $current = $this->nilaiData[$siswaId][$tpId] ?? 'tercapai';
        $this->nilaiData[$siswaId][$tpId] = $current === 'tercapai' ? 'belum_tercapai' : 'tercapai';
    }

    public function setAllTp(string $tpId, string $status)
    {
        foreach ($this->nilaiData as $siswaId => $tps) {
            if (isset($this->nilaiData[$siswaId][$tpId])) {
                $this->nilaiData[$siswaId][$tpId] = $status;
            }
        }
    }

    public function simpanNilai()
    {
        $user = auth()->user();

        foreach ($this->nilaiData as $siswaId => $tps) {
            foreach ($tps as $tpId => $status) {
                NilaiKktp::updateOrCreate(
                    ['siswa_id' => $siswaId, 'tp_id' => $tpId, 'rombel_id' => $this->rombel->id],
                    ['status' => $status, 'guru_id' => $user->id]
                );
            }
        }

        $this->dispatch('show-toast', message: 'Nilai KKTP berhasil disimpan!', type: 'success');
    }

    public function render()
    {
        $siswaList = Siswa::where('rombel_id', $this->rombel->id)->orderBy('nama')->get();
        $tps = TujuanPembelajaran::where('mapel_id', $this->mapel->id)->orderBy('order_sequence')->get();

        return view('livewire.guru.nilai-kktp-detail', [
            'siswaList' => $siswaList,
            'tps' => $tps,
        ]);
    }
}
