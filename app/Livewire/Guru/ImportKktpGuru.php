<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Imports\KktpImport;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\TujuanPembelajaran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Import KKTP Guru')]
class ImportKktpGuru extends Component
{
    use WithFileUploads;

    public $file;
    public array $metadata = [];
    public array $tpData = [];
    public ?string $selectedMapelId = null;
    public ?int $selectedTingkat = null;
    public bool $parsed = false;
    public string $error = '';
    public int $importedCount = 0;
    public bool $showResult = false;
    public bool $showAllMapels = false;

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');
        
        // Metadata section
        $sheet->setCellValue('B2', 'FORMAT KKTP (KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN) MGMP');
        $sheet->setCellValue('B4', 'Mata Pelajaran:');
        $sheet->setCellValue('D4', '[Nama Mata Pelajaran / KKA]');
        $sheet->setCellValue('B5', 'Tingkat/Fase:');
        $sheet->setCellValue('D5', '[X/E]');
        $sheet->setCellValue('B6', 'Kelas');
        $sheet->setCellValue('D6', '[X APHP / X TKJ]');
        $sheet->setCellValue('B7', 'Semester:');
        $sheet->setCellValue('D7', '[Ganjil/Genap]');
        $sheet->setCellValue('B8', 'Tahun Pelajaran:');
        $sheet->setCellValue('D8', '[2026/2027]');
        $sheet->setCellValue('B9', 'Nama Guru:');
        $sheet->setCellValue('D9', '[' . (auth()->user()?->name ?? 'Nama Guru') . ']');
        
        // Table header
        $sheet->setCellValue('B11', 'No.');
        $sheet->setCellValue('C11', 'Pertemuan Ke-');
        $sheet->setCellValue('D11', 'Capaian Pembelajaran (CP)');
        $sheet->setCellValue('E11', 'Tujuan Pembelajaran (TP)');
        
        $sheet->getStyle('B11:E11')->getFont()->setBold(true);
        $sheet->getStyle('B11:E11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('B11:E11')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Sample rows
        for ($i = 1; $i <= 20; $i++) {
            $row = 11 + $i;
            $sheet->setCellValue('B' . $row, $i);
            $sheet->setCellValue('C' . $row, 'Pertemuan ' . $i);
        }
        
        foreach (['B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'template_kktp_guru.xlsx';
        $tempPath = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function parse()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240'
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx atau .xls.',
        ]);

        $path = $this->file->getRealPath();
        $importer = new KktpImport();

        if ($importer->parse($path, auth()->id())) {
            $this->metadata = $importer->metadata;
            $this->tpData = $importer->tpData;
            $this->selectedMapelId = $importer->mapelId;
            $this->selectedTingkat = $importer->metadata['detected_tingkat'] ?? null;
            $this->parsed = true;
            $this->error = '';

            // If mapel not found in DB but has name in file, auto-create
            if (!$this->selectedMapelId && !empty($importer->metadata['mapel'])) {
                $cleaned = KktpImport::cleanValue($importer->metadata['mapel']);
                $newMapel = MataPelajaran::firstOrCreate(
                    ['nama_mapel' => $cleaned],
                    ['kode_mapel' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleaned), 0, 10))]
                );
                $this->selectedMapelId = $newMapel->id;
            }
        } else {
            $this->error = 'Gagal membaca file: ' . $importer->error;
            $this->parsed = false;
        }
    }

    public function importData()
    {
        if (empty($this->tpData)) {
            $this->error = 'Tidak ada data TP yang terbaca dari file!';
            return;
        }

        if (!$this->selectedMapelId) {
            $rawMapel = $this->metadata['mapel'] ?? '';
            if (!empty($rawMapel)) {
                $cleaned = KktpImport::cleanValue($rawMapel);
                $newMapel = MataPelajaran::firstOrCreate(
                    ['nama_mapel' => $cleaned],
                    ['kode_mapel' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleaned), 0, 10))]
                );
                $this->selectedMapelId = $newMapel->id;
            } else {
                $this->error = 'Pilih mata pelajaran terlebih dahulu!';
                return;
            }
        }

        $importer = new KktpImport();
        $importer->tpData = $this->tpData;

        $this->importedCount = $importer->import($this->selectedMapelId, auth()->id(), $this->selectedTingkat);
        $this->showResult = true;
        $this->parsed = false;
        $this->dispatch('show-toast', message: "Berhasil mengimport {$this->importedCount} Tujuan Pembelajaran ke akun Anda!", type: 'success');
    }

    public function resetForm()
    {
        $this->reset(['file', 'metadata', 'tpData', 'selectedMapelId', 'selectedTingkat', 'parsed', 'error', 'importedCount', 'showResult']);
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

        if ($mapels->isEmpty()) {
            return MataPelajaran::orderBy('nama_mapel')->get();
        }

        return $mapels;
    }

    public function toggleAllMapels()
    {
        $this->showAllMapels = !$this->showAllMapels;
    }

    public function render()
    {
        $mapels = $this->getMapels();
        $myTpCount = TujuanPembelajaran::where('ketua_mgmp_id', auth()->id())
            ->orWhereIn('mapel_id', $mapels->pluck('id'))
            ->count();

        return view('livewire.guru.import-kktp-guru', [
            'mapels' => $mapels,
            'myTpCount' => $myTpCount,
        ]);
    }
}
