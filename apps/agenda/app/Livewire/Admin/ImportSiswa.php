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

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'Nama Siswa');
        $sheet->setCellValue('D1', 'Kelas');
        
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $siswas = Siswa::with('rombel')->orderBy('nama')->get();

        $row = 2;
        $no = 1;
        foreach ($siswas as $siswa) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $row, $siswa->nama);
            $sheet->setCellValue('D' . $row, $siswa->rombel->nama_kelas ?? '-');
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_siswa_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $siswas = Siswa::with('rombel')->orderBy('nama')->get();

        $headers = [
            ['name' => 'No', 'width' => '6%', 'align' => 'center'],
            ['name' => 'NIS / NISN', 'width' => '20%', 'align' => 'center'],
            ['name' => 'Nama Lengkap Peserta Didik', 'width' => '50%', 'align' => 'left'],
            ['name' => 'Kelas / Rombel', 'width' => '24%', 'align' => 'center'],
        ];

        $rows = [];
        $no = 1;
        foreach ($siswas as $s) {
            $rows[] = [
                $no++,
                e($s->nis ?? '-'),
                '<strong>' . e($s->nama) . '</strong>',
                e($s->rombel->nama_kelas ?? '-'),
            ];
        }

        $filename = 'Laporan_Data_Siswa_' . date('Y-m-d') . '.pdf';
        return \App\Services\PdfReportService::download(
            'LAPORAN DATA PESERTA DIDIK',
            'Sistem Informasi Agenda Guru SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'portrait',
            ['Total Siswa Terdaftar' => count($rows) . ' Orang']
        );
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
