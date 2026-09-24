<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use App\Models\TugasTambahanGuru;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Manajemen Tugas Tambahan Guru')]
class ManajemenTugasTambahanGuru extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterJenis = '';
    public string $filterTahun = '2025/2026';

    public $importFile;

    // Form modal state
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $guru_id = '';
    public string $jenis_tugas = 'wali_kelas';
    public ?string $rombel_id = null;
    public string $tahun_ajaran = '2025/2026';
    public string $sk_penugasan = '';
    public string $keterangan = '';
    public bool $is_active = true;

    // Delete confirmation
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterJenis() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'guru_id', 'rombel_id', 'sk_penugasan', 'keterangan', 'editing']);
        $this->jenis_tugas = 'wali_kelas';
        $this->tahun_ajaran = '2025/2026';
        $this->is_active = true;
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $tugas = TugasTambahanGuru::findOrFail($id);
        $this->editId = $tugas->id;
        $this->guru_id = $tugas->guru_id;
        $this->jenis_tugas = $tugas->jenis_tugas;
        $this->rombel_id = $tugas->rombel_id;
        $this->tahun_ajaran = $tugas->tahun_ajaran;
        $this->sk_penugasan = $tugas->sk_penugasan ?? '';
        $this->keterangan = $tugas->keterangan ?? '';
        $this->is_active = (bool) $tugas->is_active;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'guru_id' => 'required|exists:users,id',
            'jenis_tugas' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'rombel_id' => $this->jenis_tugas === 'wali_kelas' ? 'required|exists:rombel,id' : 'nullable|exists:rombel,id',
        ]);

        $data = [
            'guru_id' => $this->guru_id,
            'jenis_tugas' => $this->jenis_tugas,
            'rombel_id' => $this->jenis_tugas === 'wali_kelas' ? $this->rombel_id : null,
            'tahun_ajaran' => $this->tahun_ajaran,
            'sk_penugasan' => $this->sk_penugasan,
            'keterangan' => $this->keterangan,
            'is_active' => $this->is_active,
        ];

        if ($this->editing) {
            $tugas = TugasTambahanGuru::findOrFail($this->editId);
            $tugas->update($data);
            $this->dispatch('show-toast', message: 'Tugas tambahan berhasil diperbarui!', type: 'success');
        } else {
            TugasTambahanGuru::create($data);
            $this->dispatch('show-toast', message: 'Tugas tambahan berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'guru_id', 'rombel_id', 'sk_penugasan', 'keterangan', 'editing']);
    }

    public function confirmDeleteTugas(string $id)
    {
        $t = TugasTambahanGuru::with(['guru', 'rombel'])->findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = ($t->guru?->name ?? 'Guru') . ' - ' . TugasTambahanGuru::getLabelJenisTugas($t->jenis_tugas);
        $this->confirmDelete = true;
    }

    public function deleteTugas()
    {
        $tugas = TugasTambahanGuru::findOrFail($this->deleteId);
        $tugas->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Data penugasan berhasil dihapus!', type: 'success');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tugas Tambahan');

        // Headers
        $headers = ['email_guru', 'nama_guru', 'jenis_tugas', 'kelas_rombel', 'tahun_ajaran', 'sk_penugasan', 'keterangan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');

        // Sample Rows
        $samples = [
            ['budi@smkn2indramayu.sch.id', 'Budi Santoso, S.Pd', 'wali_kelas', 'XII RPL 1', '2025/2026', 'SK/01/GTK/2025', 'Wali Kelas Aktif'],
            ['siti@smkn2indramayu.sch.id', 'Siti Rahmawati, S.Kom', 'pembina_kesiswaan', '', '2025/2026', 'SK/02/GTK/2025', 'Bidang OSIS & MPK'],
            ['ahmad@smkn2indramayu.sch.id', 'Ahmad Ridwan, S.Pd', 'guru_bk', 'X TKJ 1', '2025/2026', 'SK/03/GTK/2025', 'Guru BK Konseling Siswa'],
            ['dewi@smkn2indramayu.sch.id', 'Dewi Sartika, M.Pd', 'guru_piket', '', '2025/2026', 'SK/04/GTK/2025', 'Petugas Piket Hari Senin & Kamis'],
            ['eko@smkn2indramayu.sch.id', 'Eko Prasetyo, S.Hum', 'koordinator_literasi', '', '2025/2026', 'SK/05/GTK/2025', 'Pengelola Gerakan Literasi Sekolah (GLS)'],
        ];

        $rowNum = 2;
        foreach ($samples as $s) {
            foreach ($s as $idx => $val) {
                $sheet->setCellValue($cols[$idx] . $rowNum, $val);
            }
            $rowNum++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_tugas_tambahan_guru.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function importData()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        try {
            $spreadsheet = IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows); // Remove header
            $count = 0;

            foreach ($rows as $row) {
                $email = trim($row['A'] ?? '');
                $nama = trim($row['B'] ?? '');
                $jenisRaw = strtolower(trim(str_replace([' ', '-'], '_', $row['C'] ?? '')));
                $kelasName = trim($row['D'] ?? '');
                $tahun = trim($row['E'] ?? '2025/2026') ?: '2025/2026';
                $sk = trim($row['F'] ?? '');
                $ket = trim($row['G'] ?? '');

                if (empty($email) && empty($nama)) continue;

                // Cari User Guru
                $user = User::where('email', $email)
                    ->orWhere('name', 'like', "%{$nama}%")
                    ->first();

                if (!$user && !empty($email)) {
                    $user = User::create([
                        'name' => $nama ?: explode('@', $email)[0],
                        'email' => $email,
                        'password' => bcrypt('password123'),
                        'role' => 'guru',
                    ]);
                }

                if (!$user) continue;

                // Normalisasi jenis tugas
                $jenis = match ($jenisRaw) {
                    'wali_kelas', 'walikelas', 'wali' => 'wali_kelas',
                    'kesiswaan', 'pembina_kesiswaan', 'pembina_osis' => 'pembina_kesiswaan',
                    'bk', 'guru_bk', 'bimbingan_konseling' => 'guru_bk',
                    'piket', 'guru_piket', 'petugas_piket' => 'guru_piket',
                    'literasi', 'koordinator_literasi', 'gls' => 'koordinator_literasi',
                    default => $jenisRaw ?: 'tugas_tambahan',
                };

                // Cari Rombel jika wali kelas / penugasan kelas
                $rombelId = null;
                if (!empty($kelasName)) {
                    $rombel = Rombel::where('nama_kelas', $kelasName)
                        ->orWhere('nama_kelas', 'like', "%{$kelasName}%")
                        ->first();
                    $rombelId = $rombel?->id;
                }

                TugasTambahanGuru::updateOrCreate([
                    'guru_id' => $user->id,
                    'jenis_tugas' => $jenis,
                    'tahun_ajaran' => $tahun,
                    'rombel_id' => $rombelId,
                ], [
                    'sk_penugasan' => $sk ?: 'SK/' . date('Y'),
                    'keterangan' => $ket ?: 'Diimpor otomatis dari Excel',
                    'is_active' => true,
                ]);

                $count++;
            }

            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil mengimpor {$count} tugas tambahan guru!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengimpor: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tugas Tambahan Guru');

        $headers = ['No', 'Nama Guru / PTK', 'Email Akun', 'Tugas Tambahan', 'Kelas / Rombel', 'Tahun Ajaran', 'No. SK Penugasan', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A1:H1')->getFont()->getColor()->setRGB('FFFFFF');

        $query = TugasTambahanGuru::with(['guru', 'rombel'])
            ->when($this->search, function ($q) {
                $q->whereHas('guru', function ($gq) {
                    $gq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterJenis, fn($q) => $q->where('jenis_tugas', $this->filterJenis))
            ->when($this->filterTahun, fn($q) => $q->where('tahun_ajaran', $this->filterTahun))
            ->orderBy('jenis_tugas')
            ->get();

        $row = 2;
        $no = 1;
        foreach ($query as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->guru?->name ?? '-');
            $sheet->setCellValue('C' . $row, $item->guru?->email ?? '-');
            $sheet->setCellValue('D' . $row, TugasTambahanGuru::getLabelJenisTugas($item->jenis_tugas));
            $sheet->setCellValue('E' . $row, $item->rombel?->nama_kelas ?? '-');
            $sheet->setCellValue('F' . $row, $item->tahun_ajaran);
            $sheet->setCellValue('G' . $row, $item->sk_penugasan ?? '-');
            $sheet->setCellValue('H' . $row, $item->is_active ? 'Aktif' : 'Non-Aktif');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_tugas_tambahan_guru_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $query = TugasTambahanGuru::with(['guru', 'rombel'])
            ->when($this->search, function ($q) {
                $q->whereHas('guru', function ($gq) {
                    $gq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterJenis, fn($q) => $q->where('jenis_tugas', $this->filterJenis))
            ->when($this->filterTahun, fn($q) => $q->where('tahun_ajaran', $this->filterTahun))
            ->latest();

        $tugasList = $query->paginate(15);
        $gurus = User::orderBy('name')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('livewire.admin.manajemen-tugas-tambahan-guru', [
            'tugasList' => $tugasList,
            'gurus' => $gurus,
            'rombels' => $rombels,
            'totalWali' => TugasTambahanGuru::where('jenis_tugas', 'wali_kelas')->count(),
            'totalBk' => TugasTambahanGuru::where('jenis_tugas', 'guru_bk')->count(),
            'totalPiket' => TugasTambahanGuru::where('jenis_tugas', 'guru_piket')->count(),
            'totalKesiswaan' => TugasTambahanGuru::where('jenis_tugas', 'pembina_kesiswaan')->count(),
            'totalLiterasi' => TugasTambahanGuru::where('jenis_tugas', 'koordinator_literasi')->count(),
        ]);
    }
}
