<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\HariLibur;
use App\Services\AuditLogService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

#[Layout('components.layouts.app')]
#[Title('Manajemen Hari Libur')]
class ManajemenHariLibur extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $tab = 'upcoming'; // 'upcoming', 'all', 'past'
    public string $search = '';
    public string $filterTipe = 'all';

    // Form Modal State
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama_hari_libur = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $tipe_libur = 'nasional';
    public string $keterangan = '';

    // Delete Modal State
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    // Import State
    public $importFile;
    public bool $showImportModal = false;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTipe() { $this->resetPage(); }
    public function updatingTab() { $this->resetPage(); }

    public function mount()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->tanggal_mulai = $today;
        $this->tanggal_selesai = $today;
    }

    public function create()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->reset(['editId', 'nama_hari_libur', 'keterangan', 'editing']);
        $this->tanggal_mulai = $today;
        $this->tanggal_selesai = $today;
        $this->tipe_libur = 'nasional';
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $libur = HariLibur::findOrFail($id);
        $this->editId = $libur->id;
        $this->nama_hari_libur = $libur->nama_hari_libur;
        $this->tanggal_mulai = $libur->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $libur->tanggal_selesai->format('Y-m-d');
        $this->tipe_libur = $libur->tipe_libur;
        $this->keterangan = $libur->keterangan ?? '';
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama_hari_libur' => 'required|min:3|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe_libur' => 'required|in:nasional,sekolah,cuti_bersama,khusus',
            'keterangan' => 'nullable|max:1000',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'nama_hari_libur.required' => 'Nama hari libur wajib diisi.',
        ]);

        if ($this->editing) {
            $libur = HariLibur::findOrFail($this->editId);
            $old = $libur->toArray();
            $libur->update([
                'nama_hari_libur' => $this->nama_hari_libur,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'tipe_libur' => $this->tipe_libur,
                'keterangan' => $this->keterangan ?: null,
            ]);
            AuditLogService::logUpdate($libur, $old);
            $this->dispatch('show-toast', message: 'Data hari libur berhasil diperbarui!', type: 'success');
        } else {
            $libur = HariLibur::create([
                'nama_hari_libur' => $this->nama_hari_libur,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'tipe_libur' => $this->tipe_libur,
                'keterangan' => $this->keterangan ?: null,
                'created_by_id' => auth()->id(),
            ]);
            AuditLogService::logCreate($libur);
            $this->dispatch('show-toast', message: 'Hari libur baru berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama_hari_libur', 'keterangan', 'editing']);
    }

    public function confirmDeleteLibur(string $id)
    {
        $libur = HariLibur::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $libur->nama_hari_libur . ' (' . $libur->tanggal_mulai->format('d/m/Y') . ')';
        $this->confirmDelete = true;
    }

    public function deleteLibur()
    {
        $libur = HariLibur::findOrFail($this->deleteId);
        AuditLogService::logDelete($libur);
        $libur->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Hari libur berhasil dihapus!', type: 'info');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Hari Libur');

        // Headers
        $sheet->setCellValue('A1', 'nama_hari_libur');
        $sheet->setCellValue('B1', 'tanggal_mulai');
        $sheet->setCellValue('C1', 'tanggal_selesai');
        $sheet->setCellValue('D1', 'tipe_libur');
        $sheet->setCellValue('E1', 'keterangan');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');

        // Example data rows
        $examples = [
            ['Tahun Baru Masehi 2026', '2026-01-01', '2026-01-01', 'nasional', 'Libur Nasional Tahun Baru'],
            ['Isra Miraj Nabi Muhammad SAW', '2026-01-16', '2026-01-16', 'nasional', 'Libur Keagamaan'],
            ['Tahun Baru Imlek 2577', '2026-02-17', '2026-02-17', 'nasional', 'Libur Keagamaan'],
            ['Hari Raya Nyepi', '2026-03-21', '2026-03-21', 'nasional', 'Tahun Baru Saka'],
            ['Hari Raya Idul Fitri 1447 H', '2026-03-20', '2026-03-22', 'nasional', 'Libur Idul Fitri'],
            ['Cuti Bersama Idul Fitri', '2026-03-23', '2026-03-24', 'cuti_bersama', 'Cuti Bersama Pemerintah'],
            ['Hari Buruh Internasional', '2026-05-01', '2026-05-01', 'nasional', 'May Day'],
            ['Kenaikan Isa Almasih', '2026-05-14', '2026-05-14', 'nasional', 'Libur Keagamaan'],
            ['Hari Lahir Pancasila', '2026-06-01', '2026-06-01', 'nasional', 'Peringatan Nasional'],
            ['Hari Kemerdekaan RI', '2026-08-17', '2026-08-17', 'nasional', 'HUT Kemerdekaan RI'],
            ['Libur Semester Ganjil', '2026-12-21', '2027-01-02', 'sekolah', 'Libur Akhir Semester'],
        ];

        $row = 2;
        foreach ($examples as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $sheet->setCellValue('C' . $row, $item[2]);
            $sheet->setCellValue('D' . $row, $item[3]);
            $sheet->setCellValue('E' . $row, $item[4]);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_hari_libur.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function importData()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'importFile.required' => 'Pilih file Excel/CSV terlebih dahulu.',
            'importFile.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $spreadsheet = IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $header = array_shift($rows);

            $count = 0;
            foreach ($rows as $row) {
                $nama = trim($row['A'] ?? '');
                $mulaiRaw = trim($row['B'] ?? '');
                $selesaiRaw = trim($row['C'] ?? '');
                $tipe = strtolower(trim($row['D'] ?? 'nasional'));
                $ket = trim($row['E'] ?? '');

                if (empty($nama) || empty($mulaiRaw)) continue;

                // Normalize dates
                $tglMulai = $this->parseDate($mulaiRaw);
                $tglSelesai = !empty($selesaiRaw) ? $this->parseDate($selesaiRaw) : $tglMulai;

                if (!$tglMulai) continue;
                if (!$tglSelesai || Carbon::parse($tglSelesai)->lt(Carbon::parse($tglMulai))) {
                    $tglSelesai = $tglMulai;
                }

                if (!in_array($tipe, ['nasional', 'sekolah', 'cuti_bersama', 'khusus'])) {
                    $tipe = 'nasional';
                }

                HariLibur::updateOrCreate(
                    [
                        'nama_hari_libur' => $nama,
                        'tanggal_mulai' => $tglMulai,
                    ],
                    [
                        'tanggal_selesai' => $tglSelesai,
                        'tipe_libur' => $tipe,
                        'keterangan' => $ket ?: null,
                        'created_by_id' => auth()->id(),
                    ]
                );
                $count++;
            }

            $this->reset('importFile');
            $this->showImportModal = false;
            $this->dispatch('show-toast', message: "Berhasil mengimport {$count} hari libur!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal import: ' . $e->getMessage(), type: 'danger');
        }
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hari Libur');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Hari Libur');
        $sheet->setCellValue('C1', 'Tanggal Mulai');
        $sheet->setCellValue('D1', 'Tanggal Selesai');
        $sheet->setCellValue('E1', 'Durasi (Hari)');
        $sheet->setCellValue('F1', 'Tipe Libur');
        $sheet->setCellValue('G1', 'Keterangan');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');

        $data = HariLibur::orderBy('tanggal_mulai', 'asc')->get();
        $row = 2;
        $no = 1;

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->nama_hari_libur);
            $sheet->setCellValue('C' . $row, $item->tanggal_mulai->format('Y-m-d'));
            $sheet->setCellValue('D' . $row, $item->tanggal_selesai->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $item->durasi_hari);
            $sheet->setCellValue('F' . $row, $item->tipe_label);
            $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'daftar_hari_libur_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $data = HariLibur::query()
            ->when($this->search, function ($q) {
                $q->where('nama_hari_libur', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTipe !== 'all', fn($q) => $q->where('tipe_libur', $this->filterTipe))
            ->orderBy('tanggal_mulai', 'asc')->get();

        $headers = [
            ['name' => 'No', 'width' => '5%', 'align' => 'center'],
            ['name' => 'Nama Hari Libur / Agenda', 'width' => '32%', 'align' => 'left'],
            ['name' => 'Tanggal Mulai', 'width' => '14%', 'align' => 'center'],
            ['name' => 'Tanggal Selesai', 'width' => '14%', 'align' => 'center'],
            ['name' => 'Durasi', 'width' => '10%', 'align' => 'center'],
            ['name' => 'Kategori', 'width' => '25%', 'align' => 'left'],
        ];

        $rows = [];
        $no = 1;
        foreach ($data as $d) {
            $rows[] = [
                $no++,
                '<strong>' . e($d->nama_hari_libur) . '</strong>',
                $d->tanggal_mulai->format('d/m/Y'),
                $d->tanggal_selesai->format('d/m/Y'),
                $d->durasi_hari . ' Hari',
                e($d->tipe_label) . ($d->keterangan ? ' (' . e($d->keterangan) . ')' : ''),
            ];
        }

        $filename = 'Laporan_Hari_Libur_Sekolah_' . date('Y-m-d') . '.pdf';
        return \App\Services\PdfReportService::download(
            'DAFTAR HARI LIBUR & AGENDA SEKOLAH',
            'Tahun Pelajaran 2026/2027 — SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'portrait',
            ['Total Hari Libur Terdaftar' => count($rows) . ' Agenda']
        );
    }

    public function render()
    {
        $todayStr = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $query = HariLibur::query()
            ->when($this->search, function ($q) {
                $q->where('nama_hari_libur', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTipe !== 'all', fn($q) => $q->where('tipe_libur', $this->filterTipe));

        if ($this->tab === 'upcoming') {
            $query->where('tanggal_selesai', '>=', $todayStr)->orderBy('tanggal_mulai', 'asc');
        } elseif ($this->tab === 'past') {
            $query->where('tanggal_selesai', '<', $todayStr)->orderBy('tanggal_mulai', 'desc');
        } else {
            $query->orderBy('tanggal_mulai', 'desc');
        }

        $hariLiburs = $query->paginate(15);

        // Summary counts
        $countUpcoming = HariLibur::where('tanggal_selesai', '>=', $todayStr)->count();
        $countAll = HariLibur::count();
        $countPast = HariLibur::where('tanggal_selesai', '<', $todayStr)->count();

        // Check if today is a holiday
        $todayHoliday = HariLibur::isHariLibur();

        return view('livewire.admin.manajemen-hari-libur', [
            'hariLiburs' => $hariLiburs,
            'countUpcoming' => $countUpcoming,
            'countAll' => $countAll,
            'countPast' => $countPast,
            'todayHoliday' => $todayHoliday,
        ]);
    }
}
