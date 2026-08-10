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

    public function mount(JadwalPelajaran $jadwal)
    {
        $this->jadwal = $jadwal->load(['rombel', 'mataPelajaran', 'jadwalGuru.guru']);

        // Check if agenda already exists for today
        $this->agenda = AgendaHarian::where('jadwal_pelajaran_id', $jadwal->id)
            ->where('guru_id', auth()->id())
            ->where('tanggal', Carbon::today('Asia/Jakarta')->format('Y-m-d'))
            ->first();

        if ($this->agenda) {
            $this->token = $this->agenda->token_handshake ?? '';
        }
    }

    public function generateToken()
    {
        $tanggal = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        $this->agenda = AgendaHarian::updateOrCreate(
            [
                'jadwal_pelajaran_id' => $this->jadwal->id,
                'guru_id' => auth()->id(),
                'tanggal' => $tanggal,
            ],
            [
                'token_handshake' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'status' => 'menunggu_token',
                'status_kehadiran_guru' => 'hadir',
            ]
        );

        $this->token = $this->agenda->token_handshake;

        $this->dispatch('show-toast', message: 'Token OTP berhasil dibuat!', type: 'success');
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
