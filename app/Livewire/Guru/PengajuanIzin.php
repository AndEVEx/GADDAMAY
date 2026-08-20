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
#[Title('Pengajuan Izin Harian Guru')]
class PengajuanIzin extends Component
{
    use WithFileUploads;

    public string $jenisIzin = 'sakit'; // 'sakit', 'izin', 'dinas', 'tugas_luar', 'cuti'
    public bool $isSeharian = true;
    public array $selectedJadwalIds = [];
    public array $selectedJam = [];
    public string $alasan = '';
    public ?string $guruPenggantiId = null;
    public $fileLampiran;

    public bool $showFormModal = false;
    public ?string $detailIzinId = null;

    public function mount()
    {
        $this->initForm();
    }

    public function initForm()
    {
        $this->jenisIzin = 'sakit';
        $this->isSeharian = true;
        $this->alasan = '';
        $this->guruPenggantiId = null;
        $this->fileLampiran = null;
        $this->selectedJadwalIds = $this->jadwalHariIni->pluck('id')->toArray();
        $this->selectedJam = [];
    }

    public function openForm()
    {
        $this->initForm();
        $this->showFormModal = true;
    }

    public function closeForm()
    {
        $this->showFormModal = false;
    }

    public function updatedIsSeharian($value)
    {
        if ($value) {
            $this->selectedJadwalIds = $this->jadwalHariIni->pluck('id')->toArray();
            $this->selectedJam = [];
        }
    }

    public function getJadwalHariIniProperty()
    {
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->dayOfWeekIso; // 1=Senin..7=Minggu

        return JadwalPelajaran::where('hari', $hariIni)
            ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', auth()->id()))
            ->with(['rombel', 'mataPelajaran'])
            ->orderBy('jam_ke_mulai')
            ->get();
    }

    public function submitIzin()
    {
        $this->validate([
            'jenisIzin' => 'required|in:izin,sakit,cuti,dinas,tugas_luar',
            'alasan' => 'required|min:3|max:1000',
            'guruPenggantiId' => 'nullable|exists:users,id',
            'fileLampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'alasan.required' => 'Mohon sertakan alasan / keterangan pengajuan izin.',
            'alasan.min' => 'Alasan izin minimal 3 karakter.',
            'fileLampiran.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        if (!$this->isSeharian && empty($this->selectedJadwalIds) && empty($this->selectedJam)) {
            $this->addError('selectedJadwalIds', 'Pilih minimal satu sesi jam mengajar atau jam pelajaran.');
            return;
        }

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $filePath = null;

        if ($this->fileLampiran) {
            $filePath = $this->fileLampiran->store('lampiran_izin', 'public');
        }

        // Build waktu keterangan
        $waktuKeterangan = 'Seharian Penuh';
        if (!$this->isSeharian) {
            $parts = [];
            if (!empty($this->selectedJadwalIds)) {
                $selectedJadwals = JadwalPelajaran::whereIn('id', $this->selectedJadwalIds)->with(['rombel', 'mataPelajaran'])->get();
                foreach ($selectedJadwals as $j) {
                    $parts[] = "Jam {$j->jam_ke_mulai}-{$j->jam_ke_selesai} ({$j->rombel?->nama_kelas})";
                }
            }
            if (!empty($this->selectedJam)) {
                $parts[] = "Jam ke-" . implode(',', $this->selectedJam);
            }
            $waktuKeterangan = !empty($parts) ? implode(', ', $parts) : 'Jam Tertentu';
        }

        IzinGuru::create([
            'guru_id' => auth()->id(),
            'jenis_izin' => $this->jenisIzin,
            'is_seharian' => $this->isSeharian,
            'waktu_keterangan' => $waktuKeterangan,
            'jam_terpilih' => $this->selectedJam ?: null,
            'jadwal_ids' => $this->selectedJadwalIds ?: null,
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'alasan' => $this->alasan,
            'file_lampiran' => $filePath,
            'guru_pengganti_id' => $this->guruPenggantiId ?: null,
            'status' => 'menunggu',
        ]);

        $this->showFormModal = false;
        $this->initForm();
        $this->dispatch('show-toast', message: 'Pengajuan izin harian berhasil dikirim ke Waka / Admin!', type: 'success');
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
            'jadwalHariIni' => $this->jadwalHariIni,
        ]);
    }
}
