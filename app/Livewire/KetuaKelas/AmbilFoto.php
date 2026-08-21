<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\AgendaHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Ambil Foto Bukti Agenda')]
class AmbilFoto extends Component
{
    use WithFileUploads;

    public AgendaHarian $agenda;
    public $foto;
    public string $fotoBase64 = '';
    public bool $uploaded = false;

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru']);
        if (!empty($this->agenda->foto_bukti_path)) {
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
            // Decode base64 image data
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
            $imageData = base64_decode($imageData);

            $filename = 'foto-bukti/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($filename, $imageData);

            $this->agenda->update([
                'foto_bukti_path' => $filename,
                'status' => 'berjalan',
                'waktu_mulai' => $this->agenda->waktu_mulai ?? Carbon::now('Asia/Jakarta'),
            ]);

            $this->uploaded = true;
            $this->dispatch('show-toast', message: 'Foto bukti berwatermark berhasil disimpan! Kelas dimulai.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal menyimpan foto: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function simpanFoto()
    {
        $this->validate([
            'foto' => 'required|image|max:10240',
        ]);

        $path = $this->foto->store('foto-bukti', 'public');

        $this->agenda->update([
            'foto_bukti_path' => $path,
            'status' => 'berjalan',
            'waktu_mulai' => $this->agenda->waktu_mulai ?? Carbon::now('Asia/Jakarta'),
        ]);

        $this->uploaded = true;
        $this->dispatch('show-toast', message: 'Foto bukti berhasil disimpan! Kelas dimulai.', type: 'success');
    }

    public function render()
    {
        return view('livewire.ketua-kelas.ambil-foto');
    }
}
