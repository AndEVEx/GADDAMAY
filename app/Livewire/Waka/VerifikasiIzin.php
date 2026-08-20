<?php

namespace App\Livewire\Waka;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\IzinGuru;
use App\Models\User;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Verifikasi Izin Guru')]
class VerifikasiIzin extends Component
{
    use WithPagination;

    public string $filterStatus = 'menunggu';
    public string $search = '';

    // Modal state for Approval / Rejection
    public ?string $selectedIzinId = null;
    public ?string $substituteGuruId = null;
    public string $catatanWaka = '';
    public bool $showApprovalModal = false;
    public bool $showRejectModal = false;
    public bool $showDetailModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function openApprove(string $id)
    {
        $izin = IzinGuru::find($id);
        if (!$izin) return;

        $this->selectedIzinId = $id;
        $this->substituteGuruId = $izin->guru_pengganti_id;
        $this->catatanWaka = '';
        $this->showApprovalModal = true;
    }

    public function openReject(string $id)
    {
        $this->selectedIzinId = $id;
        $this->catatanWaka = '';
        $this->showRejectModal = true;
    }

    public function openDetail(string $id)
    {
        $this->selectedIzinId = $id;
        $this->showDetailModal = true;
    }

    public function closeModals()
    {
        $this->showApprovalModal = false;
        $this->showRejectModal = false;
        $this->showDetailModal = false;
        $this->selectedIzinId = null;
    }

