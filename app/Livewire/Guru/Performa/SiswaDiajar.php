<?php

namespace App\Livewire\Guru\Performa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Siswa;

#[Layout('components.layouts.app')]
#[Title('Daftar Siswa Yang Diajar')]
class SiswaDiajar extends Component
{
    public string $search = '';
    public string $selectedRombelMapel = 'all';

    public function render()
    {
        $user = auth()->user();

        // 1. Ambil semua kombinasi unik rombel_id & mapel_id yang diampu guru
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('rombel_id')
            ->whereNotNull('mapel_id')
            ->with(['rombel', 'mataPelajaran'])
            ->get();

        // Unique groups by rombel_id + mapel_id
        $groups = $jadwals->groupBy(fn($j) => $j->rombel_id . '_' . $j->mapel_id);

        $tables = [];
        $totalSiswaCount = 0;
        $allUniqueSiswaIds = collect();

        foreach ($groups as $key => $items) {
            $first = $items->first();
            $rombel = $first->rombel;
            $mapel = $first->mataPelajaran;

            if (!$rombel || !$mapel) continue;

            $query = Siswa::where('rombel_id', $rombel->id)->orderBy('nama');

            if (!empty($this->search)) {
                $s = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($s) {
                    $q->where('nama', 'like', $s)
                      ->orWhere('nis', 'like', $s);
                });
            }

            $siswaList = $query->get();

            // Total siswa asli di rombel (tanpa filter search)
            $totalInClass = Siswa::where('rombel_id', $rombel->id)->count();

            // Collect unique siswa
            $allUniqueSiswaIds = $allUniqueSiswaIds->merge($siswaList->pluck('id'));

            $groupKey = $rombel->id . '_' . $mapel->id;

            // Filter by dropdown selection if set
            if ($this->selectedRombelMapel !== 'all' && $this->selectedRombelMapel !== $groupKey) {
                continue;
            }

            $tables[] = (object) [
                'group_key' => $groupKey,
                'rombel_id' => $rombel->id,
                'rombel_nama' => $rombel->nama_kelas,
                'tingkat' => $rombel->tingkat,
                'mapel_id' => $mapel->id,
                'mapel_nama' => $mapel->nama_mapel,
                'kode_mapel' => $mapel->kode_mapel,
                'siswa_list' => $siswaList,
                'total_siswa' => $totalInClass,
                'filtered_count' => $siswaList->count(),
            ];
        }

        // Sort tables by rombel nama, then mapel nama
        usort($tables, fn($a, $b) => strcmp($a->rombel_nama . $a->mapel_nama, $b->rombel_nama . $b->mapel_nama));

        // Options for dropdown filter
        $dropdownOptions = $groups->map(function ($items) {
            $first = $items->first();
            return (object) [
                'key' => $first->rombel_id . '_' . $first->mapel_id,
                'label' => ($first->rombel?->nama_kelas ?? '-') . ' — ' . ($first->mataPelajaran?->nama_mapel ?? '-'),
            ];
        })->values();

        $totalTabel = count($tables);
        $totalSiswaUnik = $allUniqueSiswaIds->unique()->count();

        return view('livewire.guru.performa.siswa-diajar', [
            'tables' => $tables,
            'dropdownOptions' => $dropdownOptions,
            'totalTabel' => $totalTabel,
            'totalSiswaUnik' => $totalSiswaUnik,
        ]);
    }
}