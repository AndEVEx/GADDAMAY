<div>
    <div class="page-header">
        <h1><i class="bi bi-camera-fill me-2"></i>Ambil Foto Bukti</h1>
        <p class="subtitle mb-0">
            {{ $agenda->guru?->name }} — {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}
        </p>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 small">
                <div class="col-6"><span class="text-muted">Guru:</span> <strong>{{ $agenda->guru?->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Mapel:</span> <strong>{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</strong></div>
                <div class="col-6"><span class="text-muted">Kelas:</span> <strong>{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}</strong></div>
                <div class="col-6"><span class="text-muted">Tanggal:</span> <strong>{{ $agenda->tanggal?->format('d/m/Y') }}</strong></div>
            </div>
        </div>
    </div>

    @if(!$uploaded)
    <div class="card mb-3">
        <div class="card-body text-center py-4">
            {{-- Camera Preview --}}
            <div class="mb-3" x-data="{ useCamera: false }" x-cloak>
                <div x-show="!useCamera">
                    {{-- File Upload Option --}}
                    <div class="border border-2 border-dashed rounded-3 p-4 mb-3" style="border-color: #dee2e6;">
                        <input type="file" wire:model="foto" accept="image/*" capture="environment"
                               class="form-control" id="foto-input">

                        @error('foto')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Preview --}}
                    @if($foto)
                    <div class="mb-3">
                        <img src="{{ $foto->temporaryUrl() }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px;">
                    </div>
                    @endif
                </div>
            </div>

            {{-- Upload Button --}}
            @if($foto)
            <button wire:click="simpanFoto" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="bi bi-cloud-upload me-2"></i>Simpan Foto</span>
                <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
            </button>
            @else
            <p class="text-muted small mb-0">
                <i class="bi bi-info-circle"></i> Ambil foto suasana kelas sebagai bukti kegiatan mengajar
            </p>
            @endif
        </div>
    </div>
    @else
    <div class="card animate-fade-in-up">
        <div class="card-body text-center py-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-3">Foto Tersimpan!</h5>
            <p class="text-muted">Foto bukti telah berhasil disimpan.</p>
            <a href="{{ route('ketua.verifikasi') }}" class="btn btn-primary" wire:navigate>
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Verifikasi
            </a>
        </div>
    </div>
    @endif
</div>
