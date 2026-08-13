<div>
    <div class="page-header mb-3">
        <h1><i class="bi bi-person-bounding-box me-2"></i>Foto Guru & Suasana Kelas</h1>
        <p class="subtitle mb-0">
            Ambil foto suasana kelas / selfie bersama siswa sebelum Stopwatch & Prompter dimulai
        </p>
    </div>

    {{-- Metadata Info --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body py-2 px-3">
            <div class="row g-2 small">
                <div class="col-6"><span class="text-muted">Guru:</span> <strong>{{ auth()->user()->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Mapel:</span> <strong>{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</strong></div>
                <div class="col-6"><span class="text-muted">Kelas:</span> <strong>{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}</strong></div>
                <div class="col-6"><span class="text-muted">Tanggal:</span> <strong>{{ $agenda->tanggal?->format('d/m/Y') }}</strong></div>
            </div>
        </div>
    </div>

    <div class="card mb-3 animate-fade-in-up" x-data="guruCameraComponent()" x-init="initComponent()">
        <div class="card-body text-center p-3">

            {{-- Camera Stream Mode --}}
            <div x-show="mode === 'camera'">
                <div class="position-relative overflow-hidden rounded-3 mb-3 bg-dark shadow-sm" style="min-height: 250px;">
                    <video x-ref="video" autoplay playsinline class="w-100 h-100" style="max-height: 380px; object-fit: cover;"></video>
                    
                    <div x-show="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-white text-center">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        <span class="small">Membuka kamera...</span>
                    </div>

                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-start bg-dark bg-opacity-50 text-white" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-check text-info me-1"></i>Watermark Guru Otomatis Aktif
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" @click="captureWatermarkPhoto()" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" style="border-radius: 12px;" :disabled="processing">
                        <template x-if="!processing">
                            <span><i class="bi bi-camera-fill me-2 fs-5"></i>Tangkap Foto Guru & Watermark</span>
                        </template>
                        <template x-if="processing">
                            <span><span class="spinner-border spinner-border-sm me-2"></span>Memproses Watermark...</span>
                        </template>
                    </button>

                    <div class="d-flex gap-2 mt-1">
                        <button type="button" @click="switchCamera()" class="btn btn-outline-secondary flex-fill" style="min-height: 44px; border-radius: 10px;">
                            <i class="bi bi-arrow-repeat me-1"></i>Ganti Kamera
                        </button>
                        <button type="button" @click="triggerNativeCamera()" class="btn btn-outline-primary flex-fill" style="min-height: 44px; border-radius: 10px;">
                            <i class="bi bi-camera2 me-1"></i>Kamera HP Native
                        </button>
                    </div>

                    {{-- Native Camera Hidden Input --}}
                    <input type="file" x-ref="nativeInput" accept="image/*" capture="user" class="d-none" @change="handleNativeCapture($event)">

                    <a href="{{ route('guru.kehadiran', $agenda->id) }}" class="btn btn-link text-muted small mt-2 text-decoration-none" wire:navigate>
                        Lewati & Lanjut ke Presensi Siswa <i class="bi bi-arrow-right me-1"></i>
                    </a>
                </div>
            </div>

            {{-- Camera Error Notice if blocked --}}
            <div x-show="mode === 'error'" style="display: none;" class="alert alert-warning text-center py-4">
                <i class="bi bi-camera-video-off fs-1 text-warning d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Akses Kamera Diperlukan</h6>
                <p class="small text-muted mb-3">Browser membutuhkan izin untuk membuka kamera. Tekan tombol di bawah untuk membuka kamera live atau menggunakan kamera HP langsung.</p>
                <div class="d-grid gap-2 col-12 col-md-8 mx-auto">
                    <button type="button" @click="mode = 'camera'; startCamera();" class="btn btn-primary fw-semibold py-2" style="border-radius: 10px;">
                        <i class="bi bi-camera-fill me-2"></i>Buka Kamera Live Sekarang
                    </button>
                    <button type="button" @click="triggerNativeCamera()" class="btn btn-outline-dark fw-semibold py-2" style="border-radius: 10px;">
                        <i class="bi bi-camera2 me-2"></i>Buka Kamera HP Perangkat
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function guruCameraComponent() {
    return {
        mode: 'camera',
        facingMode: 'user',
        stream: null,
        cameraLoading: false,
        processing: false,

        initComponent() {
            this.startCamera();
            window.addEventListener('beforeunload', () => this.stopCamera());
            document.addEventListener('livewire:navigating', () => this.stopCamera());
        },

        destroy() {
            this.stopCamera();
        },

        async startCamera() {
            this.mode = 'camera';
            this.cameraLoading = true;
            try {
                this.stopCamera();

                // Try 1: Ideal constraints
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: this.facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
                    });
                } catch (e1) {
                    // Try 2: Simple facingMode
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: this.facingMode }
                        });
                    } catch (e2) {
                        // Try 3: Basic video true
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    }
                }

                const videoEl = this.$refs.video;
                if (videoEl && this.stream) {
                    videoEl.srcObject = this.stream;
                    await videoEl.play();
                }
            } catch (err) {
                console.warn('Camera stream error:', err);
                this.mode = 'error';
            } finally {
                this.cameraLoading = false;
            }
        },

        async switchCamera() {
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            await this.startCamera();
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => {
                    track.stop();
                    try { this.stream.removeTrack(track); } catch (e) {}
                });
                this.stream = null;
            }
            if (this.$refs && this.$refs.video) {
                this.$refs.video.srcObject = null;
            }
        },

        triggerNativeCamera() {
            if (this.$refs.nativeInput) {
                this.$refs.nativeInput.click();
            }
        },

        async handleNativeCapture(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.processing = true;
            try {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                await new Promise((resolve) => img.onload = resolve);

                const metadata = {
                    namaSekolah: 'SMKN 2 INDRAMAYU',
                    namaKelas: '{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas ?? "Kelas" }}',
                    namaMapel: '{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? "Mapel" }}',
                    namaGuru: '{{ auth()->user()->name ?? "Guru" }}',
                    tagline: 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari'
                };

                const base64Image = await window.WatermarkCamera.processAndWatermark(img, metadata);
                URL.revokeObjectURL(img.src);
                @this.simpanFotoBase64(base64Image);
            } catch (err) {
                alert('Gagal memproses foto: ' + err.message);
            } finally {
                this.processing = false;
            }
        },

        async captureWatermarkPhoto() {
            if (!this.$refs.video) return;
            this.processing = true;

            try {
                const metadata = {
                    namaSekolah: 'SMKN 2 INDRAMAYU',
                    namaKelas: '{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas ?? "Kelas" }}',
                    namaMapel: '{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? "Mapel" }}',
                    namaGuru: '{{ auth()->user()->name ?? "Guru" }}',
                    tagline: 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari'
                };

                const base64Image = await window.WatermarkCamera.processAndWatermark(this.$refs.video, metadata);
                this.stopCamera();
                @this.simpanFotoBase64(base64Image);
            } catch (err) {
                alert('Gagal memproses watermark foto: ' + err.message);
            } finally {
                this.processing = false;
            }
        }
    }
}
</script>
