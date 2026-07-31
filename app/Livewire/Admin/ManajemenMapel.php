<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\MataPelajaran;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Mata Pelajaran')]
class ManajemenMapel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama_mapel = '';
    public string $kode_mapel = '';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama_mapel', 'kode_mapel', 'editing']);
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $this->editId = $mapel->id;
        $this->nama_mapel = $mapel->nama_mapel;
        $this->kode_mapel = $mapel->kode_mapel ?? '';
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama_mapel' => 'required|min:2',
            'kode_mapel' => 'required',
        ]);

        if ($this->editing) {
            $mapel = MataPelajaran::findOrFail($this->editId);
            $old = $mapel->toArray();
            $mapel->update([
                'nama_mapel' => $this->nama_mapel,
                'kode_mapel' => $this->kode_mapel,
            ]);
            AuditLogService::logUpdate($mapel, $old);
            $this->dispatch('show-toast', message: 'Mata pelajaran berhasil diperbarui!', type: 'success');
        } else {
            $mapel = MataPelajaran::create([
                'nama_mapel' => $this->nama_mapel,
                'kode_mapel' => $this->kode_mapel,
            ]);
            AuditLogService::logCreate($mapel);
            $this->dispatch('show-toast', message: 'Mata pelajaran berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama_mapel', 'kode_mapel', 'editing']);
    }

    public function confirmDeleteMapel(string $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $mapel->nama_mapel;
        $this->confirmDelete = true;
    }

    public function deleteMapel()
    {
        $mapel = MataPelajaran::findOrFail($this->deleteId);
        AuditLogService::logDelete($mapel);
        $mapel->tujuanPembelajaran()->delete();
        $mapel->jadwalPelajaran()->delete();
        $mapel->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Mata pelajaran berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        $mapels = MataPelajaran::when($this->search, function($q) {
                $q->where('nama_mapel', 'like', "%{$this->search}%")
                  ->orWhere('kode_mapel', 'like', "%{$this->search}%");
            })
            ->orderBy('nama_mapel')
            ->paginate(15);

        return view('livewire.admin.manajemen-mapel', ['mapels' => $mapels]);
    }
}
