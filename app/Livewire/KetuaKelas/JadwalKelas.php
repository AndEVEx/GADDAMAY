<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Jadwal Pelajaran Kelas')]
class JadwalKelas extends Component
{
    public ?Rombel $studentRombel = null;
    public int $selectedHari = 1;

    public function mount()
    {
        $user = Auth::user();
        $this->studentRombel = $user?->rombel;

        $dayOfWeek = Carbon::now('Asia/Jakarta')->dayOfWeekIso;
        // If weekend (Sabtu=6, Minggu=7), default to Senin (1), else current day
        $this->selectedHari = in_array($dayOfWeek, [1, 2, 3, 4, 5]) ? $dayOfWeek : 1;
    }

    public function setHari(int $hari)
    {
        $this->selectedHari = $hari;
    }

    public function render()
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $currentDayOfWeek = Carbon::now('Asia/Jakarta')->dayOfWeekIso;
        $isHariIni = ($this->selectedHari === $currentDayOfWeek);

        $jadwalList = collect();

        if ($this->studentRombel) {
            $rawJadwal = JadwalPelajaran::where('rombel_id', $this->studentRombel->id)
                ->where('hari', $this->selectedHari)
                ->with(['mataPelajaran', 'jadwalGuru.guru', 'agendaHarian' => function ($q) use ($today) {
                    $q->where('tanggal', $today);
                }])
                ->orderBy('jam_ke_mulai')
                ->get();

            $jadwalList = $rawJadwal->map(function ($j) use ($isHariIni) {
                $agenda = $j->agendaHarian->first();
                $range = $j->getEffectiveTimeRange();

                $statusLabel = 'Terjadwal';
                $statusBadge = 'bg-secondary bg-opacity-10 text-secondary';
                $statusIcon = 'bi-calendar-check';

                if ($isHariIni) {
                    if ($agenda) {
                        $agendaStatus = $agenda->status;
                        $isIzin = in_array($agenda->status_kehadiran_guru ?? '', ['izin', 'cuti', 'sakit', 'dinas', 'tugas_luar']);

                        if ($isIzin) {
                            $statusLabel = 'Guru Izin (' . ucfirst($agenda->status_kehadiran_guru) . ')';
                            $statusBadge = 'text-white';
                            $statusIcon = 'bi-info-circle-fill';
                        } elseif ($agendaStatus === 'selesai') {
                            $statusLabel = 'Selesai';
                            $statusBadge = 'bg-success bg-opacity-10 text-success';
                            $statusIcon = 'bi-check-circle-fill';
                        } elseif ($agendaStatus === 'berjalan') {
                            $statusLabel = 'Sedang Berlangsung';
                            $statusBadge = 'bg-primary bg-opacity-10 text-primary';
                            $statusIcon = 'bi-play-circle-fill';
                        } elseif (in_array($agendaStatus, ['menunggu_token', 'token_terverifikasi'])) {
                            $statusLabel = 'Proses Masuk / OTP';
                            $statusBadge = 'bg-warning bg-opacity-10 text-warning';
                            $statusIcon = 'bi-hourglass-split';
                        }
                    } elseif ($j->isKegiatanKhusus()) {
                        $statusLabel = 'Kegiatan Khusus';
                        $statusBadge = 'bg-secondary bg-opacity-10 text-secondary';
                        $statusIcon = 'bi-flag';
                    } elseif ($j->hasPeriodEnded()) {
                        $statusLabel = 'Sudah Lewat';
                        $statusBadge = 'bg-light text-muted border';
                        $statusIcon = 'bi-slash-circle';
                    } else {
                        $statusLabel = 'Belum Mulai';
                        $statusBadge = 'bg-light text-dark border';
                        $statusIcon = 'bi-clock';
                    }
                }

                return (object) [
                    'id' => $j->id,
                    'jam_ke_mulai' => $j->jam_ke_mulai,
                    'jam_ke_selesai' => $j->jam_ke_selesai,
                    'jam_display' => $j->jam_ke_mulai === $j->jam_ke_selesai ? 'Jam ' . $j->jam_ke_mulai : 'Jam ' . $j->jam_ke_mulai . ' - ' . $j->jam_ke_selesai,
                    'waktu_mulai' => $range['waktu_mulai'],
                    'waktu_selesai' => $range['waktu_selesai'],
                    'waktu_display' => $range['waktu_mulai'] . ' - ' . $range['waktu_selesai'] . ' WIB',
                    'mapel_nama' => $j->isKegiatanKhusus() ? $j->kegiatan_khusus : ($j->mataPelajaran?->nama_mapel ?? '-'),
                    'kode_mapel' => $j->mataPelajaran?->kode_mapel,
                    'guru_nama' => $j->jadwalGuru->pluck('guru.name')->filter()->join(', ') ?: '-',
                    'is_kegiatan_khusus' => $j->isKegiatanKhusus(),
                    'kegiatan_khusus' => $j->kegiatan_khusus,
                    'status_label' => $statusLabel,
                    'status_badge' => $statusBadge,
                    'status_icon' => $statusIcon,
                    'agenda' => $agenda,
                ];
            });
        }

        $hariLabels = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];

        return view('livewire.ketua-kelas.jadwal-kelas', [
            'jadwalList' => $jadwalList,
            'hariLabels' => $hariLabels,
            'currentDayOfWeek' => $currentDayOfWeek,
            'isHariIni' => $isHariIni,
        ]);
    }
}
