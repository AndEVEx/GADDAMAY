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
        $this->studentRombel = $this->resolveStudentRombel($user);
    }

    /**
     * Resolve the student's Rombel model from user record or user name/email
     */
    private function resolveStudentRombel($user): ?Rombel
    {
        if (!$user) return null;

        // 1. Direct foreign key
        if (!empty($user->rombel_id)) {
            $rombel = Rombel::find($user->rombel_id);
            if ($rombel) return $rombel;
        }

        $rombels = Rombel::all();
        $userNameLower = strtolower(trim($user->name ?? ''));
        $userEmailLower = strtolower(trim($user->email ?? ''));

        // 2. Exact full-name match with longest nama_kelas first
        $sortedRombels = $rombels->sortByDesc(fn($r) => strlen($r->nama_kelas));

        foreach ($sortedRombels as $r) {
            $kelasLower = strtolower(trim($r->nama_kelas));
            if (empty($kelasLower)) continue;

            if (str_contains($userNameLower, $kelasLower)) {
                $user->update(['rombel_id' => $r->id]);
                return $r;
            }
        }

        // 3. Email slug match
        foreach ($sortedRombels as $r) {
            $namaKelas = $r->nama_kelas;
            $slug1 = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaKelas));
            $slug2 = strtolower(str_replace(' ', '', $namaKelas));
            $slug3 = strtolower(str_replace(' ', '.', $namaKelas));

            foreach ([$slug1, $slug2, $slug3] as $slug) {
                if (!empty($slug) && str_contains($userEmailLower, $slug)) {
                    $user->update(['rombel_id' => $r->id]);
                    return $r;
                }
            }
        }

        return null;
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