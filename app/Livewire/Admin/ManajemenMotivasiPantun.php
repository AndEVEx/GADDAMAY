<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MotivasiPantun;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Motivasi & Pantun')]
class ManajemenMotivasiPantun extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $importFile;
    public string $search = '';
    public string $filterTipe = '';
    public string $filterKategori = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $isi = '';
    public string $tipe = 'pantun';
    public string $kategori = 'sebelum_mengajar';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteContent = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTipe() { $this->resetPage(); }
    public function updatingFilterKategori() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'isi', 'tipe', 'kategori', 'editing']);
        $this->tipe = 'pantun';
        $this->kategori = 'sebelum_mengajar';
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $item = MotivasiPantun::findOrFail($id);
        $this->editId = $item->id;
        $this->isi = $item->isi;
        $this->tipe = $item->tipe;
        $this->kategori = $item->kategori;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'isi' => 'required|min:5',
            'tipe' => 'required|in:pantun,kata_mutiara',
            'kategori' => 'required|in:sebelum_mengajar,siap_mengajar',
        ]);

        if ($this->editing) {
            $item = MotivasiPantun::findOrFail($this->editId);
            $old = $item->toArray();
            $item->update([
                'isi' => $this->isi,
                'tipe' => $this->tipe,
                'kategori' => $this->kategori,
            ]);
            AuditLogService::logUpdate($item, $old);
            $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil diperbarui!', type: 'success');
        } else {
            $item = MotivasiPantun::create([
                'isi' => $this->isi,
                'tipe' => $this->tipe,
                'kategori' => $this->kategori,
            ]);
            AuditLogService::logCreate($item);
            $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'isi', 'tipe', 'kategori', 'editing']);
    }

    public function confirmDeleteMotivasi(string $id)
    {
        $item = MotivasiPantun::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteContent = mb_strimwidth($item->isi, 0, 50, '...');
        $this->confirmDelete = true;
    }

    public function deleteMotivasi()
    {
        $item = MotivasiPantun::findOrFail($this->deleteId);
        AuditLogService::logDelete($item);
        $item->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Motivasi / Pantun berhasil dihapus!', type: 'success');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Motivasi');

        // Headers
        $sheet->setCellValue('A1', 'isi');
        $sheet->setCellValue('B1', 'tipe');
        $sheet->setCellValue('C1', 'kategori');

        // Sample Data
        $sheet->setCellValue('A2', 'Berlayar ke pulau membawa kail, Jangan lupa membawa bekal. Semangat terus anak yang rajin, Sukses kelak pasti terkejar.');
        $sheet->setCellValue('B2', 'pantun');
        $sheet->setCellValue('C2', 'sebelum_mengajar');

        $sheet->setCellValue('A3', 'Pendidikan adalah senjata paling mematikan di dunia, karena dengan pendidikan Anda dapat mengubah dunia.');
        $sheet->setCellValue('B3', 'kata_mutiara');
        $sheet->setCellValue('C3', 'siap_mengajar');

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setRGB('FFFFFF');

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_motivasi_pantun.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function importData()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;

            foreach ($rows as $row) {
                $isi = trim($row['A'] ?? '');
                $tipe = strtolower(trim($row['B'] ?? 'pantun'));
                $kategori = strtolower(trim($row['C'] ?? 'sebelum_mengajar'));

                if (empty($isi)) continue;

                $tipeValid = in_array($tipe, ['pantun', 'kata_mutiara']) ? $tipe : 'pantun';
                $kategoriValid = in_array($kategori, ['sebelum_mengajar', 'siap_mengajar']) ? $kategori : 'sebelum_mengajar';

                MotivasiPantun::create([
                    'isi' => $isi,
                    'tipe' => $tipeValid,
                    'kategori' => $kategoriValid,
                ]);
                $count++;
            }

            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil meng-import {$count} motivasi / pantun!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal meng-import: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Motivasi Pantun');

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tipe');
        $sheet->setCellValue('C1', 'Isi');
        $sheet->setCellValue('D1', 'Status Aktif');
        
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $items = MotivasiPantun::when($this->search, fn($q) => $q->where('isi', 'like', "%{$this->search}%"))
            ->when($this->filterTipe, fn($q) => $q->where('tipe', $this->filterTipe))
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy('created_at', 'desc')->get();

        $row = 2;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->tipe === 'pantun' ? 'Pantun' : 'Motivasi');
            $sheet->setCellValue('C' . $row, $item->isi);
            $sheet->setCellValue('D' . $row, 'Aktif');
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_motivasi_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $items = MotivasiPantun::when($this->search, fn($q) => $q->where('isi', 'like', "%{$this->search}%"))
            ->when($this->filterTipe, fn($q) => $q->where('tipe', $this->filterTipe))
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.manajemen-motivasi-pantun', ['items' => $items]);
    }
}
