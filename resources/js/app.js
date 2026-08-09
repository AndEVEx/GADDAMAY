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
