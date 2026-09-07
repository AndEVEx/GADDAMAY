<?php

namespace App\Livewire\Guru\Performa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use App\Models\HariLibur;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Realisasi Jam Masuk Kelas Bulanan')]
class KehadiranBulanan extends Component
{
    public string $bulan; // Format: 'Y-m'

    public function mount()
    {
        $this->bulan = Carbon::now('Asia/Jakarta')->format('Y-m');
    }

    public function render()
    {
        $user = auth()->user();
        $startOfMonth = Carbon::parse($this->bulan)->startOfMonth();
        $endOfMonth = Carbon::parse($this->bulan)->endOfMonth();
        $namaBulan = Carbon::parse($this->bulan)->translatedFormat('F Y');

        // 1. Ambil jadwal guru mingguan
        $jadwals = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->with(['rombel', 'mataPelajaran'])
            ->get();

        // 2. Hitung TARGET JAM (JP) semestinya di bulan terpilih
        // Iterasi setiap hari di bulan tersebut (Senin - Jumat)
        $targetJpTotal = 0;
        $targetPertemuanTotal = 0;
        $targetPerMapelKelas = []; // key: rombel_id_mapel_id => target_jp

        // Inisialisasi counter per rombel+mapel
        foreach ($jadwals as $j) {
            $k = $j->rombel_id . '_' . $j->mapel_id;
            if (!isset($targetPerMapelKelas[$k])) {
                $targetPerMapelKelas[$k] = [
                    'rombel_id' => $j->rombel_id,
                    'rombel_nama' => $j->rombel?->nama_kelas ?? ($j->kegiatan_khusus ?? 'Non-Kelas'),
                    'mapel_id' => $j->mapel_id,
                    'mapel_nama' => $j->mataPelajaran?->nama_mapel ?? ($j->kegiatan_khusus ?? 'Kegiatan Khusus'),
                    'target_jp' => 0,
                    'target_pertemuan' => 0,
                    'realisasi_jp' => 0,
                    'realisasi_pertemuan' => 0,
                    'izin_jp' => 0,
                    'sakit_jp' => 0,
                ];
            }
        }

        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dayOfWeek = $currentDate->dayOfWeekIso; // 1 = Senin ... 7 = Minggu
            
            // Periksa apakah hari kerja (1-5) dan bukan hari libur
            if ($dayOfWeek <= 5) {
                $isLibur = HariLibur::isHariLibur($currentDate);
                if (!$isLibur) {
                    $jadwalsToday = $jadwals->where('hari', $dayOfWeek);
                    foreach ($jadwalsToday as $jt) {
                        $jp = max(1, (int)$jt->jam_ke_selesai - (int)$jt->jam_ke_mulai + 1);
                        $targetJpTotal += $jp;
                        $targetPertemuanTotal++;

                        $k = $jt->rombel_id . '_' . $jt->mapel_id;
                        if (isset($targetPerMapelKelas[$k])) {
                            $targetPerMapelKelas[$k]['target_jp'] += $jp;
                            $targetPerMapelKelas[$k]['target_pertemuan']++;
                        }
                    }
                }
            }
            $currentDate->addDay();
        }

        // 3. Ambil REALISASI AGENDA HARIAN guru di bulan tersebut
        $agendas = AgendaHarian::where(function ($q) use ($user) {
                $q->where('guru_id', $user->id)
                  ->orWhere('guru_pengganti_id', $user->id);
            })
            ->whereBetween('tanggal', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $realisasiJpTotal = 0;
        $realisasiPertemuanTotal = 0;
        $totalIzinJp = 0;
        $totalSakitJp = 0;

        foreach ($agendas as $agenda) {
            $jp = 1;
            if ($agenda->jadwalPelajaran) {
                $jp = max(1, (int)$agenda->jadwalPelajaran->jam_ke_selesai - (int)$agenda->jadwalPelajaran->jam_ke_mulai + 1);
            }
            $agenda->durasi_jp = $jp;

            $k = ($agenda->jadwalPelajaran?->rombel_id ?? '') . '_' . ($agenda->jadwalPelajaran?->mapel_id ?? '');

            if (in_array($agenda->status, ['selesai', 'berjalan', 'token_terverifikasi']) && $agenda->status_kehadiran_guru === 'hadir') {
                $realisasiJpTotal += $jp;
                $realisasiPertemuanTotal++;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['realisasi_jp'] += $jp;
                    $targetPerMapelKelas[$k]['realisasi_pertemuan']++;
                }
            } elseif (in_array($agenda->status_kehadiran_guru, ['izin', 'cuti', 'dinas', 'tugas_luar'])) {
                $totalIzinJp += $jp;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['izin_jp'] += $jp;
                }
            } elseif ($agenda->status_kehadiran_guru === 'sakit') {
                $totalSakitJp += $jp;
                if (isset($targetPerMapelKelas[$k])) {
                    $targetPerMapelKelas[$k]['sakit_jp'] += $jp;
                }
            }
        }

        // Persentase Kinerja
        $persentaseKehadiran = $targetJpTotal > 0 ? round(($realisasiJpTotal / $targetJpTotal) * 100, 1) : 0;

        // Breakdown array
        $rekapKelas = collect($targetPerMapelKelas)->map(function ($item) {
            $target = $item['target_jp'];
            $real = $item['realisasi_jp'];
            $pct = $target > 0 ? round(($real / $target) * 100, 1) : 0;
            $selisih = $real - $target;

            return (object) array_merge($item, [
                'persentase' => $pct,
                'selisih_jp' => $selisih,
            ]);
        })->values();

        return view('livewire.guru.performa.kehadiran-bulanan', [
            'namaBulan' => $namaBulan,
            'targetJpTotal' => $targetJpTotal,
            'realisasiJpTotal' => $realisasiJpTotal,
            'targetPertemuanTotal' => $targetPertemuanTotal,
            'realisasiPertemuanTotal' => $realisasiPertemuanTotal,
            'totalIzinJp' => $totalIzinJp,
            'totalSakitJp' => $totalSakitJp,
            'persentaseKehadiran' => $persentaseKehadiran,
            'rekapKelas' => $rekapKelas,
            'agendas' => $agendas,
        ]);
    }
}