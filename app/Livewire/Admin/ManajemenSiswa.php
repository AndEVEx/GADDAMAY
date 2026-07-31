<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Siswa')]
class ManajemenSiswa extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterRombel = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama = '';
    public string $nis = '';
    public string $rombel_id = '';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRombel() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama', 'nis', 'rombel_id', 'editing']);
        $firstRombel = Rombel::orderBy('nama_kelas')->first();
        if ($firstRombel) {
            $this->rombel_id = $firstRombel->id;
        }
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->editId = $siswa->id;
        $this->nama = $siswa->nama;
        $this->nis = $siswa->nis ?? '';
        $this->rombel_id = $siswa->rombel_id;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|min:2',
            'nis' => 'nullable|string',
            'rombel_id' => 'required|exists:rombel,id',
        ]);

        if ($this->editing) {
            $siswa = Siswa::findOrFail($this->editId);
            $old = $siswa->toArray();
            $siswa->update([
                'nama' => $this->nama,
                'nis' => $this->nis ?: null,
                'rombel_id' => $this->rombel_id,
            ]);
            AuditLogService::logUpdate($siswa, $old);
            $this->dispatch('show-toast', message: 'Data siswa berhasil diperbarui!', type: 'success');
        } else {
            $siswa = Siswa::create([
                'nama' => $this->nama,
                'nis' => $this->nis ?: null,
                'rombel_id' => $this->rombel_id,
            ]);
            AuditLogService::logCreate($siswa);
            $this->dispatch('show-toast', message: 'Data siswa berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama', 'nis', 'rombel_id', 'editing']);
    }

    public function confirmDeleteSiswa(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $siswa->nama;
        $this->confirmDelete = true;
    }

    public function deleteSiswa()
    {
        $siswa = Siswa::findOrFail($this->deleteId);
        AuditLogService::logDelete($siswa);
        $siswa->kehadiranMurid()->delete();
        $siswa->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Data siswa berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $siswas = Siswa::with('rombel')
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            })
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->orderBy('nama')
            ->paginate(20);

        return view('livewire.admin.manajemen-siswa', [
            'siswas' => $siswas,
            'rombels' => $rombels,
        ]);
    }
}
