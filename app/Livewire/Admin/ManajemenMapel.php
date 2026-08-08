<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MataPelajaran;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Mata Pelajaran')]
class ManajemenMapel extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $importFile;

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

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Mapel');
        $sheet->setCellValue('A1', 'nama_mapel');
        $sheet->setCellValue('B1', 'kode_mapel');
        $sheet->setCellValue('A2', 'Matematika');
        $sheet->setCellValue('B2', 'MTK');
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');
        foreach (['A','B'] as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $path = storage_path('app/template_import_mapel.xlsx');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);
        return response()->download($path, 'template_import_mapel.xlsx')->deleteFileAfterSend(true);
    }

    public function importData()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $header = array_shift($rows);
            $count = 0;
            foreach ($rows as $row) {
                $nama_mapel = trim($row['A'] ?? '');
                $kode_mapel = trim($row['B'] ?? '');
                if (empty($nama_mapel) || empty($kode_mapel)) continue;
                \App\Models\MataPelajaran::updateOrCreate(
                    ['nama_mapel' => $nama_mapel],
                    ['kode_mapel' => $kode_mapel]
                );
                $count++;
            }
            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil import {$count} mata pelajaran!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal import: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Mapel');

        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Kode Mapel');
        $sheet->setCellValue('C1', 'Nama Mata Pelajaran');

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        foreach ($mapels as $index => $m) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $m->kode_mapel ?? '-');
            $sheet->setCellValue('C' . $row, $m->nama_mapel);
            $row++;
        }

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_mata_pelajaran_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Mapel');

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Mapel');
        $sheet->setCellValue('C1', 'Nama Mata Pelajaran');
        
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setRGB('FFFFFF');

        $mapels = MataPelajaran::when($this->search, function($q) {
                $q->where('nama_mapel', 'like', "%{$this->search}%")
                  ->orWhere('kode_mapel', 'like', "%{$this->search}%");
            })
            ->orderBy('nama_mapel')->get();

        $row = 2;
        $no = 1;
        foreach ($mapels as $mapel) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $mapel->kode_mapel);
            $sheet->setCellValue('C' . $row, $mapel->nama_mapel);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_mapel_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
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
