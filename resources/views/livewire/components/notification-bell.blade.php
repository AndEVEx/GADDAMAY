<div>
    <a href="javascript:void(0)" class="dropdown-item py-2 d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#notificationListCollapse" onclick="event.stopPropagation();">
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill text-warning me-2 fs-6"></i>
            <span class="small fw-semibold">Notifikasi & Pengingat</span>
        </div>
        @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @else
            <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill" style="font-size: 0.7rem;">0</span>
        @endif
    </a>

    <div class="collapse px-2 py-2 bg-light rounded my-1 border shadow-sm" id="notificationListCollapse" onclick="event.stopPropagation();" style="max-height: 280px; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
            <span class="fw-bold text-dark" style="font-size: 0.75rem;"><i class="bi bi-clock-history me-1 text-primary"></i>Pengingat Mengajar</span>
            @if($unreadCount > 0)
                <button class="btn btn-link btn-sm text-primary p-0 text-decoration-none" wire:click="markAllRead" style="font-size: 0.7rem;">
                    Tandai dibaca
                </button>
            @endif
        </div>

        {{-- Browser Notification Permission Button --}}
        <div class="mb-2 p-2 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25 text-center">
            <button type="button" onclick="requestBrowserNotificationPermission()" class="btn btn-primary btn-sm w-100 py-1 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">
                <i class="bi bi-bell me-1"></i>Aktifkan Alarm Pengingat HP/Browser
            </button>
        </div>

        @forelse($notifications as $notification)
            <div class="p-2 mb-1 rounded bg-white border {{ !$notification->read_at ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                 wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer; font-size: 0.75rem;">
                <div class="fw-bold text-dark d-flex align-items-center justify-content-between">
                    <span>{{ $notification->data['title'] ?? 'Pengingat Mengajar' }}</span>
                    @if(!$notification->read_at)
                        <span class="badge bg-danger rounded-circle p-1" style="width: 6px; height: 6px;"></span>
                    @endif
                </div>
                <div class="text-muted small mt-1" style="line-height: 1.2;">{{ $notification->data['message'] ?? '' }}</div>
                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                    <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-3 small" style="font-size: 0.72rem;">
                <i class="bi bi-bell text-muted fs-4 d-block mb-1"></i>
                Belum ada notifikasi / pengingat mengajar baru.
            </div>
        @endforelse
    </div>
</div>

<script>
function requestBrowserNotificationPermission() {
    if (!('Notification' in window)) {
        alert('Browser atau perangkat Anda tidak mendukung Web Notification API.');
        return;
    }

    // 1. If already granted
    if (Notification.permission === 'granted') {
        try {
            new Notification('AgenDAmay SMKN 2 Indramayu', {
                body: '🔔 Alarm pengingat mengajar di browser sudah aktif!',
                icon: '/icons/logo-sekolah.png'
            });
        } catch (e) {}
        if (window.showToast) window.showToast('Alarm pengingat mengajar di browser sudah aktif!', 'success');
        return;
    }

    // 2. If previously denied by user in site settings
    if (Notification.permission === 'denied') {
        alert("⚠️ Izin Notifikasi saat ini diblokir di setelan browser Anda.\n\nCara Mengaktifkan:\n1. Klik ikon Gembok 🔒 / Setelan Situs di sebelah kiri alamat URL browser.\n2. Ubah Izin 'Notifikasi' menjadi 'Izinkan' (Allow).\n3. Refresh / Muat ulang halaman ini.");
        return;
    }

    // 3. Request permission from user
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            try {
                new Notification('AgenDAmay SMKN 2 Indramayu', {
                    body: '🔔 Alarm pengingat mengajar di browser berhasil diaktifkan!',
                    icon: '/icons/logo-sekolah.png'
                });
            } catch (e) {}
            if (window.showToast) window.showToast('Alarm pengingat mengajar di browser berhasil diaktifkan!', 'success');
        } else if (permission === 'denied') {
            alert("⚠️ Izin notifikasi ditolak. Anda dapat mengaktifkannya kapan saja melalui ikon Gembok 🔒 di sebelah kiri URL browser.");
        }
    }).catch(err => {
        console.warn('Notification permission error:', err);
    });
}
</script>
