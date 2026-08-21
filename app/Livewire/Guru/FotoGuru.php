<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\AgendaHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Foto Guru & Suasana Kelas')]
class FotoGuru extends Component
{
    use WithFileUploads;

    public AgendaHarian $agenda;
    public $foto;
    public bool $uploaded = false;

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru']);
        if (!empty($this->agenda->foto_guru_path)) {
            $this->uploaded = true;
        }
    }

    public function simpanFotoBase64(string $base64Data)
    {
        if (empty($base64Data)) {
            $this->dispatch('show-toast', message: 'Gambar tidak valid!', type: 'danger');
            return;
        }

        try {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
            $imageData = base64_decode($imageData);

            $filename = 'foto-guru/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($filename, $imageData);

            $this->agenda->update([
                'foto_guru_path' => $filename,
                'status' => 'berjalan',
                'waktu_mulai' => $this->agenda->waktu_mulai ?? Carbon::now('Asia/Jakarta'),
            ]);

            $this->dispatch('show-toast', message: 'Foto Guru & Suasana Kelas tersimpan! Lanjut ke Presensi Siswa.', type: 'success');
            return redirect()->route('guru.kehadiran', $this->agenda->id);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal menyimpan foto: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function simpanFoto()
    {
        $this->validate([
            'foto' => 'required|image|max:5120',
        ]);

        $path = $this->foto->store('foto-guru', 'public');

        $this->agenda->update([
            'foto_guru_path' => $path,
            'status' => 'berjalan',
            'waktu_mulai' => $this->agenda->waktu_mulai ?? Carbon::now('Asia/Jakarta'),
        ]);

        $this->dispatch('show-toast', message: 'Foto Guru tersimpan! Lanjut ke Presensi Siswa.', type: 'success');
        return redirect()->route('guru.kehadiran', $this->agenda->id);
    }

    public function render()
    {
        return view('livewire.guru.foto-guru');
    }
}
