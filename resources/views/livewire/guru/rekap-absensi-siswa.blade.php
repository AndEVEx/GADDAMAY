<div>
    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" wire:navigate>
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Tabel Rekap Absensi Siswa</h5>
                <small class="text-muted">Matrix Presensi Pertemuan per Mata Pelajaran & Kelas</small>
            </div>
        </div>

        {{-- Export Action Buttons --}}
        <div class="d-flex flex-wrap gap-2">
            @if($availableClasses->isNotEmpty())
            <button wire:click="exportExcelAll" class="btn btn-outline-success btn-sm d-flex align-items-center gap-2 shadow-sm" style="min-height: 38px; border-radius: 10px;" wire:loading.attr="disabled" title="Download seluruh kelas yang diampu dalam 1 file Excel (multi-sheet)">
                <span wire:loading.remove wire:target="exportExcelAll"><i class="bi bi-file-earmark-spreadsheet-fill"></i></span>
                <span wire:loading wire:target="exportExcelAll" class="spinner-border spinner-border-sm"></span>
                <span class="fw-semibold">Download Semua Kelas (.xlsx)</span>
            </button>
            @endif

            @if($selectedRombel && $selectedMapel && $matrixData->isNotEmpty())
            <button wire:click="exportExcel" class="btn btn-success btn-sm d-flex align-items-center gap-2 shadow-sm" style="min-height: 38px; border-radius: 10px;" wire:loading.attr="disabled" title="Download tabel presensi kelas yang sedang dibuka">
                <span wire:loading.remove wire:target="exportExcel"><i class="bi bi-file-earmark-excel-fill"></i></span>
                <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm"></span>
                <span class="fw-semibold">Download Kelas Ini (.xlsx)</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Class & Subject Selector - Structured Grid --}}
    <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="bi bi-collection me-1 text-primary"></i>Pilih Kelas & Mata Pelajaran:
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $availableClasses->count() }} Kelas Diampu</span>
            </div>

            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
                @forelse($availableClasses as $c)
                @php
                    $isSelected = ($selectedRombelId === $c->rombel_id && $selectedMapelId === $c->mapel_id);
                @endphp
                <div class="col">
                    <button wire:click="selectClass('{{ $c->rombel_id }}', '{{ $c->mapel_id }}')" 
                            class="btn btn-sm w-100 h-100 d-flex flex-column justify-content-center p-2 text-start transition-all {{ $isSelected ? 'btn-primary shadow text-white' : 'btn-outline-secondary bg-light bg-opacity-50 text-dark border' }}"
                            style="border-radius: 12px; min-height: 62px;">
                        <div class="d-flex align-items-center gap-1 mb-1 w-100">
                            <i class="bi bi-mortarboard-fill {{ $isSelected ? 'text-white' : 'text-primary' }} flex-shrink-0" style="font-size: 0.9rem;"></i>
                            <span class="fw-bold text-truncate" style="font-size: 0.82rem;" title="{{ $c->rombel_nama }}">{{ $c->rombel_nama }}</span>
                        </div>
                        <span class="text-truncate small w-100 {{ $isSelected ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.7rem;" title="{{ $c->mapel_nama }}">
                            {{ $c->mapel_nama }}
                        </span>
                    </button>
                </div>
                @empty
                <div class="col-12 text-muted small py-2">Belum ada jadwal kelas yang diampu.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if($selectedRombel && $selectedMapel)
    {{-- Summary Stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $totalPertemuan }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">Total Pertemuan KBM</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $totalSiswa }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">Jumlah Siswa Kelas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 h-100" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-pie-chart-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $avgKehadiran }}%</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">Rata-rata Kehadiran Kelas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar & Clear Legend --}}
    <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center justify-content-between">
                {{-- Search & Month Filter --}}
                <div class="col-12 col-md-6 d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari nama atau NIS siswa..." style="min-height: 38px;">
                    </div>

                    @if($availableMonths->isNotEmpty())
                    <select wire:model.live="selectedBulan" class="form-select form-select-sm bg-light border-0" style="min-height: 38px; width: auto; max-width: 170px;">
                        <option value="all">Semua Bulan</option>
                        @foreach($availableMonths as $bm)
                        <option value="{{ $bm }}">{{ \Carbon\Carbon::parse($bm . '-01')->translatedFormat('F Y') }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>

                {{-- Prominent Legend Badges --}}
                <div class="col-12 col-md-6 d-flex flex-wrap align-items-center justify-content-md-end gap-2 pt-1" style="font-size: 0.78rem;">
                    <span class="text-muted small fw-bold">Keterangan:</span>
                    <span class="badge bg-success text-white px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i><strong>H</strong>: Hadir
                    </span>
                    <span class="badge bg-info text-dark px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-heart-pulse-fill"></i><strong>S</strong>: Sakit
                    </span>
                    <span class="badge bg-warning text-dark px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-envelope-paper-fill"></i><strong>I</strong>: Izin
                    </span>
                    <span class="badge bg-danger text-white px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-x-circle-fill"></i><strong>A</strong>: Alpa
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Matrix Table --}}
    <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 14px;">
        <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-bold text-dark">
                <i class="bi bi-table me-2 text-primary"></i>Tabel Presensi: <span class="text-primary">{{ $selectedRombel->nama_kelas }}</span> — {{ $selectedMapel->nama_mapel }}
            </div>
            <span class="badge bg-secondary bg-opacity-10 text-dark">{{ $matrixData->count() }} Siswa</span>
        </div>

        <div class="table-responsive" style="max-height: 650px;">
            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.82rem;">
                <thead class="table-light sticky-top" style="z-index: 2;">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th style="min-width: 90px;">NIS</th>
                        <th class="text-start" style="min-width: 200px;">Nama Siswa</th>
                        <th class="bg-primary bg-opacity-10 text-primary" style="width: 45px;" title="Total Hadir">H</th>
                        <th class="bg-info bg-opacity-10 text-info" style="width: 45px;" title="Total Sakit">S</th>
                        <th class="bg-warning bg-opacity-10 text-dark" style="width: 45px;" title="Total Izin">I</th>
                        <th class="bg-danger bg-opacity-10 text-danger" style="width: 45px;" title="Total Alpa">A</th>
                        <th class="bg-light fw-bold" style="width: 65px;" title="Persentase Kehadiran">%</th>

                        {{-- Chronological Meetings Columns --}}
                        @forelse($agendas as $idx => $ag)
                        @php
                            $tglObj = \Carbon\Carbon::parse($ag->tanggal);
                        @endphp
                        <th class="text-nowrap px-2" style="min-width: 65px; background-color: #f8fafc;">
                            <div class="fw-bold text-dark" style="font-size: 0.75rem;">P-{{ $idx + 1 }}</div>
                            <div class="text-muted small" style="font-size: 0.65rem;">{{ $tglObj->format('d/m') }}</div>
                        </th>
                        @empty
                        <th class="text-muted small text-start px-3 py-3" style="min-width: 200px;">
                            <i class="bi bi-info-circle me-1"></i>Belum ada pertemuan KBM pada periode ini
                        </th>
                        @endforelse
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrixData as $idx => $row)
                    <tr>
                        <td class="text-muted small text-center">{{ $idx + 1 }}</td>
                        <td class="text-muted small font-monospace">{{ $row->siswa->nis ?? '-' }}</td>
                        <td class="text-start fw-semibold text-dark">{{ $row->siswa->nama }}</td>
                        
                        {{-- Summary Totals --}}
                        <td class="fw-bold text-success bg-success bg-opacity-10">{{ $row->hadir }}</td>
                        <td class="fw-bold text-info bg-info bg-opacity-10">{{ $row->sakit }}</td>
                        <td class="fw-bold text-warning bg-warning bg-opacity-10">{{ $row->izin }}</td>
                        <td class="fw-bold text-danger bg-danger bg-opacity-10">{{ $row->alpa }}</td>
                        <td class="fw-bold {{ $row->persentase >= 80 ? 'text-success' : ($row->persentase >= 60 ? 'text-warning' : 'text-danger') }}">
                            {{ $row->persentase }}%
                        </td>

                        {{-- Status per Pertemuan --}}
                        @foreach($agendas as $ag)
                        @php
                            $st = $row->statuses[$ag->id] ?? '-';
                        @endphp
                        <td class="p-1">
                            @if($st === 'H')
                            <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem; min-width: 28px;">H</span>
                            @elseif($st === 'S')
                            <span class="badge bg-info text-dark fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem; min-width: 28px;">S</span>
                            @elseif($st === 'I')
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem; min-width: 28px;">I</span>
                            @elseif($st === 'A')
                            <span class="badge bg-danger text-white fw-bold px-2 py-1 shadow-sm" style="font-size: 0.72rem; min-width: 28px;">A</span>
                            @else
                            <span class="text-muted" style="font-size: 0.75rem;">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 8 + max(1, $agendas->count()) }}" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-4 d-block mb-2 text-muted"></i>
                            Tidak ada data siswa yang cocok dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
