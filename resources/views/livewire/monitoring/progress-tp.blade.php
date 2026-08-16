<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Analytic Dashboard Progress KKTP</h4>
            <p class="text-muted small mb-0">Pemantauan & Analisis Ketercapaian Kriteria Tujuan Pembelajaran SMKN 2 Indramayu</p>
        </div>
        <button wire:click="exportExcel" class="btn btn-outline-success d-flex align-items-center gap-2" style="min-height: 42px; border-radius: 10px;" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="bi bi-file-earmark-excel-fill"></i> Export Rekap Excel</span>
            <span wire:loading><span class="spinner-border spinner-border-sm"></span> Menyiapkan...</span>
        </button>
    </div>

    {{-- Top Executive KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-primary text-white p-3 h-100" style="border-radius: 14px; background: linear-gradient(135deg, #1a56db, #0d47a1);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 small fw-semibold">Rata-Rata Ketercapaian</div>
                        <div class="fs-2 fw-extrabold my-1">{{ $globalPercentage }}%</div>
                        <div class="small text-white-50" style="font-size: 0.72rem;">{{ number_format($totalTercapai) }} tuntas / {{ number_format($totalTercapai + $totalBelum) }} data</div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-3">
                        <i class="bi bi-award-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Total Tujuan Pembelajaran</div>
                        <div class="fs-2 fw-extrabold text-dark my-1">{{ number_format($totalTp) }}</div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Dari {{ $totalMapel }} Mata Pelajaran</div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                        <i class="bi bi-list-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Total Kelas (Rombel)</div>
                        <div class="fs-2 fw-extrabold text-dark my-1">{{ $totalRombel }}</div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Tingkat X, XI, dan XII</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                        <i class="bi bi-door-open-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Total Siswa Terdata</div>
                        <div class="fs-2 fw-extrabold text-dark my-1">{{ number_format($totalSiswa) }}</div>
                        <div class="small text-success" style="font-size: 0.72rem;"><i class="bi bi-check2"></i> Aktif di Sistem</div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mode Navigation Tabs --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-1">
                <li class="nav-item">
                    <button class="nav-link py-2 {{ $viewMode === 'overview' ? 'active fw-bold' : 'text-dark' }}" wire:click="setViewMode('overview')" style="border-radius: 8px;">
                        <i class="bi bi-pie-chart-fill me-1"></i> Ringkasan Eksekutif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 {{ $viewMode === 'mapel' ? 'active fw-bold' : 'text-dark' }}" wire:click="setViewMode('mapel')" style="border-radius: 8px;">
                        <i class="bi bi-book-half me-1"></i> Analisis Mapel & TP
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 {{ $viewMode === 'kelas' ? 'active fw-bold' : 'text-dark' }}" wire:click="setViewMode('kelas')" style="border-radius: 8px;">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Analisis Per Kelas
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 {{ $viewMode === 'siswa' ? 'active fw-bold' : 'text-dark' }}" wire:click="setViewMode('siswa')" style="border-radius: 8px;">
                        <i class="bi bi-person-lines-fill me-1"></i> Analisis Per Siswa
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- TAB 1: EXECUTIVE OVERVIEW --}}
    @if($viewMode === 'overview')
    <div class="row g-3">
        {{-- Ketercapaian Per Tingkat --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-layers-fill text-primary me-2"></i>Ketercapaian Berdasarkan Jenjang / Tingkat</h6>
                
                @foreach($tingkatStats as $tingkat => $data)
                <div class="mb-3 p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">{{ $data['label'] }}</span>
                        <span class="badge bg-{{ $data['percentage'] >= 75 ? 'success' : ($data['percentage'] >= 50 ? 'warning' : 'danger') }} px-2 py-1 fs-6">
                            {{ $data['percentage'] }}%
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 10px; border-radius: 6px;">
                        <div class="progress-bar bg-{{ $data['percentage'] >= 75 ? 'success' : ($data['percentage'] >= 50 ? 'warning' : 'danger') }}"
                             style="width: {{ $data['percentage'] }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small" style="font-size: 0.75rem;">
                        <span>{{ $data['kelas_count'] }} Kelas &bull; {{ $data['siswa_count'] }} Siswa</span>
                        <span><strong class="text-success">{{ number_format($data['tercapai']) }}</strong> Tercapai / <strong class="text-danger">{{ number_format($data['belum']) }}</strong> Belum</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top & Bottom Mapel --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-line-fill text-success me-2"></i>Top Ketercapaian Mata Pelajaran</h6>
                
                <div class="list-group list-group-flush mb-3">
                    @forelse($topMapel as $m)
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <div class="text-truncate me-2" style="max-width: 250px;">
                            <div class="fw-semibold text-dark small">{{ $m->nama_mapel }}</div>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $m->tp_count }} TP &bull; {{ number_format($m->tercapai) }} data tercapai</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1 fs-6">
                            {{ $m->percentage }}%
                        </span>
                    </div>
                    @empty
                    <div class="text-muted small text-center py-3">Belum ada data nilai KKTP.</div>
                    @endforelse
                </div>

                @if($bottomMapel->isNotEmpty() && $bottomMapel->first()->percentage < 60)
                <h6 class="fw-bold text-danger mb-2 mt-2" style="font-size: 0.85rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Perlu Perhatian (Ketercapaian Terendah)</h6>
                <div class="list-group list-group-flush">
                    @foreach($bottomMapel->take(3) as $m)
                    <div class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="small text-dark text-truncate" style="max-width: 220px;">{{ $m->nama_mapel }}</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">{{ $m->percentage }}%</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 2: ANALISIS PER MAPEL & TP --}}
    @if($viewMode === 'mapel')
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold">Pilih Mata Pelajaran</label>
                    <select wire:model.live="selectedMapel" class="form-select" style="min-height: 44px;">
                        <option value="">— Pilih Mata Pelajaran —</option>
                        @foreach($mapelList as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold">Filter Kelas (Opsional)</label>
                    <select wire:model.live="selectedRombel" class="form-select" style="min-height: 44px;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombelList as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($selectedMapel && $detailTpList->isNotEmpty())
        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-card-checklist text-primary me-2"></i>Daftar TP & Ketercapaian Siswa ({{ $detailTpList->count() }} TP)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Kode TP</th>
                            <th>Deskripsi Tujuan Pembelajaran</th>
                            <th class="text-center" style="width: 140px;">Siswa Tuntas</th>
                            <th class="text-center" style="width: 140px;">Belum Tuntas</th>
                            <th class="text-center" style="width: 130px;">Ketercapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailTpList as $item)
                        <tr>
                            <td><span class="badge bg-primary px-2 py-1 fw-bold">{{ $item->tp->kode_tp }}</span></td>
                            <td><div class="small text-dark">{{ $item->tp->deskripsi_tp }}</div></td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>{{ number_format($item->tercapai) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i>{{ number_format($item->belum) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-{{ $item->percentage >= 75 ? 'success' : ($item->percentage >= 50 ? 'warning' : 'danger') }}">
                                    {{ $item->percentage }}%
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $item->percentage >= 75 ? 'success' : ($item->percentage >= 50 ? 'warning' : 'danger') }}"
                                         style="width: {{ $item->percentage }}%;"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($selectedMapel)
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Belum Ada Tujuan Pembelajaran (TP)</h6>
                <p class="text-muted small mb-0">Mata pelajaran ini belum memiliki data TP.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-arrow-up-circle fs-1 text-primary d-block mb-2"></i>
                <h6 class="fw-bold">Silakan Pilih Mata Pelajaran</h6>
                <p class="text-muted small mb-0">Pilih mapel di atas untuk melihat analisis ketercapaian tiap butir TP.</p>
            </div>
        </div>
    @endif
    @endif

    {{-- TAB 3: ANALISIS PER KELAS --}}
    @if($viewMode === 'kelas')
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold small text-muted">Filter Tingkat:</span>
                <button wire:click="$set('selectedTingkat', 'all')" class="btn btn-sm {{ $selectedTingkat === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</button>
                <button wire:click="$set('selectedTingkat', '10')" class="btn btn-sm {{ $selectedTingkat === '10' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas X</button>
                <button wire:click="$set('selectedTingkat', '11')" class="btn btn-sm {{ $selectedTingkat === '11' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XI</button>
                <button wire:click="$set('selectedTingkat', '12')" class="btn btn-sm {{ $selectedTingkat === '12' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XII</button>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach($rombelAnalytics as $r)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold text-dark mb-0">{{ $r->nama_kelas }}</h6>
                    <span class="badge bg-{{ $r->percentage >= 75 ? 'success' : ($r->percentage >= 50 ? 'warning' : 'danger') }} fs-6">
                        {{ $r->percentage }}%
                    </span>
                </div>
                <div class="text-muted small mb-2">{{ $r->siswa_count }} Siswa terdaftar</div>
                
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-{{ $r->percentage >= 75 ? 'success' : ($r->percentage >= 50 ? 'warning' : 'danger') }}"
                         style="width: {{ $r->percentage }}%;"></div>
                </div>

                <div class="d-flex justify-content-between text-muted small pt-2 border-top mt-auto" style="font-size: 0.75rem;">
                    <span>Tuntas: <strong class="text-success">{{ number_format($r->tercapai) }}</strong></span>
                    <span>Belum: <strong class="text-danger">{{ number_format($r->belum) }}</strong></span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- TAB 4: ANALISIS PER SISWA --}}
    @if($viewMode === 'siswa')
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold">Pilih Kelas <span class="text-danger">*</span></label>
                    <select wire:model.live="selectedRombel" class="form-select">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($rombelList as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold">Pilih Mata Pelajaran <span class="text-danger">*</span></label>
                    <select wire:model.live="selectedMapel" class="form-select">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold">Cari Siswa</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Ketik nama siswa...">
                </div>
            </div>
        </div>
    </div>

    @if($selectedRombel && $selectedMapel && $siswaData->isNotEmpty())
        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="ps-3">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">NIS</th>
                            <th class="text-center">TP Tuntas</th>
                            <th class="text-center">TP Belum</th>
                            <th class="text-center" style="width: 140px;">Ketercapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaData as $idx => $s)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td><div class="fw-semibold text-dark">{{ $s->siswa->nama }}</div></td>
                            <td class="text-center text-muted small">{{ $s->siswa->nis ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">
                                    {{ $s->tercapai }} / {{ $s->total_tp }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">
                                    {{ $s->belum }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-{{ $s->percentage >= 75 ? 'success' : ($s->percentage >= 50 ? 'warning' : 'danger') }}">
                                    {{ $s->percentage }}%
                                </span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $s->percentage >= 75 ? 'success' : ($s->percentage >= 50 ? 'warning' : 'danger') }}"
                                         style="width: {{ $s->percentage }}%;"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($selectedRombel && $selectedMapel)
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Tidak Ada Data Siswa</h6>
                <p class="text-muted small mb-0">Belum ada siswa pada kelas yang dipilih atau pencarian tidak ditemukan.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-funnel-fill fs-1 text-primary d-block mb-2"></i>
                <h6 class="fw-bold">Pilih Kelas & Mata Pelajaran</h6>
                <p class="text-muted small mb-0">Pilih kelas dan mata pelajaran di atas untuk melihat rincian ketuntasan tiap siswa.</p>
            </div>
        </div>
    @endif
    @endif
</div>
