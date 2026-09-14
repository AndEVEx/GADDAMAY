<div wire:poll.30s>
    {{-- Page Header --}}
    <div class="page-header mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="text-white fw-bold mb-1"><i class="bi bi-calendar3 me-2"></i>Jadwal Pelajaran Kelas</h1>
                <p class="subtitle mb-0 text-white-50">
                    Jadwal mata pelajaran dan status KBM kelas <strong>{{ $studentRombel?->nama_kelas ?? '-' }}</strong>
                </p>
            </div>
            @if($studentRombel)
            <div class="badge bg-white text-primary px-3 py-2 fw-bold" style="font-size: 0.85rem; border-radius: 8px;">
                <i class="bi bi-door-open me-1"></i>Kelas {{ $studentRombel->nama_kelas }}
            </div>
            @endif
        </div>
    </div>

    @if(!$studentRombel)
    <div class="alert alert-warning animate-fade-in-up mb-3" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Perhatian:</strong> Akun Anda belum terhubung dengan data kelas di sistem. Silakan hubungi Administrator.
    </div>
    @else
    {{-- Day Selector Tabs (Senin - Jumat) --}}
    <div class="card border-0 shadow-sm mb-3 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-body p-2">
            <div class="d-flex gap-1 overflow-auto pb-1 flex-nowrap">
                @foreach($hariLabels as $dayNum => $dayName)
                    <button type="button" wire:click="setHari({{ $dayNum }})"
                            class="btn {{ $selectedHari === $dayNum ? 'btn-primary' : 'btn-light text-dark' }} flex-fill py-2 px-3 fw-bold text-nowrap d-flex align-items-center justify-content-center gap-1"
                            style="border-radius: 10px; min-height: 42px; font-size: 0.88rem;">
                        <span>{{ $dayName }}</span>
                        @if($dayNum === $currentDayOfWeek)
                            <span class="badge {{ $selectedHari === $dayNum ? 'bg-white text-primary' : 'bg-primary text-white' }} ms-1" style="font-size: 0.65rem;">HARI INI</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Schedule Card / Table --}}
    <div class="card border-0 shadow-sm animate-fade-in-up" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-journal-text text-primary me-2"></i>Jadwal Hari {{ $hariLabels[$selectedHari] ?? '' }} &bull; {{ $studentRombel->nama_kelas }}
                </h6>
                <small class="text-muted">Total {{ count($jadwalList) }} sesi mata pelajaran</small>
            </div>
            @if($isHariIni)
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2" style="border-radius: 8px;">
                    <i class="bi bi-broadcast me-1"></i>Status Hari Ini (Realtime)
                </span>
            @endif
        </div>

        @if($jadwalList->isEmpty())
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                <h6 class="fw-bold text-dark">Tidak Ada Jadwal Pelajaran</h6>
                <p class="text-muted small mb-0">Tidak ada jadwal pelajaran yang tercatat untuk hari {{ $hariLabels[$selectedHari] ?? '' }}.</p>
            </div>
        @else
            {{-- Desktop / Tablet Table View --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 120px;">Jam Ke</th>
                            <th style="width: 140px;">Waktu</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru Pengajar</th>
                            @if($isHariIni)
                                <th class="text-center" style="width: 180px;">Status KBM Hari Ini</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalList as $index => $item)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1" style="border-radius: 6px;">
                                        {{ $item->jam_display }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small fw-semibold">
                                        <i class="bi bi-clock me-1"></i>{{ $item->waktu_display }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->mapel_nama }}</div>
                                    @if(!empty($item->kegiatan_khusus))
                                        <span class="badge bg-secondary bg-opacity-15 text-dark" style="font-size: 0.72rem;">Kegiatan Khusus</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-person me-1 text-primary"></i>{{ $item->guru_nama }}
                                    </div>
                                </td>
                                @if($isHariIni)
                                    <td class="text-center">
                                        <span class="badge {{ $item->status_badge }} px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; border-radius: 6px;">
                                            <i class="bi {{ $item->status_icon }}"></i>
                                            <span>{{ $item->status_label }}</span>
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif
</div>
