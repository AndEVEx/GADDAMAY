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
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            const maxWidth = 1200;
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

            // 1. Draw photo onto canvas
            ctx.drawImage(imageSource, 0, 0, w, h);

            // 2. Compact Banner Dark Gradient at bottom (portrait vs landscape height)
            const isPortrait = h > w;
            const bannerHeight = isPortrait ? Math.max(85, Math.min(110, h * 0.11)) : Math.max(105, Math.min(130, h * 0.15));
            const gradient = ctx.createLinearGradient(0, h - bannerHeight - 12, 0, h);
            gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
            gradient.addColorStop(0.35, 'rgba(15, 23, 42, 0.88)');
            gradient.addColorStop(1, 'rgba(15, 23, 42, 0.98)');

            ctx.fillStyle = gradient;
            ctx.fillRect(0, h - bannerHeight - 12, w, bannerHeight + 12);

            ctx.textBaseline = 'top';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
            ctx.shadowBlur = 6;

            const paddingLeft = 18;

            // 3. Load School Logo Image with Async Promise Wait
            const logoImg = new Image();
            logoImg.crossOrigin = 'anonymous';

            await new Promise((resolveLogo) => {
                let resolved = false;
                const onDone = () => {
                    if (!resolved) {
                        resolved = true;
                        resolveLogo(true);
                    }
                };
                logoImg.onload = onDone;
                logoImg.onerror = () => {
                    if (!logoImg.src.includes('/icons/')) {
                        logoImg.src = '/icons/logo-sekolah.png';
                    } else {
                        onDone();
                    }
                };
                logoImg.src = '/pwa-icons/logosekolah.png';
                if (logoImg.complete && logoImg.naturalWidth > 0) onDone();
                setTimeout(onDone, 1500);
            });

            const logoSize = Math.max(54, bannerHeight * 0.55);
            const logoX = paddingLeft;
            const logoY = h - bannerHeight + (isPortrait ? 4 : 6);

            // Draw White Rounded Box for School Logo
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(logoX, logoY, logoSize, logoSize, 10);
            } else {
                ctx.rect(logoX, logoY, logoSize, logoSize);
            }
            ctx.fill();

            // Draw Logo Image inside White Box
            if (logoImg.complete && logoImg.naturalWidth > 0) {
                try {
                    ctx.drawImage(logoImg, logoX + 3, logoY + 3, logoSize - 6, logoSize - 6);
                } catch (e) {}
            }

            const textX = logoX + logoSize + 14;
            let currentY = logoY - 2;

            // A. Header Branding: AGEN DAMAY  •  SMKN 2 INDRAMAYU (Same size, 2 spaces apart, distinct colors)
            const headerFontSize = Math.max(19, Math.round(w * 0.023));
            ctx.font = `800 ${headerFontSize}px "Inter", "Segoe UI", sans-serif`;

            // AGEN DAMAY (Cyan)
            ctx.fillStyle = '#38bdf8';
            ctx.fillText('AGEN DAMAY', textX, currentY);

            // Measure AGEN DAMAY width + 2 spaces for exact alignment
            const agenDamayWidth = ctx.measureText('AGEN DAMAY  ').width;

            // SMKN 2 INDRAMAYU (Bright Yellow, Same Font Size)
            ctx.fillStyle = '#fde047';
            ctx.fillText('•  SMKN 2 INDRAMAYU', textX + agenDamayWidth, currentY);

            currentY += headerFontSize + 4;

            // B. Kelas & Mapel (Larger white text)
            const titleFontSize = Math.max(17, Math.round(w * 0.020));
            ctx.font = `800 ${titleFontSize}px "Inter", "Segoe UI", sans-serif`;
            ctx.fillStyle = '#ffffff';
            const mainInfo = `${metadata.namaKelas || 'Kelas'} • ${metadata.namaMapel || 'Mata Pelajaran'}`;
            ctx.fillText(mainInfo, textX, currentY);

            currentY += titleFontSize + 4;

            // C. Pengajar & Waktu (Larger slate text)
            const now = new Date();
            const optionsDate = { day: '2-digit', month: 'short', year: 'numeric' };
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const dateStr = now.toLocaleDateString('id-ID', optionsDate);
            const timeStr = now.toLocaleTimeString('id-ID', optionsTime);

            const detailFontSize = Math.max(13, Math.round(w * 0.014));
            ctx.font = `600 ${detailFontSize}px "Inter", "Segoe UI", sans-serif`;
            ctx.fillStyle = '#e2e8f0';
            const detailText = `Pengajar: ${metadata.namaGuru || 'Guru'} | ${dateStr} - ${timeStr} WIB`;
            ctx.fillText(detailText, textX, currentY);

            currentY += detailFontSize + 3;

            // D. Tagline (Larger italic yellow text)
            const tagline = metadata.tagline || 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari';
            const taglineFontSize = Math.max(12, Math.round(w * 0.013));
            ctx.font = `italic 600 ${taglineFontSize}px "Inter", "Segoe UI", sans-serif`;
            ctx.fillStyle = '#fde047';
            ctx.fillText(`"${tagline}"`, textX, currentY);

            // Compress to Base64 JPEG
            return canvas.toDataURL('image/jpeg', 0.85);
        } catch (error) {
            throw error;
        }
    }
};

