<div>
    <a href="javascript:void(0)" class="dropdown-item py-2 d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#notificationListCollapse" onclick="event.stopPropagation();">
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill text-primary me-2 fs-6"></i>
            <span class="small">Notifikasi</span>
        </div>
        @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @else
            <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill" style="font-size: 0.7rem;">0</span>
        @endif
    </a>

    <div class="collapse px-2 py-2 bg-light rounded my-1 border" id="notificationListCollapse" onclick="event.stopPropagation();" style="max-height: 250px; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
            <span class="fw-bold text-dark" style="font-size: 0.75rem;">Notifikasi Saya</span>
            @if($unreadCount > 0)
                <button class="btn btn-link btn-sm text-primary p-0 text-decoration-none" wire:click="markAllRead" style="font-size: 0.7rem;">
                    Tandai dibaca
                </button>
            @endif
        </div>

        @forelse($notifications as $notification)
            <div class="p-2 mb-1 rounded bg-white border {{ !$notification->read_at ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                 wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer; font-size: 0.75rem;">
                <div class="fw-semibold text-dark">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                <div class="text-muted small mt-1">{{ $notification->data['message'] ?? '' }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.65rem;">
                    <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-2 small" style="font-size: 0.72rem;">
                Belum ada notifikasi baru
            </div>
        @endforelse
    </div>
</div>
