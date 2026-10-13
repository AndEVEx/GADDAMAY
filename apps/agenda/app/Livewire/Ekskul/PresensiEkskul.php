<?php

namespace App\Livewire\Ekskul;

use App\Models\Ekskul;
use App\Models\EkskulAnggota;
use App\Models\EkskulPertemuan;
use App\Models\EkskulPresensi;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Presensi & Manajemen Ekstrakurikuler - SMKN 2 Indramayu')]
class PresensiEkskul extends Component
{
    // Ekskul terpilih
    public $selectedEkskulId = null;
    public $activeTab = 'presensi'; // presensi, riwayat, anggota

    // Sesi pertemuan aktif
    public $selectedPertemuanId = null;
    public $tanggalPertemuan;
    public $topikLatihan = 'Latihan Rutin & Pemantapan Keterampilan';
    public $jamMulai = '15:30';
    public $jamSelesai = '17:30';

    // Scanner NIS input
    public $scannerInput = '';
    public $scanFeedback = null;
    public $scanStatus = null; // success, error, warning

    // Form Anggota Baru
    public $tambahSiswaId = '';
    public $tambahJabatan = 'Anggota';

    public function mount()
    {
        $user = Auth::user();
        $this->tanggalPertemuan = Carbon::today()->format('Y-m-d');

        // Cari ekskul yang sesuai dengan user
        if ($user->role === 'admin' || in_array($user->role, ['waka', 'kepsek', 'kesiswaan'])) {
            $firstEkskul = Ekskul::where('is_aktif', true)->first();
            $this->selectedEkskulId = $firstEkskul?->id;
        } else {
            // Guru pembina atau pelatih
            $myEkskul = Ekskul::where('pembina_guru_id', $user->id)
                ->orWhere('pelatih_user_id', $user->id)
                ->first();

            if (!$myEkskul) {
                // Fallback jika bukan pembina langsung (misal siswa ingin melihat atau guru lain)
                $myEkskul = Ekskul::where('is_aktif', true)->first();
            }
            $this->selectedEkskulId = $myEkskul?->id;
        }

        $this->loadOrCreatePertemuanHariIni();
    }

    public function selectEkskul($ekskulId)
    {
        $this->selectedEkskulId = $ekskulId;
        $this->loadOrCreatePertemuanHariIni();
        $this->scanFeedback = null;
    }

    public function loadOrCreatePertemuanHariIni()
    {
        if (!$this->selectedEkskulId) return;

        // Cek apakah sudah ada pertemuan pada tanggal ini
        $pertemuan = EkskulPertemuan::where('ekskul_id', $this->selectedEkskulId)
            ->whereDate('tanggal_pertemuan', $this->tanggalPertemuan)
            ->latest()
            ->first();

        if ($pertemuan) {
            $this->selectedPertemuanId = $pertemuan->id;
            $this->topikLatihan = $pertemuan->topik_latihan;
            $this->jamMulai = $pertemuan->jam_mulai ?? '15:30';
            $this->jamSelesai = $pertemuan->jam_selesai ?? '17:30';
        } else {
            // Buat pertemuan otomatis hari ini
            $this->buatPertemuanBaru();
        }
    }

    public function buatPertemuanBaru()
    {
        if (!$this->selectedEkskulId) return;

        $user = Auth::user();
        $pertemuan = EkskulPertemuan::create([
            'ekskul_id' => $this->selectedEkskulId,
            'tanggal_pertemuan' => $this->tanggalPertemuan,
            'jam_mulai' => $this->jamMulai,
            'jam_selesai' => $this->jamSelesai,
            'topik_latihan' => $this->topikLatihan,
            'pencatat_user_id' => $user->id,
            'status_pertemuan' => 'selesai',
        ]);

        $this->selectedPertemuanId = $pertemuan->id;

        // Inisialisasi daftar presensi untuk semua anggota aktif ekskul (default Alpa/A sampai discan/diubah)
        $anggotaList = EkskulAnggota::where('ekskul_id', $this->selectedEkskulId)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        foreach ($anggotaList as $anggota) {
            EkskulPresensi::firstOrCreate(
                [
                    'ekskul_pertemuan_id' => $pertemuan->id,
                    'ekskul_anggota_id' => $anggota->id,
                    'siswa_id' => $anggota->siswa_id,
                ],
                [
                    'status' => 'A',
                    'metode_presensi' => 'input_manual',
                    'catatan' => 'Belum terverifikasi hadir',
                ]
            );
        }
    }