    public function approveIzin()
    {
        $izin = IzinGuru::with('guru')->find($this->selectedIzinId);
        if (!$izin) return;

        $now = Carbon::now('Asia/Jakarta');

        // Update Izin Record
        $izin->update([
            'status' => 'disetujui',
            'guru_pengganti_id' => $this->substituteGuruId ?: $izin->guru_pengganti_id,
            'diverifikasi_oleh_id' => auth()->id(),
            'catatan_waka' => $this->catatanWaka ?: 'Disetujui oleh Waka Kurikulum.',
            'waktu_verifikasi' => $now,
        ]);

        // Auto-create/update AgendaHarian for all affected dates & schedules
        $start = Carbon::parse($izin->tanggal_mulai);
        $end = Carbon::parse($izin->tanggal_selesai);

        $affectedCount = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeekIso; // 1=Senin..7=Minggu
            $dateStr = $date->format('Y-m-d');

            // Find all teaching schedules for this teacher on this day
            $jadwalsQuery = JadwalPelajaran::where('hari', $dayOfWeek)
                ->whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $izin->guru_id));

            $jadwals = $jadwalsQuery->get();

            // If not seharian, filter by selected schedules or periods
            if (!$izin->is_seharian) {
                if (!empty($izin->jadwal_ids) && is_array($izin->jadwal_ids)) {
                    $jadwals = $jadwals->whereIn('id', $izin->jadwal_ids);
                } elseif (!empty($izin->jam_terpilih) && is_array($izin->jam_terpilih)) {
                    $jadwals = $jadwals->filter(function ($j) use ($izin) {
                        $periodRange = range($j->jam_ke_mulai, $j->jam_ke_selesai);
                        return count(array_intersect($periodRange, $izin->jam_terpilih)) > 0;
                    });
                }
            }

            foreach ($jadwals as $jadwal) {
                // Determine agenda status and presence
                $kehadiranStatus = in_array($izin->jenis_izin, ['izin', 'sakit', 'cuti'])
                    ? $izin->jenis_izin
                    : 'izin';

                AgendaHarian::updateOrCreate(
                    [
                        'jadwal_pelajaran_id' => $jadwal->id,
                        'guru_id' => $izin->guru_id,
                        'tanggal' => $dateStr,
                    ],
                    [
                        'status' => 'selesai',
                        'status_kehadiran_guru' => $kehadiranStatus,
                        'guru_pengganti_id' => $izin->guru_pengganti_id,
                        'koreksi_oleh_id' => auth()->id(),
                        'materi_diajarkan' => 'Izin: ' . $izin->jenis_izin_label . ' (' . $izin->waktu_display . ' - ' . $izin->alasan . ')',
                        'waktu_mulai' => $date->copy()->setTime(7, 0),
                        'waktu_selesai' => $date->copy()->setTime(15, 0),
                    ]
                );
                $affectedCount++;
            }
        }

        $this->closeModals();

        // Kirim notifikasi ke Guru bersangkutan
        try {
            $guru = $izin->guru;
            if ($guru) {
                $guru->notify(new \App\Notifications\StatusIzinGuruNotification($izin, 'disetujui', $this->catatanWaka));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi izin disetujui: ' . $e->getMessage());
        }

        $this->dispatch('show-toast', message: "Izin guru {$izin->guru?->name} ({$izin->waktu_display}) berhasil disetujui & diverifikasi! ({$affectedCount} sesi KBM disesuaikan).", type: 'success');
    }

    public function rejectIzin()
    {
        $this->validate([
            'catatanWaka' => 'required|min:5',
        ], [
            'catatanWaka.required' => 'Mohon sertakan alasan penolakan izin.',
            'catatanWaka.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $izin = IzinGuru::with('guru')->find($this->selectedIzinId);
        if (!$izin) return;

        $now = Carbon::now('Asia/Jakarta');

        $izin->update([
            'status' => 'ditolak',
            'diverifikasi_oleh_id' => auth()->id(),
            'catatan_waka' => $this->catatanWaka,
            'waktu_verifikasi' => $now,
        ]);

        $this->closeModals();

        // Kirim notifikasi penolakan ke Guru bersangkutan
        try {
            $guru = $izin->guru;
            if ($guru) {
                $guru->notify(new \App\Notifications\StatusIzinGuruNotification($izin, 'ditolak', $this->catatanWaka));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi izin ditolak: ' . $e->getMessage());
        }

        $this->dispatch('show-toast', message: "Pengajuan izin guru {$izin->guru?->name} ditolak.", type: 'warning');
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Izin Guru');

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Pengajuan');
        $sheet->setCellValue('C1', 'Nama Guru');
        $sheet->setCellValue('D1', 'Jenis Izin');
        $sheet->setCellValue('E1', 'Waktu / Jam');
        $sheet->setCellValue('F1', 'Alasan');
        $sheet->setCellValue('G1', 'Guru Pengganti');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Diverifikasi Oleh');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1A56DB');
        $sheet->getStyle('A1:I1')->getFont()->getColor()->setRGB('FFFFFF');

        $items = IzinGuru::with(['guru', 'guruPengganti', 'diverifikasiOleh'])
            ->when($this->filterStatus !== 'semua', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->whereHas('guru', fn($g) => $g->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('alasan', 'like', '%' . $this->search . '%');
            })
            ->latest()->get();

        $row = 2;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('C' . $row, $item->guru?->name ?? '-');
            $sheet->setCellValue('D' . $row, $item->jenis_izin_label);
            $sheet->setCellValue('E' . $row, $item->waktu_display);
            $sheet->setCellValue('F' . $row, $item->alasan);
            $sheet->setCellValue('G' . $row, $item->guruPengganti?->name ?? '-');
            $sheet->setCellValue('H' . $row, ucfirst($item->status));
            $sheet->setCellValue('I' . $row, $item->diverifikasiOleh?->name ?? '-');
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'rekap_izin_guru_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $items = IzinGuru::with(['guru', 'guruPengganti', 'diverifikasiOleh'])
            ->when($this->filterStatus !== 'semua', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->whereHas('guru', fn($g) => $g->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('alasan', 'like', '%' . $this->search . '%');
            })
            ->latest()->get();

        $headers = [
            ['name' => 'No', 'width' => '5%', 'align' => 'center'],
            ['name' => 'Tanggal & Waktu', 'width' => '15%', 'align' => 'center'],
            ['name' => 'Nama Guru', 'width' => '22%', 'align' => 'left'],
            ['name' => 'Jenis Izin', 'width' => '12%', 'align' => 'center'],
            ['name' => 'Sesi / Jam', 'width' => '15%', 'align' => 'center'],
            ['name' => 'Alasan & Keterangan', 'width' => '21%', 'align' => 'left'],
            ['name' => 'Status', 'width' => '10%', 'align' => 'center'],
        ];

        $rows = [];
        $no = 1;
        foreach ($items as $item) {
            $rows[] = [
                $no++,
                $item->created_at->format('d/m/Y H:i'),
                '<strong>' . e($item->guru?->name ?? '-') . '</strong>',
                e($item->jenis_izin_label),
                e($item->waktu_display),
                e($item->alasan),
                e(ucfirst($item->status)),
            ];
        }

        $filename = 'Laporan_Izin_Guru_' . date('Y-m-d') . '.pdf';
        return \App\Services\PdfReportService::download(
            'REKAPITULASI PENGAJUAN & VERIFIKASI IZIN GURU',
            'Tahun Pelajaran 2026/2027 — SMKN 2 Indramayu',
            $headers,
            $rows,
            $filename,
            'A4',
            'landscape',
            ['Total Data Izin' => count($rows) . ' Pengajuan'],
            auth()->user()?->name,
            'Waka Kurikulum / Verifikator'
        );
    }

    public function render()
    {
        $query = IzinGuru::with(['guru', 'guruPengganti', 'diverifikasiOleh'])
            ->when($this->filterStatus !== 'semua', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->whereHas('guru', fn($g) => $g->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('alasan', 'like', '%' . $this->search . '%');
            })
            ->latest();

        $daftarIzin = $query->paginate(10);

        $daftarGuru = User::whereIn('role', ['guru', 'ketua_mgmp'])
            ->orderBy('name')
            ->get();

        $selectedIzin = $this->selectedIzinId ? IzinGuru::with(['guru', 'guruPengganti', 'diverifikasiOleh'])->find($this->selectedIzinId) : null;

        $countMenunggu = IzinGuru::where('status', 'menunggu')->count();
        $countDisetujui = IzinGuru::where('status', 'disetujui')->count();
        $countDitolak = IzinGuru::where('status', 'ditolak')->count();

        return view('livewire.waka.verifikasi-izin', [
            'daftarIzin' => $daftarIzin,
            'daftarGuru' => $daftarGuru,
            'selectedIzin' => $selectedIzin,
            'countMenunggu' => $countMenunggu,
            'countDisetujui' => $countDisetujui,
            'countDitolak' => $countDitolak,
        ]);
    }
}
