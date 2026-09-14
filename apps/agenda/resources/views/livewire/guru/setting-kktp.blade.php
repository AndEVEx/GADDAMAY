<div>
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.35s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card-custom {
            border-radius: 14px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.06);
            border: none;
            margin-bottom: 1rem;
        }
        .btn-touch {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
    </style>

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 animate-fade-in-up">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('guru.kktp-hub') }}" class="btn btn-outline-secondary btn-touch px-3" wire:navigate>
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold"><i class="bi bi-gear text-primary me-2"></i>Setting KKTP / Tujuan Pembelajaran</h4>
                <p class="text-muted small mb-0">Kelola TP per pertemuan untuk setiap mata pelajaran yang diampu</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($selectedMapelId)
                <button type="button" class="btn btn-success btn-touch px-3" wire:click="toggleImport">
                    <i class="bi bi-file-earmark-excel me-1"></i> Import Excel KKTP
                </button>
            @endif
        </div>
    </div>

    <!-- Mapel Selector -->
    <div class="card card-custom animate-fade-in-up mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <label class="form-label text-muted small fw-bold mb-0">
                    <i class="bi bi-book me-1"></i> Pilih Mata Pelajaran
                </label>
                <button type="button" wire:click="toggleAllMapels" class="btn btn-sm btn-link text-decoration-none p-0 text-primary">
                    <i class="bi {{ $showAllMapels ? 'bi-filter' : 'bi-collection' }} me-1"></i>
                    {{ $showAllMapels ? 'Tampilkan Mapel Saya Saja' : 'Tampilkan Semua Mapel Sekolah' }}
                </button>
            </div>
            <select class="form-select form-select-lg" wire:model.live="selectedMapelId" style="min-height: 48px; border-radius: 10px;">
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($mapels as $mapel)
                    <option value="{{ $mapel->id }}">
                        {{ $mapel->nama_mapel }} @if(!empty($mapel->kode_mapel)) ({{ $mapel->kode_mapel }}) @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedMapelId)
        {{-- Tingkat Selector --}}
        <div class="card card-custom animate-fade-in-up mb-3">
            <div class="card-body p-3">
                <label class="form-label text-muted small fw-bold mb-2">
                    <i class="bi bi-layers me-1"></i> Pilih Tingkat / Jenjang Kelas
                </label>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" wire:click="$set('selectedTingkat', null)" 
                            class="btn {{ is_null($selectedTingkat) ? 'btn-primary' : 'btn-outline-primary' }} btn-touch px-3">
                        Semua Tingkat
                    </button>
                    @foreach([10 => 'X', 11 => 'XI', 12 => 'XII'] as $val => $label)
                        <button type="button" wire:click="$set('selectedTingkat', {{ $val }})" 
                                class="btn {{ $selectedTingkat === $val ? 'btn-primary' : 'btn-outline-primary' }} btn-touch px-4">
                            Tingkat {{ $label }}
                        </button>
                    @endforeach
                </div>
                @if($selectedTingkat)
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-info-circle me-1"></i> Menampilkan paket KKTP untuk {{ $currentMapel?->nama_mapel }} — Tingkat {{ match($selectedTingkat) { 10 => 'X', 11 => 'XI', 12 => 'XII', default => $selectedTingkat } }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Import Section -->
        <!-- Import Section -->
        @if($showImport)
            <div class="card card-custom bg-light animate-fade-in-up mb-3 border border-success">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold text-success"><i class="bi bi-cloud-upload me-2"></i>Import KKTP dari File Excel</h5>
                        <button type="button" class="btn-close" wire:click="toggleImport"></button>
                    </div>

                    @if(!$importParsed)
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih file format KKTP (.xlsx, .xls)</label>
                            <input type="file" class="form-control" wire:model="importFile" accept=".xlsx,.xls" style="min-height: 48px;">
                            @error('importFile') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="form-text small">Upload file format KKTP MGMP (baris data dimulai dari baris ke-12).</div>
                        </div>
                        <button type="button" class="btn btn-primary btn-touch w-100" wire:click="parseImport" wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="parseImport"><i class="bi bi-search me-1"></i> Baca & Pratinjau File</span>
                            <span wire:loading wire:target="parseImport"><span class="spinner-border spinner-border-sm me-2"></span>Membaca file...</span>
                        </button>
                    @else
                        <!-- Preview Metadata -->
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Informasi File Terbaca:</div>
                            <div class="row g-1">
                                <div class="col-sm-6"><strong>Mata Pelajaran:</strong> {{ $importMetadata['mapel'] ?? '-' }}</div>
                                <div class="col-sm-6"><strong>Guru:</strong> {{ $importMetadata['guru'] ?? '-' }}</div>
                                <div class="col-sm-6"><strong>Kelas/Tingkat:</strong> {{ $importMetadata['kelas'] ?? '-' }} ({{ $importMetadata['tingkat'] ?? '-' }})</div>
                                <div class="col-sm-6"><strong>Total TP Terbaca:</strong> {{ count($importPreview) }} Pertemuan / Item</div>
                            </div>
                        </div>

                        <!-- Preview Table -->
                        <div class="table-responsive mb-3 bg-white rounded border" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 80px;">No</th>
                                        <th style="width: 140px;">Pertemuan</th>
                                        <th>Capaian & Tujuan Pembelajaran (CP/TP)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($importPreview as $preview)
                                        <tr>
                                            <td class="fw-bold">{{ $preview['no'] ?? $loop->iteration }}</td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $preview['pertemuan'] ?? 'Pertemuan ' . $loop->iteration }}</span></td>
                                            <td class="small">{{ $preview['deskripsi_tp'] ?? $preview['tp'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-touch flex-grow-1" wire:click="$set('importParsed', false)">Batal</button>
                            <button type="button" class="btn btn-success btn-touch flex-grow-1" wire:click="executeImport" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="executeImport"><i class="bi bi-cloud-upload me-1"></i> Simpan {{ count($importPreview) }} TP ke Sistem</span>
                                <span wire:loading wire:target="executeImport"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TP List Card -->
        <div class="card card-custom animate-fade-in-up">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-list-check text-primary me-2"></i>Daftar TP: {{ $currentMapel?->nama_mapel }}
                    </h5>
                    <small class="text-muted">Total {{ $tps->count() }} Tujuan Pembelajaran terdaftar</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2">
                    {{ $tps->count() }} TP
                </span>
            </div>
            <div class="card-body p-0">
                @if($tps->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                        <p class="mb-2 fw-semibold">Belum ada Tujuan Pembelajaran untuk mata pelajaran ini.</p>
                        <p class="small text-muted mb-3">Silakan gunakan fitur <strong>Import Excel</strong> di atas atau input manual di bawah.</p>
                        <button type="button" class="btn btn-success btn-touch px-4" wire:click="toggleImport">
                            <i class="bi bi-file-earmark-excel me-1"></i> Import KKTP dari Excel
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">No</th>
                                    <th style="width: 120px;">Kode</th>
                                    <th>Deskripsi Tujuan Pembelajaran</th>
                                    <th style="width: 110px;" class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tps as $tp)
                                    @if($editingTpId === $tp->id)
                                        <tr class="table-warning">
                                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" wire:model="editKodeTP">
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm" rows="2" wire:model="editDeskripsiTP"></textarea>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-success" wire:click="saveTpEdit" title="Simpan"><i class="bi bi-check-lg"></i></button>
                                                    <button type="button" class="btn btn-secondary" wire:click="cancelEdit" title="Batal"><i class="bi bi-x-lg"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-primary text-white px-2 py-1">{{ $tp->kode_tp }}</span>
                                            </td>
                                            <td>
                                                <div class="small text-dark" style="line-height: 1.5;">{{ $tp->deskripsi_tp }}</div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" wire:click="startEdit('{{ $tp->id }}')" title="Edit"><i class="bi bi-pencil"></i></button>
                                                    <button type="button" class="btn btn-outline-danger" wire:confirm="Yakin ingin menghapus TP ini?" wire:click="deleteTp('{{ $tp->id }}')" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Manual TP Card -->
        <div class="card card-custom animate-fade-in-up">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah TP Baru Manual</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <form wire:submit.prevent="addTp">
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Kode TP / Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="newKodeTP" placeholder="Contoh: TP-01" style="min-height: 44px;">
                            @error('newKodeTP') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">Deskripsi Tujuan Pembelajaran <span class="text-danger">*</span></label>
                            <textarea class="form-control" wire:model="newDeskripsiTP" rows="2" placeholder="Contoh: [Pertemuan 1] Murid dapat menerapkan berpikir komputasional..."></textarea>
                            @error('newDeskripsiTP') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-touch px-4" wire:loading.attr="disabled">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Tujuan Pembelajaran
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
