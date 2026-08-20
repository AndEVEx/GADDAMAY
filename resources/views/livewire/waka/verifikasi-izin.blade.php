<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-patch-check-fill text-success me-2"></i>Verifikasi & Izin Guru</h4>
            <p class="text-muted small mb-0">Verifikasi pengajuan izin mengajar guru dan atur penugasan guru pengganti</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="exportExcel" class="btn btn-outline-success d-flex align-items-center gap-1" style="min-height: 40px; border-radius: 8px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportExcel"><i class="bi bi-file-earmark-excel"></i> Export Excel</span>
                <span wire:loading wire:target="exportExcel"><span class="spinner-border spinner-border-sm"></span> Menyiapkan...</span>
            </button>
            <button wire:click="exportPdf" class="btn btn-outline-danger d-flex align-items-center gap-1" style="min-height: 40px; border-radius: 8px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf"><i class="bi bi-file-earmark-pdf"></i> Export PDF (Kop Surat)</span>
                <span wire:loading wire:target="exportPdf"><span class="spinner-border spinner-border-sm"></span> Menyiapkan...</span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-2 mb-3">
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'menunggu' ? 'border-bottom border-warning border-3 bg-warning bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('filterStatus', 'menunggu')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-warning">{{ $countMenunggu }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Menunggu</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'disetujui' ? 'border-bottom border-success border-3 bg-success bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('filterStatus', 'disetujui')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-success">{{ $countDisetujui }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Disetujui</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'ditolak' ? 'border-bottom border-danger border-3 bg-danger bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('filterStatus', 'ditolak')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-danger">{{ $countDitolak }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Ditolak</div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama guru atau alasan izin...">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu">Menunggu Verifikasi</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Izin Table / Cards --}}
    @if($daftarIzin->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Tidak Ada Pengajuan Izin</h6>
                <p class="text-muted small mb-0">Tidak ditemukan data pengajuan izin dengan filter saat ini.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nama Guru</th>
                            <th>Jenis & Periode</th>
                            <th>Alasan</th>
                            <th>Pengganti</th>
                            <th>Status</th>
                            <th class="text-end pe-3" style="min-width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarIzin as $item)
                            @php $badge = $item->status_badge; @endphp
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $item->guru?->name }}</div>
                                    <small class="text-muted">{{ $item->guru?->email }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $item->jenis_izin_label }}</span>
                                        <span class="badge {{ $item->is_seharian ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning border border-warning' }} px-2 py-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-clock-fill me-1"></i>{{ $item->waktu_display }}
                                        </span>
                                    </div>
                                    <div class="small fw-semibold text-dark">
                                        {{ $item->tanggal_mulai->translatedFormat('l, d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 220px;" title="{{ $item->alasan }}">
                                        {{ $item->alasan }}
                                    </div>
                                    @if($item->file_lampiran)
                                        <a href="{{ asset('storage/' . $item->file_lampiran) }}" target="_blank" class="badge bg-info text-dark text-decoration-none mt-1">
                                            <i class="bi bi-paperclip"></i> Ada Lampiran
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($item->guruPengganti)
                                        <span class="badge bg-light text-primary border">
                                            <i class="bi bi-person-check me-1"></i>{{ $item->guruPengganti->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $badge['class'] }} px-2 py-1">
                                        <i class="bi {{ $badge['icon'] }} me-1"></i>{{ $badge['text'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="openDetail('{{ $item->id }}')" class="btn btn-outline-secondary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($item->status === 'menunggu')
                                            <button wire:click="openApprove('{{ $item->id }}')" class="btn btn-success" title="Setujui Izin">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                            <button wire:click="openReject('{{ $item->id }}')" class="btn btn-danger" title="Tolak">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($daftarIzin->hasPages())
                <div class="card-footer bg-white py-2 px-3">
                    {{ $daftarIzin->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- MODAL PERSETUJUAN (APPROVE) --}}
    @if($showApprovalModal && $selectedIzin)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white bg-success" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Persetujuan Izin Guru</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Menyetujui izin akan otomatis memperbarui agenda KBM guru yang bersangkutan pada tanggal tersebut.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Guru Pemohon</label>
                        <div class="fw-bold fs-6 text-dark">{{ $selectedIzin->guru?->name }}</div>
                        <div class="small text-muted">{{ $selectedIzin->jenis_izin_label }} &bull; {{ $selectedIzin->tanggal_mulai->translatedFormat('d M Y') }} s/d {{ $selectedIzin->tanggal_selesai->translatedFormat('d M Y') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tugaskan Guru Pengganti (Opsional)</label>
                        <select wire:model="substituteGuruId" class="form-select" style="min-height: 48px;">
                            <option value="">— Tanpa Guru Pengganti / Tugas Mandiri —</option>
                            @foreach($daftarGuru as $g)
                                @if($g->id !== $selectedIzin->guru_id)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan / Arahan Waka (Opsional)</label>
                        <textarea wire:model="catatanWaka" class="form-control" rows="2" placeholder="Contoh: Disetujui, silakan koordinasi materi dengan guru pengganti."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-3" wire:click="closeModals" style="border-radius: 10px;">Batal</button>
                    <button type="button" wire:click="approveIzin" class="btn btn-success px-4" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2-circle me-1"></i>Setujui & Verifikasi</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PENOLAKAN (REJECT) --}}
    @if($showRejectModal && $selectedIzin)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white bg-danger" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pengajuan Izin</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Guru Pemohon</label>
                        <div class="fw-bold text-dark">{{ $selectedIzin->guru?->name }}</div>
                        <div class="small text-muted">{{ $selectedIzin->alasan }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea wire:model="catatanWaka" class="form-control @error('catatanWaka') is-invalid @enderror" rows="3" placeholder="Tuliskan alasan mengapa izin belum dapat disetujui..."></textarea>
                        @error('catatanWaka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-3" wire:click="closeModals" style="border-radius: 10px;">Batal</button>
                    <button type="button" wire:click="rejectIzin" class="btn btn-danger px-4" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-x-circle me-1"></i>Konfirmasi Tolak</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL --}}
    @if($showDetailModal && $selectedIzin)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-text text-primary me-2"></i>Rincian Pengajuan Izin</h5>
                    <button type="button" class="btn-close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge {{ $selectedIzin->status_badge['class'] }} px-3 py-2">
                            <i class="bi {{ $selectedIzin->status_badge['icon'] }} me-1"></i>{{ $selectedIzin->status_badge['text'] }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">{{ $selectedIzin->jenis_izin_label }}</span>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Guru</small>
                            <span class="fw-bold text-dark">{{ $selectedIzin->guru?->name }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Periode</small>
                            <span class="fw-semibold text-dark">
                                {{ $selectedIzin->tanggal_mulai->translatedFormat('d F Y') }} — {{ $selectedIzin->tanggal_selesai->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Alasan</small>
                            <span class="text-dark">{{ $selectedIzin->alasan }}</span>
                        </div>
                        @if($selectedIzin->guruPengganti)
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Guru Pengganti</small>
                            <span class="fw-bold text-primary">{{ $selectedIzin->guruPengganti->name }}</span>
                        </div>
                        @endif
                        @if($selectedIzin->file_lampiran)
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Lampiran</small>
                            <a href="{{ asset('storage/' . $selectedIzin->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="bi bi-paperclip me-1"></i> Buka File Lampiran
                            </a>
                        </div>
                        @endif
                        @if($selectedIzin->diverifikasiOleh)
                        <div class="list-group-item px-0 py-2 bg-light rounded-3 p-2 mt-2">
                            <small class="text-muted d-block">Diverifikasi oleh</small>
                            <span class="fw-bold text-dark">{{ $selectedIzin->diverifikasiOleh->name }}</span>
                            <small class="text-muted d-block">Pada {{ $selectedIzin->waktu_verifikasi?->translatedFormat('d F Y, H:i') }} WIB</small>
                            @if($selectedIzin->catatan_waka)
                                <div class="mt-1 small"><strong>Catatan:</strong> {{ $selectedIzin->catatan_waka }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary w-100" wire:click="closeModals" style="border-radius: 10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
