<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\KehadiranMurid;
use App\Models\Siswa;
use App\Models\PerizinanSiswa;

#[Layout('components.layouts.app')]
#[Title('Input Kehadiran')]
class InputKehadiran extends Component
{
    public AgendaHarian $agenda;
    public array $kehadiran = []; // siswa_id => status
    public array $izinSiswaIds = []; // siswa_id yang izinnya sah

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran']);

        $rombelId = $agenda->jadwalPelajaran?->rombel_id;
        if (!$rombelId) return;

        $siswaList = Siswa::where('rombel_id', $rombelId)->orderBy('nama')->get();

        // Load existing kehadiran or default to 'hadir'
        $existingKehadiran = KehadiranMurid::where('agenda_harian_id', $agenda->id)
            ->pluck('status', 'siswa_id')
            ->toArray();

        // Cek perizinan terverifikasi yang sah untuk siswa rombel ini pada tanggal agenda
        $izinSah = PerizinanSiswa::whereIn('siswa_id', $siswaList->pluck('id'))
            ->whereDate('tanggal_mulai', '<=', $agenda->tanggal)
            ->whereDate('tanggal_selesai', '>=', $agenda->tanggal)
            ->whereIn('status', ['disetujui_walas', 'disetujui_piket', 'disetujui_bk'])
            ->get()
            ->keyBy('siswa_id');

        $this->izinSiswaIds = $izinSah->keys()->toArray();

        foreach ($siswaList as $siswa) {
            if (isset($izinSah[$siswa->id])) {
                $this->kehadiran[$siswa->id] = ($izinSah[$siswa->id]->kategori === 'sakit') ? 'sakit' : 'izin';
            } else {
                $this->kehadiran[$siswa->id] = $existingKehadiran[$siswa->id] ?? 'hadir';
            }
        }
    }

    public function setStatus(int|string $siswaId, string $status)
    {
        $this->kehadiran[$siswaId] = $status;
    }

    public function setAllStatus(string $status)
    {
        foreach ($this->kehadiran as $siswaId => $current) {
            $this->kehadiran[$siswaId] = $status;
        }
        $this->dispatch('show-toast', message: "Seluruh siswa diset ke status " . strtoupper($status), type: 'info');
    }

    public function simpan()
    {
        foreach ($this->kehadiran as $siswaId => $status) {
            KehadiranMurid::updateOrCreate(
                ['agenda_harian_id' => $this->agenda->id, 'siswa_id' => $siswaId],
                ['status' => $status]
            );
        }

        // Also sync to sibling agendas in same block if any
        if ($this->agenda->jadwalPelajaran?->mapel_id) {
            $jp = $this->agenda->jadwalPelajaran;
            $siblings = AgendaHarian::where('tanggal', $this->agenda->tanggal)
                ->where('guru_id', $this->agenda->guru_id)
                ->where('id', '!=', $this->agenda->id)
                ->whereHas('jadwalPelajaran', fn($q) => $q
                    ->where('rombel_id', $jp->rombel_id)
                    ->where('mapel_id', $jp->mapel_id)
                )->get();

            foreach ($siblings as $sib) {
                foreach ($this->kehadiran as $siswaId => $status) {
                    KehadiranMurid::updateOrCreate(
                        ['agenda_harian_id' => $sib->id, 'siswa_id' => $siswaId],
                        ['status' => $status]
                    );
                }
            }
        }

        $this->dispatch('show-toast', message: 'Kehadiran berhasil disimpan!', type: 'success');

        return redirect()->route('guru.stopwatch', $this->agenda->id);
    }

    public function render()
    {
        $rombelId = $this->agenda->jadwalPelajaran?->rombel_id;
        $siswaList = $rombelId ? Siswa::where('rombel_id', $rombelId)->orderBy('nama')->get() : collect();

        $summary = collect($this->kehadiran)->countBy();

        return view('livewire.guru.input-kehadiran', [
            'siswaList' => $siswaList,
            'summary' => $summary,
        ]);
    }
}
