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

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');
        
        // Headers
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'nis');
        $sheet->setCellValue('C1', 'kelas');
        
        // Example data
        $sheet->setCellValue('A2', 'Ahmad Fauzi');
        $sheet->setCellValue('B2', '12345');
        $sheet->setCellValue('C2', 'X TKJ 1');
        
        $sheet->setCellValue('A3', 'Siti Nurhaliza');
        $sheet->setCellValue('B3', '12346');
        $sheet->setCellValue('C3', 'X TKJ 1');
        
        // Style header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setRGB('FFFFFF');
        
        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'template_import_siswa.xlsx';
        $tempPath = storage_path('app/' . $filename);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

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
