<div wire:poll.30s="loadCount">
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm notification-bell position-relative" type="button" data-bs-toggle="dropdown" style="min-height: 40px; min-width: 40px;">
            <i class="bi bi-bell-fill"></i>
            @if($unreadCount > 0)
                <span class="badge-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 1rem;">
            <li class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom">
                <strong class="small">Notifikasi</strong>
                @if($unreadCount > 0)
                    <button class="btn btn-link btn-sm text-primary p-0" wire:click="markAllRead" style="font-size: 0.75rem;">
                        Tandai semua dibaca
                    </button>
                @endif
            </li>

            @forelse($notifications as $notification)
                <li>
                    <a href="#" class="dropdown-item py-2 px-3 {{ !$notification->read_at ? 'bg-primary bg-opacity-10' : '' }}"
                       wire:click.prevent="markAsRead('{{ $notification->id }}')" style="white-space: normal; font-size: 0.85rem;">
                        <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                        <div class="text-muted small mt-1">{{ $notification->data['message'] ?? '' }}</div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-3 py-4 text-center text-muted">
                    <i class="bi bi-bell-slash" style="font-size: 1.5rem;"></i>
                    <p class="small mb-0 mt-1">Belum ada notifikasi</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
