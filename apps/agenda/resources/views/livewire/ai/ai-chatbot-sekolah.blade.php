<div class="container-fluid px-2 px-md-3 py-2 py-md-3 d-flex flex-column" style="min-height: calc(100vh - 70px);">
    {{-- Header PWA Ramah Siswa --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                        <i class="bi bi-robot fs-2 text-primary"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="fw-bold mb-0 text-dark">Tanya AI / Chatbot Sekolah</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small">
                                <i class="bi bi-chat-quote-fill me-1"></i>Text-Only Edukasi
                            </span>
                            @if($isServerOnline)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small d-flex align-items-center gap-1">
                                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 6px; height: 6px;" role="status"></span>
                                    Server AI Online
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1 small">
                                    <i class="bi bi-cloud-slash me-1"></i>Offline / Terputus
                                </span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                            Teman belajar digital interaktif untuk tanya jawab materi, penjelasan konsep, dan ringkasan teks pelajaran.
                        </p>
                    </div>
                </div>

                {{-- Status Sesi Siswa & Tombol Kontrol --}}
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-start justify-content-md-end">
                    <div class="bg-light px-3 py-1.5 rounded-pill border d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                        <i class="bi bi-person-check-fill text-primary"></i>
                        <span class="text-muted">Sesi Privat:</span>
                        <span class="fw-bold text-dark text-truncate" style="max-width: 140px;">{{ Auth::user()->name }}</span>
                    </div>

                    <button wire:click="refreshConnection" wire:loading.attr="disabled" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 shadow-sm" title="Periksa status koneksi server">
                        <span wire:loading.remove wire:target="refreshConnection">
                            <i class="bi bi-arrow-clockwise me-1"></i>Cek Status
                        </span>
                        <span wire:loading wire:target="refreshConnection">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>Memeriksa...
                        </span>
                    </button>
                </div>
            </div>

            {{-- Flash Alert --}}
            @if (session()->has('status'))
                <div class="alert alert-success alert-dismissible fade show mt-3 mb-0 py-2 px-3 small rounded-3" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        {{-- Baris Ide Pertanyaan Cepat (Quick Text Prompt Pills) --}}
        <div class="bg-light border-top px-3 py-2">
            <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <span class="small fw-bold text-muted text-nowrap" style="font-size: 0.75rem;">
                    <i class="bi bi-stars text-warning me-1"></i>Ide Tanya Teks:
                </span>
                @foreach($quickPrompts as $prompt)
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $prompt['text'] }}'); alert('Teks disalin! Silakan tempelkan (paste) di kolom percakapan chatbot.');" class="btn btn-white bg-white border border-secondary border-opacity-25 btn-sm rounded-pill py-1 px-2.5 text-nowrap d-flex align-items-center gap-1.5 shadow-2xs hover-shadow" style="font-size: 0.75rem;">
                        <i class="bi {{ $prompt['icon'] }} text-primary"></i>
                        <span class="text-secondary fw-semibold">{{ $prompt['title'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA: EMBEDDED IFRAME vs FALLBACK STATE --}}
    <div class="flex-grow-1 d-flex flex-column position-relative">
        @if($isServerOnline)
            {{-- STATE 1: SERVER ONLINE - SMART EMBEDDED OPEN-WEBUI --}}
            <div class="card border-0 shadow-sm rounded-4 flex-grow-1 overflow-hidden d-flex flex-column position-relative bg-white" style="min-height: 580px;">
                {{-- Banner Keamanan & Batasan --}}
                <div class="bg-light border-bottom px-3 py-1.5 d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.72rem;">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        <span>Koneksi Aman Cloudflare Tunnel &bull; Khusus Obrolan Teks Edukatif (Unduhan & Eksekusi Kode Dibatasi)</span>
                    </div>
                    <a href="javascript:void(0)" onclick="document.getElementById('openwebui-frame').src += ''" class="text-decoration-none text-muted" title="Muat ulang halaman chat">
                        <i class="bi bi-arrow-repeat"></i> Refresh Layar
                    </a>
                </div>

                {{-- Loading Skeleton Placeholder --}}
                <div id="iframe-skeleton" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-white" style="z-index: 1;">
                    <div class="spinner-border text-primary mb-3" style="width: 2.5rem; height: 2.5rem;" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Membuka Ruang Tanya AI Sekolah...</h6>
                    <p class="text-muted small mb-0">Menyinkronkan sesi privat {{ Auth::user()->name }} via server AI lokal</p>
                </div>

                {{-- Embedded Iframe Pintar (Strict Sandbox: NO DOWNLOADS, NO CODE EXECUTION) --}}
                <iframe 
                    id="openwebui-frame"
                    src="{{ $iframeUrl }}" 
                    class="w-100 flex-grow-1 border-0" 
                    style="min-height: 540px; height: 100%;"
                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
                    allow="clipboard-write"
                    onload="document.getElementById('iframe-skeleton').style.display='none';"
                    title="Open-WebUI Chatbot Sekolah">
                </iframe>
            </div>
        @else
            {{-- STATE 2: FALLBACK STATE ELEGAN (SERVER OFFLINE / TUNNEL PUTUS) --}}
            <div class="card border-0 shadow-sm rounded-4 flex-grow-1 d-flex flex-column justify-content-center align-items-center text-center p-4 p-md-5 bg-white">
                <div class="position-relative mb-4">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 90px; height: 90px;">
                        <i class="bi bi-moon-stars-fill display-5 text-warning"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 bg-danger text-white rounded-circle p-1.5 shadow" style="transform: translate(10%, 10%);">
                        <i class="bi bi-wifi-off fs-6 d-block"></i>
                    </span>
                </div>

                <h4 class="fw-bold text-dark mb-2">Layanan Tanya AI Sedang Beristirahat</h4>
                <p class="text-muted small mb-4 mx-auto" style="max-width: 520px; line-height: 1.6;">
                    {{ $errorMessage ?: 'Koneksi ke server AI sekolah saat ini terputus. Mungkin komputer server lokal di lab sekolah sedang dinonaktifkan di luar jam belajar, atau jaringan Cloudflare Tunnel sedang dalam pemeliharaan berkala.' }}
                </p>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <button wire:click="refreshConnection" wire:loading.attr="disabled" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <span wire:loading.remove wire:target="refreshConnection">
                            <i class="bi bi-arrow-repeat me-1"></i> Coba Hubungkan Kembali
                        </span>
                        <span wire:loading wire:target="refreshConnection">
                            <span class="spinner-border spinner-border-sm me-1"></span> Mencoba Menghubungkan...
                        </span>
                    </button>
                    <a href="{{ route('lms.guru.materi') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                        <i class="bi bi-journal-bookmark me-1"></i> Buka Materi di LMS
                    </a>
                </div>

                {{-- Panduan Siswa Mandiri --}}
                <div class="card border bg-light rounded-3 p-3 text-start mx-auto w-100" style="max-width: 540px;">
                    <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-2">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Informasi Untuk Siswa &amp; Guru:</span>
                    </div>
                    <ul class="text-muted small mb-0 ps-3" style="line-height: 1.6;">
                        <li>Layanan AI ini dijalankan di server lokal sekolah untuk menjaga privasi data Anda.</li>
                        <li>Jika kendala berlanjut, hubungi Guru Mata Pelajaran atau Tim IT Sekolah.</li>
                        <li>Anda tetap dapat mengakses seluruh materi, jadwal pelajaran, dan latihan soal secara normal.</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('iframe-reload', () => {
            const frame = document.getElementById('openwebui-frame');
            if (frame) {
                frame.src = frame.src;
            }
        });
    });
</script>
