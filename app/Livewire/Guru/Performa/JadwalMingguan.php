<?php

namespace App\Livewire\Guru\Performa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\User;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Jam Mengajar Guru Dalam Seminggu')]
class JadwalMingguan extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Ambil seluruh jadwal yang diampu guru
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_ke_mulai')
            ->get();

        // Hitung durasi JP dan waktu
        $officialPeriods = JadwalPelajaran::$officialPeriods ?? [
            0  => ['mulai' => '06:25', 'selesai' => '06:45'],
            1  => ['mulai' => '06:45', 'selesai' => '07:30'],
            2  => ['mulai' => '07:30', 'selesai' => '08:15'],
            3  => ['mulai' => '08:15', 'selesai' => '09:00'],
            4  => ['mulai' => '09:00', 'selesai' => '09:45'],
            5  => ['mulai' => '09:45', 'selesai' => '10:00'],
            6  => ['mulai' => '10:00', 'selesai' => '10:45'],
            7  => ['mulai' => '10:45', 'selesai' => '11:30'],
            8  => ['mulai' => '11:30', 'selesai' => '12:15'],
            9  => ['mulai' => '12:15', 'selesai' => '12:45'],
            10 => ['mulai' => '12:45', 'selesai' => '13:30'],
            11 => ['mulai' => '13:30', 'selesai' => '14:15'],
            12 => ['mulai' => '14:15', 'selesai' => '15:00'],
        ];

        $jadwalList = $jadwals->map(function ($j) use ($officialPeriods) {
            $startKey = (int) $j->jam_ke_mulai;
            $endKey = (int) $j->jam_ke_selesai;
            $totalJp = max(1, $endKey - $startKey + 1);

            $waktuMulai = $officialPeriods[$startKey]['mulai'] ?? '06:45';
            $waktuSelesai = $officialPeriods[$endKey]['selesai'] ?? '15:00';

            return (object) [
                'id' => $j->id,
                'hari_num' => (int) $j->hari,
                'hari_label' => $j->hari_label,
                'jam_ke_mulai' => $startKey,
                'jam_ke_selesai' => $endKey,
                'jam_range' => $startKey === $endKey ? "Jam ke-{$startKey}" : "Jam ke-{$startKey} s.d {$endKey}",
                'waktu_range' => "{$waktuMulai} - {$waktuSelesai}",
                'total_jp' => $totalJp,
                'rombel' => $j->rombel?->nama_kelas ?? ($j->kegiatan_khusus ?? 'Non-Kelas'),
                'mapel' => $j->mataPelajaran?->nama_mapel ?? ($j->kegiatan_khusus ?? 'Kegiatan Khusus'),
                'kode_mapel' => $j->mataPelajaran?->kode_mapel ?? '-',
                'keterangan' => $j->keterangan,
            ];
        });

        // Grouping per hari
        $jadwalPerHari = $jadwalList->groupBy('hari_num');

        // Statistik summary
        $totalJpSeminggu = $jadwalList->sum('total_jp');
        $totalRombelUnik = $jadwals->pluck('rombel_id')->filter()->unique()->count();
        $totalMapelUnik = $jadwals->pluck('mapel_id')->filter()->unique()->count();

        // Rincian JP per Mapel
        $rekapPerMapel = $jadwalList->groupBy('mapel')->map(function ($items, $mapel) {
            return (object) [
                'mapel' => $mapel,
                'total_jp' => $items->sum('total_jp'),
                'kelas_count' => $items->pluck('rombel')->unique()->count(),
            ];
        })->values();

        // Rincian JP per Kelas
        $rekapPerKelas = $jadwalList->groupBy('rombel')->map(function ($items, $kelas) {
            return (object) [
                'kelas' => $kelas,
                'total_jp' => $items->sum('total_jp'),
                'mapel_list' => $items->pluck('mapel')->unique()->implode(', '),
            ];
        })->values();

        return view('livewire.guru.performa.jadwal-mingguan', [
            'jadwalList' => $jadwalList,
            'jadwalPerHari' => $jadwalPerHari,
            'totalJpSeminggu' => $totalJpSeminggu,
            'totalRombelUnik' => $totalRombelUnik,
            'totalMapelUnik' => $totalMapelUnik,
            'rekapPerMapel' => $rekapPerMapel,
            'rekapPerKelas' => $rekapPerKelas,
        ]);
    }
}