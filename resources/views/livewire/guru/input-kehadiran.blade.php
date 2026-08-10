<div>
    <div class="page-header">
        <h1><i class="bi bi-person-check me-2"></i>Input Kehadiran</h1>
        <p class="subtitle mb-0">
            {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
        </p>
    </div>

    {{-- Summary --}}
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="card text-center py-2">
                <div class="fw-bold text-success">{{ $summary['hadir'] ?? 0 }}</div>
                <div class="small text-muted">Hadir</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2">
                <div class="fw-bold text-info">{{ $summary['sakit'] ?? 0 }}</div>
                <div class="small text-muted">Sakit</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2">
                <div class="fw-bold text-warning">{{ $summary['izin'] ?? 0 }}</div>
                <div class="small text-muted">Izin</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2">
                <div class="fw-bold text-danger">{{ $summary['alpa'] ?? 0 }}</div>
                <div class="small text-muted">Alpa</div>
            </div>
        </div>
    </div>

    {{-- Student List --}}
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-people me-2"></i>Daftar Siswa ({{ $siswaList->count() }})
        </div>
        <div class="card-body p-0">
            @forelse($siswaList as $index => $siswa)
            <div class="d-flex align-items-center gap-2 p-3 border-bottom" style="min-height: 56px;">
                <div class="flex-fill">
                    <div class="fw-semibold small">{{ $index + 1 }}. {{ $siswa->nama }}</div>
                    @if($siswa->nis)
                    <div class="text-muted" style="font-size: 0.7rem;">NIS: {{ $siswa->nis }}</div>
                    @endif
                </div>
                <div class="d-flex gap-1 flex-shrink-0">
                    @foreach(['hadir' => ['label' => 'Hadir', 'badge' => 'btn-success'], 'sakit' => ['label' => 'Sakit', 'badge' => 'btn-info'], 'izin' => ['label' => 'Izin', 'badge' => 'btn-warning'], 'alpa' => ['label' => 'Alpa', 'badge' => 'btn-danger']] as $status => $cfg)
                        @php $isSelected = ($kehadiran[$siswa->id] ?? 'hadir') === $status; @endphp
                        <button type="button" 
                                wire:click="setStatus({{ $siswa->id }}, '{{ $status }}')"
                                class="btn btn-sm {{ $isSelected ? $cfg['badge'] . ' text-white fw-bold shadow-sm' : 'btn-outline-secondary opacity-50' }} px-2 py-1"
                                style="min-width: 44px; min-height: 44px; border-radius: 8px;">
                            {{ strtoupper($status[0]) }}
                        </button>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="p-3 text-center text-muted small">
                <i class="bi bi-info-circle"></i> Belum ada data siswa untuk kelas ini.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Submit --}}
    <button wire:click="simpan" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="bi bi-check-circle me-2"></i>Simpan Kehadiran</span>
        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
    </button>

    <div class="mt-3">
        <a href="{{ route('guru.materi', $agenda->id) }}" class="btn btn-outline-secondary" wire:navigate>
            <i class="bi bi-arrow-left"></i> Kembali ke Materi
        </a>
    </div>
</div>
