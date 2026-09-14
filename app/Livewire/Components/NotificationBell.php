<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public bool $isNavbar = false;
    public ?string $lastCheckedAt = null;

    public function mount(bool $isNavbar = false)
    {
        $this->isNavbar = $isNavbar;
        $this->lastCheckedAt = now()->toIso8601String();
        $this->loadCount();
    }

    public function loadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();

            // Check for new notifications since last check (for banner)
            if ($this->lastCheckedAt) {
                $newNotifs = Auth::user()->unreadNotifications()
                    ->where('created_at', '>', Carbon::parse($this->lastCheckedAt))
                    ->latest()
                    ->first();

                if ($newNotifs) {
                    $data = $newNotifs->data ?? [];
                    $title = $data['title'] ?? 'Notifikasi Baru';
                    $message = $data['message'] ?? '';
                    $actionUrl = $data['action_url'] ?? null;

                    $this->dispatch('show-device-notif', [
                        'title' => $title,
                        'body' => $message,
                        'url' => $actionUrl,
                    ]);

                    $this->dispatch('show-toast',
                        message: $title . ': ' . $message,
                        type: 'info'
                    );
                }
            }
            $this->lastCheckedAt = now()->toIso8601String();
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
