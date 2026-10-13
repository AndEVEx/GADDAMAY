<div class="perpus-reader-wrapper min-vh-100 d-flex flex-column" id="reader-root" style="background-color: #1e293b; color: #f8fafc; user-select: none;">
    {{-- Header Reader Bar --}}
    <header class="navbar navbar-dark bg-dark bg-opacity-75 backdrop-blur px-3 py-2 border-bottom border-secondary border-opacity-25 sticky-top z-3">
        <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
            {{-- Tombol Kembali & Info Buku --}}
            <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                <a href="{{ route('perpus.katalog') }}" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Kembali ke Katalog">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="overflow-hidden">
                    <h6 class="mb-0 text-truncate fw-bold text-white small" style="max-width: 280px;" id="reader-book-title">
                        {{ $buku->judul ?? 'Membaca E-Book Digital' }}
                    </h6>
                    <div class="d-flex align-items-center gap-2 text-white-50" style="font-size: 0.72rem;">
                        <span class="text-truncate">{{ $buku->penulis ?? 'Penulis Vokasi' }}</span>
                        <span>•</span>
                        <span class="badge bg-primary bg-opacity-75 rounded-pill px-2 py-0.5" id="badge-page-indicator">Hal. {{ $halamanMulai }}</span>
                        @if($isOfflineMode)
                            <span class="badge bg-success rounded-pill px-2 py-0.5"><i class="bi bi-cloud-check-fill me-1"></i>Offline</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Toolbar Interaktif: Timer, Theme, Bookmark, Fullscreen --}}
            <div class="d-flex align-items-center gap-1.5 gap-md-2">
                {{-- Live Reading Duration Timer --}}
                <div class="bg-black bg-opacity-40 rounded-pill px-2.5 py-1 d-flex align-items-center gap-1.5 border border-secondary border-opacity-25 text-warning small" style="font-size: 0.75rem;" title="Durasi Membaca Aktif">
                    <i class="bi bi-stopwatch-fill"></i>
                    <span id="active-read-timer">00:00</span>
                </div>

                {{-- Mode Tampilan Kertas (Sepia / Dark / Light) --}}
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm rounded-circle border border-secondary border-opacity-25 text-light" style="width: 36px; height: 36px;" type="button" data-bs-toggle="dropdown" title="Warna Kertas">
                        <i class="bi bi-palette-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg rounded-4 p-2">
                        <li><h6 class="dropdown-header small text-uppercase text-secondary">Tema Membaca</h6></li>
                        <li><button class="dropdown-item rounded-3 small py-2 d-flex align-items-center gap-2" onclick="setReaderTheme('light')"><span class="d-inline-block rounded-circle bg-white border" style="width: 14px; height: 14px;"></span> Terang (Putih Bersih)</button></li>
                        <li><button class="dropdown-item rounded-3 small py-2 d-flex align-items-center gap-2" onclick="setReaderTheme('sepia')"><span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background: #fbf0d9;"></span> Sepia (Ramah Mata)</button></li>
                        <li><button class="dropdown-item rounded-3 small py-2 d-flex align-items-center gap-2" onclick="setReaderTheme('dark')"><span class="d-inline-block rounded-circle bg-dark border border-secondary" style="width: 14px; height: 14px;"></span> Gelap (Mode Malam)</button></li>
                    </ul>
                </div>

                {{-- Auto-Bookmark Indicator --}}
                <button type="button" class="btn btn-dark btn-sm rounded-circle border border-secondary border-opacity-25 text-warning" style="width: 36px; height: 36px;" id="btn-bookmark-toast" title="Penanda Terakhir">
                    <i class="bi bi-bookmark-check-fill"></i>
                </button>

                {{-- Toggle Fullscreen --}}
                <button type="button" class="btn btn-dark btn-sm rounded-circle border border-secondary border-opacity-25 text-light" style="width: 36px; height: 36px;" onclick="toggleFullscreen()" title="Layar Penuh">
                    <i class="bi bi-arrows-fullscreen" id="fullscreen-icon"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Reader Canvas Area --}}
    <main class="flex-grow-1 position-relative d-flex justify-content-center align-items-center overflow-hidden p-2 p-md-3" id="reader-container">
        {{-- Loading Overlay --}}
        <div id="pdf-loading-overlay" class="position-absolute top-50 start-50 translate-middle text-center z-2">
            <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading PDF...</span>
            </div>
            <h6 class="fw-bold text-white mb-1">Membuka Lembaran Buku Digital...</h6>
            <p class="text-white-50 small" id="pdf-loading-status">Memuat modul pembaca dan menginisialisasi efek lipatan 3D...</p>
        </div>

        {{-- Book Page-Flip Stage --}}
        <div id="flipbook" class="flipbook shadow-lg" style="visibility: hidden; transition: opacity 0.3s ease;">
            {{-- Halaman akan diinjeksi secara dinamis via JavaScript PageFlip & PDF.js --}}
        </div>

        {{-- Navigasi Mengambang (Floating Previous & Next Pill) --}}
        <button type="button" id="btn-prev-page" class="btn btn-dark bg-opacity-75 text-white rounded-circle shadow-lg position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-4 border border-secondary border-opacity-25 z-2" style="width: 48px; height: 48px;" title="Halaman Sebelumnya">
            <i class="bi bi-chevron-left fs-5"></i>
        </button>
        <button type="button" id="btn-next-page" class="btn btn-dark bg-opacity-75 text-white rounded-circle shadow-lg position-absolute end-0 top-50 translate-middle-y me-2 me-md-4 border border-secondary border-opacity-25 z-2" style="width: 48px; height: 48px;" title="Halaman Berikutnya">
            <i class="bi bi-chevron-right fs-5"></i>
        </button>
    </main>

    {{-- Bottom Controller Bar (Slider Halaman & Status Progress) --}}
    <footer class="bg-dark bg-opacity-75 backdrop-blur px-3 py-2 border-top border-secondary border-opacity-25 sticky-bottom z-3">
        <div class="container-fluid px-0">
            <div class="row align-items-center g-2">
                {{-- Quick Jump Slider --}}
                <div class="col-12 col-md-8 d-flex align-items-center gap-2">
                    <span class="small text-white-50 text-nowrap" id="slider-current-page">1</span>
                    <input type="range" class="form-range flex-grow-1" id="page-slider" min="1" max="{{ $buku->jumlah_halaman ?? 10 }}" value="{{ $halamanMulai }}" step="1">
                    <span class="small text-white-50 text-nowrap" id="slider-total-pages">{{ $buku->jumlah_halaman ?? 10 }} Hal</span>
                </div>

                {{-- Status Tersimpan & Tombol Lipat Cepat --}}
                <div class="col-12 col-md-4 d-flex justify-content-between justify-content-md-end align-items-center gap-3">
                    <div class="text-white-50 small d-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                        <span class="spinner-grow spinner-grow-sm text-success d-none" id="save-indicator-spinner"></span>
                        <i class="bi bi-cloud-check text-success fs-6" id="save-indicator-icon"></i>
                        <span id="save-indicator-text">Tersimpan otomatis</span>
                    </div>

                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-light rounded-pill px-3 py-1 text-nowrap" onclick="flipToFirst()">
                            <i class="bi bi-skip-backward-fill"></i> Awal
                        </button>
                        <button type="button" class="btn btn-outline-light rounded-pill px-3 py-1 text-nowrap ms-1" onclick="flipToLast()">
                            Akhir <i class="bi bi-skip-forward-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

