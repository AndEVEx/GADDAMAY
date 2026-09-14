<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Database\Seeders\XmlJadwalSeeder;
use App\Models\JadwalPelajaran;
use App\Models\JadwalGuru;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.app')]
#[Title('Import Jadwal')]
class ImportJadwal extends Component
{
    use WithFileUploads;

    public $xmlFile;
    public bool $importing = false;
    public bool $imported = false;
    public string $result = '';
    public bool $confirmClear = false;

    public function import()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $this->validate(['xmlFile' => 'required|file|max:10240']);

        $this->importing = true;

        try {
            $path = $this->xmlFile->getRealPath();

            // Copy to temp location
            $tempPath = storage_path('app/temp_jadwal.xml');
            copy($path, $tempPath);

            // Override the XML path in config and run seeder
            config(['app.xml_jadwal_path' => $tempPath]);

            // Truncate old schedule entries with foreign key checks disabled
            Schema::disableForeignKeyConstraints();
            JadwalGuru::truncate();
            JadwalPelajaran::truncate();
            Schema::enableForeignKeyConstraints();

            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\XmlJadwalSeeder', '--force' => true]);

            $this->result = Artisan::output();
            $this->imported = true;
            $this->dispatch('show-toast', message: 'Import jadwal berhasil!', type: 'success');

            // Cleanup temp file
            @unlink($tempPath);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Error: ' . $e->getMessage(), type: 'danger');
        }

        $this->importing = false;
    }

    public function clearAll()
    {
        Schema::disableForeignKeyConstraints();
        JadwalGuru::truncate();
        JadwalPelajaran::truncate();
        Schema::enableForeignKeyConstraints();

        $this->confirmClear = false;
        $this->dispatch('show-toast', message: 'Semua jadwal berhasil dihapus!', type: 'success');
    }

    public function clearGuru()
    {
        Schema::disableForeignKeyConstraints();
        User::where('role', 'guru')->delete();
        Schema::enableForeignKeyConstraints();

        $this->dispatch('show-toast', message: 'Semua guru berhasil dihapus!', type: 'success');
    }

    public function clearKelas()
    {
        Schema::disableForeignKeyConstraints();
        Rombel::truncate();
        Schema::enableForeignKeyConstraints();

        $this->dispatch('show-toast', message: 'Semua kelas berhasil dihapus!', type: 'success');
    }

    public function clearMapel()
    {
        Schema::disableForeignKeyConstraints();
        MataPelajaran::truncate();
        Schema::enableForeignKeyConstraints();

        $this->dispatch('show-toast', message: 'Semua mata pelajaran berhasil dihapus!', type: 'success');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Panduan Format XML');

        $sheet->setCellValue('A1', 'PANDUAN STRUKTUR XML aSc TIMETABLES');
        $sheet->setCellValue('A2', 'Aplikasi GADDAMAY mendukung import XML resmi dari aplikasi jadwal pelajaran aSc Timetables.');
        $sheet->setCellValue('A4', 'Elemen XML Kunci yang Diekstrak:');
        $sheet->setCellValue('A5', '1. <teachers> : Daftar guru pengajar (id, name, short)');
        $sheet->setCellValue('A6', '2. <classes> : Daftar rombongan belajar / kelas (id, name, short)');
        $sheet->setCellValue('A7', '3. <subjects> : Daftar mata pelajaran (id, name, short)');
        $sheet->setCellValue('A8', '4. <lessons> & <cards> : Pemetaan hari, jam pelajaran ke- berapa, guru pengampu, kelas, dan mapel');

        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(70);

        $filename = 'panduan_import_jadwal_asc.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Pelajaran');

        $headers = ['No', 'Hari', 'Jam Ke', 'Kelas', 'Mata Pelajaran', 'Guru Pengampu'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:F1')->getFont()->getColor()->setRGB('FFFFFF');

        $jadwals = JadwalPelajaran::with(['rombel', 'mataPelajaran', 'jadwalGuru.guru'])
            ->orderBy('hari')
            ->orderBy('jam_ke_mulai')
            ->get();

        $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

        $row = 2;
        $no = 1;
        foreach ($jadwals as $j) {
            $gurus = $j->jadwalGuru->map(fn($jg) => $jg->guru?->name)->filter()->implode(', ');

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $namaHari[$j->hari] ?? $j->hari);
            $sheet->setCellValue('C' . $row, $j->jam_ke_mulai . ' - ' . $j->jam_ke_selesai);
            $sheet->setCellValue('D' . $row, $j->rombel?->nama_kelas ?? '-');
            $sheet->setCellValue('E' . $row, $j->mataPelajaran?->nama_mapel ?? '-');
            $sheet->setCellValue('F' . $row, $gurus ?: '-');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_jadwal_pelajaran_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.import-jadwal', [
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalKelas' => Rombel::count(),
            'totalMapel' => MataPelajaran::count(),
            'totalJadwal' => JadwalPelajaran::count(),
        ]);
    }
}
