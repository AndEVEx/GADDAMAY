<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        $this->unreadCount = Auth::user()->unreadNotifications()->count();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->unreadCount = 0;
    }

    public function markAsRead(string $notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);
        }
    }

    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();
        $this->unreadCount = 0;
        $this->dispatch('show-toast', message: 'Semua riwayat notifikasi berhasil dibersihkan.', type: 'info');
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();

        return view('livewire.components.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
