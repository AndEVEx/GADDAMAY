<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-person-workspace text-primary me-2"></i>Tugas Tambahan Guru & PTK</h1>
            <p class="text-muted small mb-0">Kelola & impor penugasan Wali Kelas, Pembina Kesiswaan, Guru BK, Guru Piket, dan Koordinator Literasi.</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="create" class="btn btn-primary" style="min-height: 42px;">
                <i class="bi bi-plus-circle me-1"></i> Tambah Penugasan
            </button>
            <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 42px;">
                <i class="bi bi-download me-1"></i> Template Excel
            </button>
            <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 42px;">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- Ringkasan Metrik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Wali Kelas</div>
                <div class="fs-4 fw-black text-primary">{{ $totalWali }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Guru BK</div>
                <div class="fs-4 fw-black text-success">{{ $totalBk }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 bg-warning bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Guru Piket</div>
                <div class="fs-4 fw-black text-warning text-dark">{{ $totalPiket }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 bg-info bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Kesiswaan / OSIS</div>
                <div class="fs-4 fw-black text-info">{{ $totalKesiswaan }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 bg-danger bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Koordinator Literasi</div>
                <div class="fs-4 fw-black text-danger">{{ $totalLiterasi }}</div>
            </div>
        </div>
    </div>

    {{-- Import Card Section --}}
    <div class="card mb-4 border-primary shadow-sm rounded-4">
        <div class="card-header bg-primary text-white fw-bold d-flex align-items-center justify-content-between py-3">
            <span><i class="bi bi-cloud-upload me-2"></i>Import Tugas Tambahan Guru via Excel (.xlsx / .csv)</span>
            <span class="badge bg-white text-primary">Massal</span>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info py-2 small mb-3">
                <i class="bi bi-info-circle-fill me-1"></i>
                File Excel harus memuat kolom: <strong>email_guru</strong>, <strong>nama_guru</strong>, <strong>jenis_tugas</strong> (wali_kelas, pembina_kesiswaan, guru_bk, guru_piket, koordinator_literasi), <strong>kelas_rombel</strong> (opsional/untuk wali kelas), <strong>tahun_ajaran</strong>, <strong>sk_penugasan</strong>, dan <strong>keterangan</strong>.
            </div>

            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control" style="min-height: 48px;">
                </div>
                <div class="col-md-3">
                    <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="importData"><i class="bi bi-upload me-1"></i> Proses Import</span>
                        <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-1"></span> Mengimpor...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Table --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama guru atau email..." style="min-height: 44px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterJenis" class="form-select" style="min-height: 44px;">
                        <option value="">-- Semua Jenis Tugas --</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="pembina_kesiswaan">Pembina Kesiswaan</option>
                        <option value="guru_bk">Guru BK</option>
                        <option value="guru_piket">Guru Piket</option>
                        <option value="koordinator_literasi">Koordinator Literasi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterTahun" class="form-select" style="min-height: 44px;">
                        <option value="2025/2026">Tahun Ajaran 2025/2026</option>
                        <option value="2024/2025">Tahun Ajaran 2024/2025</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Guru / Pendidik</th>
                        <th>Jenis Tugas Tambahan</th>
                        <th>Rombel / Kelas Binaan</th>
                        <th>Tahun Ajaran</th>
                        <th>Nomor SK Penugasan</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasList as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $t->guru?->name ?? 'Guru Tidak Ditemukan' }}</div>
                                <div class="small text-muted">{{ $t->guru?->email ?? '-' }}</div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($t->jenis_tugas) {
                                        'wali_kelas' => 'bg-primary',
                                        'guru_bk' => 'bg-success',
                                        'guru_piket' => 'bg-warning text-dark',
                                        'pembina_kesiswaan' => 'bg-info text-dark',
                                        'koordinator_literasi' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">
                                    {{ \App\Models\TugasTambahanGuru::getLabelJenisTugas($t->jenis_tugas) }}
                                </span>
                            </td>
                            <td>
                                @if($t->rombel)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-door-open-fill text-primary me-1"></i>{{ $t->rombel->nama_kelas }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td><span class="small font-monospace">{{ $t->tahun_ajaran }}</span></td>
                            <td><span class="small text-muted">{{ $t->sk_penugasan ?: '-' }}</span></td>
                            <td>
                                @if($t->is_active)
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="edit('{{ $t->id }}')" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <button wire:click="confirmDeleteTugas('{{ $t->id }}')" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                Belum ada data tugas tambahan guru yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tugasList->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $tugasList->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Form Tambah/Edit --}}
    @if($showForm)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        {{ $editing ? 'Edit Tugas Tambahan' : 'Tambah Tugas Tambahan Guru' }}
                    </h5>
                    <button type="button" wire:click="$set('showForm', false)" class="btn-close"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih Guru / PTK</label>
                            <select wire:model="guru_id" class="form-select @error('guru_id') is-invalid @enderror">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                                @endforeach
                            </select>
                            @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Jenis Tugas Tambahan</label>
                            <select wire:model.live="jenis_tugas" class="form-select @error('jenis_tugas') is-invalid @enderror">
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="pembina_kesiswaan">Pembina Kesiswaan (OSIS/MPK)</option>
                                <option value="guru_bk">Guru BK (Bimbingan Konseling)</option>
                                <option value="guru_piket">Guru Piket Sekolah</option>
                                <option value="koordinator_literasi">Koordinator Literasi (GLS)</option>
                                <option value="pembina_ekskul">Pembina Ekstrakurikuler</option>
                                <option value="kepala_lab">Kepala Laboratorium / Bengkel</option>
                            </select>
                            @error('jenis_tugas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($jenis_tugas === 'wali_kelas' || $jenis_tugas === 'guru_bk')
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Kelas / Rombel Binaan</label>
                            <select wire:model="rombel_id" class="form-select @error('rombel_id') is-invalid @enderror">
                                <option value="">-- Pilih Rombel --</option>
                                @foreach($rombels as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_kelas }} (Tingkat {{ $r->tingkat }})</option>
                                @endforeach
                            </select>
                            @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Tahun Ajaran</label>
                                <input type="text" wire:model="tahun_ajaran" class="form-control" placeholder="2025/2026">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Nomor SK Penugasan</label>
                                <input type="text" wire:model="sk_penugasan" class="form-control" placeholder="SK/01/GTK/2025">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Keterangan Tambahan</label>
                            <textarea wire:model="keterangan" rows="2" class="form-control" placeholder="Catatan tugas..."></textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveCheck">
                            <label class="form-check-label fw-semibold" for="isActiveCheck">Status Penugasan Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if($confirmDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Hapus Penugasan Ini?</h6>
                    <p class="small text-muted mb-3">{{ $deleteName }}</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button wire:click="deleteTugas" class="btn btn-danger btn-sm px-3">Ya, Hapus</button>
                        <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary btn-sm px-3">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
