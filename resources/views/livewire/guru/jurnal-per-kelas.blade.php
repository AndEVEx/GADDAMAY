<div>
    <div class="page-header">
        <h1><i class="bi bi-journal-text me-2"></i>{{ $rombel->nama_kelas }}</h1>
        <p class="subtitle mb-0">Riwayat mengajar</p>
    </div>

    @forelse($agendas as $agenda)
    <div class="card mb-2 animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.03 }}s">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold small">{{ $agenda->tanggal?->translatedFormat('l, d F Y') }}</div>
                    <div class="text-muted small">{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</div>
                </div>
                <span class="status-badge status-{{ $agenda->status === 'selesai' ? 'hijau' : 'abu' }}">
                    {{ ucfirst($agenda->status) }}
                </span>
            </div>
            @if($agenda->materi_diajarkan)
            <div class="mt-2 small bg-light rounded p-2">{{ $agenda->materi_diajarkan }}</div>
            @endif
            @if($agenda->tujuanPembelajaran->count())
            <div class="mt-2 d-flex flex-wrap gap-1">
                @foreach($agenda->tujuanPembelajaran as $tp)
                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.65rem;">{{ $tp->kode_tp }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <p class="mb-0">Belum ada riwayat mengajar di kelas ini.</p>
        </div>
    </div>
    @endforelse

    <a href="{{ route('guru.jurnal') }}" class="btn btn-outline-secondary mt-3" wire:navigate>
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