    public function gantiPertemuan($pertemuanId)
    {
        $this->selectedPertemuanId = $pertemuanId;
        $pertemuan = EkskulPertemuan::find($pertemuanId);
        if ($pertemuan) {
            $this->tanggalPertemuan = $pertemuan->tanggal_pertemuan->format('Y-m-d');
            $this->topikLatihan = $pertemuan->topik_latihan;
        }
    }

    public function processScanQr($nis)
    {
        $cleanNis = trim($nis);
        if (empty($cleanNis)) return;

        // Cari Siswa berdasarkan NIS
        $siswa = Siswa::where('nis', $cleanNis)->first();

        if (!$siswa) {
            $this->scanStatus = 'error';
            $this->scanFeedback = "Gagal: Siswa dengan NIS '{$cleanNis}' tidak ditemukan dalam master data.";
            $this->scannerInput = '';
            $this->dispatch('playAudioAlert', ['type' => 'error']);
            return;
        }

        // Cek apakah siswa terdaftar di ekskul ini
        $anggota = EkskulAnggota::where('ekskul_id', $this->selectedEkskulId)
            ->where('siswa_id', $siswa->id)
            ->first();

        if (!$anggota) {
            $this->scanStatus = 'warning';
            $this->scanFeedback = "Peringatan: Siswa {$siswa->nama} (NIS: {$cleanNis}) TIDAK TERDAFTAR di cabang ekskul ini!";
            $this->scannerInput = '';
            $this->dispatch('playAudioAlert', ['type' => 'error']);
            return;
        }

        // Pastikan ada pertemuan aktif
        if (!$this->selectedPertemuanId) {
            $this->buatPertemuanBaru();
        }

        // Simpan / update status presensi ke Hadir (H)
        $presensi = EkskulPresensi::updateOrCreate(
            [
                'ekskul_pertemuan_id' => $this->selectedPertemuanId,
                'ekskul_anggota_id' => $anggota->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'status' => 'H',
                'metode_presensi' => 'scan_qr',
                'waktu_scan' => now(),
                'catatan' => 'Hadir via Scan QR Kartu Pelajar Digital',
            ]
        );

        $this->scanStatus = 'success';
        $this->scanFeedback = "BERHASIL: {$siswa->nama} (NIS: {$siswa->nis}) dicatat HADIR pukul " . now()->format('H:i:s') . " WIB";
        $this->scannerInput = '';
        $this->dispatch('playAudioAlert', ['type' => 'success']);
    }

    public function submitManualNis()
    {
        $this->processScanQr($this->scannerInput);
    }

    public function updateStatusManual($presensiId, $statusBaru)
    {
        $presensi = EkskulPresensi::find($presensiId);
        if ($presensi) {
            $presensi->update([
                'status' => $statusBaru,
                'metode_presensi' => 'input_manual',
                'waktu_scan' => $statusBaru === 'H' ? now() : null,
                'catatan' => 'Presensi diperbarui manual oleh pembina/pelatih',
            ]);
            $this->scanFeedback = "Status kehadiran {$presensi->siswa->nama} diubah ke {$statusBaru}";
            $this->scanStatus = 'info';
        }
    }

    public function tandaiSemuaHadir()
    {
        if (!$this->selectedPertemuanId) return;

        EkskulPresensi::where('ekskul_pertemuan_id', $this->selectedPertemuanId)
            ->update([
                'status' => 'H',
                'metode_presensi' => 'input_manual',
                'waktu_scan' => now(),
                'catatan' => 'Ditandai Hadir serentak oleh Pembina/Pelatih',
            ]);

        $this->scanStatus = 'success';
        $this->scanFeedback = "Seluruh anggota ekskul berhasil ditandai HADIR!";
    }

    public function resetPresensi()
    {
        if (!$this->selectedPertemuanId) return;

        EkskulPresensi::where('ekskul_pertemuan_id', $this->selectedPertemuanId)
            ->update([
                'status' => 'A',
                'metode_presensi' => 'input_manual',
                'waktu_scan' => null,
                'catatan' => 'Direset ke status Alpa/Belum terverifikasi',
            ]);

        $this->scanStatus = 'warning';
        $this->scanFeedback = "Presensi pertemuan berhasil di-reset.";
    }

