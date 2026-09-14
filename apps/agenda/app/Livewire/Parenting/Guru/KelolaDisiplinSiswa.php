<?php

namespace App\Livewire\Parenting\Guru;

use App\Models\ParentingCatatanDisiplin;
use App\Models\Siswa;
use App\Services\Parenting\ParentingWaNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KelolaDisiplinSiswa extends Component
{
    public $siswa_id;
    public $kategori = 'pelanggaran';
    public $jenis_tindakan;
    public $poin = 5;
    public $deskripsi;
    public $tanggal_kejadian;
    public $nomorWaOrtu = '081234567890';

    // Modal Kirim WA ke Orang Tua
    public $showWaModal = false;
    public $selectedCatatanId;
    public $waMessage;
    public $waMeLink;
    public $targetPhone;
    public $namaSiswaTarget;

    public function mount()
    {
        $this->tanggal_kejadian = Carbon::today()->toDateString();
        $siswaFirst = Siswa::first();
        $this->siswa_id = $siswaFirst?->id;
    }

    public function simpanCatatan()
    {
        $this->validate([
            'siswa_id' => 'required',
            'kategori' => 'required|in:pelanggaran,pembinaan_bk,prestasi,apresiasi',
            'jenis_tindakan' => 'required|string|max:150',
            'poin' => 'required|numeric',
            'deskripsi' => 'required|string',
            'tanggal_kejadian' => 'required|date',
        ]);

        $catatan = ParentingCatatanDisiplin::create([
            'siswa_id' => $this->siswa_id,
            'kategori' => $this->kategori,
            'jenis_tindakan' => $this->jenis_tindakan,
            'poin' => in_array($this->kategori, ['prestasi', 'apresiasi']) ? abs($this->poin) : -abs($this->poin),
            'deskripsi' => $this->deskripsi,
            'petugas_id' => Auth::id() ?: Siswa::first()->id, // Fallback ID jika simulasi
            'peran_petugas' => Auth::user()?->role ?: 'guru_bk',
            'tanggal_kejadian' => $this->tanggal_kejadian,
            'notif_wa_ortu_status' => 'pending',
        ]);

        $this->reset(['jenis_tindakan', 'deskripsi']);
        $this->openWaModal($catatan->id);

        session()->flash('success', 'Catatan kedisiplinan/prestasi berhasil disimpan.');
    }

    public function openWaModal($catatanId)
    {
        $catatan = ParentingCatatanDisiplin::with('siswa.rombel', 'petugas')->find($catatanId);
        if (!$catatan) return;

        $this->selectedCatatanId = $catatanId;
        $this->namaSiswaTarget = $catatan->siswa->nama ?? 'Siswa';
        $this->targetPhone = $this->nomorWaOrtu;
        $this->waMessage = ParentingWaNotificationService::generateDisciplineMessage($catatan);
        $this->waMeLink = ParentingWaNotificationService::generateWaMeLink($this->targetPhone, $this->waMessage);
        $this->showWaModal = true;
    }

    public function closeWaModal()
    {
        $this->showWaModal = false;
    }

    public function kirimWaOtomatis()
    {
        $catatan = ParentingCatatanDisiplin::find($this->selectedCatatanId);
        if ($catatan) {
            $result = ParentingWaNotificationService::sendAutomated($catatan, $this->targetPhone);
            if ($result['success']) {
                $catatan->update(['notif_wa_ortu_status' => 'sent_auto', 'wa_sent_at' => Carbon::now()]);
                session()->flash('success', 'Pesan notifikasi WhatsApp berhasil dikirim ke orang tua via gateway.');
            } else {
                session()->flash('info', 'Gateway server offline. Silakan gunakan tombol failover [Buka WhatsApp Pribadi] di bawah.');
            }
        }
    }

    public function render()
    {
        $siswaList = Siswa::with('rombel')->orderBy('nama')->take(50)->get();
        $riwayatCatatan = ParentingCatatanDisiplin::with(['siswa.rombel', 'petugas'])->latest()->take(20)->get();

        return view('livewire.parenting.guru.kelola-disiplin-siswa', [
            'siswaList' => $siswaList,
            'riwayatCatatan' => $riwayatCatatan,
        ])->layout('components.layouts.portal', ['title' => 'Kelola Disiplin & Prestasi Siswa - SMKN 2 Indramayu']);
    }
}