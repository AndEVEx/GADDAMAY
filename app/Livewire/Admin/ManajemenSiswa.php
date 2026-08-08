<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Siswa')]
class ManajemenSiswa extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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

    public function exportExcel()
    {
        $siswas = Siswa::with('rombel')->orderBy('nama')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'Nama Siswa');
        $sheet->setCellValue('D1', 'Kelas / Rombel');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        foreach ($siswas as $index => $s) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $s->nis ?? '-');
            $sheet->setCellValue('C' . $row, $s->nama);
            $sheet->setCellValue('D' . $row, $s->rombel->nama_kelas ?? '-');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_data_siswa_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
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
