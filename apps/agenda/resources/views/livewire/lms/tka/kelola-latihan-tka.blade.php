<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Pusat Latihan Tes Kemampuan Akademik (TKA)</h1>
            <p class="text-muted small mb-0">Kelola bank soal simulasi TKA/TPA untuk persiapan seleksi perguruan tinggi, kedinasan, & rekrutmen industri.</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="createPaket" class="btn btn-primary" style="min-height: 42px;">
                <i class="bi bi-plus-circle me-1"></i> Buat Paket Soal Baru
            </button>
            <a href="{{ route('lms.tka.simulasi') }}" class="btn btn-outline-success" style="min-height: 42px;">
                <i class="bi bi-play-circle me-1"></i> Mode Simulasi Siswa
            </a>
        </div>
    </div>

    {{-- Ringkasan Metrik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Total Paket Soal TKA</div>
                <div class="fs-4 fw-black text-primary">{{ $totalPaket }} Paket</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Total Percobaan Siswa</div>
                <div class="fs-4 fw-black text-success">{{ $totalHasil }} Selesai</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 bg-info bg-opacity-10 rounded-4 p-3 text-center">
                <div class="text-muted small fw-bold">Aksesibilitas Hasil</div>
                <div class="fs-6 fw-bold text-info">Siswa &amp; Wali Kelas (Realtime)</div>
            </div>
        </div>
    </div>

    {{-- Table Paket Soal TKA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-0">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari judul paket atau mata uji...">
                </div>
                <div class="col-md-5">
                    <select wire:model.live="filterMataUji" class="form-select">
                        <option value="">-- Semua Kategori Mata Uji --</option>
                        <option value="TPA Skolastik & Logika">TPA Skolastik &amp; Logika</option>
                        <option value="Literasi & Bahasa Inggris Vokasi">Literasi &amp; Bahasa Inggris Vokasi</option>
                        <option value="Matematika Terapan & Numerasi">Matematika Terapan &amp; Numerasi</option>
                        <option value="Kemampuan Penalaran Analitik">Kemampuan Penalaran Analitik</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Judul Paket Latihan</th>
                        <th>Kategori Mata Uji</th>
                        <th>Durasi & Target</th>
                        <th>Jumlah Butir Soal</th>
                        <th>Dikerjakan Siswa</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paketList as $p)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $p->judul_paket }}</div>
                                <div class="small text-muted">Oleh: {{ $p->guruPembuat?->name ?? 'Tim Pengembang Kurikulum' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                    {{ $p->mata_uji }}
                                </span>
                            </td>
                            <td>
                                <div class="small"><strong>{{ $p->durasi_menit }}</strong> Menit</div>
                                <div class="small text-muted">Kelas: {{ $p->target_tingkat }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                    {{ $p->soal_count }} Soal
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-2 py-1">
                                    {{ $p->hasil_siswa_count }} Siswa
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="manageSoal('{{ $p->id }}')" class="btn btn-primary btn-sm rounded-pill px-3 me-1">
                                    <i class="bi bi-collection me-1"></i> Kelola Butir Soal
                                </button>
                                <button wire:click="editPaket('{{ $p->id }}')" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-2 text-muted"></i>
                                Belum ada paket latihan TKA yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($paketList->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $paketList->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Buat/Edit Paket --}}
    @if($showPaketModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        {{ $editingPaket ? 'Edit Paket Soal TKA' : 'Buat Paket Soal Latihan TKA' }}
                    </h5>
                    <button type="button" wire:click="$set('showPaketModal', false)" class="btn-close"></button>
                </div>
                <form wire:submit="savePaket">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Judul Paket Latihan</label>
                            <input type="text" wire:model="judul_paket" class="form-control @error('judul_paket') is-invalid @enderror" placeholder="Contoh: Tryout TKA Skolastik Gelombang 1">
                            @error('judul_paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mata Uji</label>
                            <select wire:model="mata_uji" class="form-select">
                                <option value="TPA Skolastik & Logika">TPA Skolastik &amp; Logika</option>
                                <option value="Literasi & Bahasa Inggris Vokasi">Literasi &amp; Bahasa Inggris Vokasi</option>
                                <option value="Matematika Terapan & Numerasi">Matematika Terapan &amp; Numerasi</option>
                                <option value="Kemampuan Penalaran Analitik">Kemampuan Penalaran Analitik</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Durasi Pengerjaan (Menit)</label>
                                <input type="number" wire:model="durasi_menit" class="form-control" min="5" max="180">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Target Tingkat</label>
                                <select wire:model="target_tingkat" class="form-select">
                                    <option value="Semua">Semua Tingkat</option>
                                    <option value="X">Kelas X</option>
                                    <option value="XI">Kelas XI</option>
                                    <option value="XII">Kelas XII</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Deskripsi Petunjuk Pengerjaan</label>
                            <textarea wire:model="deskripsi" rows="3" class="form-control" placeholder="Tuliskan petunjuk pengerjaan soal..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showPaketModal', false)" class="btn btn-outline-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Paket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Kelola Butir Soal & Import Excel Soal --}}
    @if($showSoalModal && $activePaket)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">{{ $activePaket->judul_paket }}</h5>
                        <p class="small text-muted mb-0">Total Soal: {{ $activePaket->soal->count() }} Butir • Durasi: {{ $activePaket->durasi_menit }} Menit</p>
                    </div>
                    <button type="button" wire:click="$set('showSoalModal', false)" class="btn-close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Toolbar Soal --}}
                    <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex gap-2">
                                <button wire:click="$toggle('showTambahSoalForm')" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> {{ $showTambahSoalForm ? 'Tutup Form Manual' : 'Tambah Butir Soal' }}
                                </button>
                                <button wire:click="downloadTemplateSoal" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-download me-1"></i> Download Template Soal (.xlsx)
                                </button>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control form-control-sm" style="max-width: 250px;">
                                <button wire:click="importSoal" class="btn btn-success btn-sm" wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="importSoal"><i class="bi bi-upload me-1"></i> Import Soal</span>
                                    <span wire:loading wire:target="importSoal">Mengimpor...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Form Tambah Soal Manual --}}
                    @if($showTambahSoalForm)
                    <div class="card border border-primary rounded-4 p-3 mb-4 animate-fade-in-up">
                        <h6 class="fw-bold text-primary mb-3">Tambah Butir Soal Pilihan Ganda</h6>
                        <form wire:submit="saveSoal">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pertanyaan / Soal Kasus</label>
                                <textarea wire:model="pertanyaan" rows="3" class="form-control" placeholder="Tuliskan pertanyaan..."></textarea>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pilihan A</label>
                                    <input type="text" wire:model="pilihan_a" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pilihan B</label>
                                    <input type="text" wire:model="pilihan_b" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pilihan C</label>
                                    <input type="text" wire:model="pilihan_c" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pilihan D</label>
                                    <input type="text" wire:model="pilihan_d" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pilihan E (Opsional)</label>
                                    <input type="text" wire:model="pilihan_e" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-success">Kunci Jawaban Benar</label>
                                    <select wire:model="kunci_jawaban" class="form-select border-success">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pembahasan Soal</label>
                                <textarea wire:model="pembahasan" rows="2" class="form-control" placeholder="Penjelasan atau kunci logika jawaban..."></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" wire:click="$set('showTambahSoalForm', false)" class="btn btn-outline-secondary btn-sm">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Soal</button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Daftar Soal Dalam Paket --}}
                    <div class="space-y-3" style="max-height: 400px; overflow-y: auto;">
                        @forelse($activePaket->soal as $index => $s)
                            <div class="card border rounded-3 p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div class="fw-bold">No. {{ $index + 1 }}. {{ $s->pertanyaan }}</div>
                                    <button wire:click="deleteSoal('{{ $s->id }}')" class="btn btn-outline-danger btn-sm p-1" title="Hapus Soal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="row g-2 small mb-2">
                                    <div class="col-6 {{ $s->kunci_jawaban === 'A' ? 'fw-bold text-success' : 'text-muted' }}">A. {{ $s->pilihan_a }}</div>
                                    <div class="col-6 {{ $s->kunci_jawaban === 'B' ? 'fw-bold text-success' : 'text-muted' }}">B. {{ $s->pilihan_b }}</div>
                                    <div class="col-6 {{ $s->kunci_jawaban === 'C' ? 'fw-bold text-success' : 'text-muted' }}">C. {{ $s->pilihan_c }}</div>
                                    <div class="col-6 {{ $s->kunci_jawaban === 'D' ? 'fw-bold text-success' : 'text-muted' }}">D. {{ $s->pilihan_d }}</div>
                                    @if($s->pilihan_e && $s->pilihan_e !== '-')
                                    <div class="col-6 {{ $s->kunci_jawaban === 'E' ? 'fw-bold text-success' : 'text-muted' }}">E. {{ $s->pilihan_e }}</div>
                                    @endif
                                </div>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Kunci: {{ $s->kunci_jawaban }}</strong> @if($s->pembahasan) • Pembahasan: {{ $s->pembahasan }} @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                Belum ada soal dalam paket ini. Silakan tambah manual atau upload via Excel.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" wire:click="$set('showSoalModal', false)" class="btn btn-secondary rounded-pill px-4">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
