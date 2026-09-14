<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
#[Title('Ganti Password')]
class GantiPassword extends Component
{
    public string $password_lama = '';
    public string $password_baru = '';
    public string $password_baru_confirmation = '';

    public function updatePassword()
    {
        $this->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'min:6', 'confirmed'],
        ], [
            'password_lama.required' => 'Password saat ini harus diisi.',
            'password_baru.required' => 'Password baru harus diisi.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->password_lama, $user->password)) {
            $this->addError('password_lama', 'Password saat ini salah.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->password_baru),
        ]);

        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation']);

        $this->dispatch('show-toast', message: 'Password Anda berhasil diperbarui!', type: 'success');
    }

    public function render()
    {
        return view('livewire.auth.ganti-password');
    }
}
