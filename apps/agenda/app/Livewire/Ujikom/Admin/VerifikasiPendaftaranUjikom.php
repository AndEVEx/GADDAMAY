<?php

namespace App\Livewire\Ujikom\Admin;

use App\Models\UjikomPendaftaran;
use App\Models\UjikomSkema;
use App\Models\User;
use App\Services\Ujikom\UjikomRegistrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VerifikasiPendaftaranUjikom extends Component
{
    use WithPagination;

    public $filterSkema = '';
    public $filterStatus = '';
    public $search = '';

    // Modal Edit / Verifikasi State
    public $selectedPendaftaranId = null;
    public $selectedPendaftaran = null;
    public $status_verifikasi;
    public $asesor_id;
    public $jadwal_asesmen;
    public $tempat_uji_kompetensi_tuk;
    public $hasil_asesmen;
    public $catatan_asesor;

    public function selectPendaftaran($id)
    {
        $this->selectedPendaftaranId = $id;
        $this->selectedPendaftaran = UjikomPendaftaran::with(['siswa.rombel', 'skema', 'asesor', 'berkas'])->find($id);

        if ($this->selectedPendaftaran) {
            $this->status_verifikasi = $this->selectedPendaftaran->status_verifikasi;
            $this->asesor_id = $this->selectedPendaftaran->asesor_id ?? Auth::id();
            $this->jadwal_asesmen = $this->selectedPendaftaran->jadwal_asesmen?->format('Y-m-d') ?? Carbon::now()->addDays(7)->format('Y-m-d');
            $this->tempat_uji_kompetensi_tuk = $this->selectedPendaftaran->tempat_uji_kompetensi_tuk ?? 'Lab RPL & Komputer 1 (TUK Sewaktu)';
            $this->hasil_asesmen = $this->selectedPendaftaran->hasil_asesmen;
            $this->catatan_asesor = $this->selectedPendaftaran->catatan_asesor;
        }
    }

    public function closeDetail()
    {
        $this->selectedPendaftaranId = null;
        $this->selectedPendaftaran = null;
    }

    public function simpanVerifikasi()
    {
        $this->validate([
            'status_verifikasi' => 'required|in:menunggu_verifikasi,lolos_administrasi,berkas_kurang,ditolak',
            'hasil_asesmen' => 'required|in:belum_dinilai,kompeten,belum_kompeten',
            'jadwal_asesmen' => 'nullable|date',
            'tempat_uji_kompetensi_tuk' => 'nullable|string|max:100',
        ]);

        if ($this->selectedPendaftaran) {
            $this->selectedPendaftaran->update([
                'status_verifikasi' => $this->status_verifikasi,
                'asesor_id' => $this->asesor_id,
                'jadwal_asesmen' => $this->jadwal_asesmen,
                'tempat_uji_kompetensi_tuk' => $this->tempat_uji_kompetensi_tuk,
                'hasil_asesmen' => $this->hasil_asesmen,
                'catatan_asesor' => $this->catatan_asesor,
            ]);

            session()->flash('success', "Data asesi " . ($this->selectedPendaftaran->siswa?->nama ?? '') . " berhasil diverifikasi dan diperbarui!");
            $this->selectPendaftaran($this->selectedPendaftaranId);
        }
    }

    public function quickApprove($id)
    {
        $p = UjikomPendaftaran::find($id);
        if ($p) {
            $p->update([
                'status_verifikasi' => 'lolos_administrasi',
                'asesor_id' => Auth::id() ?? $p->asesor_id,
            ]);
            session()->flash('success', "Asesi " . $p->nomor_pendaftaran . " dinyatakan Lolos Administrasi!");
        }
    }

    public function render()
    {
        $query = UjikomPendaftaran::with(['siswa.rombel', 'skema', 'asesor', 'berkas'])
            ->latest();

        if ($this->filterSkema) {
            $query->where('skema_id', $this->filterSkema);
        }

        if ($this->filterStatus) {
            $query->where('status_verifikasi', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                    ->orWhereHas('siswa', function ($sq) {
                        $sq->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhere('nis', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $pendaftarans = $query->paginate(10);
        $skemaList = UjikomSkema::where('is_active', true)->get();
        $asesorList = User::all();
        $stats = UjikomRegistrationService::getSummaryStatistics();

        return view('livewire.ujikom.admin.verifikasi-pendaftaran-ujikom', [
            'pendaftarans' => $pendaftarans,
            'skemaList' => $skemaList,
            'asesorList' => $asesorList,
            'stats' => $stats,
        ])->layout('components.layouts.portal', ['title' => 'Meja Asesor & Verifikasi Ujikom LSP-P1 - SMKN 2 Indramayu']);
    }
}