<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Services\AuditLogService;

#[Layout('components.layouts.app')]
#[Title('Manajemen Jadwal Pelajaran')]
class ManajemenJadwal extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterHari = '';
    public string $filterRombel = '';
    public string $filterMapel = '';

    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';

    public int $hari = 1;
    public int $jam_ke_mulai = 1;
    public int $jam_ke_selesai = 2;
    public string $rombel_id = '';
    public string $mapel_id = '';
    public string $guru_id = '';
    public string $keterangan = '';
    public string $kegiatan_khusus = '';

    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteTitle = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterHari() { $this->resetPage(); }
    public function updatingFilterRombel() { $this->resetPage(); }
    public function updatingFilterMapel() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'hari', 'jam_ke_mulai', 'jam_ke_selesai', 'rombel_id', 'mapel_id', 'guru_id', 'keterangan', 'kegiatan_khusus', 'editing']);
        $this->hari = 1;
        $this->jam_ke_mulai = 1;
        $this->jam_ke_selesai = 2;

        $firstRombel = Rombel::orderBy('nama_kelas')->first();
        if ($firstRombel) {
            $this->rombel_id = $firstRombel->id;
        }
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $jadwal = JadwalPelajaran::with('guru')->findOrFail($id);
        $this->editId = $jadwal->id;
        $this->hari = (int)$jadwal->hari;
        $this->jam_ke_mulai = (int)$jadwal->jam_ke_mulai;
        $this->jam_ke_selesai = (int)$jadwal->jam_ke_selesai;
        $this->rombel_id = $jadwal->rombel_id;
        $this->mapel_id = $jadwal->mapel_id ?? '';
        $this->guru_id = $jadwal->guru->first()?->id ?? '';
        $this->keterangan = $jadwal->keterangan ?? '';
        $this->kegiatan_khusus = $jadwal->kegiatan_khusus ?? '';
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'hari' => 'required|integer|between:1,7',
            'jam_ke_mulai' => 'required|integer|min:1',
            'jam_ke_selesai' => 'required|integer|gte:jam_ke_mulai',
            'rombel_id' => 'required|exists:rombel,id',
            'mapel_id' => 'nullable|exists:mata_pelajaran,id',
            'keterangan' => 'nullable|string',
            'kegiatan_khusus' => 'nullable|string',
        ]);

        $data = [
            'hari' => $this->hari,
            'jam_ke_mulai' => $this->jam_ke_mulai,
            'jam_ke_selesai' => $this->jam_ke_selesai,
            'rombel_id' => $this->rombel_id,
            'mapel_id' => $this->mapel_id ?: null,
            'keterangan' => $this->keterangan ?: null,
            'kegiatan_khusus' => $this->kegiatan_khusus ?: null,
        ];

        if ($this->editing) {
            $jadwal = JadwalPelajaran::findOrFail($this->editId);
            $old = $jadwal->toArray();
            $jadwal->update($data);
            AuditLogService::logUpdate($jadwal, $old);

            if ($this->guru_id) {
                $jadwal->guru()->sync([$this->guru_id]);
            } else {
                $jadwal->guru()->detach();
            }

            $this->dispatch('show-toast', message: 'Jadwal pelajaran berhasil diperbarui!', type: 'success');
        } else {
            $jadwal = JadwalPelajaran::create($data);
            AuditLogService::logCreate($jadwal);

            if ($this->guru_id) {
                $jadwal->guru()->sync([$this->guru_id]);
            }

            $this->dispatch('show-toast', message: 'Jadwal pelajaran berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'hari', 'jam_ke_mulai', 'jam_ke_selesai', 'rombel_id', 'mapel_id', 'guru_id', 'keterangan', 'kegiatan_khusus', 'editing']);
    }

    public function confirmDeleteJadwal(string $id)
    {
        $jadwal = JadwalPelajaran::with(['rombel', 'mataPelajaran'])->findOrFail($id);
        $this->deleteId = $id;
        $mapelOrKhusus = $jadwal->kegiatan_khusus ?? ($jadwal->mataPelajaran->nama_mapel ?? 'Jadwal');
        $this->deleteTitle = "{$jadwal->rombel->nama_kelas} - {$jadwal->hari_label} ({$mapelOrKhusus})";
        $this->confirmDelete = true;
    }

    public function deleteJadwal()
    {
        $jadwal = JadwalPelajaran::findOrFail($this->deleteId);
        AuditLogService::logDelete($jadwal);
        $jadwal->jadwalGuru()->delete();
        $jadwal->agendaHarian()->delete();
        $jadwal->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'Jadwal pelajaran berhasil dihapus!', type: 'success');
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Jadwal');

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Hari');
        $sheet->setCellValue('C1', 'Jam Ke');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Mata Pelajaran / Kegiatan');
        $sheet->setCellValue('F1', 'Guru Pengajar');
        
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:F1')->getFont()->getColor()->setRGB('FFFFFF');

        $jadwals = JadwalPelajaran::with(['rombel', 'mataPelajaran', 'guru'])
            ->when($this->filterHari, fn($q) => $q->where('hari', $this->filterHari))
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->when($this->filterMapel, fn($q) => $q->where('mapel_id', $this->filterMapel))
            ->when($this->search, function($q) {
                $q->where('keterangan', 'like', "%{$this->search}%")
                  ->orWhere('kegiatan_khusus', 'like', "%{$this->search}%")
                  ->orWhereHas('mataPelajaran', fn($m) => $m->where('nama_mapel', 'like', "%{$this->search}%"))
                  ->orWhereHas('rombel', fn($r) => $r->where('nama_kelas', 'like', "%{$this->search}%"));
            })
            ->orderBy('hari')
            ->orderBy('rombel_id')
            ->orderBy('jam_ke_mulai')->get();

        $row = 2;
        $no = 1;
        foreach ($jadwals as $j) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $j->hari_label);
            $sheet->setCellValue('C' . $row, $j->jam_ke_mulai . ' - ' . $j->jam_ke_selesai);
            $sheet->setCellValue('D' . $row, $j->rombel->nama_kelas ?? '-');
            
            $mapelAtauKegiatan = $j->kegiatan_khusus ? $j->kegiatan_khusus : ($j->mataPelajaran->nama_mapel ?? 'Tanpa Mapel');
            $sheet->setCellValue('E' . $row, $mapelAtauKegiatan);
            
            $guru = $j->guru->pluck('name')->join(', ') ?: 'Belum ditentukan';
            $sheet->setCellValue('F' . $row, $guru);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_jadwal_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $gurus = User::whereIn('role', ['guru', 'ketua_mgmp', 'waka', 'admin'])->orderBy('name')->get();

        $jadwals = JadwalPelajaran::with(['rombel', 'mataPelajaran', 'guru'])
            ->when($this->filterHari, fn($q) => $q->where('hari', $this->filterHari))
            ->when($this->filterRombel, fn($q) => $q->where('rombel_id', $this->filterRombel))
            ->when($this->filterMapel, fn($q) => $q->where('mapel_id', $this->filterMapel))
            ->when($this->search, function($q) {
                $q->where('keterangan', 'like', "%{$this->search}%")
                  ->orWhere('kegiatan_khusus', 'like', "%{$this->search}%")
                  ->orWhereHas('mataPelajaran', fn($m) => $m->where('nama_mapel', 'like', "%{$this->search}%"))
                  ->orWhereHas('rombel', fn($r) => $r->where('nama_kelas', 'like', "%{$this->search}%"));
            })
            ->orderBy('hari')
            ->orderBy('rombel_id')
            ->orderBy('jam_ke_mulai')
            ->paginate(15);

        return view('livewire.admin.manajemen-jadwal', [
            'jadwals' => $jadwals,
            'rombels' => $rombels,
            'mapels' => $mapels,
            'gurus' => $gurus,
        ]);
    }
}

