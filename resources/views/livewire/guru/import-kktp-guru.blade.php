<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 animate-fade-in-up">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('guru.kktp-hub') }}" class="btn btn-outline-secondary px-3" style="min-height: 44px; border-radius: 10px;" wire:navigate>
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-excel text-success me-2"></i>Import KKTP Guru</h4>
                <p class="text-muted small mb-0">Upload file Excel KKTP untuk mengisi Tujuan Pembelajaran per pertemuan secara massal</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="downloadTemplate" class="btn btn-outline-info d-flex align-items-center gap-1" style="min-height: 44px; border-radius: 10px;">
                <i class="bi bi-download"></i>
                <span class="d-none d-sm-inline">Download Template Excel (.xlsx)</span>
            </button>
            <a href="{{ route('guru.kktp-setting') }}" class="btn btn-outline-primary d-flex align-items-center gap-1" style="min-height: 44px; border-radius: 10px;" wire:navigate>
                <i class="bi bi-gear-fill"></i>
                <span>Setting KKTP</span>
            </a>
        </div>
    </div>

    @if($error)
        <div class="alert alert-danger d-flex align-items-center animate-fade-in-up mb-3" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div>{{ $error }}</div>
        </div>
    @endif

    {{-- Result Success State --}}
    @if($showResult)
        <div class="card border-0 shadow-sm border-top border-success border-4 mb-4 animate-fade-in-up" style="border-radius: 16px;">
            <div class="card-body text-center py-5">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark">Import KKTP Berhasil!</h4>
                <p class="text-muted mb-4 fs-6">
                    Sebanyak <strong>{{ $importedCount }}</strong> Tujuan Pembelajaran telah berhasil disimpan ke mata pelajaran Anda.
                </p>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <button wire:click="resetForm" class="btn btn-outline-secondary px-3" style="min-height: 46px; border-radius: 10px;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Import File Lain
                    </button>
                    <a href="{{ route('guru.kktp-setting') }}" class="btn btn-primary px-4" style="min-height: 46px; border-radius: 10px; display: inline-flex; align-items: center;" wire:navigate>
                        <i class="bi bi-gear me-1"></i> Lihat di Setting KKTP
                    </a>
                    <a href="{{ route('guru.kktp-nilai') }}" class="btn btn-success px-4" style="min-height: 46px; border-radius: 10px; display: inline-flex; align-items: center;" wire:navigate>
                        <i class="bi bi-clipboard-check me-1"></i> Nilai KKTP Murid
                    </a>
                </div>
            </div>
        </div>

    {{-- Upload State --}}
    @elseif(!$parsed)
        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm mb-3 animate-fade-in-up" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Pilih File Format KKTP</h5>
                        <p class="text-muted small mb-4">
                            Sistem mendukung format Excel resmi KKTP MGMP (termasuk kurung siku pada nama mapel dan penyusunan TP per pertemuan).
                        </p>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Upload File (.xlsx / .xls)</label>
                            <input type="file" wire:model="file" class="form-control form-control-lg @error('file') is-invalid @enderror" accept=".xlsx, .xls" style="border-radius: 10px;">
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text small mt-2">
                                <i class="bi bi-info-circle me-1"></i> File format KKTP MGMP (baris data nomor, pertemuan, CP, dan TP dimulai dari baris ke-12).
                            </div>
                        </div>

                        <button wire:click="parse" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2" style="min-height: 48px; border-radius: 10px;" wire:loading.attr="disabled" {{ empty($file) ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="parse"><i class="bi bi-search me-1"></i> Baca & Pratinjau File KKTP</span>
                            <span wire:loading wire:target="parse"><span class="spinner-border spinner-border-sm me-2"></span>Memproses dan Membaca File...</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm mb-3 bg-light animate-fade-in-up" style="border-radius: 16px;">
                    <div class="card-body p-3 p-md-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb text-warning me-1"></i> Petunjuk Import KKTP</h6>
                        <ol class="small text-muted ps-3 mb-3" style="line-height: 1.6;">
                            <li>Gunakan template KKTP yang telah dibagikan MGMP sekolah.</li>
                            <li>Baris ke-4 s/d 9 berisi informasi Mata Pelajaran, Tingkat, Kelas, dan Guru.</li>
                            <li>Baris ke-12 dan seterusnya berisi daftar Pertemuan, CP, dan TP.</li>
                            <li>Sistem otomatis mendeteksi kode mapel (seperti KKA, MTK, INF, dll.).</li>
                        </ol>
                        <button wire:click="downloadTemplate" class="btn btn-outline-primary btn-sm w-100 py-2" style="border-radius: 8px;">
                            <i class="bi bi-download me-1"></i> Unduh Template Excel Kosong
                        </button>
                    </div>
                </div>
            </div>
        </div>

    {{-- Preview State --}}
    @else
        <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-eye text-primary me-2"></i>Pratinjau Data KKTP</h5>
                    <small class="text-muted">Periksa metadata dan daftar Tujuan Pembelajaran yang terbaca</small>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2">
                    {{ count($tpData) }} Pertemuan / TP
                </span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 mb-4">
                    {{-- Metadata File --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-card-text me-1 text-primary"></i> Informasi dari File</h6>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr><td width="40%" class="text-muted">Mata Pelajaran:</td><td class="fw-bold text-dark">{{ $metadata['mapel'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Nama Guru:</td><td class="fw-bold text-dark">{{ $metadata['guru'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Tingkat / Fase:</td><td class="fw-bold text-dark">{{ $metadata['tingkat'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Kelas / Rombel:</td><td class="fw-bold text-dark">{{ $metadata['kelas'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Semester:</td><td class="fw-bold text-dark">{{ $metadata['semester'] ?? '-' }} ({{ $metadata['tahun'] ?? '-' }})</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Mapel Target Confirmation --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 h-100 border-primary bg-primary bg-opacity-10">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-check2-circle me-1"></i> Target Mata Pelajaran</h6>
                                <button type="button" wire:click="toggleAllMapels" class="btn btn-sm btn-link text-decoration-none p-0 text-primary" style="font-size: 0.75rem;">
                                    {{ $showAllMapels ? 'Mapel Saya Saja' : 'Lihat Semua Mapel' }}
                                </button>
                            </div>
                            <p class="small text-muted mb-2">Pilih mata pelajaran di database yang akan diisi dengan data KKTP ini:</p>
                            <select wire:model="selectedMapelId" class="form-select form-select-lg" style="min-height: 48px; border-radius: 10px;">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">
                                        {{ $mapel->nama_mapel }} @if(!empty($mapel->kode_mapel)) ({{ $mapel->kode_mapel }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Table of TP per Pertemuan --}}
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-list-ol me-1 text-primary"></i> Rincian TP Tiap Pertemuan ({{ count($tpData) }} Data):</h6>
                <div class="table-responsive rounded-3 border mb-4" style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 140px;">Pertemuan</th>
                                <th style="width: 100px;">Kode TP</th>
                                <th>Capaian & Tujuan Pembelajaran (CP/TP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tpData as $item)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $item['no'] ?? $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                            {{ $item['pertemuan'] ?? 'Pertemuan ' . $loop->iteration }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-white">{{ $item['kode_tp'] }}</span>
                                    </td>
                                    <td class="small text-dark" style="line-height: 1.5;">
                                        {{ $item['deskripsi_tp'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button wire:click="resetForm" class="btn btn-outline-secondary px-4" style="min-height: 46px; border-radius: 10px;">
                        <i class="bi bi-arrow-left me-1"></i> Batal & Pilih File Lain
                    </button>
                    <button wire:click="importData" class="btn btn-success px-5 d-flex align-items-center gap-2" style="min-height: 46px; border-radius: 10px;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan & Terapkan ke KKTP Saya</span>
                        <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan KKTP...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
