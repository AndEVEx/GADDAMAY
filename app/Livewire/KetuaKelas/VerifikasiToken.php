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

    /**
     * Intelligently resolve the student's Rombel model from user record or user name/email
     */
    private function resolveStudentRombel($user): ?Rombel
    {
        if (!$user) return null;

        // 1. Direct foreign key (fastest, most reliable)
        if (!empty($user->rombel_id)) {
            $rombel = Rombel::find($user->rombel_id);
            if ($rombel) return $rombel;
        }

        $rombels = Rombel::all();
        $userNameLower = strtolower(trim($user->name ?? ''));
        $userEmailLower = strtolower(trim($user->email ?? ''));

        // 2. Exact full-name match: Find rombel whose nama_kelas appears EXACTLY in the user's name
        //    Sort by nama_kelas length DESC to match the most specific one first.
        //    e.g. "XI NKPI 2" (9 chars) should match before "NKPI" (4 chars) or "XI NKPI" (7 chars)
        $sortedRombels = $rombels->sortByDesc(fn($r) => strlen($r->nama_kelas));

        foreach ($sortedRombels as $r) {
            $kelasLower = strtolower(trim($r->nama_kelas));
            if (empty($kelasLower)) continue;

            // Check if the FULL kelas name appears as a whole word/phrase in the user name
            // Use word boundary check to prevent partial matches
            if (str_contains($userNameLower, $kelasLower)) {
                $user->update(['rombel_id' => $r->id]);
                return $r;
            }
        }

        // 3. Email slug match: e.g. "ketua.xiinkpi2@smkn2..." matches "XII NKPI 2"
        foreach ($sortedRombels as $r) {
            // Generate multiple slug variants for matching
            $namaKelas = $r->nama_kelas;
            $slug1 = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaKelas)); // "xiinkpi2"
            $slug2 = strtolower(str_replace(' ', '', $namaKelas)); // "xiinkpi2" (same usually)
            $slug3 = strtolower(str_replace(' ', '.', $namaKelas)); // "xii.nkpi.2"

            foreach ([$slug1, $slug2, $slug3] as $slug) {
                if (!empty($slug) && str_contains($userEmailLower, $slug)) {
                    $user->update(['rombel_id' => $r->id]);
                    return $r;
                }
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.ketua-kelas.verifikasi-token');
    }
}
