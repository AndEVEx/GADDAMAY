<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\AgendaHarian;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard Admin')]
class DashboardAdmin extends Component
{
    public function render()
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        return view('livewire.admin.dashboard-admin', [
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalKelas' => Rombel::count(),
            'totalMapel' => MataPelajaran::count(),
            'totalJadwal' => JadwalPelajaran::count(),
            'agendaHariIni' => AgendaHarian::where('tanggal', $today)->count(),
            'agendaSelesai' => AgendaHarian::where('tanggal', $today)->where('status', 'selesai')->count(),
            'agendaBerjalan' => AgendaHarian::where('tanggal', $today)->where('status', 'berjalan')->count(),
        ]);
    }
}
