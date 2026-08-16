<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\IzinGuru;
use App\Models\User;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Pengajuan Izin Guru')]
class PengajuanIzin extends Component
{
    use WithFileUploads;

    public string $jenisIzin = 'izin';
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public string $alasan = '';
    public ?string $guruPenggantiId = null;
    public $fileLampiran;

    public bool $showFormModal = false;
    public ?string $detailIzinId = null;

    public function mount()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
    }

    public function openForm()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->reset(['jenisIzin', 'alasan', 'guruPenggantiId', 'fileLampiran']);
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
        $this->showFormModal = true;
    }

    public function closeForm()
    {
        $this->showFormModal = false;
    }

    public function submitIzin()
    {
        $this->validate([
            'jenisIzin' => 'required|in:izin,sakit,cuti,dinas,tugas_luar',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'alasan' => 'required|min:5|max:1000',
            'guruPenggantiId' => 'nullable|exists:users,id',
            'fileLampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'tanggalSelesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.required' => 'Mohon sertakan alasan / keterangan pengajuan izin.',
            'alasan.min' => 'Alasan izin minimal 5 karakter.',
            'fileLampiran.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        $filePath = null;
        if ($this->fileLampiran) {
            $filePath = $this->fileLampiran->store('lampiran_izin', 'public');
        }

        IzinGuru::create([
            'guru_id' => auth()->id(),
            'jenis_izin' => $this->jenisIzin,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggalSelesai' => $this->tanggalSelesai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'alasan' => $this->alasan,
            'file_lampiran' => $filePath,
            'guru_pengganti_id' => $this->guruPenggantiId ?: null,
            'status' => 'menunggu',
        ]);

        $this->showFormModal = false;
        $this->reset(['alasan', 'guruPenggantiId', 'fileLampiran']);
        $this->dispatch('show-toast', message: 'Pengajuan izin berhasil dikirim ke Waka Kurikulum!', type: 'success');
    }

    public function batalkanIzin(string $id)
    {
        $izin = IzinGuru::where('id', $id)
            ->where('guru_id', auth()->id())
            ->where('status', 'menunggu')
            ->first();

        if ($izin) {
            $izin->delete();
            $this->dispatch('show-toast', message: 'Pengajuan izin berhasil dibatalkan.', type: 'info');
        }
    }

    public function showDetail(string $id)
    {
        $this->detailIzinId = $id;
    }

    public function closeDetail()
    {
        $this->detailIzinId = null;
    }

    public function getJadwalTerdampakProperty()
    {
        if (!$this->tanggalMulai || !$this->tanggalSelesai) return collect();

        try {
            $start = Carbon::parse($this->tanggalMulai);
            $end = Carbon::parse($this->tanggalSelesai);
            if ($start->gt($end)) return collect();

            $days = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $days[] = $date->dayOfWeekIso; // 1=Senin ... 7=Minggu
            }
            $days = array_unique($days);

            return JadwalPelajaran::whereIn('hari', $days)
                ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', auth()->id()))
                ->with(['rombel', 'mataPelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_ke_mulai')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function render()
    {
        $riwayatIzin = IzinGuru::where('guru_id', auth()->id())
            ->with(['guruPengganti', 'diverifikasiOleh'])
            ->latest()
            ->get();

        $daftarGuru = User::whereIn('role', ['guru', 'ketua_mgmp'])
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        $detailIzin = $this->detailIzinId
            ? IzinGuru::where('id', $this->detailIzinId)->where('guru_id', auth()->id())->with(['guruPengganti', 'diverifikasiOleh'])->first()
            : null;

        return view('livewire.guru.pengajuan-izin', [
            'riwayatIzin' => $riwayatIzin,
            'daftarGuru' => $daftarGuru,
            'detailIzin' => $detailIzin,
            'jadwalTerdampak' => $this->jadwalTerdampak,
        ]);
    }
}
