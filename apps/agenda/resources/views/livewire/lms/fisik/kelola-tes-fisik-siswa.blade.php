<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-heart-pulse-fill text-success me-2"></i>Pusat Kebugaran Jasmani & Tes Fisik Siswa</h1>
            <p class="text-muted small mb-0">Pendataan kemampuan fisik siswa oleh Guru Olahraga / PJOK. Dapat dipantau langsung oleh Wali Kelas & Siswa.</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="create" class="btn btn-success" style="min-height: 42px;">
                <i class="bi bi-plus-circle me-1"></i> Catat Tes Baru
            </button>
            <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 42px;">
                <i class="bi bi-download me-1"></i> Template Excel
            </button>
            <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 42px;">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- Kartu Metrik Kebugaran --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Total Rekam Tes</div>
                <div class="fs-4 fw-black text-primary">{{ $totalTes }} Siswa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Kategori Prima / Baik</div>
                <div class="fs-4 fw-black text-success">{{ $primaCount }} Siswa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Kategori Cukup</div>
                <div class="fs-4 fw-black text-warning text-dark">{{ $cukupCount }} Siswa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Perlu Pembinaan</div>
                <div class="fs-4 fw-black text-danger">{{ $kurangCount }} Siswa</div>
            </div>
        </div>
    </div>

    {{-- Import Section Card --}}
    <div class="card mb-4 border-success shadow-sm rounded-4">
        <div class="card-header bg-success text-white fw-bold d-flex align-items-center justify-content-between py-3">
            <span><i class="bi bi-cloud-upload me-2"></i>Import Massal Tes Fisik via Excel (.xlsx / .csv)</span>
            <span class="badge bg-white text-success">PJOK Vokasi</span>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-success bg-success bg-opacity-10 border-0 py-2 small mb-3">
                <i class="bi bi-info-circle-fill me-1"></i>
                File Excel berisikan: <strong>nis_siswa</strong>, <strong>nama_siswa</strong>, <strong>tanggal_tes</strong>, <strong>semester</strong>, <strong>tinggi_cm</strong>, <strong>berat_kg</strong>, <strong>push_up_1min</strong>, <strong>sit_up_1min</strong>, <strong>lari_1200m_detik</strong>, <strong>kelenturan_cm</strong>. Sistem otomatis menghitung BMI & Skor Kebugaran!
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control" style="min-height: 48px;">
                </div>
                <div class="col-md-3">
                    <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="importData"><i class="bi bi-upload me-1"></i> Upload Nilai Fisik</span>
                        <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-1"></span> Mengimpor...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Search Bar --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama atau NIS siswa..." style="min-height: 44px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterRombel" class="form-select" style="min-height: 44px;">
                        <option value="">-- Semua Kelas / Rombel --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterSemester" class="form-select" style="min-height: 44px;">
                        <option value="Ganjil">Semester Ganjil</option>
                        <option value="Genap">Semester Genap</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterTahun" class="form-select" style="min-height: 44px;">
                        <option value="2025/2026">2025/2026</option>
                        <option value="2024/2025">2024/2025</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Tes Fisik Siswa --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Siswa & Kelas</th>
                        <th>Tanggal & Sem</th>
                        <th>Antropometri (TB/BB/BMI)</th>
                        <th>Kekuatan (Push/Sit Up)</th>
                        <th>Cardio (1200m)</th>
                        <th>Skor & Predikat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tesList as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $t->siswa?->nama ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="small text-muted">NIS: {{ $t->siswa?->nis ?? '-' }} • <span class="badge bg-light text-dark border">{{ $t->siswa?->rombel?->nama_kelas ?? '-' }}</span></div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $t->tanggal_tes->format('d/m/Y') }}</div>
                                <div class="small text-muted">Sem. {{ $t->semester }}</div>
                            </td>
                            <td>
                                @if($t->tinggi_badan_cm && $t->berat_badan_kg)
                                    <div class="small"><strong>{{ $t->tinggi_badan_cm }}</strong> cm / <strong>{{ $t->berat_badan_kg }}</strong> kg</div>
                                    <div class="small text-muted">BMI: {{ $t->bmi }} ({{ $t->kategori_bmi }})</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">Push-up: <strong>{{ $t->push_up_1min ?? 0 }}</strong> x / mnt</div>
                                <div class="small">Sit-up: <strong>{{ $t->sit_up_1min ?? 0 }}</strong> x / mnt</div>
                            </td>
                            <td>
                                @if($t->lari_1200m_detik)
                                    <div class="small fw-bold text-primary">{{ gmdate("i:s", $t->lari_1200m_detik) }} menit</div>
                                    <div class="small text-muted">({{ $t->lari_1200m_detik }} detik)</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $predBadge = match(true) {
                                        str_contains($t->predikat, 'Prima') || str_contains($t->predikat, 'Sangat Baik') => 'bg-success',
                                        str_contains($t->predikat, 'Baik') => 'bg-primary',
                                        str_contains($t->predikat, 'Cukup') => 'bg-warning text-dark',
                                        default => 'bg-danger',
                                    };
                                @endphp
                                <div class="fs-6 fw-black text-dark">{{ $t->skor_kebugaran }} <span class="small text-muted font-normal">/ 100</span></div>
                                <span class="badge {{ $predBadge }} rounded-pill px-2 py-1 small">{{ $t->predikat }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="viewDetail('{{ $t->id }}')" class="btn btn-outline-info btn-sm rounded-pill px-3 me-1">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <button wire:click="edit('{{ $t->id }}')" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-heart-pulse fs-1 d-block mb-2 text-muted"></i>
                                Belum ada catatan tes fisik yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tesList->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $tesList->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Form Tambah / Edit Catatan Fisik --}}
    @if($showForm)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-heart-pulse text-success me-2"></i>
                        {{ $editing ? 'Edit Nilai Tes Fisik Siswa' : 'Input Tes Kebugaran Fisik Siswa' }}
                    </h5>
                    <button type="button" wire:click="$set('showForm', false)" class="btn-close"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Pilih Siswa</label>
                                <select wire:model="siswa_id" class="form-select @error('siswa_id') is-invalid @enderror">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($siswas as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} (NIS: {{ $s->nis }} • {{ $s->rombel?->nama_kelas }})</option>
                                    @endforeach
                                </select>
                                @error('siswa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Tanggal Pelaksanaan</label>
                                <input type="date" wire:model="tanggal_tes" class="form-control @error('tanggal_tes') is-invalid @enderror">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Semester</label>
                                <select wire:model="semester" class="form-select">
                                    <option value="Ganjil">Semester Ganjil</option>
                                    <option value="Genap">Semester Genap</option>
                                </select>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-rulers me-1"></i> 1. Pengukuran Antropometri (TB, BB, BMI)</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Tinggi Badan (cm)</label>
                                    <input type="number" step="0.1" wire:model="tinggi_badan_cm" class="form-control" placeholder="Contoh: 172.5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Berat Badan (kg)</label>
                                    <input type="number" step="0.1" wire:model="berat_badan_kg" class="form-control" placeholder="Contoh: 65.0">
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-lightning-charge me-1"></i> 2. Uji Kekuatan & Daya Tahan Otot</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">Push-Up (1 Menit)</label>
                                    <input type="number" wire:model="push_up_1min" class="form-control" placeholder="Jml repetisi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Sit-Up (1 Menit)</label>
                                    <input type="number" wire:model="sit_up_1min" class="form-control" placeholder="Jml repetisi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Sit & Reach / Kelenturan (cm)</label>
                                    <input type="number" step="0.1" wire:model="sit_and_reach_cm" class="form-control" placeholder="Jangkauan cm">
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-stopwatch me-1"></i> 3. Uji Kardiorespirasi / Ketahanan Jantung</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Lari 1200 Meter (Detik)</label>
                                    <input type="number" wire:model="lari_1200m_detik" class="form-control" placeholder="Waktu dalam detik, misal 330 = 5m 30s">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Shuttle Run 4x10m (Detik)</label>
                                    <input type="number" step="0.1" wire:model="shuttle_run_detik" class="form-control" placeholder="Kelincahan detik">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small">Catatan Rekomendasi Guru Olahraga</label>
                            <textarea wire:model="catatan_guru_olahraga" rows="2" class="form-control" placeholder="Komentar kebugaran untuk siswa & wali kelas..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Batal</button>
                        <button type="submit" class="btn btn-success px-4">Simpan Nilai Fisik</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Detail Rapor Kebugaran Siswa --}}
    @if($selectedTes)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg p-3">
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">{{ $selectedTes->siswa?->nama }}</h5>
                        <p class="small text-muted mb-0">NIS: {{ $selectedTes->siswa?->nis }} • Kelas: {{ $selectedTes->siswa?->rombel?->nama_kelas }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="btn-close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="card border-0 bg-success bg-opacity-10 rounded-3 p-3 text-center mb-3">
                        <span class="small text-muted fw-bold">Predikat Kebugaran Jasmani Siswa</span>
                        <div class="fs-3 fw-black text-success">{{ $selectedTes->predikat }}</div>
                        <div class="fs-6 text-dark font-monospace">Indeks Skor: {{ $selectedTes->skor_kebugaran }} / 100</div>
                    </div>

                    <div class="row g-2 text-center mb-3">
                        <div class="col-4">
                            <div class="p-2 border rounded-3 bg-light">
                                <span class="small text-muted d-block">Tinggi / Berat</span>
                                <strong class="small">{{ $selectedTes->tinggi_badan_cm ?: '-' }}cm / {{ $selectedTes->berat_badan_kg ?: '-' }}kg</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded-3 bg-light">
                                <span class="small text-muted d-block">BMI Status</span>
                                <strong class="small">{{ $selectedTes->kategori_bmi ?: '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded-3 bg-light">
                                <span class="small text-muted d-block">Lari 1200m</span>
                                <strong class="small">{{ $selectedTes->lari_1200m_detik ? gmdate("i:s", $selectedTes->lari_1200m_detik) : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Repetisi Push-up (1 menit):</span>
                            <strong>{{ $selectedTes->push_up_1min ?? 0 }} kali</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Repetisi Sit-up (1 menit):</span>
                            <strong>{{ $selectedTes->sit_up_1min ?? 0 }} kali</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Penguji (Guru Olahraga):</span>
                            <strong>{{ $selectedTes->guruOlahraga?->name ?? 'Guru PJOK' }}</strong>
                        </li>
                    </ul>

                    @if($selectedTes->catatan_guru_olahraga)
                    <div class="alert alert-light border rounded-3 small mb-0">
                        <strong>Catatan Instruktur:</strong><br>
                        {{ $selectedTes->catatan_guru_olahraga }}
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" wire:click="closeDetail" class="btn btn-secondary w-100 rounded-pill">Tutup Rapor</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
