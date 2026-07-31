<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Import Data Siswa')]
class ImportSiswa extends Component
{
    use WithFileUploads;

    public $file;
    public bool $confirmClearAll = false;

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new SiswaImport, $this->file->getRealPath());
            $this->reset('file');
            $this->dispatch('show-toast', message: 'Data siswa berhasil diimport!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengimport data: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function confirmClear()
    {
        $this->confirmClearAll = true;
    }

    public function clearAllSiswa()
    {
        Siswa::query()->delete();
        $this->confirmClearAll = false;
        $this->dispatch('show-toast', message: 'Semua data siswa berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        $totalSiswa = Siswa::count();
        $totalRombel = Rombel::count();
        $rombels = Rombel::withCount('siswa')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('livewire.admin.import-siswa', [
            'totalSiswa' => $totalSiswa,
            'totalRombel' => $totalRombel,
            'rombels' => $rombels,
        ]);
    }
}
