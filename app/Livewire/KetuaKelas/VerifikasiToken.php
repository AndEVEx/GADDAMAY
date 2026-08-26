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

        // 1. Determine student's class (rombel)
        $this->studentRombel = $this->resolveStudentRombel($user);

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

        if (!$this->studentRombel) {
            $this->studentRombel = $this->resolveStudentRombel($user);
        }

        // Auto-close any expired past-due agendas first
        AgendaHarian::autoCloseExpiredAgendas($this->studentRombel?->id, $today);

        // Build query for matching 6-digit token handshake for THIS CLASS ONLY
        $query = AgendaHarian::where('token_handshake', $this->token)
            ->where('status', 'menunggu_token')
            ->where('tanggal', $today);

        if ($this->studentRombel) {
            $query->whereHas('jadwalPelajaran', function ($q) {
                $q->where('rombel_id', $this->studentRombel->id);
            });
        }

        $this->agenda = $query->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru'])->first();

        if (!$this->agenda) {
            // Check if token exists for ANOTHER class to provide helpful diagnostic error
            $otherClassAgenda = AgendaHarian::where('token_handshake', $this->token)
                ->where('status', 'menunggu_token')
                ->where('tanggal', $today)
                ->with('jadwalPelajaran.rombel')
                ->first();

            if ($otherClassAgenda) {
                $otherKelasName = $otherClassAgenda->jadwalPelajaran?->rombel?->nama_kelas ?? 'kelas lain';
                $myKelasName = $this->studentRombel?->nama_kelas ?? 'kelas Anda';
                $this->errorMessage = "Token ini adalah untuk kelas {$otherKelasName}, bukan untuk kelas Anda ({$myKelasName}). Mohon minta kode token dari Guru yang mengajar di kelas {$myKelasName}.";
            } else {
                $this->errorMessage = 'Token OTP tidak valid, belum dibuat oleh guru, atau sudah kadaluarsa.';
            }
            return;
        }

        // Auto-close any previous active agendas for this class from earlier hours
        if ($this->studentRombel) {
            AgendaHarian::where('id', '!=', $this->agenda->id)
                ->where('tanggal', $today)
                ->whereIn('status', ['menunggu_token', 'token_terverifikasi', 'berjalan'])
                ->whereHas('jadwalPelajaran', fn($q) => $q->where('rombel_id', $this->studentRombel->id))
                ->update([
                    'status' => 'selesai',
                    'waktu_selesai' => Carbon::now('Asia/Jakarta'),
                ]);
        }

        // Update status to token_terverifikasi
        $this->agenda->update([
            'status' => 'token_terverifikasi',
        ]);

        $this->dispatch('show-toast', message: 'Verifikasi berhasil! Mengalihkan ke ambil foto...', type: 'success');

        return redirect()->route('ketua.foto', $this->agenda->id);
    }

    /**
     * Intelligently resolve the student's Rombel model from user record or user name/email
     */
    private function resolveStudentRombel($user): ?Rombel
    {
        if (!$user) return null;

        // Direct foreign key
        if (!empty($user->rombel_id)) {
            $rombel = Rombel::find($user->rombel_id);
            if ($rombel) return $rombel;
        }

        // Fallback 1: Match by name ("Ketua Kelas [nama_kelas]")
        $rombels = Rombel::all();
        $userNameLower = strtolower($user->name ?? '');

        foreach ($rombels as $r) {
            $kelasLower = strtolower($r->nama_kelas);
            if (!empty($kelasLower) && str_contains($userNameLower, $kelasLower)) {
                // Auto-save resolved rombel_id for future fast lookups
                $user->update(['rombel_id' => $r->id]);
                return $r;
            }
        }

        // Fallback 2: Match by email slug ("ketua.[slug]@smkn2indramayu.sch.id")
        $userEmailLower = strtolower($user->email ?? '');
        foreach ($rombels as $r) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $r->nama_kelas));
            if (!empty($slug) && str_contains($userEmailLower, "ketua.{$slug}@")) {
                $user->update(['rombel_id' => $r->id]);
                return $r;
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.ketua-kelas.verifikasi-token');
    }
}
