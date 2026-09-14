<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;

#[Layout('components.layouts.app')]
#[Title('Detail Agenda Mengajar')]
class DetailAgenda extends Component
{
    public AgendaHarian $agenda;

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load([
            'jadwalPelajaran.rombel',
            'jadwalPelajaran.mataPelajaran',
            'guru',
            'tujuanPembelajaran',
            'kehadiranMurid.siswa',
            'kktpSiswa.siswa',
            'kktpSiswa.tujuanPembelajaran',
        ]);
    }

    public function render()
    {
        $siswaHadir = $this->agenda->kehadiranMurid->where('status', 'hadir')->count();
        $siswaSakit = $this->agenda->kehadiranMurid->where('status', 'sakit')->count();
        $siswaIzin = $this->agenda->kehadiranMurid->where('status', 'izin')->count();
        $siswaAlpa = $this->agenda->kehadiranMurid->where('status', 'alpa')->count();

        return view('livewire.guru.detail-agenda', [
            'siswaHadir' => $siswaHadir,
            'siswaSakit' => $siswaSakit,
            'siswaIzin' => $siswaIzin,
            'siswaAlpa' => $siswaAlpa,
        ]);
    }
}
