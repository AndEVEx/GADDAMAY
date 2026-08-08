<div>
    <div class="page-header">
        <h1><i class="bi bi-calendar3 me-2"></i>Manajemen Jadwal Pelajaran</h1>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari mapel/kelas/keterangan..." style="min-height: 48px;">
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterHari" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Hari</option>
                        <option value="1">Senin</option>
                        <option value="2">Selasa</option>
                        <option value="3">Rabu</option>
                        <option value="4">Kamis</option>
                        <option value="5">Jumat</option>
                    </select>
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterRombel" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-3">
                    <select wire:model.live="filterMapel" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Mapel</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
        </button>
        <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel (.xlsx)
        </button>
    </div>

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Jadwal Pelajaran
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Hari</label>
                        <select wire:model="hari" class="form-select @error('hari') is-invalid @enderror" style="min-height: 48px;">
                            <option value="1">Senin</option>
                            <option value="2">Selasa</option>
                            <option value="3">Rabu</option>
                            <option value="4">Kamis</option>
                            <option value="5">Jumat</option>
                        </select>
                        @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Jam Ke Mulai</label>
                        <input type="number" wire:model="jam_ke_mulai" min="1" max="15" class="form-control @error('jam_ke_mulai') is-invalid @enderror" style="min-height: 48px;">
                        @error('jam_ke_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Jam Ke Selesai</label>
                        <input type="number" wire:model="jam_ke_selesai" min="1" max="15" class="form-control @error('jam_ke_selesai') is-invalid @enderror" style="min-height: 48px;">
                        @error('jam_ke_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Kelas / Rombel</label>
                        <select wire:model="rombel_id" class="form-select @error('rombel_id') is-invalid @enderror" style="min-height: 48px;">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Mata Pelajaran</label>
                        <select wire:model="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror" style="min-height: 48px;">
                            <option value="">-- Kegiatan Khusus / Non-Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})</option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Guru Pengampu</label>
                    <select wire:model="guru_id" class="form-select @error('guru_id') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }} ({{ $guru->email }})</option>
                        @endforeach
                    </select>
                    @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Kegiatan Khusus (jika non-KBM regular)</label>
                        <input type="text" wire:model="kegiatan_khusus" class="form-control" placeholder="Contoh: Upacara, Istirahat, Literasi" style="min-height: 48px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Keterangan Catatan</label>
                        <input type="text" wire:model="keterangan" class="form-control" placeholder="Catatan tambahan..." style="min-height: 48px;">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="min-height: 48px;"><i class="bi bi-check me-1"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($confirmDelete)
    <div class="alert alert-danger animate-fade-in-up">
        <strong>Hapus {{ $deleteTitle }}?</strong>
        <p class="small mb-2">Riwayat agenda harian pada jadwal ini akan ikut dihapus (cascade).</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteJadwal" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Jadwal List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($jadwals as $j)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary fs-7">{{ $j->hari_label }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-dark">Jam {{ $j->jam_ke_mulai }} - {{ $j->jam_ke_selesai }}</span>
                        <span class="fw-bold text-dark fs-6">{{ $j->rombel->nama_kelas ?? '-' }}</span>
                    </div>
                    <div class="fw-bold text-primary">
                        @if($j->kegiatan_khusus)
                            <span class="badge bg-warning text-dark"><i class="bi bi-star me-1"></i>{{ $j->kegiatan_khusus }}</span>
                        @else
                            {{ $j->mataPelajaran->nama_mapel ?? 'Tanpa Mapel' }}
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-person-badge me-1"></i>Guru: 
                        <strong class="text-dark">
                            {{ $j->guru->pluck('name')->join(', ') ?: 'Belum ditentukan' }}
                        </strong>
                        @if($j->keterangan)
                            &bull; <span class="fst-italic">{{ $j->keterangan }}</span>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $j->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteJadwal('{{ $j->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data jadwal pelajaran.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $jadwals->links() }}</div>
</div>