{{-- CSS Custom Page-Flip Realistic Styles --}}
<style>
    .backdrop-blur {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .perpus-reader-wrapper {
        min-height: 100vh;
        overflow: hidden;
    }
    .flipbook {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        border-radius: 8px;
    }
    .flip-page {
        background-color: #ffffff;
        box-shadow: inset 0 0 30px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .flip-page canvas {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    /* Theme Themes */
    .theme-sepia .flip-page {
        background-color: #fbf0d9 !important;
        filter: sepia(0.35) contrast(0.95);
    }
    .theme-dark .flip-page {
        background-color: #0f172a !important;
        filter: invert(0.9) hue-rotate(180deg);
    }
    .theme-light .flip-page {
        background-color: #ffffff !important;
        filter: none;
    }
    /* Range Slider Styling */
    .form-range::-webkit-slider-thumb {
        background: #0284c7;
    }
</style>

{{-- Libraries: PDF.js & PageFlip (StPageFlip) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.js"></script>

{{-- Web Audio Realistic Page Flip Sound Generator --}}
<script>
    // Audio synthesizer untuk suara membalik kertas lembaran buku yang realistis
    function playPageFlipSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const bufferSize = ctx.sampleRate * 0.15; // 150ms suara gesekan kertas
            const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
            const data = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                data[i] = Math.random() * 2 - 1; // white noise
            }
            const noise = ctx.createBufferSource();
            noise.buffer = buffer;

            const filter = ctx.createBiquadFilter();
            filter.type = 'bandpass';
            filter.frequency.setValueAtTime(800, ctx.currentTime);
            filter.Q.setValueAtTime(3, ctx.currentTime);

            const gain = ctx.createGain();
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);

            noise.connect(filter);
            filter.connect(gain);
            gain.connect(ctx.destination);

            noise.start();
        } catch(e) {
            // Audio policy ignore
        }
    }
</script>

{{-- Core Reader Script: Render PDF to Canvas + 3D Flip Engine + Auto-Sync --}}
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        const bookId = '{{ $bukuId }}';
        const isOffline = {{ $isOfflineMode ? 'true' : 'false' }};
        const initialPage = {{ $halamanMulai }};
        const fileUrl = '{{ $buku->file_pdf_url ?? "" }}';

        let pdfDoc = null;
        let pageFlip = null;
        let totalPages = 1;
        let currentPageIndex = initialPage - 1; // 0-indexed
        let readingDurationSeconds = 0;
        let isTabActive = true;
        let pendingSaveTimer = null;

        // 1. Durasi Membaca Aktif & Page Visibility Handler
        setInterval(() => {
            if (isTabActive) {
                readingDurationSeconds++;
                const mins = String(Math.floor(readingDurationSeconds / 60)).padStart(2, '0');
                const secs = String(readingDurationSeconds % 60).padStart(2, '0');
                const timerEl = document.getElementById('active-read-timer');
                if (timerEl) timerEl.innerText = `${mins}:${secs}`;

                // Kirim simpan otomatis berkala tiap 25 detik
                if (readingDurationSeconds % 25 === 0) {
                    dispatchProgressSave();
                }
            }
        }, 1000);

        document.addEventListener('visibilitychange', () => {
            isTabActive = !document.hidden;
        });

        // 2. Muat Dokumen PDF (Online atau dari IndexedDB jika offline)
        async function loadPdfDocument() {
            const statusEl = document.getElementById('pdf-loading-status');
            try {
                let pdfData = null;

                if (isOffline) {
                    statusEl.innerText = 'Mengambil file buku dari penyimpanan offline HP...';
                    pdfData = await getBookFromIndexedDB(bookId);
                    if (!pdfData) {
                        throw new Error('Buku offline tidak ditemukan di memori perangkat. Silakan download terlebih dahulu saat online.');
                    }
                } else {
                    statusEl.innerText = 'Mengunduh berkas e-book digital SMKN 2 Indramayu...';
                    pdfData = fileUrl;
                }

                const loadingTask = pdfjsLib.getDocument(pdfData);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;

                // Update UI total halaman
                document.getElementById('slider-total-pages').innerText = `${totalPages} Hal`;
                const pageSlider = document.getElementById('page-slider');
                pageSlider.max = totalPages;

                statusEl.innerText = `Menyiapkan ${totalPages} lembar buku dengan efek lipatan 3D...`;
                await initializeFlipBook();

            } catch (err) {
                console.error('Error loading PDF:', err);
                const overlay = document.getElementById('pdf-loading-overlay');
                overlay.innerHTML = `
                    <div class="card bg-dark border-danger p-4 text-center text-white rounded-4 shadow-lg" style="max-width: 450px;">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-4 mb-3"></i>
                        <h5 class="fw-bold text-white mb-2">Gagal Membuka Buku Digital</h5>
                        <p class="text-white-50 small mb-3">${err.message || 'File PDF belum tersedia atau format tidak didukung.'}</p>
                        <a href="{{ route('perpus.katalog') }}" class="btn btn-outline-light btn-sm rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
                        </a>
                    </div>
                `;
            }
        }

        // 3. Inisialisasi StPageFlip dan Render Halaman
        async function initializeFlipBook() {
            const container = document.getElementById('flipbook');
            container.innerHTML = '';

            // Hitung dimensi yang pas untuk layar mobile maupun laptop
            const windowW = window.innerWidth;
            const windowH = window.innerHeight;
            let pageWidth = Math.min(500, Math.floor(windowW * 0.45));
            let pageHeight = Math.min(750, Math.floor(windowH * 0.78));

            // Jika di smartphone (lebar layar < 768px), gunakan mode single-page
            const isMobile = windowW < 768;
            if (isMobile) {
                pageWidth = Math.min(420, Math.floor(windowW * 0.92));
                pageHeight = Math.min(640, Math.floor(windowH * 0.74));
            }

            // Buat kontainer lembaran halaman kosong
            for (let i = 1; i <= totalPages; i++) {
                const pageDiv = document.createElement('div');
                pageDiv.className = 'flip-page';
                pageDiv.id = `page-${i}`;
                pageDiv.innerHTML = `
                    <canvas id="canvas-page-${i}"></canvas>
                    <div class="position-absolute bottom-0 text-muted p-2" style="font-size: 0.65rem;">${i}</div>
                `;
                container.appendChild(pageDiv);
            }

            // Render 3 halaman awal terlebih dahulu agar instan terbuka
            await renderPageToCanvas(Math.max(1, initialPage - 1));
            await renderPageToCanvas(initialPage);
            if (initialPage + 1 <= totalPages) {
                await renderPageToCanvas(initialPage + 1);
            }

            // Setup PageFlip Instance
            pageFlip = new St.PageFlip(container, {
                width: pageWidth,
                height: pageHeight,
                size: 'fixed',
                minWidth: 280,
                maxWidth: 600,
                minHeight: 400,
                maxHeight: 850,
                maxShadowOpacity: 0.5,
                showCover: true,
                mobileScrollSupport: false,
                usePortrait: isMobile,
                startPage: Math.min(totalPages - 1, Math.max(0, initialPage - 1))
            });

            pageFlip.loadFromHTML(document.querySelectorAll('.flip-page'));

            // Sembunyikan loading overlay, tampilkan flipbook
            document.getElementById('pdf-loading-overlay').style.display = 'none';
            container.style.visibility = 'visible';
            container.style.opacity = '1';

            // Event saat halaman dibalik
            pageFlip.on('flip', (e) => {
                playPageFlipSound();
                currentPageIndex = e.data;
                const pageNum = currentPageIndex + 1;
                updateUIPageState(pageNum);

                // Pre-render halaman sekitar (buffer 2 halaman ke depan dan ke belakang)
                renderSurroundingPages(pageNum);

                // Simpan bookmark & waktu baca ke Livewire
                scheduleProgressSave();
            });

            // Pasang event tombol navigasi
            document.getElementById('btn-prev-page').addEventListener('click', () => {
                pageFlip.flipPrev();
            });
            document.getElementById('btn-next-page').addEventListener('click', () => {
                pageFlip.flipNext();
            });

            // Slider interaksi
            const slider = document.getElementById('page-slider');
            slider.addEventListener('change', (e) => {
                const targetPage = parseInt(e.target.value, 10);
                if (targetPage >= 1 && targetPage <= totalPages) {
                    pageFlip.turnToPage(targetPage - 1);
                }
            });

            // Pre-render sisa halaman di background tanpa menghambat thread
            setTimeout(() => {
                renderAllRemainingPages();
            }, 1000);
        }

        // 4. Render Halaman ke Canvas
        const renderedPages = new Set();
        async function renderPageToCanvas(pageNum) {
            if (pageNum < 1 || pageNum > totalPages || renderedPages.has(pageNum)) return;

            try {
                const page = await pdfDoc.getPage(pageNum);
                const canvas = document.getElementById(`canvas-page-${pageNum}`);
                if (!canvas) return;

                const viewport = page.getViewport({ scale: 1.5 });
                const ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise;

                renderedPages.add(pageNum);
            } catch (e) {
                console.warn(`Render page ${pageNum} skipped:`, e);
            }
        }

        function renderSurroundingPages(centerPage) {
            for (let i = centerPage - 2; i <= centerPage + 2; i++) {
                if (i >= 1 && i <= totalPages) {
                    renderPageToCanvas(i);
                }
            }
        }

        async function renderAllRemainingPages() {
            for (let i = 1; i <= totalPages; i++) {
                if (!renderedPages.has(i)) {
                    await renderPageToCanvas(i);
                    // Beri jeda kecil agar UI tetap 60fps
                    await new Promise(r => setTimeout(r, 40));
                }
            }
        }

        // 5. Update UI Indikator Halaman
        function updateUIPageState(pageNum) {
            document.getElementById('badge-page-indicator').innerText = `Hal. ${pageNum}`;
            document.getElementById('slider-current-page').innerText = pageNum;
            document.getElementById('page-slider').value = pageNum;
        }

        // 6. Sinkronisasi Progress ke Livewire
        function scheduleProgressSave() {
            clearTimeout(pendingSaveTimer);
            pendingSaveTimer = setTimeout(() => {
                dispatchProgressSave();
            }, 1500);
        }

        async function dispatchProgressSave() {
            const pageNum = currentPageIndex + 1;
            const isCompleted = pageNum >= totalPages;

            const saveIcon = document.getElementById('save-indicator-icon');
            const saveSpinner = document.getElementById('save-indicator-spinner');
            const saveText = document.getElementById('save-indicator-text');

            if (saveSpinner) saveSpinner.classList.remove('d-none');
            if (saveIcon) saveIcon.classList.add('d-none');
            if (saveText) saveText.innerText = 'Menyimpan...';

            try {
                if (window.Livewire) {
                    const component = Livewire.find(document.getElementById('reader-root').closest('[wire\\:id]')?.getAttribute('wire:id'));
                    if (component) {
                        await component.simpanProgress(pageNum, 15, isCompleted);
                    }
                }
                if (saveSpinner) saveSpinner.classList.add('d-none');
                if (saveIcon) saveIcon.classList.remove('d-none');
                if (saveText) saveText.innerText = 'Bookmark tersimpan';
            } catch (err) {
                console.warn('Sync reading progress offline fallback:', err);
                if (saveSpinner) saveSpinner.classList.add('d-none');
                if (saveIcon) saveIcon.classList.remove('d-none');
                if (saveText) saveText.innerText = 'Tersimpan lokal';
            }
        }

        // 7. IndexedDB Helper untuk Membaca Offline
        function getBookFromIndexedDB(id) {
            return new Promise((resolve, reject) => {
                const req = indexedDB.open('PerpusDigitalSMKN2', 1);
                req.onsuccess = (e) => {
                    const db = e.target.result;
                    const tx = db.transaction('offline_books', 'readonly');
                    const store = tx.objectStore('offline_books');
                    const getReq = store.get(id);
                    getReq.onsuccess = () => {
                        if (getReq.result && getReq.result.pdfBlob) {
                            const reader = new FileReader();
                            reader.onload = () => resolve(new Uint8Array(reader.result));
                            reader.onerror = reject;
                            reader.readAsArrayBuffer(getReq.result.pdfBlob);
                        } else {
                            resolve(null);
                        }
                    };
                    getReq.onerror = reject;
                };
                req.onerror = reject;
            });
        }

        // Jalankan Muat Dokumen
        loadPdfDocument();

        // Window expose functions
        window.flipToFirst = () => pageFlip && pageFlip.turnToPage(0);
        window.flipToLast = () => pageFlip && pageFlip.turnToPage(totalPages - 1);
    });

    // Theme Switcher
    function setReaderTheme(theme) {
        const root = document.getElementById('reader-root');
        root.classList.remove('theme-light', 'theme-sepia', 'theme-dark');
        root.classList.add(`theme-${theme}`);
        localStorage.setItem('perpus_reader_theme', theme);
    }
    // Load cached theme
    const savedTheme = localStorage.getItem('perpus_reader_theme') || 'light';
    setReaderTheme(savedTheme);

    // Fullscreen Toggle
    function toggleFullscreen() {
        const elem = document.documentElement;
        const icon = document.getElementById('fullscreen-icon');
        if (!document.fullscreenElement) {
            elem.requestFullscreen?.().then(() => {
                if (icon) icon.className = 'bi bi-fullscreen-exit';
            });
        } else {
            document.exitFullscreen?.().then(() => {
                if (icon) icon.className = 'bi bi-arrows-fullscreen';
            });
        }
    }
</script>
