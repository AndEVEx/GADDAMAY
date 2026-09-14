<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\Siswa;

#[Layout('components.layouts.app')]
#[Title('Anggota Kelas')]
class AnggotaKelas extends Component
{
    public ?Rombel $studentRombel = null;
    public string $search = '';

    public function mount()
    {
        $user = auth()->user();
        // Direct explicit foreign key connection - NO fragile auto-guessing
        $this->studentRombel = $user?->rombel;
    }

    public function render()
    {
        $siswaList = collect();

        if ($this->studentRombel) {
            $siswaList = Siswa::where('rombel_id', $this->studentRombel->id)
                ->when($this->search, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhere('nis', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('nama')
                ->get();
        }

        $totalSiswa = $this->studentRombel 
            ? Siswa::where('rombel_id', $this->studentRombel->id)->count() 
            : 0;

        return view('livewire.ketua-kelas.anggota-kelas', [
            'siswaList' => $siswaList,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}