<?php

namespace App\Livewire\Admin;

use App\Models\MasterJabatan;
use App\Models\PenugasanJabatan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Manajemen Jabatan Guru & Siswa - SMKN 2 Indramayu')]
class ManajemenJabatanTerpadu extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'guru'; // guru, siswa, kamus_jabatan

    // Filter & Search
    public $search = '';
    public $filterJabatan = '';
    public $filterTahun = '2026/2027';

    // Form Modal State
    public $showForm = false;
    public $editing = false;
    public $editId = '';
    public $tipeSubjek = 'guru'; // guru, siswa
    public $userId = '';
    public $siswaId = '';
    public $masterJabatanId = '';
    public $rombelId = null;
    public $tahunAjaran = '2026/2027';
    public $nomorSk = '';
    public $keterangan = '';
    public $statusAktif = true;

    // File Import
    public $importFile;

    public function updatingSearch() { $this->resetPage('penugasanPage'); }
    public function updatingFilterJabatan() { $this->resetPage('penugasanPage'); }

    public function create($tipe = 'guru')
    {
        $this->reset(['editId', 'userId', 'siswaId', 'masterJabatanId', 'rombelId', 'nomorSk', 'keterangan', 'editing']);
        $this->tipeSubjek = $tipe;
        $this->tahunAjaran = $this->filterTahun;
        $this->statusAktif = true;

        $firstJabatan = MasterJabatan::where('kategori', $tipe)->first();
        $this->masterJabatanId = $firstJabatan?->id ?? '';

        $this->showForm = true;
    }

    public function edit($id)
    {
        $p = PenugasanJabatan::findOrFail($id);
        $this->editId = $p->id;
        $this->tipeSubjek = $p->tipe_subjek;
        $this->userId = $p->user_id ?? '';
        $this->siswaId = $p->siswa_id ?? '';
        $this->masterJabatanId = $p->master_jabatan_id;
        $this->rombelId = $p->rombel_id;
        $this->tahunAjaran = $p->tahun_ajaran;
        $this->nomorSk = $p->nomor_sk ?? '';
        $this->keterangan = $p->keterangan ?? '';
        $this->statusAktif = (bool)$p->status_aktif;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'tipeSubjek' => 'required|in:guru,siswa',
            'masterJabatanId' => 'required|exists:master_jabatan,id',
            'tahunAjaran' => 'required|string',
        ];

        if ($this->tipeSubjek === 'guru') {
            $rules['userId'] = 'required|exists:users,id';
        } else {
            $rules['siswaId'] = 'required|exists:siswa,id';
        }

        $this->validate($rules);

        $data = [
            'master_jabatan_id' => $this->masterJabatanId,
            'tipe_subjek' => $this->tipeSubjek,
            'user_id' => $this->tipeSubjek === 'guru' ? $this->userId : null,
            'siswa_id' => $this->tipeSubjek === 'siswa' ? $this->siswaId : null,
            'rombel_id' => $this->rombelId ?: null,
            'tahun_ajaran' => $this->tahunAjaran,
            'nomor_sk' => $this->nomorSk,
            'keterangan' => $this->keterangan,
            'status_aktif' => $this->statusAktif,
        ];

        if ($this->editing) {
            $p = PenugasanJabatan::findOrFail($this->editId);
            $p->update($data);
            session()->flash('success', 'Penugasan jabatan berhasil diperbarui!');
        } else {
            PenugasanJabatan::create($data);
            session()->flash('success', 'Penugasan jabatan baru berhasil disimpan!');
        }

        $this->showForm = false;
    }

    public function toggleStatus($id)
    {
        $p = PenugasanJabatan::findOrFail($id);
        $p->status_aktif = !$p->status_aktif;
        $p->save();
        session()->flash('success', 'Status keaktifan jabatan berhasil diubah.');
    }

    public function delete($id)
    {
        PenugasanJabatan::findOrFail($id)->delete();
        session()->flash('success', 'Data penugasan jabatan berhasil dihapus.');
    }

    // =========================================================================
    // EXPORT TEMPLATE / BLANKO EXCEL MULTI-SHEET
    // =========================================================================
    public function downloadTemplateExcel(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $spreadsheet = new Spreadsheet();

            // -------------------------------------------------------------
            // SHEET 1: FORMULIR INPUT DATA PENUGASAN JABATAN
            // -------------------------------------------------------------
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('DATA_PENUGASAN');

            // Header Judul
            $sheet1->setCellValue('A1', 'BLANKO IMPORT PENUGASAN JABATAN GURU & SISWA - SMKN 2 INDRAMAYU');
            $sheet1->mergeCells('A1:G1');
            $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(13);

            // Kolom Header
            $headers = ['TIPE', 'IDENTITAS_USER_NIS_EMAIL', 'NAMA_PERSONIL', 'KODE_JABATAN', 'KODE_ROMBEL', 'NOMOR_SK', 'KETERANGAN'];
            foreach ($headers as $idx => $h) {
                $col = chr(65 + $idx);
                $sheet1->setCellValue("{$col}3", $h);
                $sheet1->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet1->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
            }

            // Contoh Data Baris
            $sampleRows = [
                ['GURU', 'satibi@smkn2indramayu.sch.id', 'SATIBI, M.Pd', 'wali_kelas', 'X-PPLG-1', 'SK.800/01/2026', 'Wali Kelas X PPLG 1'],
                ['GURU', 'admin@smkn2indramayu.sch.id', 'Administrator', 'kepala_perpustakaan', '', 'SK.800/02/2026', 'Penanggung Jawab Perpus Digital'],
                ['GURU', 'guru@smkn2indramayu.sch.id', 'Guru Kesiswaan', 'pembina_kesiswaan', '', 'SK.800/03/2026', 'Pembina Ketarunaan PERDUPTAR'],
                ['SISWA', '001001', 'Siswa 1 X NKPI TEORI', 'danyon_taruna', 'X-NKPI-1', 'SK.800/BAT/2026', 'Komandan Batalyon Angkatan 2026'],
                ['SISWA', '001002', 'Siswa 2 X NKPI TEORI', 'danton_ketua_kelas', 'X-NKPI-1', 'SK.800/DNT/2026', 'Danton Kelas X NKPI 1'],
            ];

            foreach ($sampleRows as $rowIdx => $row) {
                $line = 4 + $rowIdx;
                foreach ($row as $colIdx => $val) {
                    $col = chr(65 + $colIdx);
                    $sheet1->setCellValue("{$col}{$line}", $val);
                }
            }

            foreach (range('A', 'G') as $col) {
                $sheet1->getColumnDimension($col)->setAutoSize(true);
            }

            // -------------------------------------------------------------
            // SHEET 2: KAMUS KODE JABATAN RESMI (ACUAN HAK MODUL)
            // -------------------------------------------------------------
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('REFERENSI_KODE_JABATAN');

            $sheet2->setCellValue('A1', 'KAMUS KODE JABATAN RESMI & DELEGASI MODUL GADDAMAY');
            $sheet2->mergeCells('A1:E1');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(12);

            $refHeaders = ['KODE_JABATAN', 'NAMA_JABATAN', 'KATEGORI', 'DELEGASI_MODUL', 'DESKRIPSI_TUGAS'];
            foreach ($refHeaders as $idx => $h) {
                $col = chr(65 + $idx);
                $sheet2->setCellValue("{$col}3", $h);
                $sheet2->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet2->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D9488');
            }

            $jabatanList = MasterJabatan::orderBy('kategori')->orderBy('nama_jabatan')->get();
            $line = 4;
            foreach ($jabatanList as $j) {
                $sheet2->setCellValue("A{$line}", $j->kode_jabatan);
                $sheet2->setCellValue("B{$line}", $j->nama_jabatan);
                $sheet2->setCellValue("C{$line}", strtoupper($j->kategori));
                $sheet2->setCellValue("D{$line}", $j->delegasi_modul ?? '-');
                $sheet2->setCellValue("E{$line}", $j->deskripsi_tugas ?? '-');
                $line++;
            }

            foreach (range('A', 'E') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }

            // -------------------------------------------------------------
            // SHEET 3: PETUNJUK PENGISIAN
            // -------------------------------------------------------------
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('PETUNJUK');
            $sheet3->setCellValue('A1', 'PETUNJUK PENGISIAN BLANKO JABATAN:');
            $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $instructions = [
                '1. Kolom TIPE hanya boleh diisi huruf kapital: "GURU" atau "SISWA".',
                '2. Untuk GURU, masukkan Email login guru pada kolom IDENTITAS_USER_NIS_EMAIL.',
                '3. Untuk SISWA, masukkan Nomor Induk Siswa (NIS) pada kolom IDENTITAS_USER_NIS_EMAIL.',
                '4. Kolom KODE_JABATAN wajib persis sama dengan yang tertera di sheet "REFERENSI_KODE_JABATAN".',
                '5. Untuk jabatan berbasis kelas (seperti wali_kelas atau danton_ketua_kelas), isi kolom KODE_ROMBEL.',
                '6. Unggah kembali file ini melalui tombol "Import Excel Penugasan" pada dashboard Admin.',
            ];
            foreach ($instructions as $idx => $inst) {
                $sheet3->setCellValue('A' . (3 + $idx), $inst);
            }
            $sheet3->getColumnDimension('A')->setWidth(90);

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Blanko_Penugasan_Jabatan_SMKN2.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // =========================================================================
    // IMPORT PENUGASAN DARI EXCEL
    // =========================================================================
    public function importExcel()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $spreadsheet = IOFactory::load($this->importFile->getRealPath());
            $sheet = $spreadsheet->getSheetByName('DATA_PENUGASAN') ?: $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $successCount = 0;
            // Lewati header (mulai baris ke-4, index 3)
            for ($i = 3; $i < count($rows); $i++) {
                $row = $rows[$i];
                $tipe = strtoupper(trim($row[0] ?? ''));
                $identitas = trim($row[1] ?? '');
                $kodeJabatan = trim($row[3] ?? '');
                $kodeRombel = trim($row[4] ?? '');
                $nomorSk = trim($row[5] ?? '');
                $keterangan = trim($row[6] ?? '');

                if (empty($tipe) || empty($identitas) || empty($kodeJabatan)) {
                    continue;
                }

                $master = MasterJabatan::where('kode_jabatan', $kodeJabatan)->first();
                if (!$master) continue;

                $userId = null;
                $siswaId = null;

                if ($tipe === 'GURU') {
                    $user = User::where('email', $identitas)->first();
                    if (!$user) continue;
                    $userId = $user->id;
                } else {
                    $siswa = Siswa::where('nis', $identitas)->first();
                    if (!$siswa) continue;
                    $siswaId = $siswa->id;
                }

                $rombelId = null;
                if (!empty($kodeRombel)) {
                    $rombel = Rombel::where('nama_rombel', 'like', "%{$kodeRombel}%")->first();
                    $rombelId = $rombel?->id;
                }

                PenugasanJabatan::updateOrCreate(
                    [
                        'master_jabatan_id' => $master->id,
                        'user_id' => $userId,
                        'siswa_id' => $siswaId,
                        'tahun_ajaran' => $this->filterTahun,
                    ],
                    [
                        'tipe_subjek' => strtolower($tipe),
                        'rombel_id' => $rombelId,
                        'nomor_sk' => $nomorSk ?: 'SK/IMPORT/' . date('Y'),
                        'keterangan' => $keterangan ?: 'Diimpor dari Excel',
                        'status_aktif' => true,
                    ]
                );

                $successCount++;
            }

            $this->reset('importFile');
            session()->flash('success', "Berhasil mengimpor {$successCount} data penugasan jabatan Guru & Siswa!");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Query penugasan sesuai tab aktif
        $penugasanQuery = PenugasanJabatan::with(['masterJabatan', 'guru', 'siswa.rombel', 'rombel'])
            ->where('tipe_subjek', $this->activeTab === 'siswa' ? 'siswa' : 'guru')
            ->where('tahun_ajaran', $this->filterTahun)
            ->when($this->filterJabatan, fn($q) => $q->where('master_jabatan_id', $this->filterJabatan))
            ->when($this->search, function ($q) {
                if ($this->activeTab === 'guru') {
                    $q->whereHas('guru', fn($sub) => $sub->where('name', 'like', "%{$this->search}%"));
                } else {
                    $q->whereHas('siswa', fn($sub) => $sub->where('nama', 'like', "%{$this->search}%")->orWhere('nis', 'like', "%{$this->search}%"));
                }
            });

        $penugasanList = $penugasanQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'penugasanPage');

        // Master Kamus Jabatan
        $kamusList = MasterJabatan::withCount('penugasan')->orderBy('kategori')->orderBy('level_hierarki')->get();

        // Pilihan Dropdown
        $masterOptions = MasterJabatan::where('kategori', $this->tipeSubjek)->orderBy('nama_jabatan')->get();
        $guruOptions = User::whereIn('role', ['guru', 'admin', 'kepsek', 'waka', 'guru_piket', 'ketua_mgmp'])->orderBy('name')->get();
        $siswaOptions = Siswa::with('rombel')->orderBy('nama')->take(100)->get();
        $rombelOptions = Rombel::orderBy('nama_rombel')->get();

        return view('livewire.admin.manajemen-jabatan-terpadu', [
            'penugasanList' => $penugasanList,
            'kamusList' => $kamusList,
            'masterOptions' => $masterOptions,
            'guruOptions' => $guruOptions,
            'siswaOptions' => $siswaOptions,
            'rombelOptions' => $rombelOptions,
        ]);
    }
}
