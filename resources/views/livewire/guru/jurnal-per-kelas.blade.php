<div>
    <div class="page-header">
        <h1><i class="bi bi-journal-text me-2"></i>Jurnal Mengajar {{ $rombel->nama_kelas }}</h1>
        <p class="subtitle mb-0">Riwayat mengajar & foto bukti kehadiran ber-watermark</p>
    </div>

    @forelse($agendas as $agenda)
    <div class="card mb-3 animate-fade-in-up shadow-sm" style="animation-delay: {{ $loop->index * 0.03 }}s">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-bold fs-6 text-dark">{{ $agenda->tanggal?->translatedFormat('l, d F Y') }}</div>
                    <div class="text-primary small fw-semibold">{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</div>
                </div>
                <span class="status-badge status-{{ $agenda->status === 'selesai' ? 'hijau' : ($agenda->status === 'berjalan' ? 'hijau' : 'abu') }}">
                    {{ ucfirst($agenda->status) }}
                </span>
            </div>

            @if($agenda->materi_diajarkan)
            <div class="mt-2 small bg-light rounded p-2 border">
                <strong>Materi:</strong> {{ $agenda->materi_diajarkan }}
            </div>
            @endif

            @if($agenda->refleksi)
            <div class="mt-2 small bg-info bg-opacity-10 text-dark rounded p-2 border border-info border-opacity-25">
                <i class="bi bi-journal-text me-1 text-info"></i><strong>Refleksi:</strong> {{ $agenda->refleksi }}
            </div>
            @endif

            @if($agenda->tujuanPembelajaran->count())
            <div class="mt-2 d-flex flex-wrap gap-1">
                @foreach($agenda->tujuanPembelajaran as $tp)
                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">{{ $tp->kode_tp }}</span>
                @endforeach
            </div>
            @endif

            {{-- Foto Bukti Watermark Section --}}
            @if($agenda->foto_bukti_path)
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ Storage::url($agenda->foto_bukti_path) }}" alt="Foto Bukti Agenda" 
                             class="rounded-3 border shadow-sm" style="width: 70px; height: 50px; object-fit: cover; cursor: pointer;"
                             onclick="window.open('{{ Storage::url($agenda->foto_bukti_path) }}', '_blank')">
                        <div>
                            <div class="fw-semibold small text-dark"><i class="bi bi-camera-fill me-1 text-success"></i>Foto Bukti Watermark</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Terdata otomatis saat kelas dimulai</div>
                        </div>
                    </div>

                    {{-- Tombol Download ke Galeri HP --}}
                    <a href="{{ Storage::url($agenda->foto_bukti_path) }}" download="Foto_Agenda_{{ $rombel->nama_kelas }}_{{ $agenda->tanggal?->format('Y-m-d') }}.jpg" 
                       class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1 py-1 px-3" style="min-height: 38px; border-radius: 8px;">
                        <i class="bi bi-download"></i>
                        <span class="fw-bold small">Simpan ke Galeri</span>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
            <p class="mb-0">Belum ada riwayat mengajar di kelas ini.</p>
        </div>
    </div>
    @empty
    @endforelse

    <div class="mt-3">
        <a href="{{ route('guru.jurnal') }}" class="btn btn-outline-secondary" wire:navigate>
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Jurnal Mengajar
        </a>
    </div>
</div>
