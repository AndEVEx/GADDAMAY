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
    public string $filterPkl = ''; // '', '1', '0'
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama_kelas = '';
    public string $tingkat = '10';
    public bool $is_pkl = false;
    public string $pkl_keterangan = '';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterPkl() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama_kelas', 'tingkat', 'is_pkl', 'pkl_keterangan', 'editing']);
        $this->tingkat = '10';
        $this->is_pkl = false;
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $rombel = Rombel::findOrFail($id);
        $this->editId = $rombel->id;
        $this->nama_kelas = $rombel->nama_kelas;
        $this->tingkat = (string)$rombel->tingkat;
        $this->is_pkl = (bool)$rombel->is_pkl;
        $this->pkl_keterangan = (string)$rombel->pkl_keterangan;
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
                'is_pkl' => $this->is_pkl,
                'pkl_keterangan' => $this->pkl_keterangan ?: null,
            ]);
            AuditLogService::logUpdate($rombel, $old);
            $this->dispatch('show-toast', message: 'Kelas berhasil diperbarui!', type: 'success');
        } else {
            $rombel = Rombel::create([
                'nama_kelas' => $this->nama_kelas,
                'tingkat' => $this->tingkat,
                'is_pkl' => $this->is_pkl,
                'pkl_keterangan' => $this->pkl_keterangan ?: null,
            ]);
            AuditLogService::logCreate($rombel);
            $this->dispatch('show-toast', message: 'Kelas berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama_kelas', 'tingkat', 'is_pkl', 'pkl_keterangan', 'editing']);
    }

    public function togglePkl(string $id)
    {
        $rombel = Rombel::findOrFail($id);
        $old = $rombel->toArray();
        $newStatus = !$rombel->is_pkl;
        $rombel->update([
            'is_pkl' => $newStatus,
            'pkl_keterangan' => $newStatus ? ($rombel->pkl_keterangan ?: 'Sedang PKL di Industri') : null,
        ]);
        AuditLogService::logUpdate($rombel, $old);

        $statusText = $newStatus ? 'diaktifkan (PKL)' : 'dinonaktifkan (Reguler)';
        $this->dispatch('show-toast', message: "Status PKL kelas {$rombel->nama_kelas} {$statusText}!", type: 'success');
    }

    public function setJurusanPkl(string $jurusan, bool $status)
    {
        $jurusan = trim($jurusan);
        $rombels = Rombel::where('nama_kelas', 'like', "%{$jurusan}%")->get();

        if ($rombels->isEmpty()) {
            $this->dispatch('show-toast', message: "Tidak ditemukan kelas dengan jurusan {$jurusan}.", type: 'warning');
            return;
        }

        $count = 0;
        foreach ($rombels as $rombel) {
            $old = $rombel->toArray();
            $rombel->update([
                'is_pkl' => $status,
                'pkl_keterangan' => $status ? "PKL Jurusan {$jurusan}" : null,
            ]);
            AuditLogService::logUpdate($rombel, $old);
            $count++;
        }

        $actionText = $status ? "diatur sebagai PKL" : "diatur sebagai Reguler (Non-PKL)";
        $this->dispatch('show-toast', message: "Berhasil! {$count} kelas jurusan {$jurusan} {$actionText}.", type: 'success');
    }

    public function resetAllPkl()
    {
        $count = Rombel::where('is_pkl', true)->update([
            'is_pkl' => false,
            'pkl_keterangan' => null,
        ]);

        $this->dispatch('show-toast', message: "Semua status PKL ({$count} kelas) berhasil direset ke Reguler!", type: 'info');
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
        $sheet->setCellValue('C1', 'is_pkl');
        $sheet->setCellValue('A2', 'X NKPI 1');
        $sheet->setCellValue('B2', '10');
        $sheet->setCellValue('C2', '1');
        $sheet->setCellValue('A3', 'XI TP 1');
        $sheet->setCellValue('B3', '11');
        $sheet->setCellValue('C3', '0');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setRGB('FFFFFF');
        foreach (['A','B','C'] as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
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
                $isPklRaw = trim($row['C'] ?? '0');
                $isPkl = in_array(strtolower($isPklRaw), ['1', 'true', 'ya', 'yes', 'pkl']);
                if (empty($nama_kelas) || empty($tingkat)) continue;
                \App\Models\Rombel::updateOrCreate(
                    ['nama_kelas' => $nama_kelas],
                    [
                        'tingkat' => $tingkat,
                        'is_pkl' => $isPkl,
                    ]
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
        $sheet->setCellValue('D1', 'Status PKL');
        $sheet->setCellValue('E1', 'Jumlah Siswa');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');

        $row = 2;
        $no = 1;
        foreach ($rombels as $rombel) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $rombel->nama_kelas);
            $sheet->setCellValue('C' . $row, 'Tingkat ' . $rombel->tingkat);
            $sheet->setCellValue('D' . $row, $rombel->is_pkl ? 'PKL (Industri)' : 'Reguler');
            $sheet->setCellValue('E' . $row, $rombel->siswa_count . ' Siswa');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_data_kelas_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $rombels = Rombel::withCount('siswa')
            ->when($this->search, fn($q) => $q->where('nama_kelas', 'like', "%{$this->search}%"))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterPkl !== '', fn($q) => $q->where('is_pkl', (bool)$this->filterPkl))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $headers = [
            ['name' => 'No', 'width' => '6%', 'align' => 'center'],
            ['name' => 'Nama Rombongan Belajar (Kelas)', 'width' => '38%', 'align' => 'left'],
            ['name' => 'Tingkat / Fase', 'width' => '18%', 'align' => 'center'],
            ['name' => 'Status', 'width' => '18%', 'align' => 'center'],
            ['name' => 'Jumlah Siswa', 'width' => '20%', 'align' => 'center'],
        ];

        $rows = [];
        $no = 1;
        $totalSiswa = 0;
        $totalPkl = 0;
        foreach ($rombels as $r) {
            $totalSiswa += $r->siswa_count;
            if ($r->is_pkl) $totalPkl++;
            $statusBadge = $r->is_pkl ? '<span style="color: #059669; font-weight: bold;">🏢 PKL</span>' : 'Reguler';
            $rows[] = [
                $no++,
                '<strong>' . e($r->nama_kelas) . '</strong>',
                'Tingkat ' . e($r->tingkat),
                $statusBadge,
                e($r->siswa_count) . ' Siswa',
            ];
        }

        $filename = 'Laporan_Data_Kelas_' . date('Y-m-d') . '.pdf';
        return \App\Services\PdfReportService::download(
            'LAPORAN DATA KELAS & ROMBONGAN BELAJAR',
            'Sistem Informasi Agenda Guru SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'portrait',
            [
                'Total Rombel' => count($rows) . ' Kelas',
                'Kelas PKL' => $totalPkl . ' Kelas',
                'Total Siswa' => $totalSiswa . ' Siswa',
            ]
        );
    }

    public function render()
    {
        $rombels = Rombel::withCount('siswa')
            ->when($this->search, fn($q) => $q->where('nama_kelas', 'like', "%{$this->search}%"))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterPkl !== '', fn($q) => $q->where('is_pkl', (bool)$this->filterPkl))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->paginate(15);

        $jurusanList = ['NKPI', 'TP', 'TPM', 'APHP', 'APHPi', 'RPL', 'TKJ', 'TKRO', 'TBSM', 'DKV'];

        return view('livewire.admin.manajemen-kelas', [
            'rombels' => $rombels,
            'jurusanList' => $jurusanList,
        ]);
    }
}
