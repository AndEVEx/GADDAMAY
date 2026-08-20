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
    public bool $showAllMapels = false;
    public string $newKodeTP = '';
    public string $newDeskripsiTP = '';
    public ?string $editingTpId = null;
    public string $editKodeTP = '';
    public string $editDeskripsiTP = '';
    public bool $showImport = false;
    public $importFile;
    public array $importPreview = [];
    public array $importMetadata = [];
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
        if ($this->showAllMapels) {
            return MataPelajaran::orderBy('nama_mapel')->get();
        }

        $mapelIds = JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $user->id))
            ->whereNotNull('mapel_id')
            ->pluck('mapel_id')
            ->unique();

        $mapels = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();

        // If no mapel mapped to this teacher, automatically fallback to all mapels
        if ($mapels->isEmpty()) {
            return MataPelajaran::orderBy('nama_mapel')->get();
        }

        return $mapels;
    }

    public function toggleAllMapels()
    {
        $this->showAllMapels = !$this->showAllMapels;
    }

    public function addTp()
    {
        $this->validate([
            'selectedMapelId' => 'required|exists:mata_pelajaran,id',
            'newKodeTP' => 'required|max:30',
            'newDeskripsiTP' => 'required|min:3',
        ], [
            'selectedMapelId.required' => 'Pilih mata pelajaran terlebih dahulu.',
            'newKodeTP.required' => 'Kode TP wajib diisi.',
            'newDeskripsiTP.required' => 'Deskripsi TP wajib diisi.',
        ]);

        $maxOrder = TujuanPembelajaran::where('mapel_id', $this->selectedMapelId)->max('order_sequence') ?? 0;

        TujuanPembelajaran::create([
            'mapel_id' => $this->selectedMapelId,
            'kode_tp' => $this->newKodeTP,
            'deskripsi_tp' => $this->newDeskripsiTP,
            'order_sequence' => $maxOrder + 1,
            'ketua_mgmp_id' => auth()->id(),
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
            'editKodeTP' => 'required|max:30',
            'editDeskripsiTP' => 'required|min:3',
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
        $this->reset(['importFile', 'importPreview', 'importMetadata', 'importParsed']);
    }

    public function parseImport()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls|max:10240'
        ], [
            'importFile.required' => 'Pilih file Excel terlebih dahulu.',
            'importFile.mimes' => 'Format file harus .xlsx atau .xls.',
        ]);

        $importer = new KktpImport();
        if ($importer->parse($this->importFile->getRealPath())) {
            $this->importPreview = $importer->tpData;
            $this->importMetadata = $importer->metadata;
            $this->importParsed = true;

            // Auto-select mapel if detected from file
            if ($importer->mapelId) {
                $this->selectedMapelId = $importer->mapelId;
            } elseif (!empty($importer->metadata['mapel'])) {
                // If mapel doesn't exist in DB, create it automatically
                $cleaned = KktpImport::cleanValue($importer->metadata['mapel']);
                $newMapel = MataPelajaran::firstOrCreate(
                    ['nama_mapel' => $cleaned],
                    ['kode_mapel' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleaned), 0, 10))]
                );
                $this->selectedMapelId = $newMapel->id;
            }
        } else {
            $this->dispatch('show-toast', message: 'Gagal membaca file: ' . $importer->error, type: 'danger');
        }
    }

    public function executeImport()
    {
        if (empty($this->importPreview)) {
            $this->dispatch('show-toast', message: 'Tidak ada data TP untuk diimport.', type: 'warning');
            return;
        }

        if (!$this->selectedMapelId) {
            $rawMapel = $this->importMetadata['mapel'] ?? '';
            if (!empty($rawMapel)) {
                $cleaned = KktpImport::cleanValue($rawMapel);
                $newMapel = MataPelajaran::firstOrCreate(
                    ['nama_mapel' => $cleaned],
                    ['kode_mapel' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleaned), 0, 10))]
                );
                $this->selectedMapelId = $newMapel->id;
            } else {
                $this->dispatch('show-toast', message: 'Pilih mata pelajaran terlebih dahulu!', type: 'danger');
                return;
            }
        }

        $importer = new KktpImport();
        $importer->tpData = $this->importPreview;
        $count = $importer->import($this->selectedMapelId, auth()->id());

        $this->reset(['importFile', 'importPreview', 'importMetadata', 'importParsed', 'showImport']);
        $this->dispatch('show-toast', message: "Berhasil mengimport {$count} Tujuan Pembelajaran!", type: 'success');
    }

    public function render()
    {
        $mapels = $this->getMapels();
        
        // If selectedMapelId is set, fetch TPs
        $tps = $this->selectedMapelId
            ? TujuanPembelajaran::where('mapel_id', $this->selectedMapelId)->orderBy('order_sequence')->get()
            : collect();

        $currentMapel = $this->selectedMapelId ? MataPelajaran::find($this->selectedMapelId) : null;

        return view('livewire.guru.setting-kktp', [
            'mapels' => $mapels,
            'tps' => $tps,
            'currentMapel' => $currentMapel,
        ]);
    }
}
