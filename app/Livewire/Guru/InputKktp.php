<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\KktpSiswa;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;

#[Layout('components.layouts.app')]
#[Title('Input KKTP')]
class InputKktp extends Component
{
    public AgendaHarian $agenda;
    public array $kktpData = []; // [siswa_id][tp_id] => 'tercapai' | 'belum_tercapai'
    public string $refleksi = '';

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran', 'kehadiranMurid.siswa', 'kktpSiswa']);
        $this->refleksi = $this->agenda->refleksi ?? '';

        // Initialize kktpData for students who are HADIR
        $siswaHadir = $this->agenda->kehadiranMurid
            ->where('status', 'hadir')
            ->filter(fn($k) => !empty($k->siswa))
            ->pluck('siswa');
        $tps = $this->agenda->tujuanPembelajaran;

        foreach ($siswaHadir as $siswa) {
            foreach ($tps as $tp) {
                // Check existing record
                $existing = $this->agenda->kktpSiswa
                    ->where('siswa_id', $siswa->id)
                    ->where('tp_id', $tp->id)
                    ->first();
                $this->kktpData[$siswa->id][$tp->id] = $existing ? $existing->status : 'belum_tercapai';
            }
        }
    }

    public function toggleKktp(string $siswaId, string $tpId)
    {
        $current = $this->kktpData[$siswaId][$tpId] ?? 'belum_tercapai';
        $this->kktpData[$siswaId][$tpId] = $current === 'tercapai' ? 'belum_tercapai' : 'tercapai';
    }

    public function simpanDanSelesai()
    {
        // Save all KKTP records
        foreach ($this->kktpData as $siswaId => $tps) {
            foreach ($tps as $tpId => $status) {
                KktpSiswa::updateOrCreate(
                    ['agenda_harian_id' => $this->agenda->id, 'siswa_id' => $siswaId, 'tp_id' => $tpId],
                    ['status' => $status]
                );
            }
        }

        // Set agenda as complete
        $this->agenda->update([
            'status' => 'selesai',
            'refleksi' => $this->refleksi,
        ]);

        $this->dispatch('show-toast', message: 'Pembelajaran selesai! KKTP tersimpan.', type: 'success');
        return redirect()->route('guru.dashboard');
    }

    public function batalkanAgenda()
    {
        if ($this->agenda) {
            $this->agenda->delete();
            $this->dispatch('show-toast', message: 'Sesi agenda berhasil dibatalkan!', type: 'info');
        }
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        $siswaHadir = $this->agenda->kehadiranMurid
            ->where('status', 'hadir')
            ->filter(fn($k) => !empty($k->siswa))
            ->sortBy('siswa.nama')
            ->pluck('siswa');
        $tps = $this->agenda->tujuanPembelajaran;

        return view('livewire.guru.input-kktp', [
            'siswaHadir' => $siswaHadir,
            'tps' => $tps,
        ]);
    }
}
