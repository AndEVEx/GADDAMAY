import 'bootstrap';

// ============================================================
// Service Worker Registration
// ============================================================
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg.scope))
            .catch(err => console.log('SW registration failed:', err));
    });
}

// ============================================================
// Camera API Utility
// ============================================================
window.CameraUtil = {
    async openCamera(videoElement) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            videoElement.srcObject = stream;
            videoElement.play();
            return stream;
        } catch (err) {
            console.error('Camera error:', err);
            throw err;
        }
    },

    captureAndCompress(videoElement, maxWidth = 800, quality = 0.7) {
        const canvas = document.createElement('canvas');
        const ratio = videoElement.videoHeight / videoElement.videoWidth;
        canvas.width = Math.min(videoElement.videoWidth, maxWidth);
        canvas.height = canvas.width * ratio;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

        return new Promise((resolve) => {
            canvas.toBlob((blob) => {
                resolve(blob);
            }, 'image/jpeg', quality);
        });
    },

    stopCamera(stream) {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }
};

// ============================================================
// Stopwatch Engine
// ============================================================
window.StopwatchEngine = {
    create() {
        return {
            startTime: null,
            elapsed: 0,
            interval: null,
            running: false,

            start() {
                this.startTime = Date.now() - this.elapsed;
                this.running = true;
                this.interval = setInterval(() => {
                    this.elapsed = Date.now() - this.startTime;
                }, 100);
            },

            stop() {
                clearInterval(this.interval);
                this.running = false;
            },

            reset() {
                this.stop();
                this.elapsed = 0;
            },

            get formatted() {
                const totalSeconds = Math.floor(this.elapsed / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        };
    }
};

// ============================================================
// Toast Notification Helper
// ============================================================
window.showToast = function(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show shadow-lg rounded-3 fw-bold mb-2`;
    toast.setAttribute('role', 'alert');
    toast.style.fontWeight = '700';
    toast.style.fontSize = '0.95rem';
    toast.style.borderRadius = '12px';
    toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';

    toast.innerHTML = `
        <div class="d-flex align-items-center justify-content-between px-3 py-2" style="min-height: 52px;">
            <div class="toast-body p-0 fw-bold d-flex align-items-center my-auto flex-fill me-2" style="font-weight: 800; font-size: 0.95rem; line-height: 1.3;">${message}</div>
            <button type="button" class="btn-close btn-close-white my-auto ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 start-50 translate-middle-x p-3';
    container.style.zIndex = '9999';
    container.style.width = '90%';
    container.style.maxWidth = '460px';
    document.body.appendChild(container);
    return container;
}

// ============================================================
// Livewire event listeners (Null-safe parameter extraction)
// ============================================================
document.addEventListener('livewire:init', () => {
    Livewire.on('show-toast', (event) => {
        const data = Array.isArray(event) ? event[0] : (event?.detail || event || {});
        const message = data?.message || (typeof data === 'string' ? data : '');
        const type = data?.type || 'success';
        if (message) {
            window.showToast(message, type);
        }
    });

    Livewire.on('show-motivasi', (event) => {
        const data = Array.isArray(event) ? event[0] : (event?.detail || event || {});
        if (data && data.isi) {
            showMotivasiPopup(data.isi, data.tipe || 'motivasi');
        }
    });
});

function showMotivasiPopup(isi, tipe) {
    const emoji = tipe === 'pantun' ? '📜' : '✨';
    const popup = document.createElement('div');
    popup.className = 'motivasi-popup';
    popup.innerHTML = `
        <div class="motivasi-content">
            <div class="motivasi-emoji">${emoji}</div>
            <div class="motivasi-text">${isi}</div>
            <button class="btn btn-primary" onclick="this.closest('.motivasi-popup').remove()">
                Siap Mengajar! 💪
            </button>
        </div>
    `;
    popup.addEventListener('click', (e) => {
        if (e.target === popup) popup.remove();
    });
    document.body.appendChild(popup);
}

// ============================================================
// Watermark Camera Utility
// ============================================================
window.WatermarkCamera = {
    async processAndWatermark(imageSource, metadata = {}) {
        return new Promise((resolve, reject) => {
            try {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const maxWidth = 1000;
                let srcWidth = imageSource.videoWidth || imageSource.naturalWidth || imageSource.width;
                let srcHeight = imageSource.videoHeight || imageSource.naturalHeight || imageSource.height;

                if (!srcWidth || !srcHeight) {
                    throw new Error("Sumber gambar tidak valid atau belum siap.");
                }

                const scaleFactor = Math.min(1, maxWidth / srcWidth);
                canvas.width = srcWidth * scaleFactor;
                canvas.height = srcHeight * scaleFactor;

                const w = canvas.width;
                const h = canvas.height;

                // 1. Draw photo to canvas
                ctx.drawImage(imageSource, 0, 0, w, h);

                // 2. Banner Gradient Gelap
                const bannerHeight = Math.max(120, h * 0.22);
                const gradient = ctx.createLinearGradient(0, h - bannerHeight - 40, 0, h);
                gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
                gradient.addColorStop(0.3, 'rgba(15, 23, 42, 0.75)');
                gradient.addColorStop(1, 'rgba(15, 23, 42, 0.95)');

                ctx.fillStyle = gradient;
                ctx.fillRect(0, h - bannerHeight - 40, w, bannerHeight + 40);

                // 3. Aksen Garis Kiri
                const paddingLeft = 24;
                const strokeWidth = 5;
                ctx.fillStyle = '#0284c7';
                ctx.fillRect(paddingLeft, h - bannerHeight + 10, strokeWidth, bannerHeight - 30);

                // 4. Metadata Text
                ctx.textBaseline = 'top';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.8)';
                ctx.shadowBlur = 4;

                const textX = paddingLeft + strokeWidth + 14;
                let currentY = h - bannerHeight + 10;

                // A. Header Badge
                ctx.font = 'bold 18px "Inter", "Segoe UI", sans-serif';
                ctx.fillStyle = '#38bdf8';
                const headerText = `AGEN DAMAY | ${metadata.namaSekolah || 'SMKN 2 INDRAMAYU'}`;
                ctx.fillText(headerText.toUpperCase(), textX, currentY);
                currentY += 26;

                // B. Kelas & Mapel
                ctx.font = '600 22px "Inter", "Segoe UI", sans-serif';
                ctx.fillStyle = '#ffffff';
                const mainInfo = `${metadata.namaKelas || 'Kelas'} • ${metadata.namaMapel || 'Mata Pelajaran'}`;
                ctx.fillText(mainInfo, textX, currentY);
                currentY += 28;

                // C. Guru & Waktu
                const now = new Date();
                const optionsDate = { day: '2-digit', month: 'short', year: 'numeric' };
                const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                const dateStr = now.toLocaleDateString('id-ID', optionsDate);
                const timeStr = now.toLocaleTimeString('id-ID', optionsTime);

                ctx.font = '400 15px "Inter", "Segoe UI", sans-serif';
                ctx.fillStyle = '#cbd5e1';
                const detailText = `Pengajar: ${metadata.namaGuru || 'Guru'} | ${dateStr} - ${timeStr} WIB`;
                ctx.fillText(detailText, textX, currentY);
                currentY += 24;

                // D. Tagline
                const tagline = metadata.tagline || 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari';
                ctx.font = 'italic 500 13px "Inter", "Segoe UI", sans-serif';
                ctx.fillStyle = '#f1f5f9';
                ctx.fillText(`"${tagline}"`, textX, currentY);

                // 5. Compress to Base64 JPEG
                const compressedBase64 = canvas.toDataURL('image/jpeg', 0.70);
                resolve(compressedBase64);

            } catch (error) {
                reject(error);
            }
        });
    }
};

// ============================================================
// Fullscreen Toggle Helper
// ============================================================
window.toggleFullscreen = function() {
    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }
};
