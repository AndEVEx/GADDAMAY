<?php

namespace App\Livewire\Perpus;

use App\Models\PerpusBuku;
use App\Models\PerpusKategori;
use App\Models\PerpusRiwayatBaca;
use App\Models\Rombel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Kelola Perpustakaan Digital - Kepala Perpustakaan')]
class KelolaPerpusDigital extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'katalog'; // katalog, monitoring, kategori
    public $search = '';
    public $filterKategori = '';
    public $filterRombel = '';

    // Form Modal Buku
    public $showModalBuku = false;
    public $isEditBuku = false;
    public $bukuId;
    public $kategori_id = '';
    public $judul = '';
    public $penulis = '';
    public $penerbit = '';
    public $tahun_terbit = '';
    public $isbn = '';
    public $jumlah_halaman = 50;
    public $sinopsis = '';
    public $cover_url = '';
    public $file_pdf_url = '';
    public $file_size_mb = 2.5;
    public $allow_offline_cache = true;
    public $is_published = true;
    public $uploadCover;
    public $uploadPdf;

    // Form Modal Kategori
    public $showModalKategori = false;
    public $isEditKategori = false;
    public $kategoriId;
    public $nama_kategori = '';
    public $ikon_kategori = 'bi-book';
    public $keterangan_kategori = '';

    public function render()
    {
        // 1. Data Buku
        $bukuQuery = PerpusBuku::with('kategori')
            ->withCount('riwayatBaca')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('judul', 'like', "%{$this->search}%")
                        ->orWhere('penulis', 'like', "%{$this->search}%")
                        ->orWhere('isbn', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori_id', $this->filterKategori));

        $bukuList = $bukuQuery->latest()->paginate(9, ['*'], 'bukuPage');

        // 2. Data Monitoring Pembaca
        $logQuery = PerpusRiwayatBaca::with(['buku.kategori', 'user', 'siswa', 'rombel'])
            ->when($this->search, function ($q) {
                $q->whereHas('buku', fn($b) => $b->where('judul', 'like', "%{$this->search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->when($this->filterKategori, function ($q) {
                $q->whereHas('buku', fn($b) => $b->where('kategori_id', $this->filterKategori));
            });

        $riwayatBacaList = $logQuery->latest('waktu_terakhir_baca')->paginate(15, ['*'], 'logPage');

        // 3. Statistik Perpustakaan
        $totalBuku = PerpusBuku::count();
        $totalAktivitasBaca = PerpusRiwayatBaca::count();
        $totalDurasiDetik = PerpusRiwayatBaca::sum('durasi_detik');
        $totalJamBaca = round($totalDurasiDetik / 3600, 1);
        $totalSiswaMembaca = PerpusRiwayatBaca::distinct('user_id')->count('user_id');

        $kategoriList = PerpusKategori::withCount('buku')->get();
        $rombelList = Rombel::orderBy('nama_rombel')->get();

        return view('livewire.perpus.kelola-perpus-digital', [
            'bukuList' => $bukuList,
            'riwayatBacaList' => $riwayatBacaList,
            'kategoriList' => $kategoriList,
            'rombelList' => $rombelList,
            'totalBuku' => $totalBuku,
            'totalAktivitasBaca' => $totalAktivitasBaca,
            'totalJamBaca' => $totalJamBaca,
            'totalSiswaMembaca' => $totalSiswaMembaca,
        ]);
    }

    // =========================================================================
    // CRUD BUKU
    // =========================================================================

    public function openCreateBuku()
    {
        $this->reset([
            'bukuId', 'isEditBuku', 'judul', 'penulis', 'penerbit', 'tahun_terbit',
            'isbn', 'sinopsis', 'cover_url', 'file_pdf_url', 'uploadCover', 'uploadPdf'
        ]);
        $this->kategori_id = PerpusKategori::first()?->id ?? '';
        $this->jumlah_halaman = 50;
        $this->file_size_mb = 2.5;
        $this->allow_offline_cache = true;
        $this->is_published = true;
        $this->showModalBuku = true;
    }

    public function editBuku($id)
    {
        $b = PerpusBuku::findOrFail($id);
        $this->bukuId = $b->id;
        $this->isEditBuku = true;
        $this->kategori_id = $b->kategori_id;
        $this->judul = $b->judul;
        $this->penulis = $b->penulis;
        $this->penerbit = $b->penerbit ?? '';
        $this->tahun_terbit = $b->tahun_terbit ?? '';
        $this->isbn = $b->isbn ?? '';
        $this->jumlah_halaman = $b->jumlah_halaman;
        $this->sinopsis = $b->sinopsis ?? '';
        $this->cover_url = $b->cover_url ?? '';
        $this->file_pdf_url = $b->file_pdf_url;
        $this->file_size_mb = $b->file_size_mb;
        $this->allow_offline_cache = $b->allow_offline_cache;
        $this->is_published = $b->is_published;
        $this->showModalBuku = true;
    }

    public function saveBuku()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:perpus_kategori,id',
            'penulis' => 'required|string|max:150',
            'jumlah_halaman' => 'required|integer|min:1',
        ]);

        // Upload Cover jika ada
        if ($this->uploadCover) {
            $path = $this->uploadCover->store('perpus/covers', 'public');
            $this->cover_url = '/storage/' . $path;
        }

        // Upload PDF jika ada
        if ($this->uploadPdf) {
            $pdfPath = $this->uploadPdf->store('perpus/pdf', 'public');
            $this->file_pdf_url = '/storage/' . $pdfPath;
            $this->file_size_mb = round($this->uploadPdf->getSize() / (1024 * 1024), 2);
        }

        if (empty($this->file_pdf_url)) {
            // Default demo PDF jika tidak upload
            $this->file_pdf_url = 'https://raw.githubusercontent.com/mozilla/pdf.js/master/examples/learning/helloworld.pdf';
        }

        $slug = Str::slug($this->judul) . '-' . Str::random(4);

        if ($this->isEditBuku && $this->bukuId) {
            $b = PerpusBuku::findOrFail($this->bukuId);
            $b->update([
                'kategori_id' => $this->kategori_id,
                'judul' => $this->judul,
                'penulis' => $this->penulis,
                'penerbit' => $this->penerbit,
                'tahun_terbit' => $this->tahun_terbit,
                'isbn' => $this->isbn,
                'jumlah_halaman' => $this->jumlah_halaman,
                'sinopsis' => $this->sinopsis,
                'cover_url' => $this->cover_url,
                'file_pdf_url' => $this->file_pdf_url,
                'file_size_mb' => $this->file_size_mb,
                'allow_offline_cache' => $this->allow_offline_cache,
                'is_published' => $this->is_published,
            ]);
            session()->flash('success', 'Buku digital berhasil diperbarui.');
        } else {
            PerpusBuku::create([
                'kategori_id' => $this->kategori_id,
                'judul' => $this->judul,
                'slug' => $slug,
                'penulis' => $this->penulis,
                'penerbit' => $this->penerbit,
                'tahun_terbit' => $this->tahun_terbit,
                'isbn' => $this->isbn,
                'jumlah_halaman' => $this->jumlah_halaman,
                'sinopsis' => $this->sinopsis,
                'cover_url' => $this->cover_url ?: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=600',
                'file_pdf_url' => $this->file_pdf_url,
                'file_size_mb' => $this->file_size_mb,
                'allow_offline_cache' => $this->allow_offline_cache,
                'is_published' => $this->is_published,
                'created_by' => Auth::id(),
            ]);
            session()->flash('success', 'Buku digital baru berhasil ditambahkan ke katalog.');
        }

        $this->showModalBuku = false;
    }

    public function deleteBuku($id)
    {
        $b = PerpusBuku::findOrFail($id);
        $b->delete();
        session()->flash('success', 'Buku berhasil dihapus dari perpustakaan.');
    }

    // =========================================================================
    // EXPORT REKAP PEMBACA EXCEL / CSV
    // =========================================================================

    public function exportExcelLaporan()
    {
        $query = PerpusRiwayatBaca::with(['buku.kategori', 'user', 'siswa', 'rombel'])
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->when($this->filterKategori, function ($q) {
                $q->whereHas('buku', fn($b) => $b->where('kategori_id', $this->filterKategori));
            })
            ->latest('waktu_terakhir_baca');

        $data = $query->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Laporan_Perpus_Digital_SMKN2_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($file, ["LAPORAN STATISTIK PEMBACA PERPUS DIGITAL - SMKN 2 INDRAMAYU"]);
            fputcsv($file, ["Tanggal Unduh: " . now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            fputcsv($file, [
                'No', 'Nama Pembaca', 'Peran / Kelas', 'Judul Buku', 'Kategori Buku',
                'Halaman Terakhir', 'Progres Baca (%)', 'Total Waktu Baca', 'Waktu Mulai', 'Terakhir Baca', 'Status Selesai'
            ]);

            $no = 1;
            foreach ($data as $row) {
                $roleAtauKelas = $row->rombel->nama_rombel ?? ucfirst($row->user->role ?? 'Siswa');
                fputcsv($file, [
                    $no++,
                    $row->user->name ?? '-',
                    $roleAtauKelas,
                    $row->buku->judul ?? '-',
                    $row->buku->kategori->nama_kategori ?? '-',
                    $row->halaman_terakhir . ' / ' . ($row->buku->jumlah_halaman ?? '-'),
                    $row->persentase_baca . '%',
                    $row->durasi_format,
                    $row->waktu_mulai_baca ? $row->waktu_mulai_baca->format('d/m/Y H:i') : '-',
                    $row->waktu_terakhir_baca ? $row->waktu_terakhir_baca->format('d/m/Y H:i') : '-',
                    $row->status_selesai ? 'Tuntas' : 'Sedang Membaca',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
