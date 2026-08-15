<div>
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card-custom {
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            margin-bottom: 1rem;
        }
        .btn-touch {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .form-control-touch, .form-select-touch {
            min-height: 48px;
        }
    </style>

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 animate-fade-in-up">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('guru.kktp-hub') }}" class="btn btn-outline-secondary btn-touch px-3" wire:navigate>
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0 fw-bold"><i class="bi bi-gear text-primary me-2"></i>Setting KKTP</h4>
        </div>
    </div>

    @if($mapels->count() > 1)
        <!-- Mapel Selector -->
        <div class="card card-custom animate-fade-in-up">
            <div class="card-body p-3">
                <label class="form-label text-muted small fw-bold mb-1">Pilih Mata Pelajaran</label>
                <select class="form-select form-select-touch" wire:model.live="selectedMapelId">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @elseif($mapels->count() === 1 && !$selectedMapelId)
        <div class="alert alert-info">Memuat Mata Pelajaran...</div>
    @elseif($mapels->count() === 0)
        <div class="alert alert-warning animate-fade-in-up">
            <i class="bi bi-exclamation-triangle me-2"></i>Anda belum memiliki jadwal mata pelajaran.
        </div>
    @endif

    @if($selectedMapelId)
        <!-- Import Section -->
        @if($showImport)
            <div class="card card-custom bg-light animate-fade-in-up mb-3 border">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-excel text-success me-2"></i>Import dari Excel</h5>
                        <button type="button" class="btn-close" wire:click="toggleImport"></button>
                    </div>

                    @if(!$importParsed)
                        <div class="mb-3">
                            <label class="form-label">Pilih file Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control form-control-touch" wire:model="importFile" accept=".xlsx,.xls">
                            @error('importFile') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <button type="button" class="btn btn-primary btn-touch w-100" wire:click="parseImport" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="parseImport">Baca File</span>
                            <span wire:loading wire:target="parseImport">Membaca...</span>
                        </button>
                    @else
                        <div class="alert alert-info py-2 mb-3">
                            Ditemukan <strong>{{ count($importPreview) }}</strong> Tujuan Pembelajaran.
                        </div>
                        <div class="table-responsive mb-3 bg-white rounded border">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($importPreview, 0, 5) as $preview)
                                        <tr>
                                            <td class="fw-bold">{{ $preview['kode_tp'] ?? '-' }}</td>
                                            <td class="text-truncate" style="max-width: 250px;">{{ $preview['deskripsi_tp'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(count($importPreview) > 5)
                                <div class="text-center py-2 text-muted small bg-light border-top">
                                    ... dan {{ count($importPreview) - 5 }} lainnya
                                </div>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-touch flex-grow-1" wire:click="$set('importParsed', false)">Batal</button>
                            <button type="button" class="btn btn-success btn-touch flex-grow-1" wire:click="executeImport" wire:loading.attr="disabled">
                                <i class="bi bi-cloud-upload me-2"></i>Simpan Data
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- List TP -->
        <div class="card card-custom animate-fade-in-up">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold">Daftar Tujuan Pembelajaran</h5>
                    <div class="text-muted small">Total: {{ $tps->count() }} TP</div>
                </div>
                <button type="button" class="btn btn-success btn-touch px-3 shadow-sm rounded-pill" wire:click="toggleImport">
                    <i class="bi bi-file-earmark-excel me-1"></i><span class="d-none d-md-inline">Import Excel</span>
                </button>
            </div>
            <div class="card-body p-0">
                @if($tps->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-clipboard-x display-4 text-light mb-3 d-block"></i>
                        Belum ada Tujuan Pembelajaran untuk mata pelajaran ini.
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($tps as $index => $tp)
                            <div class="list-group-item p-3 {{ $index % 2 == 0 ? 'bg-light' : '' }}">
                                @if($editingTpId === $tp->id)
                                    <!-- Edit Mode -->
                                    <div class="row g-2">
                                        <div class="col-12 col-md-3">
                                            <input type="text" class="form-control form-control-touch fw-bold" wire:model="editKodeTP" placeholder="Kode TP">
                                            @error('editKodeTP') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <textarea class="form-control" rows="2" wire:model="editDeskripsiTP" placeholder="Deskripsi TP"></textarea>
                                            @error('editDeskripsiTP') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12 col-md-2 d-flex gap-1 flex-md-column flex-row">
                                            <button class="btn btn-primary btn-sm flex-grow-1 btn-touch" wire:click="saveTpEdit"><i class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-outline-secondary btn-sm flex-grow-1 btn-touch" wire:click="cancelEdit"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                    </div>
                                @else
                                    <!-- View Mode -->
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="d-flex gap-3">
                                            <span class="badge bg-secondary rounded-circle p-2 mt-1">{{ $index + 1 }}</span>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $tp->kode_tp }}</div>
                                                <div class="text-muted small mt-1" style="white-space: pre-line;">{{ $tp->deskripsi_tp }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-nowrap gap-2">
                                            <button class="btn btn-light btn-sm text-primary shadow-sm btn-touch" style="min-height:36px; padding: 0.25rem 0.75rem;" wire:click="startEdit('{{ $tp->id }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-light btn-sm text-danger shadow-sm btn-touch" style="min-height:36px; padding: 0.25rem 0.75rem;" wire:confirm="Yakin ingin menghapus TP ini?" wire:click="deleteTp('{{ $tp->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Form -->
        <div class="card card-custom bg-light bg-gradient animate-fade-in-up border border-primary border-opacity-25 mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah TP Baru</h6>
                <form wire:submit="addTp">
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted">Kode TP</label>
                            <input type="text" class="form-control form-control-touch fw-bold" wire:model="newKodeTP" placeholder="Cth: TP-1">
                            @error('newKodeTP') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="form-label small text-muted">Deskripsi Tujuan Pembelajaran</label>
                            <textarea class="form-control" rows="2" wire:model="newDeskripsiTP" placeholder="Menjelaskan konsep dasar..."></textarea>
                            @error('newDeskripsiTP') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-touch w-100 shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addTp">Tambah</span>
                                <span wire:loading wire:target="addTp"><i class="bi bi-hourglass-split"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
