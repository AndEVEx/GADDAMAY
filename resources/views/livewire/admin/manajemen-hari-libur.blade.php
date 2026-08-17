<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-check-fill text-danger me-2"></i>Manajemen Hari Libur Sekolah</h4>
            <p class="text-muted small mb-0">Kelola kalender libur nasional & sekolah. Hari libur akan otomatis menonaktifkan jadwal KBM.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button wire:click="downloadTemplate" class="btn btn-outline-info d-flex align-items-center gap-1" style="min-height: 42px; border-radius: 10px;">
                <i class="bi bi-download"></i>
                <span class="d-none d-sm-inline">Download Template (.xlsx)</span>
            </button>
            <button wire:click="$set('showImportModal', true)" class="btn btn-success d-flex align-items-center gap-1" style="min-height: 42px; border-radius: 10px;">
                <i class="bi bi-cloud-upload-fill"></i>
                <span>Import Libur</span>
            </button>
            <button wire:click="create" class="btn btn-primary d-flex align-items-center gap-1" style="min-height: 42px; border-radius: 10px;">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Tambah Libur Manual</span>
            </button>
        </div>
    </div>

    {{-- Today Holiday Banner --}}
    @if($todayHoliday)
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-3 animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #ef4444, #b91c1c); color: #fff;">
            <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-brightness-alt-high-fill fs-2 text-white"></i>
            </div>
            <div>
                <div class="badge bg-white text-danger fw-bold mb-1">HARI INI LIBUR AKTIF</div>
                <h5 class="fw-extrabold text-white mb-0">{{ $todayHoliday->nama_hari_libur }}</h5>
                <p class="small text-white-50 mb-0">
                    Periode: {{ $todayHoliday->tanggal_mulai->translatedFormat('d F Y') }} — {{ $todayHoliday->tanggal_selesai->translatedFormat('d F Y') }} &bull; ({{ $todayHoliday->tipe_label }})
                    @if($todayHoliday->keterangan) &bull; {{ $todayHoliday->keterangan }} @endif
                </p>
            </div>
        </div>
    @endif

    {{-- Stats Cards & Tabs --}}
    <div class="row g-2 mb-3">
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $tab === 'upcoming' ? 'border-bottom border-primary border-3 bg-primary bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('tab', 'upcoming')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-primary">{{ $countUpcoming }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Libur Mendatang</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $tab === 'all' ? 'border-bottom border-success border-3 bg-success bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('tab', 'all')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-success">{{ $countAll }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Semua Hari Libur</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-2 {{ $tab === 'past' ? 'border-bottom border-secondary border-3 bg-secondary bg-opacity-10' : 'bg-light' }}"
                 wire:click="$set('tab', 'past')" style="cursor: pointer; border-radius: 12px;">
                <div class="fs-4 fw-extrabold text-secondary">{{ $countPast }}</div>
                <div class="small fw-semibold text-muted" style="font-size: 0.75rem;">Libur Berlalu</div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama hari libur atau keterangan...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select wire:model.live="filterTipe" class="form-select">
                        <option value="all">Semua Tipe Libur</option>
                        <option value="nasional">Libur Nasional</option>
                        <option value="sekolah">Libur Sekolah/Semester</option>
                        <option value="cuti_bersama">Cuti Bersama</option>
                        <option value="khusus">Kegiatan Khusus</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button wire:click="exportExcel" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span>Export (.xlsx)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Holiday List Table / Cards --}}
    @if($hariLiburs->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Tidak Ada Data Hari Libur</h6>
                <p class="text-muted small mb-3">Belum ada hari libur yang tercatat pada kategori ini.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button wire:click="create" class="btn btn-primary" style="border-radius: 10px;">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Manual
                    </button>
                    <button wire:click="$set('showImportModal', true)" class="btn btn-success" style="border-radius: 10px;">
                        <i class="bi bi-cloud-upload me-1"></i> Import Excel
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nama Hari Libur</th>
                            <th>Tanggal & Periode</th>
                            <th>Durasi</th>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th class="text-end pe-3" style="min-width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hariLiburs as $item)
                            @php
                                $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
                                $mulai = $item->tanggal_mulai->copy()->startOfDay();
                                $selesai = $item->tanggal_selesai->copy()->startOfDay();

                                $isCurrent = $today->between($mulai, $selesai);
                                $isUpcoming = $mulai->gt($today);
                                $daysDiff = $today->diffInDays($mulai, false);
                            @endphp
                            <tr class="{{ $isCurrent ? 'table-danger bg-opacity-10' : '' }}">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($isCurrent)
                                            <span class="badge bg-danger animate-pulse">BERLANGSUNG</span>
                                        @elseif($isUpcoming && $daysDiff <= 7)
                                            <span class="badge bg-warning text-dark">{{ $daysDiff }} hari lagi</span>
                                        @endif
                                        <div class="fw-bold text-dark">{{ $item->nama_hari_libur }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        @if($item->tanggal_mulai->format('Y-m-d') === $item->tanggal_selesai->format('Y-m-d'))
                                            {{ $item->tanggal_mulai->translatedFormat('d F Y') }}
                                        @else
                                            {{ $item->tanggal_mulai->translatedFormat('d M') }} — {{ $item->tanggal_selesai->translatedFormat('d M Y') }}
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $item->tanggal_mulai->translatedFormat('l') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->durasi_hari }} Hari</span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->tipe_badge_class }} px-2 py-1">
                                        {{ $item->tipe_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-muted text-truncate" style="max-width: 220px;" title="{{ $item->keterangan }}">
                                        {{ $item->keterangan ?: '—' }}
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="edit('{{ $item->id }}')" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDeleteLibur('{{ $item->id }}')" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($hariLiburs->hasPages())
                <div class="card-footer bg-white py-2 px-3">
                    {{ $hariLiburs->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- MODAL TAMBAH / EDIT HARI LIBUR MANUAL --}}
    @if($showForm)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1a56db, #0d47a1); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi {{ $editing ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2"></i>
                        {{ $editing ? 'Edit Hari Libur' : 'Tambah Hari Libur Manual' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showForm', false)"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body p-3 p-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Hari Libur / Peristiwa <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nama_hari_libur" class="form-control @error('nama_hari_libur') is-invalid @enderror" placeholder="Contoh: Hari Raya Idul Fitri 1447 H / Libur Akhir Semester" style="min-height: 44px;">
                            @error('nama_hari_libur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" wire:model.live="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" style="min-height: 44px;">
                                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" wire:model.live="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" style="min-height: 44px;">
                                @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tipe Libur <span class="text-danger">*</span></label>
                            <select wire:model="tipe_libur" class="form-select" style="min-height: 44px;">
                                <option value="nasional">Libur Nasional (Resmi Pemerintah)</option>
                                <option value="sekolah">Libur Sekolah / Semester</option>
                                <option value="cuti_bersama">Cuti Bersama</option>
                                <option value="khusus">Kegiatan Khusus / Non-KBM</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Keterangan Tambahan (Opsional)</label>
                            <textarea wire:model="keterangan" class="form-control" rows="2" placeholder="Catatan instruksi sekolah atau referensi SKB 3 Menteri..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-secondary px-3" wire:click="$set('showForm', false)" style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-save me-1"></i>Simpan</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL IMPORT EXCEL / CSV --}}
    @if($showImportModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white bg-success" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cloud-upload-fill me-2"></i>Import Hari Libur dari Excel</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showImportModal', false)"></button>
                </div>
                <form wire:submit.prevent="importData">
                    <div class="modal-body p-3 p-md-4">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Gunakan format template yang telah disediakan untuk kemudahan import massal kalender libur tahunan.
                        </div>

                        <div class="mb-3">
                            <button type="button" wire:click="downloadTemplate" class="btn btn-outline-primary btn-sm w-100 py-2">
                                <i class="bi bi-download me-1"></i> Download Format Template Excel (.xlsx)
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih File Excel / CSV <span class="text-danger">*</span></label>
                            <input type="file" wire:model="importFile" class="form-control @error('importFile') is-invalid @enderror" accept=".xlsx,.xls,.csv" style="min-height: 48px;">
                            @error('importFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text small">Mendukung format .xlsx, .xls, .csv (Maksimal 10MB).</div>
                        </div>

                        @if($importFile)
                            <div class="alert alert-success py-2 px-3 small">
                                <i class="bi bi-file-earmark-check me-1"></i> File terpilih: <strong>{{ $importFile->getClientOriginalName() }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-secondary px-3" wire:click="$set('showImportModal', false)" style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-success px-4" style="border-radius: 10px;" {{ !$importFile ? 'disabled' : '' }} wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-cloud-arrow-up me-1"></i>Mulai Import</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Mengimport...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL CONFIRM DELETE --}}
    @if($confirmDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white bg-danger" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Hari Libur</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('confirmDelete', false)"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus data hari libur <strong>{{ $deleteName }}</strong>?</p>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-3" wire:click="$set('confirmDelete', false)" style="border-radius: 10px;">Batal</button>
                    <button type="button" wire:click="deleteLibur" class="btn btn-danger px-4" style="border-radius: 10px;">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
