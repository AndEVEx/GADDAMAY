<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Services\AuditLogService;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Manajemen Siswa')]
class ManajemenSiswa extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $importFile;
    public string $search = '';
    public string $filterRombel = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama = '';
    public string $nis = '';
    public string $rombel_id = '';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRombel() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama', 'nis', 'rombel_id', 'editing']);
        $firstRombel = Rombel::orderBy('nama_kelas')->first();
        if ($firstRombel) {
            $this->rombel_id = $firstRombel->id;
        }
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->editId = $siswa->id;
        $this->nama = $siswa->nama;
        $this->nis = $siswa->nis ?? '';
        $this->rombel_id = $siswa->rombel_id;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|min:2',
            'nis' => 'nullable|string',
            'rombel_id' => 'required|exists:rombel,id',
        ]);

        if ($this->editing) {
            $siswa = Siswa::findOrFail($this->editId);
            $old = $siswa->toArray();
            $siswa->update([
                'nama' => $this->nama,
                'nis' => $this->nis ?: null,
                'rombel_id' => $this->rombel_id,
            ]);
            AuditLogService::logUpdate($siswa, $old);
            $this->dispatch('show-toast', message: 'Data siswa berhasil diperbarui!', type: 'success');
        } else {
            $siswa = Siswa::create([
                'nama' => $this->nama,
                'nis' => $this->nis ?: null,
                'rombel_id' => $this->rombel_id,
            ]);
            AuditLogService::logCreate($siswa);
            $this->dispatch('show-toast', message: 'Data siswa berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama', 'nis', 'rombel_id', 'editing']);
    }

    public function confirmDeleteSiswa(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $siswa->nama;
        $this->confirmDelete = true;
    }

    public function deleteSiswa()
    {
        $siswa = Siswa::findOrFail($this->deleteId);
        AuditLogService::logDelete($siswa);
        $siswa->kehadiranMurid()->delete();
        $siswa->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Data siswa berhasil dihapus!', type: 'success');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');
        
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'nis');
        $sheet->setCellValue('C1', 'kelas');
        
        $sheet->setCellValue('A2', 'Ahmad Fauzi');
        $sheet->setCellValue('B2', '12345');
        $sheet->setCellValue('C2', 'X TKJ 1');
        
        $sheet->setCellValue('A3', 'Siti Nurhaliza');
        $sheet->setCellValue('B3', '12346');
        $sheet->setCellValue('C3', 'X TKJ 1');
        
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

    public function importData()
    {
        try {
            $this->validate([
                'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            ]);

            Excel::import(new SiswaImport, $this->importFile->getRealPath());
            $this->dispatch('show-toast', message: 'Data siswa berhasil diimport!', type: 'success');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $this->dispatch('show-toast', message: 'File tidak valid: ' . implode(', ', $ve->validator->errors()->all()), type: 'danger');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengimport data: ' . $e->getMessage(), type: 'danger');
        } finally {
            $this->reset('importFile');
        }
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

        $siswas = Siswa::with('rombel')
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            })
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->orderBy('nama')->get();

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

    public function render()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $siswas = Siswa::with('rombel')
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            })
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->orderBy('nama')
            ->paginate(20);

        return view('livewire.admin.manajemen-siswa', [
            'siswas' => $siswas,
            'rombels' => $rombels,
        ]);
    }
}
