<?php

namespace App\Livewire\Tefa\Siswa;

use App\Models\Siswa;
use App\Models\TefaLogProduksi;
use App\Models\TefaOrder;
use App\Models\TefaTimKerja;
use App\Services\Tefa\TefaOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class LogProduksiTefa extends Component
{
    use WithFileUploads;

    public $siswaId;
    public $selectedOrderId;

    // Form Logsheet
    public $tanggal;
    public $jamMulai = '08:00';
    public $jamSelesai = '11:00';
    public $ringkasanPekerjaan;
    public $alatDanBahan;
    public $fotoProgres;

    public function mount()
    {
        $this->tanggal = Carbon::today()->toDateString();

        $user = Auth::user();
        if ($user && $user->role === 'siswa') {
            $siswa = Siswa::where('email', $user->email)->orWhere('nis', $user->username)->first();
            $this->siswaId = $siswa?->id;
        }

        if (!$this->siswaId) {
            $firstTim = TefaTimKerja::first();
            $this->siswaId = $firstTim?->siswa_id ?: Siswa::first()?->id;
        }

        $activeOrders = $this->getMyOrders();
        $this->selectedOrderId = $activeOrders->first()?->id;
    }

    public function getMyOrders()
    {
        return TefaOrder::whereHas('timKerja', function ($q) {
            $q->where('siswa_id', $this->siswaId);
        })->orWhere('status', 'dalam_produksi')->latest()->get();
    }

    public function simpanLog()
    {
        $this->validate([
            'selectedOrderId' => 'required',
            'tanggal' => 'required|date',
            'jamMulai' => 'required',
            'jamSelesai' => 'required',
            'ringkasanPekerjaan' => 'required|string|min:10',
        ]);

        $durasiMenit = TefaOrderService::calculateDurationMinutes($this->jamMulai, $this->jamSelesai);

        $fotoPath = null;
        if ($this->fotoProgres) {
            $fotoPath = $this->fotoProgres->store('uploads/tefa/progres', 'public');
        }

        TefaLogProduksi::create([
            'tefa_order_id' => $this->selectedOrderId,
            'siswa_id' => $this->siswaId,
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jamMulai,
            'jam_selesai' => $this->jamSelesai,
            'durasi_menit' => $durasiMenit,
            'ringkasan_pekerjaan' => $this->ringkasanPekerjaan,
            'alat_dan_bahan' => $this->alatDanBahan,
            'foto_progres' => $fotoPath,
            'status_qc' => 'menunggu_qc',
        ]);

        $this->reset(['ringkasanPekerjaan', 'alatDanBahan', 'fotoProgres']);
        session()->flash('success', "Logsheet jam kerja ({$durasiMenit} menit) berhasil dikirim dan menunggu verifikasi QC instruktur.");
    }

    public function render()
    {
        $myOrders = $this->getMyOrders();
        $riwayatLog = TefaLogProduksi::with('order')
            ->where('siswa_id', $this->siswaId)
            ->latest('tanggal')
            ->get();

        $totalMenitLolos = $riwayatLog->where('status_qc', 'lolos_qc')->sum('durasi_menit');
        $totalJamLolos = round($totalMenitLolos / 60, 1);

        return view('livewire.tefa.siswa.log-produksi-tefa', [
            'myOrders' => $myOrders,
            'riwayatLog' => $riwayatLog,
            'totalJamLolos' => $totalJamLolos,
        ])->layout('components.layouts.portal', ['title' => 'Logsheet Jam Kerja TEFA Siswa - SMKN 2 Indramayu']);
    }
}