<?php

namespace App\Livewire\Perduptar;

use App\Models\PerduptarKatalog;
use App\Models\PerduptarPoinSiswa;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Buku Saku & Poin Kedisiplinan PERDUPTAR 2026 - SMKN 2 Indramayu')]
class KelolaPoinPerduptar extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'buku_saku'; // buku_saku, input_poin, katalog_aturan, buku_saya

    // Filter Buku Saku
    public $searchSiswa = '';
    public $filterRombel = '';
    public $filterStatusDisiplin = '';

    // Form Pencatatan Poin Baru
    public $siswaId;
    public $tipe = 'pelanggaran'; // pelanggaran, prestasi
    public $selectedKatalogId = '';
    public $bobotPoin = -5;
    public $pasalRujukan = '';
    public $namaButir = '';
    public $kronologi = '';
    public $lokasi = 'Lingkungan Sekolah';
    public $tanggalKejadian;
    public $sanksiDiberikan = '';
    public $statusTindakan = 'pending';
    public $suratPeringatan = 'tidak_ada';
    public $fotoBukti;

    // Modal Detail Buku Saku Taruna
    public $selectedSiswaDetail = null;

    public function mount()
    {
        $this->tanggalKejadian = date('Y-m-d');

        // Jika siswa yang login, default tab ke buku_saya
        if (Auth::user()?->role === 'siswa' || Auth::user()?->role === 'ketua_kelas') {
            $this->activeTab = 'buku_saya';
        }
    }

    public function updatedSelectedKatalogId($value)
    {
        if ($value) {
            $katalog = PerduptarKatalog::find($value);
            if ($katalog) {
                $this->tipe = $katalog->tipe;
                $this->bobotPoin = $katalog->bobot_poin;
                $this->pasalRujukan = $katalog->pasal_rujukan;
                $this->namaButir = $katalog->nama_butir;
                $this->sanksiDiberikan = $katalog->sanksi_default ?? '';
            }
        }
    }

    public function simpanPencatatanPoin()
    {
        $this->validate([
            'siswaId' => 'required',
            'tipe' => 'required|in:prestasi,pelanggaran',
            'bobotPoin' => 'required|numeric',
            'namaButir' => 'required|string|max:255',
            'tanggalKejadian' => 'required|date',
            'fotoBukti' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($this->fotoBukti) {
            $fotoPath = $this->fotoBukti->store('perduptar/bukti', 'public');
        }

        // Tentukan peran petugas
        $user = Auth::user();
        $peranPetugas = match ($user?->role) {
            'admin' => 'Kesiswaan / Administrator',
            'kepsek' => 'Kepala Sekolah',
            'waka' => 'Wakil Kepala Sekolah',
            'guru' => 'Tenaga Pendidik / Guru',
            'guru_piket' => 'Guru Piket Sekolah',
            default => 'Pembina Ketarunaan',
        };

        PerduptarPoinSiswa::create([
            'siswa_id' => $this->siswaId,
            'katalog_id' => $this->selectedKatalogId ?: null,
            'tipe' => $this->tipe,
            'bobot_poin' => (int)$this->bobotPoin,
            'pasal_rujukan' => $this->pasalRujukan,
            'nama_butir' => $this->namaButir,
            'kronologi_kejadian' => $this->kronologi,
            'lokasi_kejadian' => $this->lokasi,
            'tanggal_kejadian' => $this->tanggalKejadian,
            'petugas_user_id' => $user?->id,
            'peran_petugas' => $peranPetugas,
            'sanksi_diberikan' => $this->sanksiDiberikan,
            'status_tindakan' => $this->statusTindakan,
            'surat_peringatan' => $this->suratPeringatan,
            'foto_bukti' => $fotoPath,
            'notif_ortu_terkirim' => true,
        ]);

        // Reset form
        $this->reset(['siswaId', 'selectedKatalogId', 'bobotPoin', 'pasalRujukan', 'namaButir', 'kronologi', 'sanksiDiberikan', 'fotoBukti']);
        $this->activeTab = 'buku_saku';

        session()->flash('success', 'Catatan kedisiplinan taruna berhasil dicatat ke dalam Buku Saku Digital!');
    }

    public function lihatDetailSiswa($id)
    {
        $this->selectedSiswaDetail = Siswa::with(['rombel', 'perduptarPoin.petugas'])->find($id);
    }

    public function tutupDetailSiswa()
    {
        $this->selectedSiswaDetail = null;
    }

    public function ubahStatusTindakan($id, $status)
    {
        $poin = PerduptarPoinSiswa::find($id);
        if ($poin) {
            $poin->update(['status_tindakan' => $status]);
            if ($this->selectedSiswaDetail) {
                $this->selectedSiswaDetail->load('perduptarPoin.petugas');
            }
            session()->flash('success', 'Status pelaksanaan pembinaan berhasil diperbarui.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Data Siswa Saya (Jika Siswa yang Login)
        $siswaSaya = null;
        if ($user && ($user->role === 'siswa' || $user->role === 'ketua_kelas')) {
            $siswaSaya = Siswa::where('email', $user->email)
                ->orWhere('nis', $user->username ?? '')
                ->orWhere('rombel_id', $user->rombel_id)
                ->first() ?: Siswa::with(['rombel', 'perduptarPoin.petugas'])->first();
        }

        // 2. Query Siswa untuk Buku Saku Admin/Guru
        $siswaQuery = Siswa::with(['rombel', 'perduptarPoin'])
            ->when($this->searchSiswa, function ($q) {
                $q->where('nama', 'like', "%{$this->searchSiswa}%")
                    ->orWhere('nis', 'like', "%{$this->searchSiswa}%");
            })
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel));

        $siswaList = $siswaQuery->paginate(12, ['*'], 'perduptarPage');

        // 3. Statistik Ringkasan Kedisiplinan Sekolah
        $totalPelanggaran = PerduptarPoinSiswa::where('tipe', 'pelanggaran')->count();
        $totalPrestasi = PerduptarPoinSiswa::where('tipe', 'prestasi')->count();
        $totalTarunaDibina = PerduptarPoinSiswa::where('status_tindakan', 'sedang_dibina')->count();
        $totalSpAktif = PerduptarPoinSiswa::whereIn('surat_peringatan', ['sp1', 'sp2', 'sp3', 'panggilan_ortu'])->count();

        // 4. Data Katalog Aturan
        $katalogPrestasi = PerduptarKatalog::where('tipe', 'prestasi')->orderBy('bobot_poin', 'desc')->get();
        $katalogPelanggaran = PerduptarKatalog::where('tipe', 'pelanggaran')->orderBy('bobot_poin', 'asc')->get();

        $rombelList = Rombel::orderBy('nama_rombel')->get();
        $allSiswaSelect = Siswa::with('rombel')->orderBy('nama')->get();

        return view('livewire.perduptar.kelola-poin-perduptar', [
            'siswaList' => $siswaList,
            'siswaSaya' => $siswaSaya,
            'totalPelanggaran' => $totalPelanggaran,
            'totalPrestasi' => $totalPrestasi,
            'totalTarunaDibina' => $totalTarunaDibina,
            'totalSpAktif' => $totalSpAktif,
            'katalogPrestasi' => $katalogPrestasi,
            'katalogPelanggaran' => $katalogPelanggaran,
            'rombelList' => $rombelList,
            'allSiswaSelect' => $allSiswaSelect,
        ]);
    }
}
