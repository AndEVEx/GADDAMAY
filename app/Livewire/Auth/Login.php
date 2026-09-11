<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
#[Title('Login')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $user = Auth::user();

            // Update last_login on user model
            $user->update([
                'last_login_at' => \Carbon\Carbon::now('Asia/Jakarta'),
                'last_login_ip' => request()->ip(),
                'last_login_device' => request()->userAgent(),
            ]);

            // Record login event in AuditLog
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'auditable_type' => \App\Models\User::class,
                'auditable_id' => $user->id,
                'new_values' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'user_agent' => request()->userAgent(),
                    'logged_in_at' => \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString(),
                ],
                'ip_address' => request()->ip(),
            ]);

            // Track online status in cache
            \Illuminate\Support\Facades\Cache::put('user_online_' . $user->id, [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_seen_at' => \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ], now()->addMinutes(10));

            $onlineUserIds = \Illuminate\Support\Facades\Cache::get('online_user_ids', []);
            if (!in_array($user->id, $onlineUserIds)) {
                $onlineUserIds[] = $user->id;
                \Illuminate\Support\Facades\Cache::put('online_user_ids', $onlineUserIds, now()->addMinutes(30));
            }

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'kepsek', 'waka' => redirect()->route('monitoring.dashboard'),
                'ketua_mgmp' => redirect()->route('guru.dashboard'),
                'guru' => redirect()->route('guru.dashboard'),
                'ketua_kelas' => redirect()->route('ketua.verifikasi'),
                default => redirect('/'),
            };
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
