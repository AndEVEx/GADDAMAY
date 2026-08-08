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
    toast.className = `toast align-items-center text-bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
};

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
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
