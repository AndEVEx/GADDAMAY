<div>
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-excel-fill me-2"></i>Import Data Siswa</h1>
    </div>

    {{-- Overview Stats --}}
    <div class="row g-3 mb-4 animate-fade-in-up">
        <div class="col-6 col-md-6">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Siswa</div>
                        <div class="fs-4 fw-bold text-primary">{{ $totalSiswa }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kelas / Rombel</div>
                        <div class="fs-4 fw-bold text-success">{{ $totalRombel }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Form Card --}}
    <div class="card mb-4 animate-fade-in-up border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-cloud-upload me-2"></i>Upload File Excel (.xlsx / .xls)
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Format file Excel harus memiliki header kolom: <strong>nama</strong>, <strong>nis</strong>, dan <strong>kelas</strong> (nama kelas harus sesuai dengan data Rombel).
            </div>

            <form wire:submit="import">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Pilih File Excel</label>
                    <input type="file" wire:model="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" style="min-height: 48px;">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="min-height: 48px;" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-upload me-1"></i> Upload & Import</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></i> Mengimport...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Clear All Button & Confirmation --}}
    <div class="card mb-4 animate-fade-in-up border-danger">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Kosongkan Data Siswa</h6>
                <p class="text-muted small mb-0">Hapus seluruh data siswa di database untuk melakukan reset / import ulang.</p>
            </div>
            <button wire:click="confirmClear" class="btn btn-outline-danger" style="min-height: 48px;">
                <i class="bi bi-trash me-1"></i> Kosongkan Semua Siswa
            </button>
        </div>
    </div>

    @if($confirmClearAll)
    <div class="alert alert-danger animate-fade-in-up mb-4">
        <strong>Konfirmasi Hapus Seluruh Data Siswa?</strong>
        <p class="small mb-2">Tindakan ini akan menghapus <strong>{{ $totalSiswa }} siswa</strong> secara permanen.</p>
        <div class="d-flex gap-2">
            <button wire:click="clearAllSiswa" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Ya, Hapus Semua</button>
            <button wire:click="$set('confirmClearAll', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Rombel Student Count List --}}
    <div class="card animate-fade-in-up">
        <div class="card-header bg-light fw-bold">
            <i class="bi bi-list-ul me-2"></i>Jumlah Siswa Per Kelas
        </div>
        <div class="card-body p-0">
            @forelse($rombels as $rombel)
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <div>
                    <div class="fw-bold">{{ $rombel->nama_kelas }}</div>
                    <div class="text-muted small">Tingkat {{ $rombel->tingkat_label }}</div>
                </div>
                <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                    {{ $rombel->siswa_count }} Siswa
                </span>
            </div>
            @empty
            <div class="p-3 text-center text-muted">Belum ada kelas terdaftar.</div>
            @endforelse
        </div>
    </div>
</div>
