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

        // 1. Ambil semua kombinasi unik rombel & mapel yang diampu guru (termasuk pasangan blok)
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('rombel_id')
            ->whereNotNull('mapel_id')
            ->with(['rombel', 'mataPelajaran'])
            ->get();

        $rombelMapelPairs = collect();
        foreach ($jadwals as $j) {
            if (!$j->rombel || !$j->mataPelajaran) continue;

            $key = $j->rombel_id . '_' . $j->mapel_id;
            if (!$rombelMapelPairs->has($key)) {
                $rombelMapelPairs->put($key, [
                    'rombel' => $j->rombel,
                    'mapel' => $j->mataPelajaran,
                ]);
            }

            // Jika kelas blok vokasi (TP, RPL, APHP, NKPI), sertakan juga kelas pasangannya
            $partner = $j->rombel->getPartnerBlockRombel();
            if ($partner) {
                $partnerKey = $partner->id . '_' . $j->mapel_id;
                if (!$rombelMapelPairs->has($partnerKey)) {
                    $rombelMapelPairs->put($partnerKey, [
                        'rombel' => $partner,
                        'mapel' => $j->mataPelajaran,
                    ]);
                }
            }
        }

        $tables = [];
        $allUniqueSiswaIds = collect();

        foreach ($rombelMapelPairs as $key => $pair) {
            $rombel = $pair['rombel'];
            $mapel = $pair['mapel'];

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
        $dropdownOptions = $rombelMapelPairs->map(function ($pair) {
            $rombel = $pair['rombel'];
            $mapel = $pair['mapel'];
            return (object) [
                'key' => $rombel->id . '_' . $mapel->id,
                'label' => ($rombel->nama_kelas ?? '-') . ' — ' . ($mapel->nama_mapel ?? '-'),
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