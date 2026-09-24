<?php

namespace App\Livewire\Lms\Tka;

use App\Models\LmsTkaHasilSiswa;
use App\Models\LmsTkaPaket;
use App\Models\LmsTkaSoal;
use App\Models\User;
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
#[Title('Pusat Bank Soal & Paket Latihan TKA')]
class KelolaLatihanTka extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterMataUji = '';

    public $importFile;
    public ?string $importPaketId = null;

    // Form Paket Modal
    public bool $showPaketModal = false;
    public bool $editingPaket = false;
    public string $editPaketId = '';
    public string $judul_paket = '';
    public string $mata_uji = 'TPA Skolastik & Logika';
    public int $durasi_menit = 60;
    public string $target_tingkat = 'Semua';
    public string $deskripsi = '';

    // Kelola Soal Modal
    public bool $showSoalModal = false;
    public $activePaket = null;
    public bool $showTambahSoalForm = false;
    public string $pertanyaan = '';
    public string $pilihan_a = '';
    public string $pilihan_b = '';
    public string $pilihan_c = '';
    public string $pilihan_d = '';
    public string $pilihan_e = '';
    public string $kunci_jawaban = 'A';
    public string $pembahasan = '';

    public function updatingSearch() { $this->resetPage(); }

    public function createPaket()
    {
        $this->reset(['editPaketId', 'judul_paket', 'deskripsi', 'editingPaket']);
        $this->mata_uji = 'TPA Skolastik & Logika';
        $this->durasi_menit = 60;
        $this->target_tingkat = 'Semua';
        $this->showPaketModal = true;
    }

    public function editPaket(string $id)
    {
        $paket = LmsTkaPaket::findOrFail($id);
        $this->editPaketId = $paket->id;
        $this->judul_paket = $paket->judul_paket;
        $this->mata_uji = $paket->mata_uji;
        $this->durasi_menit = $paket->durasi_menit;
        $this->target_tingkat = $paket->target_tingkat;
        $this->deskripsi = $paket->deskripsi ?? '';
        $this->editingPaket = true;
        $this->showPaketModal = true;
    }

    public function savePaket()
    {
        $this->validate([
            'judul_paket' => 'required|min:3',
            'mata_uji' => 'required',
            'durasi_menit' => 'required|integer|min:5|max:180',
        ]);

        $data = [
            'judul_paket' => $this->judul_paket,
            'mata_uji' => $this->mata_uji,
            'guru_pembuat_id' => Auth::id() ?? User::where('role', 'guru')->first()?->id,
            'durasi_menit' => $this->durasi_menit,
            'target_tingkat' => $this->target_tingkat,
            'deskripsi' => $this->deskripsi,
            'is_active' => true,
        ];

        if ($this->editingPaket) {
            $p = LmsTkaPaket::findOrFail($this->editPaketId);
            $p->update($data);
            $this->dispatch('show-toast', message: 'Paket latihan TKA berhasil diperbarui!', type: 'success');
        } else {
            LmsTkaPaket::create($data);
            $this->dispatch('show-toast', message: 'Paket latihan TKA berhasil dibuat!', type: 'success');
        }

        $this->showPaketModal = false;
    }

    public function manageSoal(string $paketId)
    {
        $this->activePaket = LmsTkaPaket::with('soal')->findOrFail($paketId);
        $this->importPaketId = $paketId;
        $this->showSoalModal = true;
        $this->showTambahSoalForm = false;
    }

    public function saveSoal()
    {
        $this->validate([
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
        ]);

        $nomorBaru = $this->activePaket->soal()->count() + 1;

        LmsTkaSoal::create([
            'paket_id' => $this->activePaket->id,
            'nomor_urut' => $nomorBaru,
            'pertanyaan' => $this->pertanyaan,
            'pilihan_a' => $this->pilihan_a,
            'pilihan_b' => $this->pilihan_b,
            'pilihan_c' => $this->pilihan_c,
            'pilihan_d' => $this->pilihan_d,
            'pilihan_e' => $this->pilihan_e ?: '-',
            'kunci_jawaban' => $this->kunci_jawaban,
            'pembahasan' => $this->pembahasan,
        ]);

        $this->activePaket->update(['jumlah_soal' => $this->activePaket->soal()->count()]);
        $this->reset(['pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e', 'pembahasan']);
        $this->showTambahSoalForm = false;
        $this->activePaket->refresh();
        $this->dispatch('show-toast', message: 'Butir soal TKA berhasil ditambahkan!', type: 'success');
    }

    public function deleteSoal(string $soalId)
    {
        $soal = LmsTkaSoal::findOrFail($soalId);
        $soal->delete();
        $this->activePaket->update(['jumlah_soal' => $this->activePaket->soal()->count()]);
        $this->activePaket->refresh();
        $this->dispatch('show-toast', message: 'Soal berhasil dihapus!', type: 'success');
    }

    public function downloadTemplateSoal()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal TKA');

        $headers = ['nomor_urut', 'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e', 'kunci_jawaban', 'pembahasan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2563EB');
        $sheet->getStyle('A1:I1')->getFont()->getColor()->setRGB('FFFFFF');

        // Sample
        $sample = [
            '1',
            'Semua programmer menguasai logika algoritma. Sebagian siswa SMKN 2 Indramayu adalah programmer. Kesimpulan yang benar adalah:',
            'Semua siswa SMKN 2 Indramayu menguasai logika algoritma',
            'Sebagian siswa SMKN 2 Indramayu menguasai logika algoritma',
            'Tidak ada siswa SMKN 2 Indramayu yang menguasai logika',
            'Programmer bukan siswa SMKN 2 Indramayu',
            'Semua salah',
            'B',
            'Silogisme kategori sebagian programmer yang merupakan siswa menguasai logika.'
        ];
        foreach ($sample as $idx => $val) {
            $sheet->setCellValue($cols[$idx] . '2', $val);
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_import_soal_tka.xlsx';
        $path = storage_path('app/' . $filename);
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function importSoal()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        if (!$this->activePaket) return;

        try {
            $spreadsheet = IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows); // Header
            $count = 0;

            foreach ($rows as $row) {
                $tanya = trim($row['B'] ?? '');
                $a = trim($row['C'] ?? '');
                $b = trim($row['D'] ?? '');
                $c = trim($row['E'] ?? '');
                $d = trim($row['F'] ?? '');
                $e = trim($row['G'] ?? '') ?: '-';
                $kunci = strtoupper(trim($row['H'] ?? 'A'));
                $pemb = trim($row['I'] ?? '');

                if (empty($tanya) || empty($a) || empty($b)) continue;

                $nomor = $this->activePaket->soal()->count() + 1;

                LmsTkaSoal::create([
                    'paket_id' => $this->activePaket->id,
                    'nomor_urut' => $nomor,
                    'pertanyaan' => $tanya,
                    'pilihan_a' => $a,
                    'pilihan_b' => $b,
                    'pilihan_c' => $c,
                    'pilihan_d' => $d,
                    'pilihan_e' => $e,
                    'kunci_jawaban' => in_array($kunci, ['A','B','C','D','E']) ? $kunci : 'A',
                    'pembahasan' => $pemb,
                ]);

                $count++;
            }

            $this->activePaket->update(['jumlah_soal' => $this->activePaket->soal()->count()]);
            $this->activePaket->refresh();
            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil mengimpor {$count} butir soal ke paket!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal mengimpor: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        $query = LmsTkaPaket::with(['guruPembuat'])
            ->withCount('soal')
            ->withCount('hasilSiswa')
            ->when($this->search, fn($q) => $q->where('judul_paket', 'like', "%{$this->search}%")->orWhere('mata_uji', 'like', "%{$this->search}%"))
            ->when($this->filterMataUji, fn($q) => $q->where('mata_uji', $this->filterMataUji))
            ->latest();

        $paketList = $query->paginate(10);

        return view('livewire.lms.tka.kelola-latihan-tka', [
            'paketList' => $paketList,
            'totalPaket' => LmsTkaPaket::count(),
            'totalHasil' => LmsTkaHasilSiswa::count(),
        ]);
    }
}
