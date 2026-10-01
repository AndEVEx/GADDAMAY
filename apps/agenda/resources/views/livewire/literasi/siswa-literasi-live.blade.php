<div class="container-fluid px-2 px-md-3 py-3" wire:poll.2s style="max-width: 800px;">
    @if(!$sesi)
        {{-- Kondisi Tidak Ada Sesi --}}
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 bg-light my-4">
            <i class="bi bi-book display-1 text-primary mb-3"></i>
            <h4 class="fw-bold text-dark">Belum Ada Sesi Literasi Lapangan</h4>
            <p class="text-muted small">Saat ini belum ada sesi literasi yang sedang disiarkan oleh Pemateri atau Koordinator Literasi.</p>
            <a href="/" class="btn btn-outline-primary btn-sm mx-auto px-4 rounded-pill">
                Kembali ke Beranda
            </a>
        </div>
    @else
        {{-- Header Status Live Sesi --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e40af, #0369a1);">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if($sesi->status === 'live')
                            <span class="badge bg-danger text-white px-2 py-1 rounded-pill small d-flex align-items-center gap-1 shadow-sm">
                                <span class="spinner-grow spinner-grow-sm" role="status" style="width: 0.45rem; height: 0.45rem;"></span>
                                LIVE DISIARKAN
                            </span>
                        @else
                            <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded-pill small">
                                Sesi {{ ucfirst($sesi->status) }}
                            </span>
                        @endif
                        <span class="badge bg-warning text-dark small fw-semibold">
                            <i class="bi bi-tag-fill me-1"></i>{{ $sesi->tema }}
                        </span>
                    </div>

                    {{-- Pengatur Ukuran Font untuk Membaca di Lapangan --}}
                    <div class="btn-group btn-group-sm bg-white bg-opacity-20 rounded-pill p-1">
                        <button wire:click="setFontSize('normal')" class="btn btn-sm text-white px-2 py-0 border-0 {{ $fontSize === 'normal' ? 'fw-bold bg-white text-dark rounded-pill shadow-sm' : '' }}">A</button>
                        <button wire:click="setFontSize('large')" class="btn btn-sm text-white px-2 py-0 border-0 {{ $fontSize === 'large' ? 'fw-bold bg-white text-dark rounded-pill shadow-sm' : '' }}">A+</button>
                        <button wire:click="setFontSize('xlarge')" class="btn btn-sm text-white px-2 py-0 border-0 {{ $fontSize === 'xlarge' ? 'fw-bold bg-white text-dark rounded-pill shadow-sm' : '' }}">A++</button>
                    </div>
                </div>

                <h5 class="fw-bold mb-1 text-white">{{ $sesi->judul }}</h5>
                <div class="small text-white-50 d-flex flex-wrap align-items-center gap-2">
                    <span><i class="bi bi-person-fill text-warning me-1"></i>Pemateri: <strong>{{ $sesi->nama_pemateri }}</strong></span>
                    <span>•</span>
                    <span><i class="bi bi-calendar-event me-1"></i>{{ $sesi->tanggal->isoFormat('D MMMM Y') }}</span>
                </div>
            </div>

            {{-- Navigation Tabs (Materi Bacaan vs Kuis & Refleksi) --}}
            <div class="d-flex border-top border-white border-opacity-10 bg-black bg-opacity-20 text-center">
                <button wire:click="$set('activeTab', 'materi')" class="flex-fill py-2 btn btn-link text-decoration-none border-0 fw-bold small {{ $activeTab === 'materi' ? 'text-warning border-bottom border-warning border-3 bg-white bg-opacity-10' : 'text-white-50' }}">
                    <i class="bi bi-file-text-fill me-1"></i> Materi Bacaan
                    <span class="badge bg-white bg-opacity-25 text-white ms-1">{{ $sesi->current_slide_index }}/{{ $totalSlides }}</span>
                </button>
                <button wire:click="$set('activeTab', 'kuis')" class="flex-fill py-2 btn btn-link text-decoration-none border-0 fw-bold small {{ $activeTab === 'kuis' ? 'text-warning border-bottom border-warning border-3 bg-white bg-opacity-10' : 'text-white-50' }}">
                    <i class="bi bi-pencil-square me-1"></i> Kuis &amp; Refleksi
                    @if($sesi->allow_quiz)
                        <span class="badge bg-success ms-1">Dibuka</span>
                    @else
                        <span class="badge bg-secondary ms-1">Terkunci</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- TAB 1: MATERI BACAAN TERSINKRONISASI --}}
        @if($activeTab === 'materi')
            <div class="card border-0 shadow rounded-4 mb-4 bg-white overflow-hidden">
                {{-- Penanda Slide Terbimbing --}}
                <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill px-3 py-1">
                            Halaman {{ $sesi->current_slide_index }} dari {{ $totalSlides }}
                        </span>
                        <span class="text-muted small">
                            <i class="bi bi-broadcast text-danger me-1"></i>Tersinkron Pemateri
                        </span>
                    </div>
                    <span class="badge bg-light text-secondary border small">
                        Mode Baca Lapangan
                    </span>
                </div>

                <div class="card-body p-3 p-md-4">
                    @if($currentSlide)
                        {{-- Judul Slide --}}
                        <h4 class="fw-bold text-primary mb-3">
                            {{ $currentSlide->judul_slide }}
                        </h4>

                        {{-- Teks Bacaan Panjang --}}
                        <div class="text-dark lh-base mb-4 
                            @if($fontSize === 'large') fs-5
                            @elseif($fontSize === 'xlarge') fs-4
                            @else fs-6 @endif" 
                            style="white-space: pre-line; line-height: 1.8; text-align: justify;">
                            {{ $currentSlide->konten }}
                        </div>

                        {{-- Poin Kunci / Sorotan --}}
                        @if($currentSlide->poin_kunci)
                            <div class="card border-warning border-2 bg-warning bg-opacity-10 rounded-3 p-3 mb-3 shadow-none">
                                <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold mb-2">
                                    <i class="bi bi-lightbulb-fill fs-5 text-warning"></i>
                                    <span>Poin Utama Yang Harus Diingat:</span>
                                </div>
                                <div class="text-dark small fw-medium" style="white-space: pre-line; line-height: 1.6;">
                                    {{ $currentSlide->poin_kunci }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-hourglass-split fs-2 d-block mb-2"></i>
                            Menunggu Pemateri memulai presentasi slide...
                        </div>
                    @endif
                </div>

                {{-- Footer Info --}}
                <div class="card-footer bg-light border-top p-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-phone-vibrate me-1"></i>Halaman ini akan berpindah otomatis saat Pemateri berpindah slide di panggung.
                    </small>
                </div>
            </div>

            {{-- Callout ke Tab Kuis jika Dibuka --}}
            @if($sesi->allow_quiz && !$hasSubmitted)
                <div class="card border-success border-2 bg-success bg-opacity-10 rounded-4 p-3 mb-4 text-center">
                    <h6 class="fw-bold text-success mb-1">
                        <i class="bi bi-patch-question-fill me-1"></i> Sesi Kuis &amp; Refleksi Telah Dibuka!
                    </h6>
                    <p class="text-muted small mb-2">Pemateri telah membuka sesi pertanyaan untuk menguji pemahaman literasi Anda.</p>
                    <button wire:click="$set('activeTab', 'kuis')" class="btn btn-success fw-bold btn-sm px-4 rounded-pill shadow-sm mx-auto">
                        Kerjakan Soal Kuis Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            @endif
        @endif

        {{-- TAB 2: EVALUASI KUIS & REFLEKSI SISWA --}}
        @if($activeTab === 'kuis')
            @if(!$sesi->allow_quiz && !$hasSubmitted)
                <div class="card border-0 shadow-sm rounded-4 text-center p-5 bg-white mb-4">
                    <i class="bi bi-lock-fill display-3 text-warning mb-3"></i>
                    <h5 class="fw-bold text-dark">Kuis Belum Dibuka</h5>
                    <p class="text-muted small mx-auto" style="max-width: 420px;">
                        Pemateri belum membuka sesi evaluasi. Silakan fokus menyimak dan membaca materi presentasi di tab <strong>"Materi Bacaan"</strong>. Halaman ini akan terbuka otomatis saat pemateri mengaktifkannya.
                    </p>
                    <button wire:click="$set('activeTab', 'materi')" class="btn btn-primary btn-sm px-4 rounded-pill mx-auto">
                        <i class="bi bi-arrow-left me-1"></i> Kembali Menyimak Materi
                    </button>
                </div>
            @else
                {{-- Banner Jika Sudah Kirim Jawaban --}}
                @if($hasSubmitted)
                    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-success text-white p-3 p-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-lg fs-2"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Refleksi Literasi Terkirim!</h5>
                                <p class="mb-0 small text-white-50">
                                    Kehadiran dan jawaban Anda telah tercatat rapi untuk laporan Wali Kelas dan Koordinator Literasi.
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                            <span class="small">Skor Kuis Pilihan Ganda:</span>
                            <span class="badge bg-warning text-dark fs-6 px-3 py-1 fw-bold">{{ $totalSkorPg }} / 100</span>
                        </div>
                    </div>
                @endif

                {{-- Formulir Soal Evaluasi --}}
                <div class="card border-0 shadow rounded-4 mb-4 bg-white">
                    <div class="card-header bg-warning bg-opacity-25 border-0 p-3">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-patch-question-fill text-warning me-2"></i>
                            Pertanyaan Ketercapaian Literasi ({{ $soalList->count() }} Butir)
                        </h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <form wire:submit.prevent="submitEvaluasi">
                            @forelse($soalList as $idx => $soal)
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-dark rounded-pill">No. {{ $idx + 1 }}</span>
                                        <span class="badge {{ $soal->jenis_soal === 'pilihan_ganda' ? 'bg-primary' : 'bg-info text-dark' }} small">
                                            {{ $soal->jenis_soal === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Esai Reflektif' }}
                                        </span>
                                    </div>

                                    <div class="fw-bold text-dark mb-3 fs-6">
                                        {{ $soal->pertanyaan }}
                                    </div>

                                    @if($soal->jenis_soal === 'pilihan_ganda')
                                        <div class="d-flex flex-column gap-2">
                                            @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d, 'E' => $soal->opsi_e] as $pilihan => $teksOpsi)
                                                @if($teksOpsi)
                                                    <label class="p-2 px-3 border rounded-3 d-flex align-items-center gap-2 cursor-pointer transition-all {{ ($jawabanPg[$soal->id] ?? '') === $pilihan ? 'border-primary bg-primary bg-opacity-10 fw-bold' : 'bg-light' }}">
                                                        <input type="radio" wire:model="jawabanPg.{{ $soal->id }}" value="{{ $pilihan }}" class="form-check-input mt-0" {{ $hasSubmitted ? 'disabled' : '' }}>
                                                        <span class="small">{{ $pilihan }}. {{ $teksOpsi }}</span>
                                                    </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($soal->jenis_soal === 'esai_pendek')
                                        <div class="mb-2">
                                            <textarea wire:model="jawabanEsai.{{ $soal->id }}" class="form-control" rows="3" placeholder="Tuliskan jawaban atau refleksi singkat Anda..." {{ $hasSubmitted ? 'disabled' : '' }} required></textarea>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    Belum ada butir soal kuis pada sesi literasi ini.
                                </div>
                            @endforelse

                            {{-- Rangkuman / Kesimpulan Bebas Siswa --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="bi bi-chat-quote text-primary me-1"></i>
                                    Rangkuman / Intisari Yang Anda Dapatkan Hari Ini (Singkat):
                                </label>
                                <textarea wire:model="rangkumanUmum" class="form-control" rows="3" placeholder="Tuliskan 1-2 kalimat pesan penting yang Anda dapatkan dari materi pemateri..." {{ $hasSubmitted ? 'disabled' : '' }}></textarea>
                            </div>

                            @if(!$hasSubmitted)
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill shadow-sm py-3" wire:confirm="Yakin ingin mengirim jawaban sekarang?">
                                        <i class="bi bi-send-fill me-1"></i> Kirim Jawaban &amp; Catat Kehadiran Literasi
                                    </button>
                                </div>
                            @else
                                <div class="text-center text-muted small py-2">
                                    <i class="bi bi-shield-check text-success me-1"></i>
                                    Jawaban Anda telah terkunci dan tersimpan di sistem.
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>
