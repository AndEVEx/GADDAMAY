<?php

namespace App\Livewire\Perpus;

use App\Models\PerpusBuku;
use App\Models\PerpusRiwayatBaca;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Membaca Buku Digital - SMKN 2 Indramayu')]
class BacaBukuDigital extends Component
{
    public $bukuId;
    public $buku;
    public $riwayat;
    public $halamanMulai = 1;
    public $durasiDetik = 0;
    public $isOfflineMode = false;

    public function mount($id)
    {
        $this->bukuId = $id;
        $this->isOfflineMode = request()->boolean('offline', false);

        $this->buku = PerpusBuku::with('kategori')->find($id);

        if (!$this->buku && !$this->isOfflineMode) {
            abort(404, 'Buku digital tidak ditemukan.');
        }

        $userId = Auth::id();
        if ($userId && $this->buku) {
            $user = Auth::user();
            $this->riwayat = PerpusRiwayatBaca::firstOrCreate(
                [
                    'user_id' => $userId,
                    'buku_id' => $this->bukuId,
                ],
                [
                    'siswa_id' => $user->siswa_id ?? null,
                    'rombel_id' => $user->siswa?->rombel_id ?? null,
                    'halaman_terakhir' => 1,
                    'persentase_baca' => 0,
                    'durasi_detik' => 0,
                    'waktu_mulai_baca' => now(),
                    'waktu_terakhir_baca' => now(),
                    'status_selesai' => false,
                ]
            );

            $this->halamanMulai = max(1, $this->riwayat->halaman_terakhir);
            $this->durasiDetik = $this->riwayat->durasi_detik;

            // Tambah hit counter baca buku
            $this->buku->increment('total_dibaca');
        }
    }

    public function simpanProgress($halaman, $durasiTambahan, $isSelesai = false)
    {
        $userId = Auth::id();
        if (!$userId || !$this->buku) {
            return ['status' => 'guest'];
        }

        $totalHalaman = max(1, $this->buku->jumlah_halaman);
        $halaman = min($totalHalaman, max(1, (int)$halaman));
        $persentase = min(100, (int)round(($halaman / $totalHalaman) * 100));

        $riwayat = PerpusRiwayatBaca::firstOrNew([
            'user_id' => $userId,
            'buku_id' => $this->bukuId,
        ]);

        $user = Auth::user();
        $riwayat->siswa_id = $riwayat->siswa_id ?: ($user->siswa_id ?? null);
        $riwayat->rombel_id = $riwayat->rombel_id ?: ($user->siswa?->rombel_id ?? null);
        $riwayat->halaman_terakhir = $halaman;
        $riwayat->persentase_baca = $persentase;
        $riwayat->durasi_detik = ($riwayat->durasi_detik ?? 0) + (int)$durasiTambahan;
        $riwayat->waktu_terakhir_baca = now();

        if (!$riwayat->waktu_mulai_baca) {
            $riwayat->waktu_mulai_baca = now();
        }

        if ($isSelesai || $persentase >= 95) {
            $riwayat->status_selesai = true;
        }

        $riwayat->save();

        return [
            'status' => 'success',
            'halaman' => $halaman,
            'durasi_format' => $riwayat->durasi_format,
            'persentase' => $persentase,
            'status_selesai' => $riwayat->status_selesai,
        ];
    }

    public function render()
    {
        return view('livewire.perpus.baca-buku-digital');
    }
}
