<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AgendaHarian;
use App\Models\TujuanPembelajaran;
use App\Models\MotivasiPantun;

#[Layout('components.layouts.app')]
#[Title('Isi Materi')]
class IsiMateri extends Component
{
    public AgendaHarian $agenda;
    public string $materi = '';
    public array $selectedTp = [];

    public function mount(AgendaHarian $agenda)
    {
        $this->agenda = $agenda->load(['jadwalPelajaran.rombel', 'jadwalPelajaran.mataPelajaran', 'tujuanPembelajaran']);
        $this->materi = $agenda->materi_diajarkan ?? '';
        $this->selectedTp = $agenda->tujuanPembelajaran->pluck('id')->toArray();

        // Dispatch Motivasi / Kata Mutiara popup to Guru device after handshake
        $motivasi = MotivasiPantun::siapMengajar()->inRandomOrder()->first();
        if ($motivasi) {
            $this->dispatch('show-motivasi', [
                'isi' => $motivasi->isi,
                'tipe' => $motivasi->tipe,
            ]);
        }
    }

    public function getTpListProperty()
    {
        $mapelId = $this->agenda->jadwalPelajaran?->mapel_id;
        $rombelId = $this->agenda->jadwalPelajaran?->rombel_id;

        if (!$mapelId) return collect();

        $userId = auth()->id();
        $query = TujuanPembelajaran::where('mapel_id', $mapelId);

        // Filter by rombel tingkat
        $rombel = $this->agenda->jadwalPelajaran?->rombel;
        if ($rombel && $rombel->tingkat) {
            $query->where(function ($q) use ($rombel) {
                $q->where('tingkat', $rombel->tingkat)
                  ->orWhereNull('tingkat'); // backward compat: show TPs without tingkat
            });
        }

        // Check if teacher has specific TPs for this mapel
        $hasTeacherTps = TujuanPembelajaran::where('mapel_id', $mapelId)
            ->where('ketua_mgmp_id', $userId)
            ->exists();

        if ($hasTeacherTps) {
            $query->where(function ($q) use ($userId) {
                $q->where('ketua_mgmp_id', $userId)
                  ->orWhereNull('ketua_mgmp_id');
            });
        }

        $allTp = $query->orderBy('order_sequence')->get();

        // Deduplicate: If there are exact duplicate TPs (same deskripsi), display only 1
        $allTp = $allTp->unique(function ($tp) {
            return strtolower(trim(preg_replace('/\s+/', ' ', $tp->deskripsi_tp)));
        })->values();

        // Get TP IDs already taught in this rombel for this mapel
        $taughtTpIds = AgendaHarian::where('guru_id', auth()->id())
            ->whereHas('jadwalPelajaran', function ($q) use ($mapelId, $rombelId) {
                $q->where('mapel_id', $mapelId);
                if ($rombelId) $q->where('rombel_id', $rombelId);
            })
            ->where('id', '!=', $this->agenda->id)
            ->with('tujuanPembelajaran')
            ->get()
            ->flatMap(fn($a) => $a->tujuanPembelajaran->pluck('id'))
            ->unique()
            ->toArray();

        // Sort: untaught first, then taught
        return $allTp->sortBy(function ($tp) use ($taughtTpIds) {
            return in_array($tp->id, $taughtTpIds) ? 1 : 0;
        })->map(function ($tp) use ($taughtTpIds) {
            $tp->is_taught = in_array($tp->id, $taughtTpIds);
            return $tp;
        });
    }

    public function simpan()
    {
        $this->validate([
            'materi' => 'required|min:3',
        ]);

        $this->agenda->update(['materi_diajarkan' => $this->materi]);
        $this->agenda->tujuanPembelajaran()->sync($this->selectedTp);

        $this->dispatch('show-toast', message: 'Materi & TP tersimpan! Silakan ambil foto suasana kelas/murid.', type: 'success');

        return redirect()->route('guru.foto-guru', $this->agenda->id);
    }

    public function batalkanAgenda()
    {
        if ($this->agenda) {
            $this->agenda->delete();
            $this->dispatch('show-toast', message: 'Sesi agenda berhasil dibatalkan!', type: 'info');
        }
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        return view('livewire.guru.isi-materi', [
            'tpList' => $this->tpList,
        ]);
    }
}
