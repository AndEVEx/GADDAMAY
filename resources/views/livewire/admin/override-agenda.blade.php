<div>
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-shield-lock-fill me-2"></i>Override Agenda Harian</h1>
        <a href="{{ auth()->user()->role === 'waka' ? route('waka.koreksi') : route('admin.koreksi') }}" class="btn btn-outline-secondary" style="min-height: 48px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($agenda)
    {{-- Agenda Detail Card --}}
    <div class="card mb-3 border-info animate-fade-in-up">
        <div class="card-header bg-info text-dark fw-bold">
            <i class="bi bi-info-circle me-2"></i>Detail Agenda #{{ substr($agenda->id, 0, 8) }}
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-bold">{{ $agenda->tanggal ? $agenda->tanggal->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Kelas</div>
                    <div class="fw-bold">{{ $agenda->jadwalPelajaran->rombel->nama_kelas ?? '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Mata Pelajaran</div>
                    <div class="fw-bold">{{ $agenda->jadwalPelajaran->mataPelajaran->nama_mapel ?? ($agenda->jadwalPelajaran->kegiatan_khusus ?? '-') }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Guru Utama</div>
                    <div class="fw-bold">{{ $agenda->guru->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Override Form --}}
    <div class="card border-primary animate-fade-in-up mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-pencil-square me-2"></i>Form Override Data Agenda
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Status Kehadiran Guru</label>
                        <select wire:model="statusKehadiranGuru" class="form-select @error('statusKehadiranGuru') is-invalid @enderror" style="min-height: 48px;">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                        @error('statusKehadiranGuru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Status Agenda</label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror" style="min-height: 48px;">
                            <option value="berjalan">Berjalan (In Progress)</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Guru Pengganti (jika ada)</label>
                    <select wire:model="guruPenggantiId" class="form-select @error('guruPenggantiId') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Tidak ada guru pengganti --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                        @endforeach
                    </select>
                    @error('guruPenggantiId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Materi Diajarkan</label>
                    <textarea wire:model="materiDiajarkan" class="form-control @error('materiDiajarkan') is-invalid @enderror" rows="4" placeholder="Uraian materi pembelajaran..." style="min-height: 100px;"></textarea>
                    @error('materiDiajarkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="min-height: 48px;"><i class="bi bi-save me-1"></i> Simpan Override & Audit Log</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Audit Log Info Card --}}
    @if($agenda->koreksiOleh)
    <div class="alert alert-warning animate-fade-in-up">
        <i class="bi bi-clock-history me-2"></i>
        Terakhir dikoreksi/overridden oleh: <strong>{{ $agenda->koreksiOleh->name }}</strong>
        pada {{ $agenda->updated_at ? $agenda->updated_at->format('d/m/Y H:i:s') : '-' }}.
    </div>
    @endif

    @else
    <div class="alert alert-danger animate-fade-in-up">
        Data agenda harian tidak ditemukan.
    </div>
    @endif
</div>
