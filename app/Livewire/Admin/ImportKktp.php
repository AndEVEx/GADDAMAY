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

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');
        
        // Metadata section
        $sheet->setCellValue('B2', 'FORMAT KKTP (KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN) MGMP');
        $sheet->setCellValue('B4', 'Mata Pelajaran:');
        $sheet->setCellValue('D4', '[Nama Mata Pelajaran]');
        $sheet->setCellValue('B5', 'Tingkat/Fase:');
        $sheet->setCellValue('D5', '[Tingkat/Fase]');
        $sheet->setCellValue('B6', 'Kelas');
        $sheet->setCellValue('D6', '[Kelas]');
        $sheet->setCellValue('B7', 'Semester:');
        $sheet->setCellValue('D7', '[Ganjil/Genap]');
        $sheet->setCellValue('B8', 'Tahun Pelajaran:');
        $sheet->setCellValue('D8', '[2026/2027]');
        $sheet->setCellValue('B9', 'Nama Guru:');
        $sheet->setCellValue('D9', '[Nama Guru]');
        
        // Table header
        $sheet->setCellValue('B11', 'No.');
        $sheet->setCellValue('C11', 'Pertemuan Ke-');
        $sheet->setCellValue('D11', 'Capaian Pembelajaran (CP)');
        $sheet->setCellValue('E11', 'Tujuan Pembelajaran (TP)');
        
        $sheet->getStyle('B11:E11')->getFont()->setBold(true);
        $sheet->getStyle('B11:E11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('B11:E11')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Sample rows
        for ($i = 1; $i <= 25; $i++) {
            $row = 11 + $i;
            $sheet->setCellValue('B' . $row, $i);
            $sheet->setCellValue('C' . $row, 'Pertemuan ' . $i);
        }
        
        foreach (['B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'template_kktp.xlsx';
        $tempPath = storage_path('app/' . $filename);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

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

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data TP KKTP');

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode TP');
        $sheet->setCellValue('C1', 'Deskripsi TP');
        $sheet->setCellValue('D1', 'Mata Pelajaran');
        
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $tps = TujuanPembelajaran::with('mataPelajaran')->get();

        $row = 2;
        $no = 1;
        foreach ($tps as $tp) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $tp->kode_tp);
            $sheet->setCellValue('C' . $row, $tp->deskripsi_tp);
            $sheet->setCellValue('D' . $row, $tp->mataPelajaran->nama_mapel ?? '-');
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_kktp_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $tps = TujuanPembelajaran::with('mataPelajaran')->orderBy('mapel_id')->orderBy('order_sequence')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data KKTP TP');

        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Mata Pelajaran');
        $sheet->setCellValue('C1', 'Kode TP');
        $sheet->setCellValue('D1', 'Deskripsi TP');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        foreach ($tps as $index => $tp) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $tp->mataPelajaran->nama_mapel ?? '-');
            $sheet->setCellValue('C' . $row, $tp->kode_tp);
            $sheet->setCellValue('D' . $row, $tp->deskripsi_tp);
            $row++;
        }

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_kktp_tp_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
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
