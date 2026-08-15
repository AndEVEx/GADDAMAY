<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('guru.kktp-nilai') }}" wire:navigate class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold">Nilai KKTP &mdash; {{ $rombel->nama_kelas }}</h4>
                <p class="text-muted mb-0 small">{{ $mapel->nama_mapel }}</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm animate-fade-in-up mb-4" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive" style="border-radius: 12px;">
                <table class="table table-bordered table-striped mb-0" style="white-space: nowrap; min-width: 800px;">
                    <thead class="table-light align-middle">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Siswa</th>
                            @foreach($tps as $tp)
                                <th class="text-center" style="min-width: 140px;">
                                    <div title="{{ $tp->deskripsi_tp }}" style="cursor: help;" class="mb-2">
                                        {{ $tp->kode_tp }}
                                        <i class="bi bi-info-circle ms-1 small text-muted"></i>
                                    </div>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" wire:click="setAllTp('{{ $tp->id }}', 'tercapai')" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">Semua &check;</button>
                                        <button type="button" wire:click="setAllTp('{{ $tp->id }}', 'belum_tercapai')" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;">Semua &cross;</button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @forelse($siswaList as $index => $siswa)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $siswa->nama }}</td>
                                @foreach($tps as $tp)
                                    <td class="text-center p-2">
                                        @php
                                            $status = $nilaiData[$siswa->id][$tp->id] ?? 'tercapai';
                                        @endphp
                                        <button type="button" 
                                            wire:click="toggleNilai('{{ $siswa->id }}', '{{ $tp->id }}')" 
                                            class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1 {{ $status === 'tercapai' ? 'btn-success' : 'btn-danger' }}" 
                                            style="min-height: 44px; border-radius: 8px;">
                                            @if($status === 'tercapai')
                                                <i class="bi bi-check-circle-fill"></i> Tercapai
                                            @else
                                                <i class="bi bi-x-circle"></i> Belum
                                            @endif
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $tps->count() }}" class="text-center py-4 text-muted">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
        <button type="button" wire:click="simpanNilai" class="btn btn-primary btn-lg w-100 py-3 shadow-sm" style="border-radius: 12px;">
            <span wire:loading.remove wire:target="simpanNilai">
                <i class="bi bi-save me-2"></i> Simpan Nilai KKTP
            </span>
            <span wire:loading wire:target="simpanNilai">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Menyimpan...
            </span>
        </button>
        <div class="text-center mt-3 text-muted small">
            <i class="bi bi-info-circle me-1"></i> Default: Sudah Tercapai | Siswa tidak hadir: otomatis Belum Tercapai
        </div>
    </div>
</div>
