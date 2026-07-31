<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Kelas')]
class ManajemenKelas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterTingkat = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama_kelas = '';
    public string $tingkat = '10';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama_kelas', 'tingkat', 'editing']);
        $this->tingkat = '10';
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $rombel = Rombel::findOrFail($id);
        $this->editId = $rombel->id;
        $this->nama_kelas = $rombel->nama_kelas;
        $this->tingkat = (string)$rombel->tingkat;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama_kelas' => 'required|min:2',
            'tingkat' => 'required|in:10,11,12',
        ]);

        if ($this->editing) {
            $rombel = Rombel::findOrFail($this->editId);
            $old = $rombel->toArray();
            $rombel->update([
                'nama_kelas' => $this->nama_kelas,
                'tingkat' => $this->tingkat,
            ]);
            AuditLogService::logUpdate($rombel, $old);
            $this->dispatch('show-toast', message: 'Kelas berhasil diperbarui!', type: 'success');
        } else {
            $rombel = Rombel::create([
                'nama_kelas' => $this->nama_kelas,
                'tingkat' => $this->tingkat,
            ]);
            AuditLogService::logCreate($rombel);
            $this->dispatch('show-toast', message: 'Kelas berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama_kelas', 'tingkat', 'editing']);
    }

    public function confirmDeleteRombel(string $id)
    {
        $rombel = Rombel::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $rombel->nama_kelas;
        $this->confirmDelete = true;
    }

    public function deleteRombel()
    {
        $rombel = Rombel::findOrFail($this->deleteId);
        AuditLogService::logDelete($rombel);
        $rombel->siswa()->delete();
        $rombel->jadwalPelajaran()->delete();
        $rombel->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Kelas berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        $rombels = Rombel::withCount('siswa')
            ->when($this->search, fn($q) => $q->where('nama_kelas', 'like', "%{$this->search}%"))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->paginate(15);

        return view('livewire.admin.manajemen-kelas', ['rombels' => $rombels]);
    }
}
