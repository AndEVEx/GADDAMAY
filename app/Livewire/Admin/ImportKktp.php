<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Imports\KktpImport;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;

#[Layout('components.layouts.app')]
#[Title('Import KKTP')]
class ImportKktp extends Component
{
    use WithFileUploads;

    public $file;
    public array $metadata = [];
    public array $tpData = [];
    public ?string $selectedMapelId = null;
    public bool $parsed = false;
    public string $error = '';
    public int $importedCount = 0;
    public bool $showResult = false;

    public function parse()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls|max:5120']);

        $path = $this->file->getRealPath();
        $importer = new KktpImport();

        if ($importer->parse($path)) {
            $this->metadata = $importer->metadata;
            $this->tpData = $importer->tpData;
            $this->selectedMapelId = $importer->mapelId;
            $this->parsed = true;
            $this->error = '';
        } else {
            $this->error = 'Gagal membaca file: ' . $importer->error;
            $this->parsed = false;
        }
    }

    public function importData()
    {
        if (!$this->selectedMapelId) {
            $this->error = 'Pilih mata pelajaran terlebih dahulu!';
            return;
        }

        $path = $this->file->getRealPath();
        $importer = new KktpImport();
        $importer->parse($path);
        $importer->tpData = $this->tpData;

        $this->importedCount = $importer->import($this->selectedMapelId);
        $this->showResult = true;
        $this->parsed = false;
        $this->dispatch('show-toast', message: "Berhasil import {$this->importedCount} Tujuan Pembelajaran!", type: 'success');
    }

    public function resetForm()
    {
        $this->reset(['file', 'metadata', 'tpData', 'selectedMapelId', 'parsed', 'error', 'importedCount', 'showResult']);
    }

    public function render()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $existingTpCount = TujuanPembelajaran::count();

        return view('livewire.admin.import-kktp', [
            'mapels' => $mapels,
            'existingTpCount' => $existingTpCount,
        ]);
    }
}
