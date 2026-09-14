<?php

namespace App\Livewire\Tefa\Admin;

use App\Models\Siswa;
use App\Models\TefaLogProduksi;
use App\Models\TefaOrder;
use App\Models\TefaTimKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailOrderTefa extends Component
{
    public $orderId;
    public $order;

    // Form Tambah Anggota Tim Siswa
    public $showAddMemberModal = false;
    public $siswa_id;
    public $peran_dalam_tim = 'teknisi_utama';
    public $job_desc;

    // Modal Catatan QC
    public $selectedLogId;
    public $catatanQc;

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();

        $siswaFirst = Siswa::first();
        $this->siswa_id = $siswaFirst?->id;
    }

    public function loadOrder()
    {
        $this->order = TefaOrder::with(['instruktur', 'timKerja.siswa.rombel', 'logProduksi.siswa'])->findOrFail($this->orderId);
    }

    public function tambahAnggotaTim()
    {
        $this->validate([
            'siswa_id' => 'required',
            'peran_dalam_tim' => 'required',
        ]);

        $exists = TefaTimKerja::where('tefa_order_id', $this->orderId)
            ->where('siswa_id', $this->siswa_id)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Siswa tersebut sudah terdaftar dalam tim kerja proyek ini.');
            return;
        }

        TefaTimKerja::create([
            'tefa_order_id' => $this->orderId,
            'siswa_id' => $this->siswa_id,
            'peran_dalam_tim' => $this->peran_dalam_tim,
            'job_desc' => $this->job_desc,
            'status_tim' => 'aktif',
        ]);

        $this->showAddMemberModal = false;
        $this->reset(['job_desc']);
        $this->loadOrder();

        session()->flash('success', 'Anggota tim kerja siswa berhasil ditugaskan.');
    }

    public function setujuiQc($logId)
    {
        $log = TefaLogProduksi::find($logId);
        if ($log) {
            $log->update([
                'status_qc' => 'lolos_qc',
                'diperiksa_oleh_id' => Auth::id(),
                'diperiksa_at' => Carbon::now(),
            ]);
            $this->loadOrder();
            session()->flash('success', 'Logsheet jam kerja lolos inspeksi QC dan masuk ke portofolio jam terbang siswa.');
        }
    }

    public function tolakQc($logId, $alasan = null)
    {
        $log = TefaLogProduksi::find($logId);
        if ($log) {
            $log->update([
                'status_qc' => 'perlu_revisi',
                'catatan_instruktur' => $alasan ?: 'Perlu perbaikan sesuai standar spesifikasi pemesan.',
                'diperiksa_oleh_id' => Auth::id(),
                'diperiksa_at' => Carbon::now(),
            ]);
            $this->loadOrder();
            session()->flash('info', 'Status QC disetel menjadi perlu revisi.');
        }
    }

    public function selesaikanOrder()
    {
        $this->order->update([
            'status' => 'selesai_diserahkan',
            'tanggal_selesai_aktual' => Carbon::today()->toDateString(),
        ]);
        $this->loadOrder();
        session()->flash('success', 'Proyek pesanan TEFA telah ditandai SELESAI & siap diserahkan ke pemesan.');
    }

    public function render()
    {
        $siswaPilihan = Siswa::with('rombel')->orderBy('nama')->take(50)->get();

        return view('livewire.tefa.admin.detail-order-tefa', [
            'siswaPilihan' => $siswaPilihan,
        ])->layout('components.layouts.portal', ['title' => "Detail Order {$this->order->kode_order} - TEFA SMKN 2 Indramayu"]);
    }
}