<div>
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-excel me-2"></i>Import KKTP (Excel)</h1>
        <p class="subtitle mb-0">Import data Kriteria Ketercapaian Tujuan Pembelajaran</p>
    </div>

    @if($error)
        <div class="alert alert-danger d-flex align-items-center animate-fade-in-up" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div>{{ $error }}</div>
        </div>
    @endif

    @if($showResult)
        <div class="card border-success mb-4 animate-fade-in-up">
            <div class="card-body text-center py-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h4 class="mt-3">Import Berhasil!</h4>
                <p class="text-muted mb-4">{{ $importedCount }} Tujuan Pembelajaran telah ditambahkan atau diperbarui.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button wire:click="resetForm" class="btn btn-outline-secondary" style="min-height: 48px;">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Import Lagi
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="min-height: 48px; display: inline-flex; align-items: center;" wire:navigate>
                        <i class="bi bi-house me-2"></i>Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    @elseif(!$parsed)
        <div class="card mb-4 animate-fade-in-up">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 48px;">
                        <i class="bi bi-download me-2"></i>Download Template KKTP (.xlsx)
                    </button>
                    <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel (.xlsx)
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih File Excel (.xlsx)</label>
                    <input type="file" wire:model="file" class="form-control form-control-lg" accept=".xlsx, .xls">
                    <div class="form-text mt-2">
                        Upload file Excel sesuai dengan format template KKTP. 
                        Pastikan data TP/CP dimulai pada baris ke-12.
                    </div>
                </div>

                <button wire:click="parse" class="btn btn-primary btn-lg w-100" style="min-height: 48px;" wire:loading.attr="disabled" {{ empty($file) ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="parse"><i class="bi bi-search me-2"></i>Pratinjau Data</span>
                    <span wire:loading wire:target="parse"><span class="spinner-border spinner-border-sm me-2"></span>Memproses File...</span>
                </button>
            </div>
        </div>
    @else
        <div class="card mb-4 animate-fade-in-up">
            <div class="card-header bg-primary bg-opacity-10">
                <h6 class="mb-0 fw-bold"><i class="bi bi-eye me-2"></i>Pratinjau Data Import</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded h-100">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Metadata</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-muted">Mata Pelajaran</td><td class="fw-bold">{{ $metadata['mapel'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Guru</td><td class="fw-bold">{{ $metadata['guru'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Tingkat/Fase</td><td class="fw-bold">{{ $metadata['tingkat'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Kelas</td><td class="fw-bold">{{ $metadata['kelas'] ?? '-' }}</td></tr>
                                <tr><td class="text-muted">Total TP</td><td class="fw-bold text-primary">{{ count($tpData) }} item</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100 border-primary">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Konfirmasi Mata Pelajaran</h6>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Sistem mencoba mencocokkan otomatis, silakan verifikasi:</label>
                                <select wire:model="selectedMapelId" class="form-select form-select-lg">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($mapels as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Daftar Tujuan Pembelajaran</h6>
                <div class="table-responsive rounded border mb-4" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="10%">No</th>
                                <th width="15%">Pertemuan</th>
                                <th width="35%">Capaian Pembelajaran (CP)</th>
                                <th width="40%">Tujuan Pembelajaran (TP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tpData as $index => $item)
                                <tr>
                                    <td>{{ $item['no'] }}</td>
                                    <td>{{ $item['pertemuan'] }}</td>
                                    <td><small>{{ $item['cp'] }}</small></td>
                                    <td><strong>{{ $item['tp'] }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Tidak ada data TP/CP yang ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <button wire:click="resetForm" class="btn btn-outline-secondary w-25" style="min-height: 48px;">Batal</button>
                    <button wire:click="importData" class="btn btn-primary w-75" style="min-height: 48px;" wire:loading.attr="disabled" {{ empty($selectedMapelId) || empty($tpData) ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-arrow-up me-2"></i>Import Sekarang</span>
                        <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Data...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
