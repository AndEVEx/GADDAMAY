<div>
    <div class="page-header mb-3">
        <h1><i class="bi bi-person-check me-2"></i>Input Kehadiran Siswa</h1>
        <p class="subtitle mb-0">
            {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
        </p>
    </div>

    {{-- Summary --}}
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="card text-center py-2 border shadow-sm">
                <div class="fw-bold text-success fs-5">{{ $summary['hadir'] ?? 0 }}</div>
                <div class="small text-muted">Hadir (H)</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2 border shadow-sm">
                <div class="fw-bold text-info fs-5">{{ $summary['sakit'] ?? 0 }}</div>
                <div class="small text-muted">Sakit (S)</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2 border shadow-sm">
                <div class="fw-bold text-warning fs-5">{{ $summary['izin'] ?? 0 }}</div>
                <div class="small text-muted">Izin (I)</div>
            </div>
        </div>
        <div class="col-3">
            <div class="card text-center py-2 border shadow-sm">
                <div class="fw-bold text-danger fs-5">{{ $summary['alpa'] ?? 0 }}</div>
                <div class="small text-muted">Alpa (A)</div>
            </div>
        </div>
    </div>

    {{-- Student List & Mass Actions --}}
    <div class="card mb-3 border shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <div class="fw-bold text-dark">
                <i class="bi bi-people me-2 text-primary"></i>Daftar Siswa ({{ $siswaList->count() }})
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <button type="button" wire:click="setAllStatus('hadir')" class="btn btn-outline-success btn-sm fw-semibold" style="border-radius: 8px;">
                    <i class="bi bi-check-all me-1"></i>Hadir Semua
                </button>
                <button type="button" wire:click="setAllStatus('sakit')" class="btn btn-outline-info btn-sm fw-semibold" style="border-radius: 8px;">
                    Sakit Semua
                </button>
                <button type="button" wire:click="setAllStatus('izin')" class="btn btn-outline-warning btn-sm fw-semibold" style="border-radius: 8px;">
                    Izin Semua
                </button>
                <button type="button" wire:click="setAllStatus('alpa')" class="btn btn-outline-danger btn-sm fw-semibold" style="border-radius: 8px;">
                    Alpa Semua
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            @forelse($siswaList as $index => $siswa)
            @php $currentStatus = $kehadiran[$siswa->id] ?? 'hadir'; @endphp
            <div class="d-flex align-items-center gap-2 p-3 border-bottom" style="min-height: 60px;">
                <div class="flex-fill" style="overflow-x: auto; min-width: 0;">
                    <div class="fw-semibold text-dark small" style="white-space: nowrap;">
                        {{ $index + 1 }}. {{ $siswa->nama }}
                        @if(in_array($siswa->id, $izinSiswaIds ?? []))
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">
                                <i class="bi bi-shield-check me-1"></i>Izin Sah
                            </span>
                        @endif
                    </div>
                    @if($siswa->nis)
                    <div class="text-muted" style="font-size: 0.7rem; white-space: nowrap;">NIS: {{ $siswa->nis }}</div>
                    @endif
                </div>

                {{-- Status Radio Segment Controls --}}
                <div class="btn-group flex-shrink-0" role="group" aria-label="Status Kehadiran">
                    {{-- Hadir (H) --}}
                    <input type="radio" 
                           class="btn-check" 
                           name="kehadiran_{{ $siswa->id }}" 
                           id="btn_hadir_{{ $siswa->id }}" 
                           value="hadir" 
                           wire:model.live="kehadiran.{{ $siswa->id }}"
                           wire:click="setStatus({{ $siswa->id }}, 'hadir')">
                    <label class="btn btn-sm {{ $currentStatus === 'hadir' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-outline-secondary' }} px-3 py-2" 
                           for="btn_hadir_{{ $siswa->id }}" 
                           style="min-width: 44px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                        H
                    </label>

                    {{-- Sakit (S) --}}
                    <input type="radio" 
                           class="btn-check" 
                           name="kehadiran_{{ $siswa->id }}" 
                           id="btn_sakit_{{ $siswa->id }}" 
                           value="sakit" 
                           wire:model.live="kehadiran.{{ $siswa->id }}"
                           wire:click="setStatus({{ $siswa->id }}, 'sakit')">
                    <label class="btn btn-sm {{ $currentStatus === 'sakit' ? 'btn-info text-white fw-bold shadow-sm' : 'btn-outline-secondary' }} px-3 py-2" 
                           for="btn_sakit_{{ $siswa->id }}" 
                           style="min-width: 44px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                        S
                    </label>

                    {{-- Izin (I) --}}
                    <input type="radio" 
                           class="btn-check" 
                           name="kehadiran_{{ $siswa->id }}" 
                           id="btn_izin_{{ $siswa->id }}" 
                           value="izin" 
                           wire:model.live="kehadiran.{{ $siswa->id }}"
                           wire:click="setStatus({{ $siswa->id }}, 'izin')">
                    <label class="btn btn-sm {{ $currentStatus === 'izin' ? 'btn-warning text-white fw-bold shadow-sm' : 'btn-outline-secondary' }} px-3 py-2" 
                           for="btn_izin_{{ $siswa->id }}" 
                           style="min-width: 44px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                        I
                    </label>

                    {{-- Alpa (A) --}}
                    <input type="radio" 
                           class="btn-check" 
                           name="kehadiran_{{ $siswa->id }}" 
                           id="btn_alpa_{{ $siswa->id }}" 
                           value="alpa" 
                           wire:model.live="kehadiran.{{ $siswa->id }}"
                           wire:click="setStatus({{ $siswa->id }}, 'alpa')">
                    <label class="btn btn-sm {{ $currentStatus === 'alpa' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-outline-secondary' }} px-3 py-2" 
                           for="btn_alpa_{{ $siswa->id }}" 
                           style="min-width: 44px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                        A
                    </label>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted small">
                <i class="bi bi-info-circle me-1"></i> Belum ada data siswa untuk kelas ini.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Submit Button --}}
    <button wire:click="simpan" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm mb-3" style="border-radius: 12px;" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="bi bi-check-circle me-2 fs-5"></i>Simpan Kehadiran & Lanjut</span>
        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Kehadiran...</span>
    </button>

    <div class="mb-4">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary w-100 fw-semibold" style="min-height: 44px; border-radius: 10px;" wire:navigate>
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Guru
        </a>
    </div>
</div>
