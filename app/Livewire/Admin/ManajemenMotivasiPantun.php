<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\MotivasiPantun;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Motivasi & Pantun')]
class ManajemenMotivasiPantun extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterTipe = '';
    public string $filterKategori = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $isi = '';
    public string $tipe = 'pantun';
    public string $kategori = 'sebelum_mengajar';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteContent = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTipe() { $this->resetPage(); }
    public function updatingFilterKategori() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'isi', 'tipe', 'kategori', 'editing']);
        $this->tipe = 'pantun';
        $this->kategori = 'sebelum_mengajar';
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $item = MotivasiPantun::findOrFail($id);
        $this->editId = $item->id;
        $this->isi = $item->isi;
        $this->tipe = $item->tipe;
        $this->kategori = $item->kategori;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'isi' => 'required|min:5',
            'tipe' => 'required|in:pantun,kata_mutiara',
            'kategori' => 'required|in:sebelum_mengajar,siap_mengajar',
        ]);

        if ($this->editing) {
            $item = MotivasiPantun::findOrFail($this->editId);
            $old = $item->toArray();
            $item->update([
                'isi' => $this->isi,
                'tipe' => $this->tipe,
                'kategori' => $this->kategori,
            ]);
            AuditLogService::logUpdate($item, $old);
            $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil diperbarui!', type: 'success');
        } else {
            $item = MotivasiPantun::create([
                'isi' => $this->isi,
                'tipe' => $this->tipe,
                'kategori' => $this->kategori,
            ]);
            AuditLogService::logCreate($item);
            $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'isi', 'tipe', 'kategori', 'editing']);
    }

    public function confirmDeleteMotivasi(string $id)
    {
        $item = MotivasiPantun::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteContent = mb_strimwidth($item->isi, 0, 50, '...');
        $this->confirmDelete = true;
    }

    public function deleteMotivasi()
    {
        $item = MotivasiPantun::findOrFail($this->deleteId);
        AuditLogService::logDelete($item);
        $item->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        $items = MotivasiPantun::when($this->search, fn($q) => $q->where('isi', 'like', "%{$this->search}%"))
            ->when($this->filterTipe, fn($q) => $q->where('tipe', $this->filterTipe))
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.manajemen-motivasi-pantun', ['items' => $items]);
    }
}
