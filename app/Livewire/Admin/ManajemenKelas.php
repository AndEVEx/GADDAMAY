<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Rombel;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Kelas')]
class ManajemenKelas extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $importFile;

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

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelas');
        $sheet->setCellValue('A1', 'nama_kelas');
        $sheet->setCellValue('B1', 'tingkat');
        $sheet->setCellValue('A2', 'X TKJ 1');
        $sheet->setCellValue('B2', '10');
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');
        foreach (['A','B'] as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $path = storage_path('app/template_import_kelas.xlsx');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);
        return response()->download($path, 'template_import_kelas.xlsx')->deleteFileAfterSend(true);
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
                $nama_kelas = trim($row['A'] ?? '');
                $tingkat = trim($row['B'] ?? '');
                if (empty($nama_kelas) || empty($tingkat)) continue;
                \App\Models\Rombel::updateOrCreate(
                    ['nama_kelas' => $nama_kelas],
                    ['tingkat' => $tingkat]
                );
                $count++;
            }
            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil import {$count} kelas!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal import: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $rombels = Rombel::withCount('siswa')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelas');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Kelas');
        $sheet->setCellValue('C1', 'Tingkat / Fase');
        $sheet->setCellValue('D1', 'Jumlah Siswa');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        $no = 1;
        foreach ($rombels as $rombel) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $rombel->nama_kelas);
            $sheet->setCellValue('C' . $row, 'Tingkat ' . $rombel->tingkat);
            $sheet->setCellValue('D' . $row, $rombel->siswa_count . ' Siswa');
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_data_kelas_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
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
