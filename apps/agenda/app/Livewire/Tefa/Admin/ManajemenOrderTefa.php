<?php

namespace App\Livewire\Tefa\Admin;

use App\Models\TefaOrder;
use App\Models\User;
use App\Services\Tefa\TefaOrderService;
use App\Services\Tefa\TefaWaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManajemenOrderTefa extends Component
{
    public $filterStatus = 'semua';
    public $search = '';

    // Form Order Baru
    public $showCreateModal = false;
    public $nama_pemesan;
    public $nomor_wa_pemesan;
    public $instansi_pemesan;
    public $judul_proyek;
    public $kategori_kejuruan = 'PPLG';
    public $biaya_proyek = 1500000;
    public $tanggal_masuk;
    public $target_selesai;
    public $deskripsi_proyek;

    // Modal WhatsApp Update Konsumen
    public $showWaModal = false;
    public $selectedOrderId;
    public $waStage = 'konfirmasi'; // konfirmasi / progres / selesai
    public $waMessage;
    public $waMeLink;
    public $targetPhone;
    public $targetName;

    public function mount()
    {
        $this->tanggal_masuk = Carbon::today()->toDateString();
        $this->target_selesai = Carbon::today()->addDays(14)->toDateString();
    }

    public function simpanOrder()
    {
        $this->validate([
            'nama_pemesan' => 'required|string|max:100',
            'nomor_wa_pemesan' => 'required|string',
            'judul_proyek' => 'required|string|max:150',
            'biaya_proyek' => 'required|numeric',
            'target_selesai' => 'required|date',
        ]);

        $order = TefaOrder::create([
            'kode_order' => TefaOrderService::generateKodeOrder(),
            'nama_pemesan' => $this->nama_pemesan,
            'nomor_wa_pemesan' => $this->nomor_wa_pemesan,
            'instansi_pemesan' => $this->instansi_pemesan,
            'judul_proyek' => $this->judul_proyek,
            'kategori_kejuruan' => $this->kategori_kejuruan,
            'biaya_proyek' => (float) $this->biaya_proyek,
            'tanggal_masuk' => $this->tanggal_masuk,
            'target_selesai' => $this->target_selesai,
            'status' => 'dalam_produksi',
            'instruktur_id' => Auth::id() ?: User::first()->id,
            'deskripsi_proyek' => $this->deskripsi_proyek,
        ]);

        $this->showCreateModal = false;
        $this->reset(['nama_pemesan', 'nomor_wa_pemesan', 'instansi_pemesan', 'judul_proyek', 'deskripsi_proyek']);

        // Buka modal WhatsApp konfirmasi SPK
        $this->openWaModal($order->id, 'konfirmasi');

        session()->flash('success', "Order baru {$order->kode_order} berhasil dibuat dan masuk tahap antrean produksi.");
    }

    public function openWaModal($orderId, $stage = 'konfirmasi')
    {
        $order = TefaOrder::with('instruktur')->find($orderId);
        if (!$order) return;

        $this->selectedOrderId = $orderId;
        $this->waStage = $stage;
        $this->targetName = $order->nama_pemesan;
        $this->targetPhone = $order->nomor_wa_pemesan;
        $this->waMessage = TefaWaService::generateOrderNotification($order, $stage);
        $this->waMeLink = TefaWaService::generateWaMeLink($this->targetPhone, $this->waMessage);
        $this->showWaModal = true;
    }

    public function changeWaStage($stage)
    {
        $this->waStage = $stage;
        $order = TefaOrder::with('instruktur')->find($this->selectedOrderId);
        if ($order) {
            $this->waMessage = TefaWaService::generateOrderNotification($order, $stage);
            $this->waMeLink = TefaWaService::generateWaMeLink($this->targetPhone, $this->waMessage);
        }
    }

    public function closeWaModal()
    {
        $this->showWaModal = false;
    }

    public function kirimWaOtomatis()
    {
        $order = TefaOrder::find($this->selectedOrderId);
        if ($order) {
            $result = TefaWaService::sendAutomated($order, $this->waStage);
            if ($result['success']) {
                session()->flash('success', 'Pemberitahuan WhatsApp berhasil dikirimkan ke pemesan via gateway.');
            } else {
                session()->flash('info', 'Gateway server offline. Silakan gunakan tombol failover [Buka WhatsApp Pribadi] di bawah.');
            }
        }
    }

    public function render()
    {
        $query = TefaOrder::with(['instruktur', 'timKerja.siswa', 'logProduksi'])->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul_proyek', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_pemesan', 'like', '%' . $this->search . '%');
            });
        }

        $orderList = $query->get();

        return view('livewire.tefa.admin.manajemen-order-tefa', [
            'orderList' => $orderList,
        ])->layout('components.layouts.portal', ['title' => 'Manajemen Order TEFA - SMKN 2 Indramayu']);
    }
}