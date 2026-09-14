<div>
    <div class="page-header">
        <h1><i class="bi bi-calendar3 me-2"></i>Manajemen Jadwal Pelajaran</h1>
    </div>

    {{-- Block Schedule Swap Banner --}}
    <div class="card border-0 shadow-sm mb-3 animate-fade-in-up" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 14px; color: white;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="bi bi-arrow-left-right me-1"></i>Sistem Blok Rolling</span>
                        <span class="badge bg-white bg-opacity-25 text-white">TP • APHPi • NKPI • RPL</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">Tukar Jadwal Blok Mingguan (Teori ↔ Produktif)</h5>
                    <p class="mb-0 text-white-50 small">
                        Tukar seluruh sesi jadwal pelajaran antara Rombel 1 dan Rombel 2 untuk jurusan <strong>TP</strong>, <strong>APHP/APHPi</strong>, <strong>NKPI</strong>, dan <strong>RPL</strong> di semua tingkat (X, XI, XII). Dilengkapi dengan preview simulasi, riwayat, dan tombol undo.
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" wire:click="openSwapModal" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2" style="border-radius: 10px; min-height: 44px;">
                        <i class="bi bi-arrow-repeat fs-5"></i>
                        <span>Menu Tukar & Riwayat / Undo</span>
                    </button>
                    <button type="button" wire:click="previewSwapVocational" class="btn btn-light fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2" style="border-radius: 10px; min-height: 44px;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="previewSwapVocational"><i class="bi bi-eye me-1"></i>Preview Semua Blok</span>
                        <span wire:loading wire:target="previewSwapVocational"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari mapel/kelas/keterangan..." style="min-height: 48px;">
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterHari" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Hari</option>
                        <option value="1">Senin</option>
                        <option value="2">Selasa</option>
                        <option value="3">Rabu</option>
                        <option value="4">Kamis</option>
                        <option value="5">Jumat</option>
                    </select>
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterRombel" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterMapel" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Mapel</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
        </button>
        <button wire:click="openSwapModal" class="btn btn-warning text-dark fw-bold" style="min-height: 48px;">
            <i class="bi bi-arrow-left-right me-1"></i> Tukar Jadwal Blok / Antar Kelas
        </button>
        <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel (.xlsx)
        </button>
        <button wire:click="exportPdf" class="btn btn-outline-danger" style="min-height: 48px;">
            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF (Kop Surat)
        </button>
    </div>

    {{-- Swap Schedule Modal / Card --}}
    @if($showSwapModal)
    <div class="card mb-4 border-warning shadow animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center" style="border-radius: 14px 14px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold mb-0"><i class="bi bi-arrow-left-right me-2"></i>Tukar Jadwal Pelajaran (Sistem Blok Rolling)</h5>
            </div>
            <button type="button" wire:click="closeSwapModal" class="btn-close"></button>
        </div>
        <div class="card-body p-3 p-md-4">
            {{-- Navigation Tabs --}}
            <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3">
                <li class="nav-item">
                    <button type="button" wire:click="switchSwapTab('swap')" class="nav-link py-2 px-3 fw-bold rounded-3 {{ $swapTab === 'swap' ? 'active bg-warning text-dark' : 'bg-light text-dark' }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Form & Preview Tukar
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="switchSwapTab('history')" class="nav-link py-2 px-3 fw-bold rounded-3 {{ $swapTab === 'history' ? 'active bg-dark text-white' : 'bg-light text-dark' }}">
                        <i class="bi bi-clock-history me-1"></i> Riwayat & Tombol Undo
                        @php $activeCount = $swapLogs->where('status', 'active')->count(); @endphp
                        @if($activeCount > 0)
                            <span class="badge bg-danger ms-1">{{ $activeCount }} Aktif</span>
                        @endif
                    </button>
                </li>
            </ul>

            @if($swapTab === 'swap')
                {{-- Mode 1: Form & Preview --}}
                @if(!$showPreviewModal)
                    {{-- Quick Swap for Vocational Blocks --}}
                    <div class="p-3 mb-4 rounded-3 border bg-light">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Tukar Otomatis Jadwal Blok Jurusan (TP, APHPi, NKPI, RPL)
                                </h6>
                                <p class="text-muted small mb-0">
                                    Menukar seluruh jadwal pelajaran antara <strong>Rombel 1</strong> dan <strong>Rombel 2</strong> untuk jurusan <strong>TP (Pemesinan)</strong>, <strong>APHPi</strong>, <strong>NKPI</strong>, dan <strong>RPL</strong> pada semua tingkat (X, XI, XII).
                                </p>
                            </div>
                            <button type="button" wire:click="previewSwapVocational" class="btn btn-warning text-dark fw-bold px-3 py-2 text-nowrap" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="previewSwapVocational"><i class="bi bi-eye me-1"></i>Preview Tukar Semua Blok</span>
                                <span wire:loading wire:target="previewSwapVocational"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</span>
                            </button>
                        </div>
                    </div>

                    <hr>

                    {{-- Custom Swap Form --}}
                    <h6 class="fw-bold mb-3"><i class="bi bi-sliders me-2"></i>Tukar Jadwal Antar 2 Kelas (Custom)</h6>
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted text-uppercase">Pilih Kelas / Rombel A</label>
                            <select wire:model="swapRombelA" class="form-select @error('swapRombelA') is-invalid @enderror" style="min-height: 48px;">
                                <option value="">-- Pilih Rombel A --</option>
                                @foreach($rombels as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_kelas }} (Tingkat {{ $r->tingkat_label }})</option>
                                @endforeach
                            </select>
                            @error('swapRombelA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 text-center d-none d-md-block pb-2">
                            <i class="bi bi-arrow-left-right fs-3 text-warning"></i>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted text-uppercase">Pilih Kelas / Rombel B</label>
                            <select wire:model="swapRombelB" class="form-select @error('swapRombelB') is-invalid @enderror" style="min-height: 48px;">
                                <option value="">-- Pilih Rombel B --</option>
                                @foreach($rombels as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_kelas }} (Tingkat {{ $r->tingkat_label }})</option>
                                @endforeach
                            </select>
                            @error('swapRombelB') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeSwapModal" class="btn btn-secondary px-3" style="min-height: 44px;">Tutup</button>
                        <button type="button" wire:click="previewSwapCustom" class="btn btn-primary px-4 fw-bold" style="min-height: 44px;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="previewSwapCustom"><i class="bi bi-eye me-1"></i> Preview Tukar 2 Kelas</span>
                            <span wire:loading wire:target="previewSwapCustom"><span class="spinner-border spinner-border-sm me-1"></span>Memeriksa...</span>
                        </button>
                    </div>
                @else
                    {{-- Preview Simulation Display --}}
                    <div class="card border-primary mb-3 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0"><i class="bi bi-binoculars-fill me-2"></i>Pratinjau Simulasi Penukaran Jadwal</h6>
                            <span class="badge bg-white text-primary fw-bold">{{ $previewType === 'all_vocational' ? 'Semua Blok Vokasi' : 'Kustom 2 Kelas' }}</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                {{ $previewData['message'] ?? 'Silakan periksa simulasi penukaran di bawah ini sebelum mengeksekusi.' }}
                            </div>

                            @if($previewType === 'all_vocational')
                                <div class="table-responsive" style="max-height: 380px;">
                                    <table class="table table-bordered table-hover align-middle mb-0 small text-center">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 40px;">No</th>
                                                <th>Jurusan & Tingkat</th>
                                                <th class="text-start bg-primary bg-opacity-10">Rombel 1 (Sebelum)</th>
                                                <th style="width: 40px;"><i class="bi bi-arrow-left-right text-primary"></i></th>
                                                <th class="text-start bg-success bg-opacity-10">Rombel 2 (Sebelum)</th>
                                                <th>Total Sesi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($previewData['pairs'] ?? [] as $idx => $pair)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><span class="badge bg-secondary">{{ $pair['jurusan'] }} - Tingkat {{ $pair['tingkat'] }}</span></td>
                                                    <td class="text-start">
                                                        <div class="fw-bold text-primary">{{ $pair['rombel_a_nama'] }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $pair['count_a'] }} Sesi • {{ implode(', ', array_slice($pair['mapels_a'], 0, 3)) }}@if(count($pair['mapels_a']) > 3)...@endif</div>
                                                    </td>
                                                    <td><i class="bi bi-arrow-left-right text-muted"></i></td>
                                                    <td class="text-start">
                                                        <div class="fw-bold text-success">{{ $pair['rombel_b_nama'] }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $pair['count_b'] }} Sesi • {{ implode(', ', array_slice($pair['mapels_b'], 0, 3)) }}@if(count($pair['mapels_b']) > 3)...@endif</div>
                                                    </td>
                                                    <td class="fw-bold">{{ $pair['count_a'] + $pair['count_b'] }} Sesi</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-muted py-3">Tidak ada pasangan rombel yang cocok.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($previewType === 'custom_pair' && !empty($previewData['pair']))
                                @php $pair = $previewData['pair']; @endphp
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-primary bg-primary bg-opacity-10 p-3 h-100 rounded-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-primary mb-0">{{ $pair['rombel_a_nama'] }}</h6>
                                                <span class="badge bg-primary">{{ $pair['count_a'] }} Sesi Jadwal</span>
                                            </div>
                                            <div class="small text-muted mb-2">Mata Pelajaran Saat Ini:</div>
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @forelse($pair['mapels_a'] as $m)
                                                    <span class="badge bg-white text-dark border">{{ $m }}</span>
                                                @empty
                                                    <span class="text-muted small">Tidak ada mapel</span>
                                                @endforelse
                                            </div>
                                            <div class="mt-auto pt-2 border-top small text-success fw-semibold">
                                                <i class="bi bi-arrow-right-circle me-1"></i>Setelah tukar: Akan mendapat jadwal {{ $pair['rombel_b_nama'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-success bg-success bg-opacity-10 p-3 h-100 rounded-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-success mb-0">{{ $pair['rombel_b_nama'] }}</h6>
                                                <span class="badge bg-success">{{ $pair['count_b'] }} Sesi Jadwal</span>
                                            </div>
                                            <div class="small text-muted mb-2">Mata Pelajaran Saat Ini:</div>
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @forelse($pair['mapels_b'] as $m)
                                                    <span class="badge bg-white text-dark border">{{ $m }}</span>
                                                @empty
                                                    <span class="text-muted small">Tidak ada mapel</span>
                                                @endforelse
                                            </div>
                                            <div class="mt-auto pt-2 border-top small text-primary fw-semibold">
                                                <i class="bi bi-arrow-right-circle me-1"></i>Setelah tukar: Akan mendapat jadwal {{ $pair['rombel_a_nama'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <button type="button" wire:click="cancelPreview" class="btn btn-outline-secondary px-3">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali / Ubah Pilihan
                                </button>
                                <button type="button" wire:click="confirmExecuteSwap" wire:confirm="Yakin ingin mengeksekusi penukaran jadwal sesuai pratinjau ini?" class="btn btn-success px-4 fw-bold shadow-sm" wire:loading.attr="disabled" {{ empty($previewData['can_swap']) ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="confirmExecuteSwap"><i class="bi bi-check2-circle me-1"></i> Konfirmasi & Eksekusi Tukar</span>
                                    <span wire:loading wire:target="confirmExecuteSwap"><span class="spinner-border spinner-border-sm me-1"></span>Mengeksekusi...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif($swapTab === 'history')
                {{-- Mode 2: Riwayat Penukaran & Undo --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-text me-2"></i>Catatan Riwayat Penukaran Jadwal</h6>
                    <span class="text-muted small">Menampilkan 30 log penukaran terakhir</span>
                </div>

                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover table-bordered align-middle mb-0 small text-center">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Waktu & Tanggal</th>
                                <th>Tipe / Batch ID</th>
                                <th>Pasangan Kelas yang Ditukar</th>
                                <th>Sesi Ditukar</th>
                                <th>Oleh Admin</th>
                                <th>Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($swapLogs as $idx => $log)
                                <tr class="{{ $log->status === 'undone' ? 'table-light text-muted' : '' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($log->created_at)->locale('id')->isoFormat('D MMM Y') }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->swap_type === 'all_vocational' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                            {{ $log->swap_type === 'all_vocational' ? 'Blok Vokasi' : 'Kustom 2 Kelas' }}
                                        </span>
                                        <div class="text-muted font-monospace" style="font-size: 0.65rem;">{{ $log->batch_id }}</div>
                                    </td>
                                    <td class="text-start">
                                        <span class="fw-bold text-primary">{{ $log->rombel_a_nama }}</span>
                                        <i class="bi bi-arrow-left-right mx-1 text-muted"></i>
                                        <span class="fw-bold text-success">{{ $log->rombel_b_nama }}</span>
                                    </td>
                                    <td><strong>{{ $log->schedules_count_a + $log->schedules_count_b }}</strong> sesi</td>
                                    <td>{{ $log->user?->name ?? 'Admin' }}</td>
                                    <td>
                                        @if($log->status === 'active')
                                            <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1" title="Dibatalkan pada {{ $log->undone_at ? \Carbon\Carbon::parse($log->undone_at)->format('d/m/Y H:i') : '-' }}">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->status === 'active')
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" wire:click="undoSwap('{{ $log->id }}')" wire:confirm="Yakin ingin membatalkan (Undo) penukaran jadwal antara {{ $log->rombel_a_nama }} dan {{ $log->rombel_b_nama }}? Jadwal akan dikembalikan ke posisi semula." class="btn btn-sm btn-outline-danger py-1 px-2 fw-semibold" title="Batalkan Penukaran Kelas Ini">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Undo
                                                </button>
                                                @if($log->swap_type === 'all_vocational')
                                                    <button type="button" wire:click="undoBatch('{{ $log->batch_id }}')" wire:confirm="Yakin ingin membatalkan (Undo) seluruh penukaran pada Batch {{ $log->batch_id }}?" class="btn btn-sm btn-danger py-1 px-2 fw-semibold" title="Batalkan Seluruh Batch Ini">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batch
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Sudah di-undo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted py-4">
                                        <i class="bi bi-clock-history fs-3 d-block mb-1"></i>
                                        Belum ada riwayat penukaran jadwal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Jadwal Pelajaran
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Hari</label>
                        <select wire:model="hari" class="form-select @error('hari') is-invalid @enderror" style="min-height: 48px;">
                            <option value="1">Senin</option>
                            <option value="2">Selasa</option>
                            <option value="3">Rabu</option>
                            <option value="4">Kamis</option>
                            <option value="5">Jumat</option>
                        </select>
                        @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Jam Ke Mulai</label>
                        <input type="number" wire:model="jam_ke_mulai" min="1" max="15" class="form-control @error('jam_ke_mulai') is-invalid @enderror" style="min-height: 48px;">
                        @error('jam_ke_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Jam Ke Selesai</label>
                        <input type="number" wire:model="jam_ke_selesai" min="1" max="15" class="form-control @error('jam_ke_selesai') is-invalid @enderror" style="min-height: 48px;">
                        @error('jam_ke_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Kelas / Rombel</label>
                        <select wire:model="rombel_id" class="form-select @error('rombel_id') is-invalid @enderror" style="min-height: 48px;">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Mata Pelajaran</label>
                        <select wire:model="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror" style="min-height: 48px;">
                            <option value="">-- Kegiatan Khusus / Non-Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})</option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Guru Pengampu</label>
                    <select wire:model="guru_id" class="form-select @error('guru_id') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }} ({{ $guru->email }})</option>
                        @endforeach
                    </select>
                    @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Kegiatan Khusus (jika non-KBM regular)</label>
                        <input type="text" wire:model="kegiatan_khusus" class="form-control" placeholder="Contoh: Upacara, Istirahat, Literasi" style="min-height: 48px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Keterangan Catatan</label>
                        <input type="text" wire:model="keterangan" class="form-control" placeholder="Catatan tambahan..." style="min-height: 48px;">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="min-height: 48px;"><i class="bi bi-check me-1"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($confirmDelete)
    <div class="alert alert-danger animate-fade-in-up">
        <strong>Hapus {{ $deleteTitle }}?</strong>
        <p class="small mb-2">Riwayat agenda harian pada jadwal ini akan ikut dihapus (cascade).</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteJadwal" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Jadwal List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($jadwals as $j)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary fs-7">{{ $j->hari_label }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-dark">Jam {{ $j->jam_ke_mulai }} - {{ $j->jam_ke_selesai }}</span>
                        <span class="fw-bold text-dark fs-6">{{ $j->rombel->nama_kelas ?? '-' }}</span>
                    </div>
                    <div class="fw-bold text-primary">
                        @if($j->kegiatan_khusus)
                            <span class="badge bg-warning text-dark"><i class="bi bi-star me-1"></i>{{ $j->kegiatan_khusus }}</span>
                        @else
                            {{ $j->mataPelajaran->nama_mapel ?? 'Tanpa Mapel' }}
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-person-badge me-1"></i>Guru: 
                        <strong class="text-dark">
                            {{ $j->guru->pluck('name')->join(', ') ?: 'Belum ditentukan' }}
                        </strong>
                        @if($j->keterangan)
                            &bull; <span class="fst-italic">{{ $j->keterangan }}</span>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $j->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteJadwal('{{ $j->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data jadwal pelajaran.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $jadwals->links() }}</div>
</div>
