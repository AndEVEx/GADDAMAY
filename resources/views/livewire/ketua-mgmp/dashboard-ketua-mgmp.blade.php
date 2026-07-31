<div>
    {{-- Hero Greeting Header --}}
    <div class="card bg-primary text-white mb-4 animate-fade-in-up border-0 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge bg-white text-primary px-3 py-2 mb-2 font-weight-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-calendar-event me-1"></i> {{ $todayDate }}
                    </span>
                    <h2 class="fw-bold mb-1">{{ $greeting }}, {{ auth()->user()->name }}!</h2>
                    <p class="mb-0 text-white-50">Selamat datang di Dashboard Ketua MGMP. Kelola Tujuan Pembelajaran (TP) untuk mata pelajaran yang diampu.</p>
                </div>
                <div>
                    <a href="{{ route('mgmp.tp') }}" class="btn btn-light text-primary font-weight-bold shadow-sm" style="min-height: 48px; padding-left: 1.5rem; padding-right: 1.5rem;">
                        <i class="bi bi-journal-plus me-2"></i> Kelola TP Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Overview Stats --}}
    <div class="row g-3 mb-4 animate-fade-in-up">
        <div class="col-12 col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Mapel Terdaftar</div>
                        <div class="fs-4 fw-bold text-primary">{{ $mapels->count() }} Mapel</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check2-square fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">TP Disusun Anda</div>
                        <div class="fs-4 fw-bold text-success">{{ $totalTpUser }} TP</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info text-dark p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-layers fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total TP Seluruh Mapel</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalTpSystem }} TP</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mapel List & TP Summary --}}
    <div class="card animate-fade-in-up">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Daftar Mata Pelajaran & Jumlah TP</h5>
            <a href="{{ route('mgmp.tp') }}" class="btn btn-outline-primary btn-sm" style="min-height: 48px;">
                <i class="bi bi-gear me-1"></i> Manajemen TP
            </a>
        </div>
        <div class="card-body p-0">
            @forelse($mapels as $mapel)
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <div>
                    <div class="fw-bold fs-6 text-dark">{{ $mapel->nama_mapel }}</div>
                    <div class="text-muted small">
                        Kode: <span class="badge bg-secondary bg-opacity-10 text-dark">{{ $mapel->kode_mapel ?? '-' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                        {{ $mapel->tujuan_pembelajaran_count }} TP Terdaftar
                    </span>
                    <a href="{{ route('mgmp.tp') }}" class="btn btn-sm btn-outline-secondary" style="min-height: 48px; min-width: 48px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada mata pelajaran terdaftar.
            </div>
            @endforelse
        </div>
    </div>
</div>
