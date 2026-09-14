<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Mulai Kelas')]
class MulaiKelas extends Component
{
    public JadwalPelajaran $jadwal;
    public ?AgendaHarian $agenda = null;
    public string $token = '';
    public string $alasan_terlambat = '';

    public function mount(JadwalPelajaran $jadwal)
    {
        $this->jadwal = $jadwal->load(['rombel', 'mataPelajaran', 'jadwalGuru.guru']);
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        // Check if agenda already exists for this jadwal today
        $this->agenda = AgendaHarian::where('jadwal_pelajaran_id', $jadwal->id)
            ->where('guru_id', auth()->id())
            ->where('tanggal', $today)
            ->first();

        // If no agenda for this jadwal, check if there's one for a sibling jadwal
        // (same rombel + same mapel + same day = merged teaching block)
        if (!$this->agenda && $jadwal->mapel_id) {
            $siblingJadwals = JadwalPelajaran::where('hari', $jadwal->hari)
                ->where('rombel_id', $jadwal->rombel_id)
                ->where('mapel_id', $jadwal->mapel_id)
                ->where('id', '!=', $jadwal->id)
                ->pluck('id');

            if ($siblingJadwals->isNotEmpty()) {
                $siblingAgenda = AgendaHarian::whereIn('jadwal_pelajaran_id', $siblingJadwals)
                    ->where('guru_id', auth()->id())
                    ->where('tanggal', $today)
                    ->latest()
                    ->first();

                if ($siblingAgenda) {
                    $this->agenda = $siblingAgenda;
                }
            }
        }

        // Auto-close any expired agendas whose periods have ended
        AgendaHarian::autoCloseExpiredAgendas(null, $today);

        $timeNow = Carbon::now('Asia/Jakarta')->format('H:i');
        if ($this->jadwal->hasPeriodEnded($timeNow)) {
            // If class was not handshaked during its period, deny access
            if (!$this->agenda || $this->agenda->status === 'menunggu_token') {
                $range = $this->jadwal->getEffectiveTimeRange();
                session()->flash('error', "Jam pelajaran telah berakhir pada pukul {$range['waktu_selesai']}. Handshake hanya dapat dilaksanakan selama jam pelajaran berlangsung.");
                return redirect()->route('guru.dashboard');
            }
        }

        if ($this->agenda) {
            $this->alasan_terlambat = $this->agenda->alasan_terlambat ?? '';

            // If already verified or running, automatically forward to the next step!
            if ($this->agenda->status === 'token_terverifikasi') {
                return redirect()->route('guru.materi', $this->agenda->id);
            }
            if ($this->agenda->status === 'berjalan') {
                if (empty($this->agenda->materi_diajarkan)) {
                    return redirect()->route('guru.materi', $this->agenda->id);
                }
                if (empty($this->agenda->foto_guru_path)) {
                    return redirect()->route('guru.foto-guru', $this->agenda->id);
                }
                if (!$this->agenda->kehadiranMurid()->exists()) {
                    return redirect()->route('guru.kehadiran', $this->agenda->id);
                }
                return redirect()->route('guru.stopwatch', $this->agenda->id);
            }
            $this->token = $this->agenda->token_handshake ?? '';
        }
    }

    public function generateToken()
    {
        $timeNow = Carbon::now('Asia/Jakarta')->format('H:i');
        $range = $this->jadwal->getEffectiveTimeRange();

        if ($timeNow < $range['waktu_mulai']) {
            $this->dispatch('show-toast', message: "Jam pelajaran belum dimulai (mulai pukul {$range['waktu_mulai']}).", type: 'warning');
            return;
        }

        if ($this->jadwal->hasPeriodEnded($timeNow)) {
            $this->dispatch('show-toast', message: "Jam pelajaran telah berakhir pada pukul {$range['waktu_selesai']}. Handshake tidak dapat dilaksanakan.", type: 'danger');
            return;
        }

        $tanggal = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        $this->agenda = AgendaHarian::updateOrCreate(
            [
                'jadwal_pelajaran_id' => $this->jadwal->id,
                'guru_id' => auth()->id(),
                'tanggal' => $tanggal,
            ],
            [
                'token_handshake' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'waktu_mulai' => Carbon::now('Asia/Jakarta'),
                'status' => 'menunggu_token',
                'status_kehadiran_guru' => 'hadir',
                'alasan_terlambat' => !empty(trim($this->alasan_terlambat)) ? trim($this->alasan_terlambat) : null,
            ]
        );

        $this->token = $this->agenda->token_handshake;

        // Auto-create linked agendas for sibling jadwals (same block split by breaks)
        if ($this->jadwal->mapel_id) {
            $siblingJadwals = JadwalPelajaran::where('hari', $this->jadwal->hari)
                ->where('rombel_id', $this->jadwal->rombel_id)
                ->where('mapel_id', $this->jadwal->mapel_id)
                ->where('id', '!=', $this->jadwal->id)
                ->get();

            foreach ($siblingJadwals as $sibling) {
                AgendaHarian::updateOrCreate(
                    [
                        'jadwal_pelajaran_id' => $sibling->id,
                        'guru_id' => auth()->id(),
                        'tanggal' => $tanggal,
                    ],
                    [
                        'token_handshake' => $this->agenda->token_handshake,
                        'waktu_mulai' => $this->agenda->waktu_mulai,
                        'status' => 'menunggu_token',
                        'status_kehadiran_guru' => 'hadir',
                        'alasan_terlambat' => !empty(trim($this->alasan_terlambat)) ? trim($this->alasan_terlambat) : null,
                    ]
                );
            }
        }

        $this->dispatch('show-toast', message: 'Token OTP berhasil dibuat!', type: 'success');
    }

    public function updatedAlasanTerlambat()
    {
        if ($this->agenda) {
            $val = !empty(trim($this->alasan_terlambat)) ? trim($this->alasan_terlambat) : null;
            $this->agenda->update(['alasan_terlambat' => $val]);

            // Also update sibling agendas
            if ($this->jadwal->mapel_id) {
                $siblingJadwalIds = JadwalPelajaran::where('hari', $this->jadwal->hari)
                    ->where('rombel_id', $this->jadwal->rombel_id)
                    ->where('mapel_id', $this->jadwal->mapel_id)
                    ->where('id', '!=', $this->jadwal->id)
                    ->pluck('id');

                AgendaHarian::whereIn('jadwal_pelajaran_id', $siblingJadwalIds)
                    ->where('guru_id', auth()->id())
                    ->where('tanggal', $this->agenda->tanggal)
                    ->update(['alasan_terlambat' => $val]);
            }
            $this->dispatch('show-toast', message: 'Keterangan alasan disimpan.', type: 'info');
        }
    }

    public function refreshStatus()
    {
        if ($this->agenda) {
            $this->agenda->refresh();

            if (in_array($this->agenda->status, ['token_terverifikasi', 'berjalan'])) {
                return redirect()->route('guru.materi', $this->agenda->id);
            }
        }
    }

    public function batalkanAgenda()
    {
        if ($this->agenda) {
            $this->agenda->delete();
            $this->dispatch('show-toast', message: 'Sesi agenda berhasil dibatalkan!', type: 'info');
        }
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        return view('livewire.guru.mulai-kelas');
    }
}
