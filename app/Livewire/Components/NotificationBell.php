<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public bool $isNavbar = false;

    public function mount(bool $isNavbar = false)
    {
        $this->isNavbar = $isNavbar;
        $this->loadCount();
    }

    public function loadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        }
    }

    public function markAllRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->unreadCount = 0;
        }
    }

    public function openNotification(string $notificationId, ?string $targetUrl = null)
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->unreadCount = max(0, $this->unreadCount - 1);
            }
        }

        if (!empty($targetUrl)) {
            return $this->redirect($targetUrl, navigate: true);
        }
    }

    public function markAsRead(string $notificationId)
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->unreadCount = max(0, $this->unreadCount - 1);
            }
        }
    }

    public function deleteAllNotifications()
    {
        if (Auth::check()) {
            Auth::user()->notifications()->delete();
            $this->unreadCount = 0;
            $this->dispatch('show-toast', message: 'Semua riwayat notifikasi berhasil dibersihkan.', type: 'info');
        }
    }

    public function render()
    {
        $notifications = Auth::check()
            ? Auth::user()->notifications()->latest()->take(15)->get()
            : collect();

        $this->loadCount();

        return view('livewire.components.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
