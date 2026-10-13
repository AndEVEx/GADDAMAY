<?php

namespace App\Livewire\Perpus;

use App\Models\PerpusBuku;
use App\Models\PerpusKategori;
use App\Models\PerpusRiwayatBaca;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Katalog Perpustakaan Digital - SMKN 2 Indramayu')]
class KatalogPerpusDigital extends Component
{
    use WithPagination;

    public $activeTab = 'katalog'; // katalog, buku_saya, offline
    public $search = '';
    public $filterKategori = '';

    public function render()
    {
        $userId = Auth::id();

        // 1. Koleksi Buku Umum
        $bukuQuery = PerpusBuku::with('kategori')
            ->where('is_published', true)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('judul', 'like', "%{$this->search}%")
                        ->orWhere('penulis', 'like', "%{$this->search}%")
                        ->orWhere('isbn', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori_id', $this->filterKategori));

        $bukuList = $bukuQuery->orderBy('total_dibaca', 'desc')->paginate(12, ['*'], 'katalogPage');

        // 2. Buku Saya (Sedang Dibaca)
        $bukuSayaList = [];
        if ($userId) {
            $bukuSayaList = PerpusRiwayatBaca::with(['buku.kategori'])
                ->where('user_id', $userId)
                ->orderBy('waktu_terakhir_baca', 'desc')
                ->get();
        }

        $kategoriList = PerpusKategori::withCount(['buku' => fn($q) => $q->where('is_published', true)])->get();

        return view('livewire.perpus.katalog-perpus-digital', [
            'bukuList' => $bukuList,
            'bukuSayaList' => $bukuSayaList,
            'kategoriList' => $kategoriList,
        ]);
    }
}