    public function tambahAnggota()
    {
        if (!$this->tambahSiswaId || !$this->selectedEkskulId) return;

        $exists = EkskulAnggota::where('ekskul_id', $this->selectedEkskulId)
            ->where('siswa_id', $this->tambahSiswaId)
            ->exists();

        if ($exists) {
            session()->flash('anggota_error', 'Siswa tersebut sudah menjadi anggota ekskul ini.');
            return;
        }

        $anggota = EkskulAnggota::create([
            'ekskul_id' => $this->selectedEkskulId,
            'siswa_id' => $this->tambahSiswaId,
            'jabatan_anggota' => $this->tambahJabatan,
            'tahun_ajaran' => '2025/2026',
            'status_keanggotaan' => 'aktif',
            'nilai_predikat' => 'A',
        ]);

        // Jika ada pertemuan aktif hari ini, tambahkan juga ke record presensi
        if ($this->selectedPertemuanId) {
            EkskulPresensi::firstOrCreate([
                'ekskul_pertemuan_id' => $this->selectedPertemuanId,
                'ekskul_anggota_id' => $anggota->id,
                'siswa_id' => $this->tambahSiswaId,
            ], [
                'status' => 'A',
                'metode_presensi' => 'input_manual',
            ]);
        }

        $this->tambahSiswaId = '';
        session()->flash('anggota_success', 'Anggota baru berhasil ditambahkan!');
    }

    public function hapusAnggota($anggotaId)
    {
        $anggota = EkskulAnggota::find($anggotaId);
        if ($anggota) {
            $anggota->presensi()->delete();
            $anggota->delete();
            session()->flash('anggota_success', 'Anggota berhasil dihapus dari ekskul.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Daftar semua ekskul
        $ekskulList = Ekskul::with(['pembina', 'anggota'])
            ->where('is_aktif', true)
            ->orderBy('nama_ekskul')
            ->get();

        $currentEkskul = Ekskul::with(['pembina', 'pelatihUser'])->find($this->selectedEkskulId);

        // Pertemuan terpilih
        $currentPertemuan = null;
        $presensiList = collect();
        $statistik = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0];

        if ($this->selectedPertemuanId) {
            $currentPertemuan = EkskulPertemuan::find($this->selectedPertemuanId);

            $presensiList = EkskulPresensi::with(['siswa.rombel', 'anggota'])
                ->where('ekskul_pertemuan_id', $this->selectedPertemuanId)
                ->get()
                ->sortBy('siswa.nama');

            $statistik['H'] = $presensiList->where('status', 'H')->count();
            $statistik['S'] = $presensiList->where('status', 'S')->count();
            $statistik['I'] = $presensiList->where('status', 'I')->count();
            $statistik['A'] = $presensiList->where('status', 'A')->count();
            $statistik['total'] = $presensiList->count();
        }

        // Riwayat pertemuan ekskul
        $riwayatPertemuan = collect();
        if ($this->selectedEkskulId) {
            $riwayatPertemuan = EkskulPertemuan::with(['pencatat', 'presensi'])
                ->where('ekskul_id', $this->selectedEkskulId)
                ->latest('tanggal_pertemuan')
                ->take(15)
                ->get();
        }

        // Daftar Anggota ekskul
        $anggotaList = collect();
        if ($this->selectedEkskulId) {
            $anggotaList = EkskulAnggota::with('siswa.rombel')
                ->where('ekskul_id', $this->selectedEkskulId)
                ->orderBy('jabatan_anggota')
                ->get();
        }

        // Daftar siswa sekolah untuk opsi tambah anggota
        $allSiswaSekolah = Siswa::with('rombel')->orderBy('nama')->take(100)->get();

        return view('livewire.ekskul.presensi-ekskul', [
            'ekskulList' => $ekskulList,
            'currentEkskul' => $currentEkskul,
            'currentPertemuan' => $currentPertemuan,
            'presensiList' => $presensiList,
            'statistik' => $statistik,
            'riwayatPertemuan' => $riwayatPertemuan,
            'anggotaList' => $anggotaList,
            'allSiswaSekolah' => $allSiswaSekolah,
            'currentUser' => $user,
        ]);
    }
}
