<div>
    <div class="page-header mb-3">
        <h1><i class="bi bi-camera-fill me-2"></i>Ambil Foto Bukti Agenda</h1>
        <p class="subtitle mb-0">
            {{ $agenda->guru?->name }} &bull; {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} ({{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }})
        </p>
    </div>

    {{-- Metadata Info --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body py-2 px-3">
            <div class="row g-2 small">
                <div class="col-6"><span class="text-muted">Guru:</span> <strong>{{ $agenda->guru?->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Mapel:</span> <strong>{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</strong></div>
                <div class="col-6"><span class="text-muted">Kelas:</span> <strong>{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}</strong></div>
                <div class="col-6"><span class="text-muted">Tanggal:</span> <strong>{{ $agenda->tanggal?->format('d/m/Y') }}</strong></div>
            </div>
        </div>
    </div>

    @if(!$uploaded)
    <div class="card mb-3 animate-fade-in-up" x-data="pwaCameraComponent()" x-init="startCamera()">
        <div class="card-body text-center p-3">

            {{-- Mode Camera Preview --}}
            <div x-show="mode === 'camera'">
                <div class="position-relative overflow-hidden rounded-3 mb-3 bg-dark shadow-sm" style="min-height: 250px;">
                    <video x-ref="video" autoplay playsinline class="w-100 h-100" style="max-height: 380px; object-fit: cover;"></video>
                    
                    <div x-show="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-white text-center">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        <span class="small">Membuka kamera...</span>
                    </div>

                    {{-- Camera Watermark Overlay Tag --}}
                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-start bg-dark bg-opacity-50 text-white" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-check text-info me-1"></i>Watermark Otomatis Aktif
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" @click="captureWatermarkPhoto()" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" style="border-radius: 12px;" :disabled="processing">
                        <template x-if="!processing">
                            <span><i class="bi bi-camera-fill me-2 fs-5"></i>Tangkap Foto & Watermark</span>
                        </template>
                        <template x-if="processing">
                            <span><span class="spinner-border spinner-border-sm me-2"></span>Memproses Watermark...</span>
                        </template>
                    </button>

                    <div class="d-flex gap-2 mt-1">
                        <button type="button" @click="switchCamera()" class="btn btn-outline-secondary flex-fill" style="min-height: 44px; border-radius: 10px;">
                            <i class="bi bi-arrow-repeat me-1"></i>Ganti Kamera
                        </button>
                        <button type="button" @click="mode = 'file'; stopCamera();" class="btn btn-outline-secondary flex-fill" style="min-height: 44px; border-radius: 10px;">
                            <i class="bi bi-folder2-open me-1"></i>Pilih dari Galeri
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mode File Upload Fallback --}}
            <div x-show="mode === 'file'" style="display: none;">
                <div class="border border-2 border-dashed rounded-3 p-4 mb-3" style="border-color: #dee2e6;">
                    <i class="bi bi-image fs-1 text-muted mb-2 d-block"></i>
                    <label class="form-label fw-bold">Pilih Foto dari Perangkat</label>
                    <input type="file" wire:model="foto" accept="image/*" class="form-control mb-2" id="foto-file-input">
                    @error('foto') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                @if($foto)
                <div class="mb-3">
                    <img src="{{ $foto->temporaryUrl() }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px;" id="preview-file-img">
                </div>
                <button type="button" @click="watermarkFileImage()" class="btn btn-primary btn-lg w-100 fw-bold" style="border-radius: 12px;">
                    <i class="bi bi-cloud-upload me-2"></i>Simpan Foto Bukti
                </button>
                @endif

                <button type="button" @click="mode = 'camera'; startCamera();" class="btn btn-outline-primary w-100 mt-2" style="min-height: 44px; border-radius: 10px;">
                    <i class="bi bi-camera me-1"></i>Kembali ke Kamera Live
                </button>
            </div>

            {{-- Watermarked Image Result Canvas Preview --}}
            <div x-show="previewBase64" class="mt-3" style="display: none;">
                <h6 class="fw-bold mb-2">Pratinjau Hasil Watermark:</h6>
                <img :src="previewBase64" class="img-fluid rounded-3 shadow mb-3" style="max-height: 350px;">
            </div>

        </div>
    </div>
    @else
    <div class="card animate-fade-in-up">
        <div class="card-body text-center py-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Foto Bukti Tersimpan!</h4>
            <p class="text-muted small mb-4">Agenda kelas {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }} resmi dimulai.</p>
            <a href="{{ route('ketua.verifikasi') }}" class="btn btn-primary btn-lg px-4 shadow-sm" style="border-radius: 12px;" wire:navigate>
                <i class="bi bi-check2-circle me-2"></i>Selesai / Verifikasi Baru
            </a>
        </div>
    </div>
    @endif
</div>

<script>
function pwaCameraComponent() {
    return {
        mode: 'camera',
        facingMode: 'environment',
        stream: null,
        cameraLoading: false,
        processing: false,
        previewBase64: '',

        async startCamera() {
            this.cameraLoading = true;
            try {
                if (this.stream) {
                    this.stopCamera();
                }
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
                });
                const videoEl = this.$refs.video;
                if (videoEl) {
                    videoEl.srcObject = this.stream;
                    await videoEl.play();
                }
            } catch (err) {
                console.warn('Camera stream error:', err);
                this.mode = 'file';
            } finally {
                this.cameraLoading = false;
            }
        },

        async switchCamera() {
            this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
            await this.startCamera();
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
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
                    namaGuru: '{{ $agenda->guru?->name ?? "Guru" }}',
                    tagline: 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari'
                };

                const base64Image = await window.WatermarkCamera.processAndWatermark(this.$refs.video, metadata);
                this.previewBase64 = base64Image;

                // Submit to Livewire
                @this.simpanFotoBase64(base64Image);
            } catch (err) {
                alert('Gagal memproses watermark foto: ' + err.message);
            } finally {
                this.processing = false;
            }
        },

        async watermarkFileImage() {
            const previewImg = document.getElementById('preview-file-img');
            if (!previewImg) {
                @this.simpanFoto();
                return;
            }

            try {
                const metadata = {
                    namaSekolah: 'SMKN 2 INDRAMAYU',
                    namaKelas: '{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas ?? "Kelas" }}',
                    namaMapel: '{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? "Mapel" }}',
                    namaGuru: '{{ $agenda->guru?->name ?? "Guru" }}',
                    tagline: 'Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari'
                };

                const base64Image = await window.WatermarkCamera.processAndWatermark(previewImg, metadata);
                @this.simpanFotoBase64(base64Image);
            } catch (err) {
                @this.simpanFoto();
            }
        }
    }
}
</script>
