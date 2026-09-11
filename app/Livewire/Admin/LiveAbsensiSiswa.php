<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Live Absensi Siswa')]
class LiveAbsensiSiswa extends Component
{
    public string $tanggal = '';
    public ?string $selectedRombelId = null;
    public string $filterStatus = 'all'; // 'all', 'bermasalah', 'alpa', 'izin', 'sakit', 'hadir_penuh'
    public string $search = '';
    public string $filterTingkat = 'all'; // 'all', '10', '11', '12'

    public function mount()
    {
        $this->tanggal = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        
        $rombels = $this->getRombels();
        if ($rombels->isNotEmpty()) {
            $this->selectedRombelId = $rombels->first()->id;
        }
    }

    public function selectRombel(string $rombelId)
    {
        $this->selectedRombelId = $rombelId;
    }

    public function getRombels()
    {
        $query = Rombel::query();

        if ($this->filterTingkat !== 'all') {
            $query->where('tingkat', $this->filterTingkat);
        }

        return $query->orderBy('tingkat')->orderBy('nama_kelas')->get();
    }

    public function render()
    {
        $allRombels = $this->getRombels();

        // Pastikan selectedRombelId valid
        if ($this->selectedRombelId && !$allRombels->contains('id', $this->selectedRombelId)) {
            $this->selectedRombelId = $allRombels->first()?->id;
        }

        $selectedRombel = $this->selectedRombelId ? Rombel::find($this->selectedRombelId) : null;

        // 1. Ambil Sesi Agenda Hari Ini untuk Rombel Terpilih
        $agendas = collect();
        if ($selectedRombel) {
            $targetRombelIds = [$selectedRombel->id];
            $partner = $selectedRombel->getPartnerBlockRombel();
            if ($partner) {
                $targetRombelIds[] = $partner->id;
            }

            $agendas = AgendaHarian::whereDate('tanggal', $this->tanggal)
                ->whereHas('jadwalPelajaran', fn($q) => $q->whereIn('rombel_id', $targetRombelIds))
                ->with([
                    'jadwalPelajaran.mataPelajaran',
                    'jadwalPelajaran.rombel',
                    'jadwalPelajaran.jamMulai',
                    'jadwalPelajaran.jamSelesai',
                    'guru',
                    'guruPengganti',
                    'kehadiranMurid',
                ])
                ->orderBy('waktu_mulai')
                ->get();
        }

        // 2. Ambil Siswa di Rombel Terpilih
        $siswaList = collect();
        if ($selectedRombel) {
            $siswaQuery = Siswa::where('rombel_id', $selectedRombel->id);
            if (!empty($this->search)) {
                $s = '%' . trim($this->search) . '%';
                $siswaQuery->where(function ($q) use ($s) {
                    $q->where('nama', 'like', $s)->orWhere('nis', 'like', $s);
                });
            }
            $siswaList = $siswaQuery->orderBy('nama')->get();
        }

        // 3. Bangun Matriks Kehadiran Per Siswa
        $siswaMatrix = $siswaList->map(function ($siswa) use ($agendas) {
            $sessionStatuses = [];
            $counts = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'belum' => 0];

            foreach ($agendas as $agenda) {
                $record = $agenda->kehadiranMurid->firstWhere('siswa_id', $siswa->id);
                $status = $record ? $record->status : 'belum';
                $sessionStatuses[$agenda->id] = $status;

                if (isset($counts[$status])) {
                    $counts[$status]++;
                }
            }

            // Hitung Kesimpulan Hari Ini
            $totalSesi = count($agendas);
            $summaryStatus = 'belum_ada_sesi';
            $summaryBadgeClass = 'bg-secondary';
            $summaryLabel = 'Belum Ada Sesi';

            if ($totalSesi > 0) {
                if ($counts['alpa'] > 0) {
                    if ($counts['hadir'] > 0 || $counts['izin'] > 0 || $counts['sakit'] > 0) {
                        $summaryStatus = 'cabut';
                        $summaryBadgeClass = 'bg-danger text-white';
                        $summaryLabel = 'Bolos Jam (' . $counts['alpa'] . ' Sesi)';
                    } else {
                        $summaryStatus = 'alpa';
                        $summaryBadgeClass = 'bg-danger';
                        $summaryLabel = 'Alpa Penuh';
                    }
                } elseif ($counts['hadir'] === $totalSesi) {
                    $summaryStatus = 'hadir_penuh';
                    $summaryBadgeClass = 'bg-success';
                    $summaryLabel = 'Hadir Penuh';
                } elseif ($counts['sakit'] > 0 && $counts['hadir'] === 0 && $counts['izin'] === 0) {
                    $summaryStatus = 'sakit';
                    $summaryBadgeClass = 'bg-primary';
                    $summaryLabel = 'Sakit';
                } elseif ($counts['izin'] > 0 && $counts['hadir'] === 0 && $counts['sakit'] === 0) {
                    $summaryStatus = 'izin';
                    $summaryBadgeClass = 'bg-warning text-dark';
                    $summaryLabel = 'Izin';
                } else {
                    $summaryStatus = 'sebagian';
                    $summaryBadgeClass = 'bg-info text-dark';
                    $summaryLabel = 'Sebagian Hadir';
                }
            }

            return (object) [
                'siswa' => $siswa,
                'sessions' => $sessionStatuses,
                'counts' => $counts,
                'summaryStatus' => $summaryStatus,
                'summaryBadgeClass' => $summaryBadgeClass,
                'summaryLabel' => $summaryLabel,
                'isProblematic' => ($summaryStatus === 'cabut' || $summaryStatus === 'alpa'),
            ];
        });

        // Filter status jika dipilih
        if ($this->filterStatus !== 'all') {
            $siswaMatrix = $siswaMatrix->filter(function ($item) {
                return match ($this->filterStatus) {
                    'bermasalah' => $item->isProblematic,
                    'alpa' => in_array($item->summaryStatus, ['alpa', 'cabut']),
                    'izin' => $item->summaryStatus === 'izin',
                    'sakit' => $item->summaryStatus === 'sakit',
                    'hadir_penuh' => $item->summaryStatus === 'hadir_penuh',
                    default => true,
                };
            });
        }

        // 4. Hitung Statistik Global Sekolah Hari Ini
        $todayAttendance = KehadiranMurid::whereHas('agendaHarian', fn($q) => $q->whereDate('tanggal', $this->tanggal))->get();
        $totalPresensi = $todayAttendance->count();
        $globalHadir = $todayAttendance->where('status', 'hadir')->count();
        $globalSakit = $todayAttendance->where('status', 'sakit')->count();
        $globalIzin = $todayAttendance->where('status', 'izin')->count();
        $globalAlpa = $todayAttendance->where('status', 'alpa')->count();

        $persenHadir = $totalPresensi > 0 ? round(($globalHadir / $totalPresensi) * 100, 1) : 0;

        return view('livewire.admin.live-absensi-siswa', [
            'allRombels' => $allRombels,
            'selectedRombel' => $selectedRombel,
            'agendas' => $agendas,
            'siswaMatrix' => $siswaMatrix,
            'stats' => [
                'totalPresensi' => $totalPresensi,
                'hadir' => $globalHadir,
                'sakit' => $globalSakit,
                'izin' => $globalIzin,
                'alpa' => $globalAlpa,
                'persenHadir' => $persenHadir,
            ],
        ]);
    }
}
