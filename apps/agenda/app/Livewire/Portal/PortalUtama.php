<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\User;
use App\Models\PerizinanSiswa;
use Carbon\Carbon;

#[Layout('components.layouts.portal')]
#[Title('GADDAMAY — Portal Sistem Terpadu SMKN 2 Indramayu')]
class PortalUtama extends Component
{
    public function render()
    {
        $today = Carbon::now('Asia/Jakarta');
        
        $stats = [
            'totalSiswa' => Siswa::count(),
            'totalRombel' => Rombel::count(),
            'totalGuru' => User::whereIn('role', ['guru', 'ketua_mgmp', 'guru_piket'])->count(),
            'izinHariIni' => PerizinanSiswa::whereDate('tanggal_mulai', '<=', $today->toDateString())
                ->whereDate('tanggal_selesai', '>=', $today->toDateString())
                ->whereIn('status', ['disetujui_walas', 'disetujui_piket', 'disetujui_bk'])
                ->count(),
        ];

        return view('livewire.portal.portal-utama', [
            'stats' => $stats,
            'today' => $today,
        ]);
    }
}