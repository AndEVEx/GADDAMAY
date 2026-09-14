<div>
    <div class="page-header mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1><i class="bi bi-file-earmark-text me-2"></i>Detail Agenda Mengajar</h1>
                <p class="subtitle mb-0">
                    {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} &bull; {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
                </p>
            </div>
            <span class="status-badge status-{{ $agenda->status === 'selesai' ? 'hijau' : 'kuning' }} fs-6">
                <i class="bi bi-check-circle-fill me-1"></i> Status: {{ ucfirst($agenda->status) }} (Ter kunci)
            </span>
        </div>
    </div>

    {{-- General Info Card --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <div class="text-muted">Guru Pengajar</div>
                    <div class="fw-bold text-dark fs-6">{{ $agenda->guru?->name }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Mata Pelajaran</div>
                    <div class="fw-bold text-dark fs-6">{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Kelas / Rombel</div>
                    <div class="fw-bold text-dark fs-6">{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Tanggal</div>
                    <div class="fw-bold text-dark fs-6">{{ $agenda->tanggal?->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Materi & TP Card --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-light fw-bold text-dark">
            <i class="bi bi-book me-2 text-primary"></i>Materi & Tujuan Pembelajaran (TP)
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="fw-bold text-muted small mb-1">Materi yang Diajarkan:</div>
                <div class="p-3 bg-light rounded-3 border text-dark fw-medium">
                    {{ $agenda->materi_diajarkan ?: 'Tidak ada materi dicatat' }}
                </div>
            </div>

            <div>
                <div class="fw-bold text-muted small mb-1">Tujuan Pembelajaran (TP) Terpilih:</div>
                <div class="d-flex flex-column gap-2">
                    @forelse($agenda->tujuanPembelajaran as $tp)
                        <div class="p-2 border rounded-3 bg-primary bg-opacity-10 text-primary small d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <strong>{{ $tp->kode_tp }}:</strong> {{ $tp->deskripsi_tp }}
                            </div>
                        </div>
                    @empty
                        <span class="text-muted small">Tidak ada TP dipilih</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Kehadiran Ringkasan --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-light fw-bold text-dark">
            <i class="bi bi-people me-2 text-primary"></i>Rekap Kehadiran Siswa
        </div>
        <div class="card-body">
            <div class="row g-2 text-center mb-3">
                <div class="col-3">
                    <div class="p-2 rounded bg-success bg-opacity-10 text-success fw-bold">
                        <div class="fs-4">{{ $siswaHadir }}</div>
                        <div class="small">Hadir</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded bg-warning bg-opacity-10 text-warning fw-bold">
                        <div class="fs-4">{{ $siswaSakit }}</div>
                        <div class="small">Sakit</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded bg-info bg-opacity-10 text-info fw-bold">
                        <div class="fs-4">{{ $siswaIzin }}</div>
                        <div class="small">Izin</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded bg-danger bg-opacity-10 text-danger fw-bold">
                        <div class="fs-4">{{ $siswaAlpa }}</div>
                        <div class="small">Alpa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Link ke KKTP --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <a href="{{ route('guru.kktp', $agenda->id) }}" class="card-body d-flex align-items-center justify-content-between text-decoration-none" wire:navigate>
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-list-check fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">KKTP Siswa</div>
                    <div class="text-muted small">Lihat / Edit Kriteria Ketercapaian TP</div>
                </div>
            </div>
            <i class="bi bi-chevron-right text-muted fs-5"></i>
        </a>
    </div>

    {{-- Refleksi & Prompter Card --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-light fw-bold text-dark">
            <i class="bi bi-journal-text me-2 text-primary"></i>Refleksi Pembelajaran Guru & Catatan
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="fw-bold text-muted small mb-1"><i class="bi bi-journal-check text-info me-1"></i>Refleksi Guru (Proses KBM, Kendala, & Rencana):</div>
                <div class="p-3 bg-info bg-opacity-10 text-dark rounded-3 border border-info border-opacity-25 fw-medium">
                    {{ $agenda->refleksi ?: 'Belum ada catatan refleksi pembelajaran.' }}
                </div>
            </div>

            @if($agenda->prompter_custom)
            <div>
                <div class="fw-bold text-muted small mb-1"><i class="bi bi-chat-left-text me-1 text-secondary"></i>Catatan Prompter:</div>
                <div class="p-3 bg-light text-dark rounded-3 border">
                    {{ $agenda->prompter_custom }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- 2 Foto Bukti Section (Foto Murid & Foto Guru) --}}
    <div class="card mb-4 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-light fw-bold text-dark">
            <i class="bi bi-camera-fill me-2 text-primary"></i>Dokumentasi Foto Bukti
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Foto 1: Murid (Ketua Kelas) --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-3 text-center bg-light">
                        <div class="fw-bold small text-dark mb-2"><i class="bi bi-camera me-1 text-primary"></i>Foto Bukti Murid / Ketua Kelas</div>
                        @if($agenda->foto_bukti_path)
                            <img src="{{ Storage::url($agenda->foto_bukti_path) }}" class="img-fluid rounded-3 shadow-sm mb-2" style="max-height: 220px; cursor: pointer;"
                                 onclick="window.open('{{ Storage::url($agenda->foto_bukti_path) }}', '_blank')">
                            <div>
                                <a href="{{ Storage::url($agenda->foto_bukti_path) }}" download="Foto_Murid_{{ $agenda->tanggal?->format('Y-m-d') }}.jpg" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i>Unduh Foto Murid
                                </a>
                            </div>
                        @else
                            <div class="py-4 text-muted small"><i class="bi bi-exclamation-circle me-1"></i>Foto Murid Belum Diunggah</div>
                        @endif
                    </div>
                </div>

                {{-- Foto 2: Guru (Selfie / Suasana Kelas) --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-3 text-center bg-light">
                        <div class="fw-bold small text-dark mb-2"><i class="bi bi-person-bounding-box me-1 text-success"></i>Foto Guru & Suasana Kelas</div>
                        @if($agenda->foto_guru_path)
                            <img src="{{ Storage::url($agenda->foto_guru_path) }}" class="img-fluid rounded-3 shadow-sm mb-2" style="max-height: 220px; cursor: pointer;"
                                 onclick="window.open('{{ Storage::url($agenda->foto_guru_path) }}', '_blank')">
                            <div>
                                <a href="{{ Storage::url($agenda->foto_guru_path) }}" download="Foto_Guru_{{ $agenda->tanggal?->format('Y-m-d') }}.jpg" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i>Unduh Foto Guru
                                </a>
                            </div>
                        @else
                            <div class="py-4 text-muted small"><i class="bi bi-exclamation-circle me-1"></i>Foto Guru Belum Diunggah</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="border-radius: 12px;" wire:navigate>
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard Guru
        </a>
    </div>
</div>
