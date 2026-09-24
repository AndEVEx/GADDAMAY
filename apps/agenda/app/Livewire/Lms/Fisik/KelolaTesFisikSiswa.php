<?php

namespace App\Livewire\Lms\Fisik;

use App\Models\LmsTesFisikSiswa;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TugasTambahanGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Pusat Kebugaran Jasmani & Tes Fisik Siswa')]
class KelolaTesFisikSiswa extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterRombel = '';
    public string $filterSemester = 'Ganjil';
    public string $filterTahun = '2025/2026';

    public $importFile;

    // Form Modal
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $siswa_id = '';
    public string $tanggal_tes = '';
    public string $semester = 'Ganjil';
    public string $tahun_ajaran = '2025/2026';

    // Nilai Komponen Fisik
    public $tinggi_badan_cm;
    public $berat_badan_kg;
    public $lari_1200m_detik;
    public $push_up_1min;
    public $sit_up_1min;
    public $shuttle_run_detik;
    public $sit_and_reach_cm;
    public string $catatan_guru_olahraga = '';

    // Detail Modal Siswa
    public $selectedTes = null;

    public function mount()
    {
        $this->tanggal_tes = Carbon::today()->toDateString();

        // Jika user adalah wali kelas, auto-filter rombelnya jika ada
        $user = Auth::user();
        if ($user) {
            $wali = TugasTambahanGuru::where('guru_id', $user->id)
                ->where('jenis_tugas', 'wali_kelas')
                ->where('is_active', true)
                ->first();
            if ($wali && $wali->rombel_id) {
                $this->filterRombel = $wali->rombel_id;
            }
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRombel() { $this->resetPage(); }

    public function create()
    {
        $this->reset([
            'editId', 'siswa_id', 'tinggi_badan_cm', 'berat_badan_kg',
            'lari_1200m_detik', 'push_up_1min', 'sit_up_1min',
            'shuttle_run_detik', 'sit_and_reach_cm', 'catatan_guru_olahraga', 'editing'
        ]);
        $this->tanggal_tes = Carbon::today()->toDateString();
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $tes = LmsTesFisikSiswa::findOrFail($id);
        $this->editId = $tes->id;
        $this->siswa_id = $tes->siswa_id;
        $this->tanggal_tes = $tes->tanggal_tes->format('Y-m-d');
        $this->semester = $tes->semester;
        $this->tahun_ajaran = $tes->tahun_ajaran;
        $this->tinggi_badan_cm = $tes->tinggi_badan_cm;
        $this->berat_badan_kg = $tes->berat_badan_kg;
        $this->lari_1200m_detik = $tes->lari_1200m_detik;
        $this->push_up_1min = $tes->push_up_1min;
        $this->sit_up_1min = $tes->sit_up_1min;
        $this->shuttle_run_detik = $tes->shuttle_run_detik;
        $this->sit_and_reach_cm = $tes->sit_and_reach_cm;
        $this->catatan_guru_olahraga = $tes->catatan_guru_olahraga ?? '';
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal_tes' => 'required|date',
            'tinggi_badan_cm' => 'nullable|numeric|min:50|max:250',
            'berat_badan_kg' => 'nullable|numeric|min:20|max:200',
            'lari_1200m_detik' => 'nullable|integer|min:60|max:1800',
            'push_up_1min' => 'nullable|integer|min:0|max:150',
            'sit_up_1min' => 'nullable|integer|min:0|max:150',
        ]);

        $bmiData = LmsTesFisikSiswa::calculateBmi(
            $this->tinggi_badan_cm ? (float)$this->tinggi_badan_cm : null,
            $this->berat_badan_kg ? (float)$this->berat_badan_kg : null
        );

        $evaluasi = LmsTesFisikSiswa::evaluateKebugaran([
            'push_up_1min' => $this->push_up_1min,
            'sit_up_1min' => $this->sit_up_1min,
            'lari_1200m_detik' => $this->lari_1200m_detik,
        ]);

        $data = [
            'siswa_id' => $this->siswa_id,
            'guru_olahraga_id' => Auth::id() ?? User::where('role', 'guru')->first()?->id,
            'tanggal_tes' => $this->tanggal_tes,
            'semester' => $this->semester,
            'tahun_ajaran' => $this->tahun_ajaran,
            'tinggi_badan_cm' => $this->tinggi_badan_cm ?: null,
            'berat_badan_kg' => $this->berat_badan_kg ?: null,
            'bmi' => $bmiData['bmi'],
            'kategori_bmi' => $bmiData['kategori'],
            'lari_1200m_detik' => $this->lari_1200m_detik ?: null,
            'push_up_1min' => $this->push_up_1min ?: null,
            'sit_up_1min' => $this->sit_up_1min ?: null,
            'shuttle_run_detik' => $this->shuttle_run_detik ?: null,
            'sit_and_reach_cm' => $this->sit_and_reach_cm ?: null,
            'skor_kebugaran' => $evaluasi['skor'],
            'predikat' => $evaluasi['predikat'],
            'catatan_guru_olahraga' => $this->catatan_guru_olahraga,
        ];

        if ($this->editing) {
            $tes = LmsTesFisikSiswa::findOrFail($this->editId);
            $tes->update($data);
            $this->dispatch('show-toast', message: 'Data kemampuan fisik siswa berhasil diperbarui!', type: 'success');
        } else {
            LmsTesFisikSiswa::create($data);
            $this->dispatch('show-toast', message: 'Data kemampuan fisik siswa berhasil disimpan!', type: 'success');
        }

        $this->showForm = false;
    }

    public function viewDetail(string $id)
    {
        $this->selectedTes = LmsTesFisikSiswa::with(['siswa.rombel', 'guruOlahraga'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTes = null;
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tes Fisik Siswa');

        $headers = ['nis_siswa', 'nama_siswa', 'tanggal_tes', 'semester', 'tahun_ajaran', 'tinggi_cm', 'berat_kg', 'push_up_1min', 'sit_up_1min', 'lari_1200m_detik', 'kelenturan_cm', 'catatan_guru'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('059669');
        $sheet->getStyle('A1:L1')->getFont()->getColor()->setRGB('FFFFFF');

        // Sample
        $sample = ['12345', 'Ahmad Fauzi', date('Y-m-d'), 'Ganjil', '2025/2026', '170.5', '62.0', '28', '32', '320', '18.5', 'Kebugaran fisik prima, siap magang industri'];
        foreach ($sample as $idx => $val) {
            $sheet->setCellValue($cols[$idx] . '2', $val);
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_tes_fisik_siswa.xlsx';
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
            array_shift($rows); // Hapus header
            $count = 0;

            foreach ($rows as $row) {
                $nis = trim($row['A'] ?? '');
                $nama = trim($row['B'] ?? '');
                $tgl = trim($row['C'] ?? '') ?: date('Y-m-d');
                $sem = trim($row['D'] ?? 'Ganjil') ?: 'Ganjil';
                $thn = trim($row['E'] ?? '2025/2026') ?: '2025/2026';
                $tb = (float)($row['F'] ?? 0);
                $bb = (float)($row['G'] ?? 0);
                $push = (int)($row['H'] ?? 0);
                $sit = (int)($row['I'] ?? 0);
                $lari = (int)($row['J'] ?? 0);
                $flex = (float)($row['K'] ?? 0);
                $cat = trim($row['L'] ?? '');

                if (empty($nis) && empty($nama)) continue;

                $siswa = Siswa::where('nis', $nis)
                    ->orWhere('nama', 'like', "%{$nama}%")
                    ->first();

                if (!$siswa) continue;

                $bmiData = LmsTesFisikSiswa::calculateBmi($tb, $bb);
                $eval = LmsTesFisikSiswa::evaluateKebugaran([
                    'push_up_1min' => $push,
                    'sit_up_1min' => $sit,
                    'lari_1200m_detik' => $lari,
                ]);

                LmsTesFisikSiswa::updateOrCreate([
                    'siswa_id' => $siswa->id,
                    'semester' => $sem,
                    'tahun_ajaran' => $thn,
                ], [
                    'guru_olahraga_id' => Auth::id() ?? User::where('role', 'guru')->first()?->id,
                    'tanggal_tes' => $tgl,
                    'tinggi_badan_cm' => $tb ?: null,
                    'berat_badan_kg' => $bb ?: null,
                    'bmi' => $bmiData['bmi'],
                    'kategori_bmi' => $bmiData['kategori'],
                    'push_up_1min' => $push ?: null,
                    'sit_up_1min' => $sit ?: null,
                    'lari_1200m_detik' => $lari ?: null,
                    'sit_and_reach_cm' => $flex ?: null,
                    'skor_kebugaran' => $eval['skor'],
                    'predikat' => $eval['predikat'],
                    'catatan_guru_olahraga' => $cat ?: 'Import massal nilai fisik',
                ]);

                $count++;
            }

            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil mengimpor {$count} rekam tes fisik siswa!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengimpor: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Tes Fisik');

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Tanggal Tes', 'TB (cm)', 'BB (kg)', 'BMI', 'Kategori BMI', 'Push-Up', 'Sit-Up', 'Lari 1200m (detik)', 'Skor Akhir', 'Predikat Kebugaran'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('059669');
        $sheet->getStyle('A1:N1')->getFont()->getColor()->setRGB('FFFFFF');

        $query = LmsTesFisikSiswa::with(['siswa.rombel'])
            ->when($this->search, function ($q) {
                $q->whereHas('siswa', function ($sq) {
                    $sq->where('nama', 'like', "%{$this->search}%")
                       ->orWhere('nis', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRombel, function ($q) {
                $q->whereHas('siswa', fn($sq) => $sq->where('rombel_id', $this->filterRombel));
            })
            ->when($this->filterSemester, fn($q) => $q->where('semester', $this->filterSemester))
            ->when($this->filterTahun, fn($q) => $q->where('tahun_ajaran', $this->filterTahun))
            ->latest('tanggal_tes')
            ->get();

        $row = 2;
        $no = 1;
        foreach ($query as $t) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $t->siswa?->nis ?? '-');
            $sheet->setCellValue('C' . $row, $t->siswa?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $t->siswa?->rombel?->nama_kelas ?? '-');
            $sheet->setCellValue('E' . $row, $t->tanggal_tes->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, $t->tinggi_badan_cm ?: '-');
            $sheet->setCellValue('G' . $row, $t->berat_badan_kg ?: '-');
            $sheet->setCellValue('H' . $row, $t->bmi ?: '-');
            $sheet->setCellValue('I' . $row, $t->kategori_bmi ?: '-');
            $sheet->setCellValue('J' . $row, $t->push_up_1min ?: 0);
            $sheet->setCellValue('K' . $row, $t->sit_up_1min ?: 0);
            $sheet->setCellValue('L' . $row, $t->lari_1200m_detik ?: 0);
            $sheet->setCellValue('M' . $row, $t->skor_kebugaran);
            $sheet->setCellValue('N' . $row, $t->predikat);
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'rekap_tes_kebugaran_fisik_siswa_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $query = LmsTesFisikSiswa::with(['siswa.rombel', 'guruOlahraga'])
            ->when($this->search, function ($q) {
                $q->whereHas('siswa', function ($sq) {
                    $sq->where('nama', 'like', "%{$this->search}%")
                       ->orWhere('nis', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRombel, function ($q) {
                $q->whereHas('siswa', fn($sq) => $sq->where('rombel_id', $this->filterRombel));
            })
            ->when($this->filterSemester, fn($q) => $q->where('semester', $this->filterSemester))
            ->when($this->filterTahun, fn($q) => $q->where('tahun_ajaran', $this->filterTahun))
            ->latest('tanggal_tes');

        $tesList = $query->paginate(15);
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $siswas = Siswa::with('rombel')->orderBy('nama')->get();

        // Metrik Statistik Kebugaran
        $totalTes = LmsTesFisikSiswa::where('tahun_ajaran', $this->filterTahun)->count();
        $primaCount = LmsTesFisikSiswa::where('tahun_ajaran', $this->filterTahun)->where('predikat', 'like', '%Baik%')->count();
        $cukupCount = LmsTesFisikSiswa::where('tahun_ajaran', $this->filterTahun)->where('predikat', 'like', '%Cukup%')->count();
        $kurangCount = LmsTesFisikSiswa::where('tahun_ajaran', $this->filterTahun)->where('predikat', 'like', '%Kurang%')->count();

        return view('livewire.lms.fisik.kelola-tes-fisik-siswa', [
            'tesList' => $tesList,
            'rombels' => $rombels,
            'siswas' => $siswas,
            'totalTes' => $totalTes,
            'primaCount' => $primaCount,
            'cukupCount' => $cukupCount,
            'kurangCount' => $kurangCount,
        ]);
    }
}
