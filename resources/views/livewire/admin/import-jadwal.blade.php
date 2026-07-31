<div>
    <div class="page-header">
        <h1><i class="bi bi-cloud-upload me-2"></i>Import Jadwal</h1>
        <p class="subtitle mb-0">Upload file XML dari aSc Timetables</p>
    </div>

    {{-- Current Stats --}}
    <div class="row g-2 mb-3">
        <div class="col-3"><div class="card text-center py-2"><div class="fw-bold text-primary">{{ $totalGuru }}</div><div class="small text-muted">Guru</div></div></div>
        <div class="col-3"><div class="card text-center py-2"><div class="fw-bold text-success">{{ $totalKelas }}</div><div class="small text-muted">Kelas</div></div></div>
        <div class="col-3"><div class="card text-center py-2"><div class="fw-bold text-info">{{ $totalMapel }}</div><div class="small text-muted">Mapel</div></div></div>
        <div class="col-3"><div class="card text-center py-2"><div class="fw-bold text-warning">{{ $totalJadwal }}</div><div class="small text-muted">Jadwal</div></div></div>
    </div>

    {{-- Upload Card --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-earmark-code me-2"></i>Upload XML</div>
        <div class="card-body">
            <div class="mb-3">
                <input type="file" wire:model="xmlFile" accept=".xml" class="form-control @error('xmlFile') is-invalid @enderror">
                @error('xmlFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">File XML dari aSc Timetables (maks 10MB)</div>
            </div>

            @if($xmlFile)
            <button wire:click="import" class="btn btn-primary w-100" wire:loading.attr="disabled" {{ $importing ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="import"><i class="bi bi-cloud-upload me-2"></i>Import Jadwal</span>
                <span wire:loading wire:target="import"><span class="spinner-border spinner-border-sm me-2"></span>Mengimport...</span>
            </button>
            @endif

            @if($imported)
            <div class="alert alert-success mt-3 small">
                <i class="bi bi-check-circle me-2"></i>Import berhasil!
                <pre class="mb-0 mt-2 small bg-light p-2 rounded">{{ $result }}</pre>
            </div>
            @endif
        </div>
    </div>

    {{-- Clear Data Per Component --}}
    <div class="card mb-3">
        <div class="card-header text-danger"><i class="bi bi-trash me-2"></i>Hapus Data (Per Komponen)</div>
        <div class="card-body">
            <p class="small text-muted">Hapus data yang telah diimport per komponen.</p>
            <div class="d-flex flex-wrap gap-2">
                <button wire:click="clearAll" wire:confirm="Hapus SEMUA jadwal pelajaran?" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-calendar-x me-1"></i>Hapus Jadwal
                </button>
                <button wire:click="clearGuru" wire:confirm="Hapus SEMUA guru (role=guru)?" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-people me-1"></i>Hapus Guru
                </button>
                <button wire:click="clearKelas" wire:confirm="Hapus SEMUA kelas/rombel?" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-building me-1"></i>Hapus Kelas
                </button>
                <button wire:click="clearMapel" wire:confirm="Hapus SEMUA mata pelajaran?" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-book me-1"></i>Hapus Mapel
                </button>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary" wire:navigate>
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
