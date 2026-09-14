<div wire:poll.15s="loadCount">
    {{-- Sidebar Notification Menu Item --}}
    <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex align-items-center justify-content-between py-2" data-bs-toggle="collapse" data-bs-target="#notificationListCollapse" onclick="event.stopPropagation();">
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill text-warning me-2 fs-6"></i>
            <span class="small fw-semibold">Notifikasi & Pengingat</span>
        </div>
        @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.72rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }} Baru</span>
        @else
            <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill" style="font-size: 0.7rem;">0</span>
        @endif
    </a>

    {{-- Notification List Collapse Drawer --}}
    <div class="collapse px-2 py-2 bg-white rounded my-1 border shadow-sm" id="notificationListCollapse" onclick="event.stopPropagation();" style="max-height: 340px; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
            <span class="fw-bold text-dark" style="font-size: 0.75rem;"><i class="bi bi-clock-history me-1 text-primary"></i>Riwayat Notifikasi</span>
            <div class="d-flex align-items-center gap-2">
                @if($unreadCount > 0)
                    <button class="btn btn-link btn-sm text-primary p-0 text-decoration-none" wire:click="markAllRead" style="font-size: 0.7rem;">
                        Tandai dibaca
                    </button>
                @endif
                @if($notifications->isNotEmpty())
                    <button class="btn btn-link btn-sm text-danger p-0 text-decoration-none" wire:click="deleteAllNotifications" wire:confirm="Hapus semua riwayat notifikasi?" style="font-size: 0.7rem;">
                        <i class="bi bi-trash me-1"></i>Bersihkan
                    </button>
                @endif
            </div>
        </div>

        {{-- Web Push Controls in Sidebar --}}
        <div class="mb-2 p-2 bg-light rounded border">
            <div class="d-flex align-items-center gap-1">
                <button type="button" onclick="subscribeToWebPush()" class="btn btn-primary btn-sm flex-fill py-1 fw-semibold d-flex align-items-center justify-content-center gap-1" style="font-size: 0.72rem; border-radius: 6px;">
                    <i class="bi bi-phone-vibrate"></i>
                    <span id="webpush-btn-text">Aktifkan Notifikasi HP</span>
                </button>
                <button type="button" onclick="testWebPushNotification()" class="btn btn-outline-secondary btn-sm py-1" title="Uji Coba Notifikasi" style="font-size: 0.72rem; border-radius: 6px;">
                    <i class="bi bi-send-check"></i> Test
                </button>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.65rem; line-height: 1.2;">
                Notifikasi akan berdering di HP Anda meski web ditutup.
            </div>
        </div>

        @forelse($notifications as $notif)
            @php
                $data = $notif->data ?? [];
                $title = $data['title'] ?? 'Notifikasi';
                $message = $data['message'] ?? '';
                $actionUrl = $data['action_url'] ?? null;
                $icon = $data['icon'] ?? 'bi-bell-fill';
                $color = $data['color'] ?? 'text-primary';
                $isUnread = !$notif->read_at;
            @endphp
            <div class="p-2 mb-1 rounded-3 {{ $isUnread ? 'bg-primary bg-opacity-10 border-start border-primary border-3' : 'bg-light' }} d-flex align-items-start gap-2"
                 wire:click="openNotification('{{ $notif->id }}', '{{ $actionUrl }}')" style="cursor: pointer; font-size: 0.75rem; transition: background-color 0.15s;">
                <div class="p-1 rounded-circle bg-white shadow-sm {{ $color }} d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                    <i class="bi {{ $icon }} fs-6"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-dark d-flex align-items-center justify-content-between">
                        <span class="text-truncate">{{ $title }}</span>
                        @if($isUnread)
                            <span class="badge bg-danger rounded-circle p-1 ms-1" style="width: 6px; height: 6px;"></span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1" style="line-height: 1.3; font-size: 0.72rem;">{{ $message }}</div>
                    <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                        <i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-3 small" style="font-size: 0.72rem;">
                <i class="bi bi-bell-slash text-muted fs-4 d-block mb-1 opacity-50"></i>
                Belum ada notifikasi baru.
            </div>
        @endforelse
    </div>
</div>
