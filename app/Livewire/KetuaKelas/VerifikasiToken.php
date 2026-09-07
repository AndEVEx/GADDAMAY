<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\Rombel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Verifikasi Token')]
class VerifikasiToken extends Component
{
    public string $token = '';
    public ?AgendaHarian $agenda = null;
    public ?Rombel $studentRombel = null;
    public string $errorMessage = '';

    public function mount()
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $user = Auth::user();

        // 1. Determine student's class explicitly from user relation - NO fragile auto-guessing
        $this->studentRombel = $user?->rombel;

        // Auto-close any expired past-due agendas for this class or today
        AgendaHarian::autoCloseExpiredAgendas($this->studentRombel?->id, $today);

        // 2. Find existing active agenda for THIS CLASS ONLY today that still needs photo
        $query = AgendaHarian::whereIn('status', ['token_terverifikasi', 'berjalan'])
            ->where('tanggal', $today)
            ->whereNull('foto_bukti_path')
            ->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru']);

        if ($this->studentRombel) {
            $query->whereHas('jadwalPelajaran', function ($q) {
                $q->where('rombel_id', $this->studentRombel->id);
            });
        }

        $this->agenda = $query->latest('updated_at')->first();
    }

    public function verifikasi()
    {
        $this->errorMessage = '';
        $this->validate(['token' => 'required|digits:6']);

        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $user = Auth::user();

        // Explicit foreign key connection
        $this->studentRombel = $user?->rombel;

        if (!$this->studentRombel) {
            $this->errorMessage = 'Akun Anda belum terhubung dengan kelas manapun. Silakan hubungi Administrator untuk mengatur kelas pada akun Anda.';
            return;
        }

        // Build query for matching 6-digit token handshake for THIS CLASS ONLY
        // Allow matching if status is waiting, already verified, or running
        $query = AgendaHarian::where('token_handshake', $this->token)
            ->where('tanggal', $today)
            ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan']);

        if ($this->studentRombel) {
            $query->whereHas('jadwalPelajaran', function ($q) {
                $q->where('rombel_id', $this->studentRombel->id);
            });
        }

        $this->agenda = $query->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru'])->first();

        if (!$this->agenda) {
            // Check if token exists for ANOTHER class to provide helpful diagnostic error
            $otherClassAgenda = AgendaHarian::where('token_handshake', $this->token)
                ->where('tanggal', $today)
                ->with('jadwalPelajaran.rombel')
                ->first();

            if ($otherClassAgenda) {
                $otherKelasName = $otherClassAgenda->jadwalPelajaran?->rombel?->nama_kelas ?? 'kelas lain';
                $myKelasName = $this->studentRombel?->nama_kelas ?? 'kelas Anda';
                $this->errorMessage = "Token ini adalah untuk kelas {$otherKelasName}, bukan untuk kelas Anda ({$myKelasName}). Mohon minta kode token dari Guru yang mengajar di kelas {$myKelasName}.";
            } else {
                $this->errorMessage = 'Token OTP tidak valid atau belum dibuat oleh guru untuk kelas Anda hari ini.';
            }
            return;
        }

        // Auto-close any previous active agendas for this class from earlier hours (exclude same token / same block)
        if ($this->studentRombel) {
            AgendaHarian::where('id', '!=', $this->agenda->id)
                ->where('tanggal', $today)
                ->where('token_handshake', '!=', $this->token)
                ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan'])
                ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $this->studentRombel->id))
                ->update([
                    'status' => 'selesai',
                    'waktu_selesai' => Carbon::now('Asia/Jakarta'),
                ]);
        }

        // Update status to token_terverifikasi if still waiting
        if ($this->agenda->status === 'menunggu_token') {
            $this->agenda->update([
                'status' => 'token_terverifikasi',
            ]);
        }

        // Also verify sibling agendas (same block, same token)
        if ($this->agenda->jadwalPelajaran?->mapel_id) {
            $jp = $this->agenda->jadwalPelajaran;
            AgendaHarian::where('tanggal', $today)
                ->where('token_handshake', $this->token)
                ->where('status', 'menunggu_token')
                ->where('id', '!=', $this->agenda->id)
                ->whereHas('jadwalPelajaran', fn($q) => $q
                    ->where('rombel_id', $jp->rombel_id)
                    ->where('mapel_id', $jp->mapel_id)
                )
                ->update(['status' => 'token_terverifikasi']);
        }

        $this->dispatch('show-toast', message: 'Verifikasi berhasil! Mengalihkan ke ambil foto...', type: 'success');

        return redirect()->route('ketua.foto', $this->agenda->id);
    }

    public function render()
    {
        return view('livewire.ketua-kelas.verifikasi-token');
    }
}
