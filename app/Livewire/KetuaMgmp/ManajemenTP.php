<?php

namespace App\Livewire\KetuaMgmp;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;

#[Layout('components.layouts.app')]
#[Title('Manajemen TP')]
class ManajemenTP extends Component
{
    public string $selectedMapel = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $kodeTp = '';
    public string $deskripsiTp = '';
    public int $orderSequence = 0;

    public function create()
    {
        $this->reset(['editId', 'kodeTp', 'deskripsiTp', 'orderSequence', 'editing']);
        $tpCount = TujuanPembelajaran::where('mapel_id', $this->selectedMapel)->count();
        $this->kodeTp = 'TP-' . str_pad($tpCount + 1, 2, '0', STR_PAD_LEFT);
        $this->orderSequence = $tpCount + 1;
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $tp = TujuanPembelajaran::findOrFail($id);
        $this->editId = $tp->id;
        $this->kodeTp = $tp->kode_tp;
        $this->deskripsiTp = $tp->deskripsi_tp;
        $this->orderSequence = $tp->order_sequence;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'selectedMapel' => 'required',
            'kodeTp' => 'required',
            'deskripsiTp' => 'required|min:5',
        ]);

        if ($this->editing) {
            $tp = TujuanPembelajaran::findOrFail($this->editId);
            $tp->update([
                'kode_tp' => $this->kodeTp,
                'deskripsi_tp' => $this->deskripsiTp,
                'order_sequence' => $this->orderSequence,
            ]);
        } else {
            TujuanPembelajaran::create([
                'mapel_id' => $this->selectedMapel,
                'kode_tp' => $this->kodeTp,
                'deskripsi_tp' => $this->deskripsiTp,
                'order_sequence' => $this->orderSequence,
                'ketua_mgmp_id' => auth()->id(),
            ]);
        }

        $this->showForm = false;
        $this->dispatch('show-toast', message: 'TP berhasil disimpan!', type: 'success');
    }

    public function deletetp(string $id)
    {
        TujuanPembelajaran::findOrFail($id)->delete();
        $this->dispatch('show-toast', message: 'TP berhasil dihapus.', type: 'success');
    }

    public function render()
    {
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();
        $tpList = $this->selectedMapel
            ? TujuanPembelajaran::where('mapel_id', $this->selectedMapel)->orderBy('order_sequence')->get()
            : collect();

        return view('livewire.ketua-mgmp.manajemen-t-p', [
            'mapelList' => $mapelList,
            'tpList' => $tpList,
        ]);
    }
}