// ============================================================
// Robust Global Standalone Lightbox Photo Modal Helper
// ============================================================
window.openPhotoModal = function(photoUrl, title = 'Pratinjau Foto Watermark') {
    const existingModal = document.getElementById('global-photo-modal-overlay');
    if (existingModal) {
        existingModal.remove();
    }

    if (!photoUrl) {
        if (window.showToast) {
            window.showToast('Foto belum tersedia / belum diunggah.', 'warning');
        } else {
            alert('Foto belum tersedia / belum diunggah.');
        }
        return;
    }

    const overlay = document.createElement('div');
    overlay.id = 'global-photo-modal-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    `;

    overlay.innerHTML = `
        <div style="background: #1e293b; border-radius: 16px; width: 100%; max-width: 720px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255,255,255,0.1);" onclick="event.stopPropagation()">
            <div style="padding: 14px 18px; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155;">
                <div style="font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-image text-info"></i>
                    <span>${title}</span>
                </div>
                <button type="button" id="btn-close-photo-modal" style="background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; line-height: 1; padding: 0 4px;">&times;</button>
            </div>
            <div style="padding: 16px; text-align: center; background: #0f172a; min-height: 240px; display: flex; align-items: center; justify-content: center;">
                <div id="photo-loading-spinner" style="color: #38bdf8;">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    <span style="font-size: 0.85rem;">Memuat foto...</span>
                </div>
                <img id="photo-modal-img" src="${photoUrl}" style="max-height: 68vh; max-width: 100%; border-radius: 10px; object-fit: contain; display: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);" alt="Foto Bukti">
            </div>
            <div style="padding: 12px 18px; background: #1e293b; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #334155;">
                <a href="${photoUrl}" download style="background: #0284c7; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-download"></i> Unduh Foto Watermark
                </a>
                <button type="button" id="btn-close-photo-modal-bottom" style="background: #334155; color: #f1f5f9; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Tutup
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const img = document.getElementById('photo-modal-img');
    const spinner = document.getElementById('photo-loading-spinner');

    img.onload = function() {
        if (spinner) spinner.style.display = 'none';
        if (img) img.style.display = 'inline-block';
    };

    img.onerror = function() {
        if (spinner) {
            spinner.innerHTML = '<div style="color: #ef4444; padding: 20px;"><i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-1"></i>Foto tidak dapat dimuat atau belum tersedia di server.</div>';
        }
    };

    const closeModal = () => {
        overlay.remove();
        document.removeEventListener('keydown', handleEsc);
    };

    const handleEsc = (e) => {
        if (e.key === 'Escape') closeModal();
    };

    const btnClose1 = document.getElementById('btn-close-photo-modal');
    const btnClose2 = document.getElementById('btn-close-photo-modal-bottom');

    if (btnClose1) btnClose1.onclick = closeModal;
    if (btnClose2) btnClose2.onclick = closeModal;
    overlay.onclick = closeModal;
    document.addEventListener('keydown', handleEsc);
};

// ============================================================
// PWA Install Prompt Helper
// ============================================================
let deferredInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstallPrompt = e;

    const installBtns = document.querySelectorAll('.pwa-install-btn');
    installBtns.forEach(btn => btn.style.display = 'flex');
});

window.installPWA = function() {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        deferredInstallPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                if (window.showToast) window.showToast('Terima kasih! Aplikasi AgenDAmay telah terinstall.', 'success');
            }
            deferredInstallPrompt = null;
        });
    } else {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (isIOS) {
            alert("📱 Cara Install di iPhone/iPad (Safari):\n1. Tap tombol 'Bagikan' (Share icon) di bagian bawah browser Safari.\n2. Pilih 'Tambahkan ke Layar Utama' (Add to Home Screen).\n3. Tap 'Tambah' (Add) di sudut kanan atas.");
        } else {
            alert("📲 Cara Install Aplikasi AgenDAmay:\n1. Buka menu browser (titik 3 di kanan atas Chrome/Edge).\n2. Pilih 'Install aplikasi AgenDAmay' atau 'Tambahkan ke Layar Utama' (Add to Home Screen).");
        }
    }
};

// ============================================================
// Fullscreen Toggle Helper (Synchronous User Gesture Execution)
// ============================================================
window.toggleFullscreen = function() {
    try {
        const doc = document;
        const docEl = document.documentElement;

        const requestFS = docEl.requestFullscreen || docEl.webkitRequestFullscreen || docEl.mozRequestFullScreen || docEl.msRequestFullscreen;
        const exitFS = doc.exitFullscreen || doc.webkitExitFullscreen || doc.mozCancelFullScreen || doc.msExitFullscreen;

        const isFS = doc.fullscreenElement || doc.webkitFullscreenElement || doc.mozFullScreenElement || doc.msFullscreenElement;

        if (!isFS) {
            if (requestFS) {
                const res = requestFS.call(docEl);
                if (res && res.catch) {
                    res.catch(err => console.warn("Fullscreen warning:", err));
                }
            }
        } else {
            if (exitFS) {
                const res = exitFS.call(doc);
                if (res && res.catch) {
                    res.catch(err => console.warn("Exit Fullscreen warning:", err));
                }
            }
        }
    } catch (e) {
        console.error("Fullscreen toggle exception:", e);
    }
};

window.triggerSidebarFullscreen = function() {
    window.toggleFullscreen();
};

// ============================================================
// Browser Notification Permission Request Helper
// ============================================================
window.requestBrowserNotificationPermission = function() {
    if (!('Notification' in window)) {
        alert('Browser atau perangkat Anda tidak mendukung Web Notification API.');
        return;
    }

    if (Notification.permission === 'granted') {
        if (window.showToast) window.showToast('Alarm pengingat mengajar di browser sudah aktif!', 'success');
        return;
    }

    if (Notification.permission === 'denied') {
        alert("⚠️ Izin Notifikasi saat ini diblokir di setelan browser Anda.\n\nCara Mengaktifkan:\n1. Klik ikon Gembok 🔒 / Setelan Situs di sebelah kiri alamat URL browser.\n2. Ubah Izin 'Notifikasi' menjadi 'Izinkan' (Allow).\n3. Refresh / Muat ulang halaman ini.");
        return;
    }

    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            if (window.showToast) window.showToast('Alarm pengingat mengajar di browser berhasil diaktifkan!', 'success');
        } else if (permission === 'denied') {
            alert("⚠️ Izin notifikasi ditolak. Anda dapat mengaktifkannya kapan saja melalui ikon Gembok 🔒 di sebelah kiri URL browser.");
        }
    }).catch(err => {
        console.warn('Notification permission error:', err);
    });
};
