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

            return match (Auth::user()->role) {
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
