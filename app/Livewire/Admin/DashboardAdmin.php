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

use Livewire\WithPagination;
use App\Models\AuditLog;
use App\Services\JadwalSwapService;
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.app')]
#[Title('Dashboard Admin')]
class DashboardAdmin extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $searchLogin = '';
    public string $filterRole = '';
    public int $loginLogsLimit = 15;

    public function updatingSearchLogin()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function tukarJadwalBlok()
    {
        $res = JadwalSwapService::swapAllVocationalBlockSchedules();

        if ($res['success']) {
            $this->dispatch('show-toast', message: $res['message'], type: 'success');
        } else {
            $this->dispatch('show-toast', message: $res['message'], type: 'warning');
        }
    }

    public function getOnlineUsersProperty(): array
    {
        $onlineUsers = [];
        $onlineIds = Cache::get('online_user_ids', []);

        foreach ($onlineIds as $id) {
            $data = Cache::get('user_online_' . $id);
            if ($data) {
                $onlineUsers[] = (object) $data;
            }
        }

        // If current auth user is not in list, add them
        if (auth()->check()) {
            $myId = auth()->id();
            $exists = collect($onlineUsers)->contains('id', $myId);
            if (!$exists) {
                $onlineUsers[] = (object) [
                    'id' => $myId,
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'role' => auth()->user()->role,
                    'last_seen_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ];
            }
        }

        // Sort by last_seen_at descending
        usort($onlineUsers, fn($a, $b) => strcmp($b->last_seen_at ?? '', $a->last_seen_at ?? ''));

        return $onlineUsers;
    }

    public function render()
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        // Fetch recent login activity logs
        $loginLogs = AuditLog::where('action', 'login')
            ->with('user')
            ->when($this->searchLogin, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('user', fn($u) => $u->where('name', 'like', "%{$this->searchLogin}%")->orWhere('email', 'like', "%{$this->searchLogin}%"))
                      ->orWhere('ip_address', 'like', "%{$this->searchLogin}%")
                      ->orWhere('new_values', 'like', "%{$this->searchLogin}%");
                });
            })
            ->when($this->filterRole, function ($q) {
                $q->whereHas('user', fn($u) => $u->where('role', $this->filterRole));
            })
            ->latest()
            ->paginate($this->loginLogsLimit);

        return view('livewire.admin.dashboard-admin', [
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalKelas' => Rombel::count(),
            'totalMapel' => MataPelajaran::count(),
            'totalJadwal' => JadwalPelajaran::count(),
            'agendaHariIni' => AgendaHarian::where('tanggal', $today)->count(),
            'agendaSelesai' => AgendaHarian::where('tanggal', $today)->where('status', 'selesai')->count(),
            'agendaBerjalan' => AgendaHarian::where('tanggal', $today)->where('status', 'berjalan')->count(),
            'onlineUsers' => $this->onlineUsers,
            'loginLogs' => $loginLogs,
        ]);
    }
}
