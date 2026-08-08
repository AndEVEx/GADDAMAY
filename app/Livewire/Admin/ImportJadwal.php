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

            // Truncate old schedule entries to prevent duplicate schedules on re-import
            JadwalGuru::truncate();
            JadwalPelajaran::truncate();

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
        JadwalGuru::truncate();
        JadwalPelajaran::truncate();
        $this->confirmClear = false;
        $this->dispatch('show-toast', message: 'Semua jadwal berhasil dihapus!', type: 'success');
    }

    public function clearGuru()
    {
        User::where('role', 'guru')->delete();
        $this->dispatch('show-toast', message: 'Semua guru berhasil dihapus!', type: 'success');
    }

    public function clearKelas()
    {
        Rombel::truncate();
        $this->dispatch('show-toast', message: 'Semua kelas berhasil dihapus!', type: 'success');
    }

    public function clearMapel()
    {
        MataPelajaran::truncate();
        $this->dispatch('show-toast', message: 'Semua mata pelajaran berhasil dihapus!', type: 'success');
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
