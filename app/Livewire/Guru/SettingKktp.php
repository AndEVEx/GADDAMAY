<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\TujuanPembelajaran;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Imports\KktpImport;

#[Layout('components.layouts.app')]
#[Title('Setting KKTP')]
class SettingKktp extends Component
{
    use WithFileUploads;

    public ?string $selectedMapelId = null;
    public string $newKodeTP = '';
    public string $newDeskripsiTP = '';
    public ?string $editingTpId = null;
    public string $editKodeTP = '';
    public string $editDeskripsiTP = '';
    public bool $showImport = false;
    public $importFile;
    public array $importPreview = [];
    public bool $importParsed = false;

    public function mount()
    {
        $mapels = $this->getMapels();
        if ($mapels->count() === 1) {
            $this->selectedMapelId = $mapels->first()->id;
        }
    }

    public function getMapels()
    {
        $user = auth()->user();
        $mapelIds = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('mapel_id')
            ->pluck('mapel_id')
            ->unique();
        return MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
    }

    public function addTp()
    {
        $this->validate([
            'newKodeTP' => 'required|max:20',
            'newDeskripsiTP' => 'required|min:5',
        ]);

        $maxOrder = TujuanPembelajaran::where('mapel_id', $this->selectedMapelId)->max('order_sequence') ?? 0;

        TujuanPembelajaran::create([
            'mapel_id' => $this->selectedMapelId,
            'kode_tp' => $this->newKodeTP,
            'deskripsi_tp' => $this->newDeskripsiTP,
            'order_sequence' => $maxOrder + 1,
        ]);

        $this->reset(['newKodeTP', 'newDeskripsiTP']);
        $this->dispatch('show-toast', message: 'Tujuan Pembelajaran berhasil ditambahkan!', type: 'success');
    }

    public function startEdit(string $tpId)
    {
        $tp = TujuanPembelajaran::find($tpId);
        if ($tp) {
            $this->editingTpId = $tpId;
            $this->editKodeTP = $tp->kode_tp;
            $this->editDeskripsiTP = $tp->deskripsi_tp;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editingTpId', 'editKodeTP', 'editDeskripsiTP']);
    }

    public function saveTpEdit()
    {
        $this->validate([
            'editKodeTP' => 'required|max:20',
            'editDeskripsiTP' => 'required|min:5',
        ]);

        $tp = TujuanPembelajaran::find($this->editingTpId);
        if ($tp) {
            $tp->update([
                'kode_tp' => $this->editKodeTP,
                'deskripsi_tp' => $this->editDeskripsiTP,
            ]);
        }

        $this->cancelEdit();
        $this->dispatch('show-toast', message: 'TP berhasil diperbarui!', type: 'success');
    }

    public function deleteTp(string $tpId)
    {
        TujuanPembelajaran::find($tpId)?->delete();
        $this->dispatch('show-toast', message: 'TP berhasil dihapus!', type: 'info');
    }

    public function toggleImport()
    {
        $this->showImport = !$this->showImport;
        $this->reset(['importFile', 'importPreview', 'importParsed']);
    }

    public function parseImport()
    {
        $this->validate(['importFile' => 'required|mimes:xlsx,xls|max:5120']);

        $importer = new KktpImport();
        if ($importer->parse($this->importFile->getRealPath())) {
            $this->importPreview = $importer->tpData;
            $this->importParsed = true;
        } else {
            $this->dispatch('show-toast', message: 'Gagal membaca file: ' . $importer->error, type: 'danger');
        }
    }

    public function executeImport()
    {
        if (!$this->selectedMapelId || empty($this->importPreview)) return;

        $importer = new KktpImport();
        $importer->tpData = $this->importPreview;
        $count = $importer->import($this->selectedMapelId);

        $this->reset(['importFile', 'importPreview', 'importParsed', 'showImport']);
        $this->dispatch('show-toast', message: "Berhasil import {$count} Tujuan Pembelajaran!", type: 'success');
    }

    public function render()
    {
        $mapels = $this->getMapels();
        $tps = $this->selectedMapelId
            ? TujuanPembelajaran::where('mapel_id', $this->selectedMapelId)->orderBy('order_sequence')->get()
            : collect();

        return view('livewire.guru.setting-kktp', [
            'mapels' => $mapels,
            'tps' => $tps,
        ]);
    }
}
