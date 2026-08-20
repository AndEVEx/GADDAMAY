<div wire:poll.15s="loadCount">
    @if($isNavbar)
        {{-- Navbar Icon with Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm position-relative d-flex align-items-center justify-content-center p-2 rounded-circle border-0" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="outside"
                    aria-expanded="false" 
                    style="width: 40px; height: 40px; background: rgba(255,255,255,0.15);">
                <i class="bi bi-bell-fill fs-5"></i>
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="width: 320px; max-width: 90vw; border-radius: 14px; overflow: hidden; z-index: 1070;">
                <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1a56db, #0d47a1);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell-fill"></i>
                        <span class="fw-bold">Notifikasi</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-warning text-dark rounded-pill">{{ $unreadCount }} Baru</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($unreadCount > 0)
                            <button class="btn btn-sm btn-link text-white text-decoration-none p-0" wire:click="markAllRead" style="font-size: 0.72rem;">
                                Baca Semua
                            </button>
                        @endif
                        @if($notifications->isNotEmpty())
                            <button class="btn btn-sm btn-link text-white-50 text-decoration-none p-0" wire:click="deleteAllNotifications" wire:confirm="Hapus semua riwayat notifikasi?" title="Hapus Semua" style="font-size: 0.72rem;">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="p-2" style="max-height: 340px; overflow-y: auto;">
                    @forelse($notifications as $notif)
                        @php
                            $data = $notif->data ?? [];
                            $type = $data['type'] ?? 'info';
                            $title = $data['title'] ?? 'Notifikasi KBM';
                            $message = $data['message'] ?? '';
                            $actionUrl = $data['action_url'] ?? null;
                            $icon = $data['icon'] ?? 'bi-bell-fill';
                            $color = $data['color'] ?? 'text-primary';
                            $isUnread = !$notif->read_at;
                        @endphp
                        <div class="p-2 mb-1 rounded-3 {{ $isUnread ? 'bg-primary bg-opacity-10 border-start border-primary border-3' : 'bg-light' }} d-flex align-items-start gap-2 position-relative"
                             wire:click="openNotification('{{ $notif->id }}', '{{ $actionUrl }}')"
                             style="cursor: pointer; transition: background-color 0.15s;">
                            <div class="p-2 rounded-circle bg-white shadow-sm {{ $color }} d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                <i class="bi {{ $icon }} fs-6"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold text-dark d-flex justify-content-between align-items-center small">
                                    <span class="text-truncate">{{ $title }}</span>
                                    @if($isUnread)
                                        <span class="badge bg-danger rounded-circle p-1 ms-1" style="width: 6px; height: 6px;"></span>
                                    @endif
                                </div>
                                <div class="text-muted small" style="font-size: 0.72rem; line-height: 1.3;">
                                    {{ $message }}
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 0.65rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small">
                            <i class="bi bi-bell-slash fs-3 d-block mb-1 text-muted opacity-50"></i>
                            Belum ada notifikasi baru.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        {{-- Sidebar View --}}
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
                <div class="p-2 mb-1 rounded bg-white border {{ $isUnread ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                     wire:click="openNotification('{{ $notif->id }}', '{{ $actionUrl }}')" style="cursor: pointer; font-size: 0.75rem;">
                    <div class="fw-bold text-dark d-flex align-items-center justify-content-between">
                        <span><i class="bi {{ $icon }} {{ $color }} me-1"></i>{{ $title }}</span>
                        @if($isUnread)
                            <span class="badge bg-danger rounded-circle p-1" style="width: 6px; height: 6px;"></span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1" style="line-height: 1.2;">{{ $message }}</div>
                    <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                        <i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-3 small" style="font-size: 0.72rem;">
                    <i class="bi bi-bell text-muted fs-4 d-block mb-1"></i>
                    Belum ada notifikasi baru.
                </div>
            @endforelse
        </div>
    @endif
</div>
