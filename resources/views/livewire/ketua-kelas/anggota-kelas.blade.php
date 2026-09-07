<div>
    {{-- Page Header --}}
    <div class="page-header mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i>
                    Anggota Kelas {{ $studentRombel?->nama_kelas ?? '' }}
                </h1>
                <p class="text-muted small mb-0">
                    Daftar seluruh siswa terdaftar pada kelas <strong>{{ $studentRombel?->nama_kelas ?? 'Anda' }}</strong>
                </p>
            </div>
            @if($studentRombel)
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 border border-primary border-opacity-25" style="border-radius: 8px;">
                    <i class="bi bi-mortarboard me-1"></i> {{ $studentRombel->tingkat_label ?? ('Tingkat ' . $studentRombel->tingkat) }} &bull; {{ $totalSiswa }} Siswa
                </span>
            </div>
            @endif
        </div>
    </div>

    @if(!$studentRombel)
        <div class="card border-warning shadow-sm animate-fade-in-up" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Kelas Belum Ditautkan</h5>
                <p class="text-muted small mb-3">
                    Akun Anda belum ditautkan dengan data rombel kelas di sistem. Silakan hubungi Administrator untuk mengatur kelas pada akun Anda di menu Manajemen User.
                </p>
                <a href="{{ route('ketua.verifikasi') }}" class="btn btn-primary" wire:navigate>
                    <i class="bi bi-qr-code-scan me-1"></i> Ke Halaman Verifikasi OTP
                </a>
            </div>
        </div>
    @else
        {{-- Search & Stat Card --}}
        <div class="card mb-3 shadow-sm animate-fade-in-up" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="search" wire:model.live.debounce.300ms="search" class="form-control border-start-0" 
                                   placeholder="Cari nama atau NIS siswa di kelas {{ $studentRombel->nama_kelas }}..." 
                                   style="min-height: 48px;">
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="text-muted small">
                            Menampilkan <strong>{{ $siswaList->count() }}</strong> dari <strong>{{ $totalSiswa }}</strong> siswa
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Table Card --}}
        <div class="card shadow-sm animate-fade-in-up" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-primary"></i> Daftar Siswa {{ $studentRombel->nama_kelas }}
                </h6>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                    Aktif Terdaftar
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small text-uppercase">
                            <th style="width: 60px;" class="text-center">No</th>
                            <th style="width: 140px;">NIS</th>
                            <th>Nama Lengkap</th>
                            <th style="width: 160px;" class="text-center">Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $index => $siswa)
                            <tr>
                                <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    @if($siswa->nis)
                                        <code class="text-dark fw-bold bg-light px-2 py-1 rounded">{{ $siswa->nis }}</code>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $siswa->nama }}</div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">Siswa SMKN 2 Indramayu</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1">
                                        {{ $studentRombel->nama_kelas }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-2 d-block mb-2 text-secondary"></i>
                                    @if($search)
                                        Tidak ditemukan siswa dengan kata kunci "<strong>{{ $search }}</strong>" di kelas {{ $studentRombel->nama_kelas }}.
                                    @else
                                        Belum ada data siswa terdaftar pada kelas {{ $studentRombel->nama_kelas }}.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light py-2 text-muted small text-center">
                Data resmi dari database SMKN 2 Indramayu &bull; AgenDAmay
            </div>
        </div>
    @endif
</div>