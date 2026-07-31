<?php

namespace App\Livewire\KetuaMgmp;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Ketua MGMP')]
class DashboardKetuaMgmp extends Component
{
    public function render()
    {
        $now = Carbon::now('Asia/Jakarta');
        $hour = $now->hour;

        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        $todayDate = $now->translatedFormat('l, d F Y');

        // Fetch mapel with total TP count and TP count created by logged in ketua MGMP
        $mapels = MataPelajaran::withCount([
            'tujuanPembelajaran',
            'tujuanPembelajaran as user_tp_count' => fn($q) => $q->where('ketua_mgmp_id', auth()->id()),
        ])->orderBy('nama_mapel')->get();

        $totalTpSystem = TujuanPembelajaran::count();
        $totalTpUser = TujuanPembelajaran::where('ketua_mgmp_id', auth()->id())->count();

        return view('livewire.ketua-mgmp.dashboard-ketua-mgmp', [
            'greeting' => $greeting,
            'todayDate' => $todayDate,
            'mapels' => $mapels,
            'totalTpSystem' => $totalTpSystem,
            'totalTpUser' => $totalTpUser,
        ]);
    }
}
