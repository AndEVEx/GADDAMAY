<?php

namespace App\Livewire\Parenting;

use App\Models\ParentingCatatanDisiplin;
use App\Models\ParentingKonsultasiOrtu;
use App\Models\PerizinanSiswa;
use App\Models\Siswa;
use App\Services\Parenting\ParentingAuthService;
use App\Services\Parenting\ParentingFeedService;
use App\Services\Parenting\ParentingWaNotificationService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class DashboardParenting extends Component
{
    use WithFileUploads;

    public $token;
    public $siswa;
    public $activeTab = 'timeline'; // timeline / disiplin / izin / konsultasi

    // Form Pengajuan Izin Langsung dari Ortu
    public $kategoriIzin = 'sakit';
    public $tanggalMulai;
    public $tanggalSelesai;
    public $alasanIzin;
    public $lampiranFoto;
    public $nomorWaOrtu;
    public $namaOrtu;

    public function mount($token = null)
    {
        $this->token = $token;
        $this->tanggalMulai = Carbon::today()->toDateString();
        $this->tanggalSelesai = Carbon::today()->toDateString();

        if ($token) {
            $this->siswa = ParentingAuthService::verifyMagicToken($token);
        }

        if (!$this->siswa && session('parenting_siswa_id')) {
            $this->siswa = Siswa::with('rombel')->find(session('parenting_siswa_id'));
        }

        if (!$this->siswa) {
            $this->siswa = Siswa::with('rombel')->first();
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function ajukanIzinOrtu()
    {
        $this->validate([
            'kategoriIzin' => 'required|in:sakit,izin_keperluan,dispensasi_sekolah',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date',
            'alasanIzin' => 'required|string|min:10',
            'nomorWaOrtu' => 'required|string',
        ]);

        if (!$this->siswa) return;

        $filePath = null;
        if ($this->lampiranFoto) {
            $filePath = $this->lampiranFoto->store('uploads/perizinan', 'public');
        }

        PerizinanSiswa::create([
            'siswa_id' => $this->siswa->id,
            'kategori' => $this->kategoriIzin,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'alasan' => $this->alasanIzin,
            'file_lampiran' => $filePath,
            'nama_pemohon' => $this->namaOrtu ?: 'Orang Tua Murid',
            'nomor_wa_pemohon' => $this->nomorWaOrtu,
            'hubungan_pemohon' => 'orang_tua',
            'status' => 'menunggu',
            'wa_notif_status' => 'pending',
        ]);

        $this->reset(['alasanIzin', 'lampiranFoto']);
        session()->flash('success', 'Permohonan perizinan anak berhasil dikirim ke Meja Piket & Wali Kelas SMKN 2 Indramayu.');
    }

    public function render()
    {
        $summary = [];
        $timeline = [];
        $disiplinList = collect();
        $riwayatIzin = collect();
        $walasWaLink = '#';

        if ($this->siswa) {
            $summary = ParentingFeedService::getStudentSummary($this->siswa);
            $timeline = ParentingFeedService::getAttendanceTimeline($this->siswa);
            $disiplinList = ParentingCatatanDisiplin::where('siswa_id', $this->siswa->id)
                ->latest('tanggal_kejadian')
                ->get();
            $riwayatIzin = PerizinanSiswa::where('siswa_id', $this->siswa->id)->latest()->take(5)->get();

            // Link WA konsultasi ke Wali Kelas (Default nomor walas / sekolah)
            $pesanWalas = "Halo Bpk/Ibu Wali Kelas {$this->siswa->rombel->nama_rombel}.\n" .
                          "Saya orang tua dari *{$this->siswa->nama}* (NISN: {$this->siswa->nisn}).\n" .
                          "Saya ingin berkonsultasi mengenai perkembangan dan kedisiplinan ananda di sekolah.";
            $walasWaLink = ParentingWaNotificationService::generateWaMeLink('081234567890', $pesanWalas);
        }

        return view('livewire.parenting.dashboard-parenting', [
            'summary' => $summary,
            'timeline' => $timeline,
            'disiplinList' => $disiplinList,
            'riwayatIzin' => $riwayatIzin,
            'walasWaLink' => $walasWaLink,
        ])->layout('components.layouts.portal', ['title' => 'Buku Parenting Digital - SMKN 2 Indramayu']);
    }
}