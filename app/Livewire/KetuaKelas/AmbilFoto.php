<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\AgendaHarian;

#[Layout('components.layouts.app')]
#[Title('Ambil Foto')]
class AmbilFoto extends Component
{
    use WithFileUploads;

    public AgendaHarian $agenda;
    public $foto;
    public bool $uploaded = false;

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru']);
    }

    public function updatedFoto()
    {
        $this->validate([
            'foto' => 'required|image|max:5120', // max 5MB
        ]);
    }

    public function simpanFoto()
    {
        $this->validate([
            'foto' => 'required|image|max:5120',
        ]);

        $path = $this->foto->store('foto-bukti', 'public');

        $this->agenda->update([
            'foto_bukti_path' => $path,
            'status' => 'berjalan',
            'waktu_mulai' => now(),
        ]);

        $this->dispatch('show-toast', message: 'Foto bukti berhasil disimpan! Kelas dimulai.', type: 'success');
        return redirect()->route('ketua.verifikasi');
    }

    public function render()
    {
        return view('livewire.ketua-kelas.ambil-foto');
    }
}
