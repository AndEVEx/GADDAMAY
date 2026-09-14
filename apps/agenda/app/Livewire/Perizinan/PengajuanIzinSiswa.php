<?php

namespace App\Livewire\Perizinan;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PerizinanSiswa;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Services\PerizinanSyncService;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Formulir Pengajuan Izin Siswa - GADDAMAY')]
class PengajuanIzinSiswa extends Component
{
    use WithFileUploads;

    public ?string $selectedSiswaId = null;
    public string $searchSiswa = '';
    public string $kategori = 'sakit';
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public ?string $jamMulai = null;
    public ?string $jamSelesai = null;
    public string $alasan = '';
    public $fileLampiran = null;

    public string $namaPemohon = '';
    public string $nomorWaPemohon = '';
    public string $hubunganPemohon = 'orang_tua';
    public bool $langsungSetujui = false;

    public function mount()
    {
        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;

        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->isGuruPiket() || $user->isGuru())) {
            $this->langsungSetujui = true;
            $this->hubunganPemohon = $user->isGuruPiket() ? 'guru_piket' : 'wali_kelas';
            $this->namaPemohon = $user->name;
        }
    }

    public function selectSiswa(string $id)
    {
        $this->selectedSiswaId = $id;
        $this->searchSiswa = '';
    }

    public function clearSelectedSiswa()
    {
        $this->selectedSiswaId = null;
    }

    public function submitPengajuan(PerizinanSyncService $syncService)
    {
        $this->validate([
            'selectedSiswaId' => 'required|exists:siswa,id',
            'kategori' => 'required|in:sakit,izin_keperluan,dispensasi_sekolah,izin_keluar_kampus',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'alasan' => 'required|min:5',
            'fileLampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'nomorWaPemohon' => 'nullable|string|max:25',
        ], [
            'selectedSiswaId.required' => 'Pilih siswa yang akan diajukan izin terlebih dahulu.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'alasan.min' => 'Alasan izin minimal 5 karakter.',
            'fileLampiran.max' => 'Ukuran berkas lampiran maksimal 3MB.',
        ]);

        $filePath = null;
        if ($this->fileLampiran) {
            $filePath = $this->fileLampiran->store('perizinan_lampiran', 'public');
        }

        $user = auth()->user();
        $isDirectApproval = $this->langsungSetujui && $user && ($user->isAdmin() || $user->isGuruPiket() || $user->isGuru());

        $status = $isDirectApproval 
            ? ($user->isGuruPiket() ? 'disetujui_piket' : 'disetujui_walas') 
            : 'menunggu';

        $peranVerifikator = $isDirectApproval 
            ? ($user->isGuruPiket() ? 'guru_piket' : 'wali_kelas') 
            : null;

        $izin = PerizinanSiswa::create([
            'siswa_id' => $this->selectedSiswaId,
            'kategori' => $this->kategori,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'jam_mulai' => $this->jamMulai,
            'jam_selesai' => $this->jamSelesai,
            'alasan' => $this->alasan,
            'file_lampiran' => $filePath,
            'nama_pemohon' => $this->namaPemohon,
            'nomor_wa_pemohon' => $this->nomorWaPemohon,
            'hubungan_pemohon' => $this->hubunganPemohon,
            'status' => $status,
            'diverifikasi_oleh_id' => $isDirectApproval ? $user->id : null,
            'peran_verifikator' => $peranVerifikator,
            'catatan_verifikator' => $isDirectApproval ? 'Izin langsung disahkan oleh ' . $user->name : null,
            'waktu_verifikasi' => $isDirectApproval ? now() : null,
        ]);

        if ($isDirectApproval) {
            // Auto-lock KBM
            $syncService->syncToAgendaKBM($izin);

            // Kirim notifikasi WA otomatis jika ada nomor pemohon
            if (!empty($this->nomorWaPemohon)) {
                $syncService->dispatchWaNotification($izin);
            }
        }

        session()->flash('success', 'Pengajuan perizinan siswa berhasil disimpan' . ($isDirectApproval ? ' dan langsung disahkan!' : '. Menunggu verifikasi petugas.'));

        return redirect()->route('perizinan.index');
    }

    public function render()
    {
        $siswaResults = collect();
        if (strlen(trim($this->searchSiswa)) >= 2) {
            $siswaResults = Siswa::with('rombel')
                ->where('nama', 'like', '%' . trim($this->searchSiswa) . '%')
                ->orWhere('nis', 'like', '%' . trim($this->searchSiswa) . '%')
                ->limit(8)
                ->get();
        }

        $selectedSiswa = $this->selectedSiswaId ? Siswa::with('rombel')->find($this->selectedSiswaId) : null;

        return view('livewire.perizinan.pengajuan-izin-siswa', [
            'siswaResults' => $siswaResults,
            'selectedSiswa' => $selectedSiswa,
        ]);
    }
}