<?php

namespace App\Livewire\KetuaKelas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Verifikasi Token')]
class VerifikasiToken extends Component
{
    public string $token = '';
    public ?AgendaHarian $agenda = null;
    public string $errorMessage = '';

    public function verifikasi()
    {
        $this->errorMessage = '';
        $this->validate(['token' => 'required|digits:6']);

        $this->agenda = AgendaHarian::where('token_handshake', $this->token)
            ->where('status', 'menunggu_token')
            ->where('tanggal', Carbon::today('Asia/Jakarta')->format('Y-m-d'))
            ->with(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'guru'])
            ->first();

        if (!$this->agenda) {
            $this->errorMessage = 'Token tidak valid atau sudah kadaluarsa.';
            return;
        }

        // Update status to token_terverifikasi
        $this->agenda->update([
            'status' => 'token_terverifikasi',
        ]);

        $this->dispatch('show-toast', message: 'Verifikasi berhasil! Mengalihkan ke ambil foto...', type: 'success');

        return redirect()->route('ketua.foto', $this->agenda->id);
    }

    public function render()
    {
        return view('livewire.ketua-kelas.verifikasi-token');
    }
}
