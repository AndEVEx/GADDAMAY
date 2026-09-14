<?php

namespace App\Livewire\Perizinan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PerizinanSiswa;
use App\Models\Rombel;
use App\Services\PerizinanSyncService;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Manajemen Perizinan Siswa - GADDAMAY')]
class ManajemenPerizinanSiswa extends Component
{
    use WithPagination;

    public string $filterStatus = 'menunggu';
    public string $filterKategori = 'semua';
    public string $filterRombel = 'semua';
    public string $filterTanggal = '';
    public string $search = '';

    // Modal States
    public ?string $selectedIzinId = null;
    public string $catatanVerifikator = '';
    public string $peranVerifikator = 'guru_piket'; // default guru_piket atau wali_kelas
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public bool $showDetailModal = false;
    public bool $showWaModal = false;

    // WA Failover State
    public string $waMessagePreview = '';
    public ?string $waMeLink = null;
    public ?string $waPhone = null;
    public string $waStatusText = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->isGuru() && !$user->isGuruPiket() && !$user->isAdmin() && !$user->isWaka()) {
            $this->peranVerifikator = 'wali_kelas';
        } else {
            $this->peranVerifikator = 'guru_piket';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function updatingFilterRombel()
    {
        $this->resetPage();
    }

    public function openApprove(string $id)
    {
        $this->selectedIzinId = $id;
        $this->catatanVerifikator = '';
        $this->showApproveModal = true;
    }

    public function openReject(string $id)
    {
        $this->selectedIzinId = $id;
        $this->catatanVerifikator = '';
        $this->showRejectModal = true;
    }

    public function openDetail(string $id)
    {
        $this->selectedIzinId = $id;
        $this->showDetailModal = true;
    }

    public function openWaModal(string $id)
    {
        $izin = PerizinanSiswa::with('siswa.rombel', 'verifikator')->find($id);
        if (!$izin) return;

        $this->selectedIzinId = $id;
        $this->waPhone = $izin->nomor_wa_pemohon;
        $this->waMessagePreview = $izin->generateWaMessage();
        $this->waMeLink = $izin->generateWaMeLink();
        $this->waStatusText = $izin->wa_notif_status;
        $this->showWaModal = true;
    }

    public function closeModals()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->showDetailModal = false;
        $this->showWaModal = false;
        $this->selectedIzinId = null;
    }

    public function approveIzin(PerizinanSyncService $syncService)
    {
        $izin = PerizinanSiswa::with('siswa.rombel')->find($this->selectedIzinId);
        if (!$izin) return;

        $statusApproval = ($this->peranVerifikator === 'wali_kelas') ? 'disetujui_walas' : 'disetujui_piket';

        $izin->update([
            'status' => $statusApproval,
            'diverifikasi_oleh_id' => auth()->id(),
            'peran_verifikator' => $this->peranVerifikator,
            'catatan_verifikator' => $this->catatanVerifikator ?: 'Disetujui oleh ' . ucwords(str_replace('_', ' ', $this->peranVerifikator)),
            'waktu_verifikasi' => Carbon::now('Asia/Jakarta'),
        ]);

        // Auto-lock KBM di buku agenda guru
        $syncService->syncToAgendaKBM($izin);

        // Coba kirim otomatis WhatsApp via multi-driver gateway
        $syncService->dispatchWaNotification($izin);

        $this->closeModals();
        session()->flash('success', "Izin atas nama {$izin->siswa->nama_siswa} berhasil disetujui dan disinkronkan ke buku agenda kelas.");
    }

    public function rejectIzin()
    {
        $izin = PerizinanSiswa::with('siswa')->find($this->selectedIzinId);
        if (!$izin) return;

        $izin->update([
            'status' => 'ditolak',
            'diverifikasi_oleh_id' => auth()->id(),
            'peran_verifikator' => $this->peranVerifikator,
            'catatan_verifikator' => $this->catatanVerifikator ?: 'Pengajuan izin ditolak.',
            'waktu_verifikasi' => Carbon::now('Asia/Jakarta'),
        ]);

        $this->closeModals();
        session()->flash('info', "Izin atas nama {$izin->siswa->nama_siswa} telah ditolak.");
    }

    public function retrySendAutoWa(PerizinanSyncService $syncService)
    {
        if (!$this->selectedIzinId) return;
        $izin = PerizinanSiswa::find($this->selectedIzinId);
        if (!$izin) return;

        $res = $syncService->dispatchWaNotification($izin);

        $this->waStatusText = $izin->wa_notif_status;

        if ($res['success']) {
            session()->flash('wa_feedback', 'Pesan WhatsApp otomatis BERHASIL dikirim melalui gateway.');
        } else {
            session()->flash('wa_feedback_error', 'Pengiriman gateway gagal: ' . ($res['error'] ?? 'Server offline') . '. Silakan gunakan tombol WhatsApp Web / Manual di bawah.');
        }
    }

    public function markWaSentManual(PerizinanSyncService $syncService)
    {
        if (!$this->selectedIzinId) return;
        $izin = PerizinanSiswa::find($this->selectedIzinId);
        if (!$izin) return;

        $syncService->markAsSentManually($izin);
        $this->waStatusText = 'sent_manual';

        session()->flash('wa_feedback', 'Status berhasil ditandai sebagai: Telah Terkirim Manual.');
    }

    public function render()
    {
        $query = PerizinanSiswa::with(['siswa.rombel', 'verifikator'])
            ->latest('tanggal_mulai');

        if ($this->filterStatus !== 'semua') {
            if ($this->filterStatus === 'disetujui') {
                $query->whereIn('status', ['disetujui_walas', 'disetujui_piket', 'disetujui_bk']);
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        if ($this->filterKategori !== 'semua') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->filterRombel !== 'semua') {
            $query->whereHas('siswa', function ($q) {
                $q->where('rombel_id', $this->filterRombel);
            });
        }

        if (!empty($this->filterTanggal)) {
            $query->whereDate('tanggal_mulai', '<=', $this->filterTanggal)
                  ->whereDate('tanggal_selesai', '>=', $this->filterTanggal);
        }

        if (!empty($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                  ->orWhere('nis', 'like', $search);
            });
        }

        $perizinanList = $query->paginate(10);
        $rombelList = Rombel::orderBy('nama_kelas')->get();

        // Counter stats
        $countMenunggu = PerizinanSiswa::where('status', 'menunggu')->count();
        $countHariIni = PerizinanSiswa::whereDate('tanggal_mulai', '<=', now()->toDateString())
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->whereIn('status', ['disetujui_walas', 'disetujui_piket', 'disetujui_bk'])
            ->count();

        $selectedIzin = $this->selectedIzinId ? PerizinanSiswa::with('siswa.rombel', 'verifikator')->find($this->selectedIzinId) : null;

        return view('livewire.perizinan.manajemen-perizinan-siswa', [
            'perizinanList' => $perizinanList,
            'rombelList' => $rombelList,
            'countMenunggu' => $countMenunggu,
            'countHariIni' => $countHariIni,
            'selectedIzin' => $selectedIzin,
        ]);
    }
}