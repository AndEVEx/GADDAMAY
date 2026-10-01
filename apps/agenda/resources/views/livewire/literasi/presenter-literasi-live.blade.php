<div class="container-fluid px-3 px-md-4 py-3" wire:poll.3s>
    {{-- Top Bar Remote Controller --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-dark text-white">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('literasi.kelola') }}" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $sesi->status === 'live' ? 'bg-danger animate-pulse' : ($sesi->status === 'selesai' ? 'bg-success' : 'bg-secondary') }} px-2 py-1 rounded-pill small">
                                <i class="bi bi-broadcast me-1"></i>{{ strtoupper($sesi->status) }}
                            </span>
                            <span class="text-white-50 small">Rabu Pagi, {{ $sesi->tanggal->isoFormat('D MMMM Y') }}</span>
                        </div>
                        <h5 class="fw-bold mb-0 text-truncate" style="max-width: 480px;">{{ $sesi->judul }}</h5>
                    </div>
                </div>

                {{-- Live Audience Counter & Controls --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="bg-black bg-opacity-50 px-3 py-1 rounded-pill d-flex align-items-center gap-2 border border-secondary border-opacity-50">
                        <i class="bi bi-phone text-warning"></i>
                        <span class="small text-white-50">Siswa Terhubung:</span>
                        <span class="fw-bold text-white fs-6">{{ $siswaConnectedCount }}</span>
                    </div>

                    <div class="bg-black bg-opacity-50 px-3 py-1 rounded-pill d-flex align-items-center gap-2 border border-secondary border-opacity-50">
                        <i class="bi bi-check2-circle text-success"></i>
                        <span class="small text-white-50">Selesai Kuis:</span>
                        <span class="fw-bold text-success fs-6">{{ $siswaSubmittedCount }}</span>
                    </div>

                    {{-- Tombol Buka Kuis --}}
                    <button wire:click="toggleQuiz" class="btn btn-sm {{ $sesi->allow_quiz ? 'btn-success text-white' : 'btn-outline-warning' }} fw-bold px-3 py-2 rounded-3 shadow-sm">
                        <i class="bi {{ $sesi->allow_quiz ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                        {{ $sesi->allow_quiz ? 'Kuis Aktif (Buka)' : 'Buka Kuis Siswa' }}
                    </button>

                    @if($sesi->status !== 'live')
                        <button wire:click="startLive" class="btn btn-danger btn-sm fw-bold px-3 py-2 rounded-3 shadow-sm">
                            <i class="bi bi-play-circle-fill me-1"></i> Mulai Siaran Live
                        </button>
                    @else
                        <button wire:click="finishSession" wire:confirm="Selesaikan sesi literasi hari ini?" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
                            <i class="bi bi-stop-circle-fill me-1"></i> Akhiri Sesi
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Presentation Display --}}
    <div class="row g-3">
        {{-- Layar Utama Slide (Center Screen) --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow rounded-4 overflow-hidden" style="min-height: 520px; background: #ffffff;">
                {{-- Slide Header Indicator --}}
                <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-primary fw-bold px-2 py-1 fs-6">
                            Slide {{ $sesi->current_slide_index }} / {{ $totalSlides }}
                        </span>
                        <span class="small text-white-50 d-none d-md-inline">
                            <i class="bi bi-eye-fill me-1"></i>Tampilan ini sama persis dengan yang dilihat siswa di ponsel
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark small fw-semibold">
                            Pemateri: {{ $sesi->nama_pemateri }}
                        </span>
                    </div>
                </div>

                {{-- Slide Content Canvas --}}
                <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between">
                    @if($currentSlide)
                        <div>
                            <h2 class="fw-extrabold text-primary mb-4" style="letter-spacing: -0.5px;">
                                {{ $currentSlide->judul_slide }}
                            </h2>

                            {{-- Teks Bacaan Materi yang Cukup Banyak --}}
                            <div class="fs-5 text-dark lh-base mb-4" style="white-space: pre-line; line-height: 1.8; text-align: justify;">
                                {{ $currentSlide->konten }}
                            </div>

                            {{-- Poin Kunci / Sorotan --}}
                            @if($currentSlide->poin_kunci)
                                <div class="card border-warning border-2 bg-warning bg-opacity-10 rounded-4 p-3 mb-4 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold mb-2">
                                        <i class="bi bi-lightbulb-fill fs-5 text-warning"></i>
                                        <span>Poin Kunci Yang Perlu Diingat Siswa:</span>
                                    </div>
                                    <div class="text-dark fw-medium" style="white-space: pre-line; line-height: 1.6;">
                                        {{ $currentSlide->poin_kunci }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 my-5 text-muted">
                            <i class="bi bi-file-earmark-break display-1 text-secondary mb-3"></i>
                            <h4>Belum Ada Slide Dibuat</h4>
                            <p>Silakan kembali ke menu kelola untuk menambahkan slide presentasi.</p>
                        </div>
                    @endif

                    {{-- Remote Controller Floating Bar (Bawah Slide) --}}
                    <div class="pt-4 border-top d-flex justify-content-between align-items-center">
                        <button wire:click="prevSlide" class="btn btn-outline-primary btn-lg px-4 py-2 rounded-pill fw-bold shadow-sm" {{ $sesi->current_slide_index <= 1 ? 'disabled' : '' }}>
                            <i class="bi bi-chevron-left me-1"></i> Slide Sebelumnya
                        </button>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Lompat Slide:</span>
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle border fw-bold rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                    Halaman {{ $sesi->current_slide_index }}
                                </button>
                                <ul class="dropdown-menu shadow-sm">
                                    @for($i = 1; $i <= $totalSlides; $i++)
                                        <li>
                                            <a class="dropdown-item {{ $i == $sesi->current_slide_index ? 'active' : '' }}" href="javascript:void(0)" wire:click="goToSlide({{ $i }})">
                                                Slide {{ $i }}
                                            </a>
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                        </div>

                        <button wire:click="nextSlide" class="btn btn-primary btn-lg px-4 py-2 rounded-pill fw-bold shadow-sm" {{ $sesi->current_slide_index >= $totalSlides ? 'disabled' : '' }}>
                            Slide Selanjutnya <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Samping: Speaker Notes & Thumbnail Slide (Kanan) --}}
        <div class="col-lg-3">
            {{-- Speaker Notes (Catatan Rahasia Pemateri) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 bg-warning bg-opacity-10 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold mb-2">
                        <i class="bi bi-journal-text fs-5"></i>
                        <span>Catatan Pembicara (Speaker Notes)</span>
                    </div>
                    @if($currentSlide && $currentSlide->catatan_pemateri)
                        <p class="small text-dark mb-0" style="white-space: pre-line; line-height: 1.5;">
                            {{ $currentSlide->catatan_pemateri }}
                        </p>
                    @else
                        <p class="small text-muted mb-0 fst-italic">
                            Tidak ada catatan khusus untuk slide ini.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Daftar Navigasi Slide Cepat --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-collection-play me-1 text-primary"></i>Daftar Halaman Slide
                    </h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 400px;">
                    <div class="list-group list-group-flush">
                        @foreach($sesi->slides as $slide)
                            <button wire:click="goToSlide({{ $slide->urutan }})" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 p-2 text-start {{ $sesi->current_slide_index == $slide->urutan ? 'active bg-primary text-white' : 'bg-light text-dark' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $sesi->current_slide_index == $slide->urutan ? 'bg-white text-primary' : 'bg-secondary text-white' }} small">
                                        #{{ $slide->urutan }}
                                    </span>
                                    @if($sesi->current_slide_index == $slide->urutan)
                                        <span class="badge bg-warning text-dark small">Sedang Tampil</span>
                                    @endif
                                </div>
                                <div class="small fw-bold text-truncate">{{ $slide->judul_slide }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Info QR Code / Akses Siswa --}}
            <div class="card border-0 shadow-sm rounded-4 mt-3 bg-light text-center p-3">
                <small class="text-muted d-block mb-1">Kode Sesi Lapangan:</small>
                <div class="h4 fw-extrabold text-primary font-monospace mb-1">{{ $sesi->kode_sesi }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">Siswa dapat membuka menu "Literasi Rabu Pagi" di beranda masing-masing.</small>
            </div>
        </div>
    </div>
</div>
