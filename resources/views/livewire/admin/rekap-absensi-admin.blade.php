<div class="container-fluid py-3 px-2 px-md-4">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check-fill text-primary"></i> Rekap Absensi Siswa
            </h4>
            <p class="text-muted small mb-0">
                Laporan dan rekapitulasi kehadiran presensi siswa seluruh kelas SMKN 2 Indramayu
            </p>
        </div>

        {{-- Action Buttons: Excel Export --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button wire:click="exportExcelAll" class="btn btn-sm btn-success d-flex align-items-center gap-2 shadow-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportExcelAll"><i class="bi bi-file-earmark-spreadsheet-fill"></i></span>
                <span wire:loading wire:target="exportExcelAll" class="spinner-border spinner-border-sm"></span>
                <span>Download Semua Kelas (.xlsx)</span>
            </button>
            <button wire:click="exportExcel" class="btn btn-sm btn-outline-success d-flex align-items-center gap-2 shadow-sm" wire:loading.attr="disabled" {{ !$selectedRombel ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="exportExcel"><i class="bi bi-download"></i></span>
                <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm"></span>
                <span>Download Kelas Ini (.xlsx)</span>
            </button>
        </div>
    </div>

    {{-- Filter & Mode Selector Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center mb-3 pb-3 border-bottom">
                {{-- Mode Switcher --}}
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Mode Rekapitulasi</label>
                    <div class="btn-group w-100" role="group">
                        <button type="button" wire:click="$set('modeRekap', 'bulanan')" class="btn btn-sm {{ $modeRekap === 'bulanan' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}">
                            <i class="bi bi-calendar-month me-1"></i>Rekap Bulanan
                        </button>
                        <button type="button" wire:click="$set('modeRekap', 'harian')" class="btn btn-sm {{ $modeRekap === 'harian' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}">
                            <i class="bi bi-calendar-day me-1"></i>Rekap Harian
                        </button>
                    </div>
                </div>

                {{-- Periode Filter --}}
                <div class="col-12 col-md-4">
                    @if($modeRekap === 'harian')
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Pilih Tanggal</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-primary"></i></span>
                            <input type="date" wire:model.live="selectedTanggal" class="form-control border-start-0 fw-semibold">
                        </div>
                    @else
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Pilih Bulan</label>
                        <select wire:model.live="selectedBulan" class="form-select form-select-sm fw-semibold">
                            <option value="all">Semua Bulan / Semester</option>
                            @php
                                $currentYear = date('Y');
                                $months = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                            @endphp
                            @foreach($months as $num => $name)
                                <option value="{{ $currentYear }}-{{ $num }}">{{ $name }} {{ $currentYear }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Tingkat Filter --}}
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Filter Tingkat</label>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="button" wire:click="$set('filterTingkat', 'all')" class="btn {{ $filterTingkat === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">Semua</button>
                        <button type="button" wire:click="$set('filterTingkat', '10')" class="btn {{ $filterTingkat === '10' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas X</button>
                        <button type="button" wire:click="$set('filterTingkat', '11')" class="btn {{ $filterTingkat === '11' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XI</button>
                        <button type="button" wire:click="$set('filterTingkat', '12')" class="btn {{ $filterTingkat === '12' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XII</button>
                    </div>
                </div>
            </div>

            {{-- Grid Tombol Pilihan Kelas --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-secondary text-uppercase"><i class="bi bi-door-open me-1"></i>Pilih Rombel / Kelas ({{ $allRombels->count() }} Kelas):</span>
            </div>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-2" style="max-height: 180px; overflow-y: auto;">
                @forelse($allRombels as $rombel)
                    <div class="col">
                        <button type="button" 
                                wire:click="selectRombel('{{ $rombel->id }}')"
                                class="btn w-100 text-truncate text-start py-2 px-2 rounded-3 transition-all d-flex align-items-center justify-content-between {{ $selectedRombelId === $rombel->id ? 'btn-primary shadow-sm fw-bold' : 'btn-outline-secondary text-dark' }}"
                                style="min-height: 40px; font-size: 0.82rem;">
                            <span>{{ $rombel->nama_kelas }}</span>
                            @if($selectedRombelId === $rombel->id)
                                <i class="bi bi-check-circle-fill ms-1"></i>
                            @endif
                        </button>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3 small">
                        Tidak ada kelas yang ditemukan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Main Matrix Table --}}
    @if($selectedRombel)
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Card Header with Info & Search --}}
        <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">{{ $selectedRombel->nama_kelas }}</span>
                <span class="badge bg-light text-dark border px-2 py-1 small">
                    @if($modeRekap === 'harian')
                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($selectedTanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    @else
                        <i class="bi bi-calendar-range me-1"></i>{{ $selectedBulan === 'all' ? 'Semua Bulan / Semester' : \Carbon\Carbon::createFromFormat('Y-m', $selectedBulan)->locale('id')->isoFormat('MMMM Y') }}
                    @endif
                </span>
                <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1 small">
                    {{ $agendas->count() }} {{ $modeRekap === 'harian' ? 'Sesi Jam' : 'Pertemuan' }}
                </span>
            </div>

            {{-- Search Box --}}
            <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari nama / NIS siswa...">
            </div>
        </div>

        {{-- Table Matrix Content --}}
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-hover table-bordered align-middle mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top shadow-sm" style="z-index: 2;">
                        <tr>
                            <th class="py-3" style="width: 45px;">No</th>
                            <th class="py-3 text-start" style="min-width: 180px;">Nama Siswa</th>
                            <th class="py-3" style="width: 90px;">NIS</th>

                            {{-- Dynamic Columns for Each Agenda / Meeting --}}
                            @forelse($agendas as $idx => $agenda)
                                @php
                                    $mapel = $agenda->jadwalPelajaran?->mataPelajaran;
                                    $guru = $agenda->guruPengganti ?? $agenda->guru;
                                @endphp
                                <th class="py-2 px-1 bg-light border-start border-end" style="min-width: 75px; max-width: 110px;">
                                    @if($modeRekap === 'harian')
                                        <div class="fw-bold text-primary text-truncate" title="{{ $mapel?->nama_mapel }}">
                                            {{ $mapel?->kode_mapel ?? 'Mapel' }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.70rem;">
                                            Sesi {{ $idx + 1 }}
                                        </div>
                                    @else
                                        <div class="fw-bold text-primary">P-{{ $idx + 1 }}</div>
                                        <div class="text-muted small" style="font-size: 0.70rem;">
                                            {{ \Carbon\Carbon::parse($agenda->tanggal)->format('d/m') }}
                                        </div>
                                    @endif
                                </th>
                            @empty
                                <th class="py-3 text-muted" style="min-width: 200px;">
                                    <i class="bi bi-info-circle me-1"></i>Belum ada agenda pertemuan tercatat
                                </th>
                            @endforelse

                            {{-- Total Summary Columns --}}
                            <th class="py-3 bg-success bg-opacity-10 text-success fw-bold" style="width: 45px;">H</th>
                            <th class="py-3 bg-primary bg-opacity-10 text-primary fw-bold" style="width: 45px;">S</th>
                            <th class="py-3 bg-warning bg-opacity-10 text-warning fw-bold" style="width: 45px;">I</th>
                            <th class="py-3 bg-danger bg-opacity-10 text-danger fw-bold" style="width: 45px;">A</th>
                            <th class="py-3 bg-light" style="width: 90px;">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaMatrix as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $item->siswa->nama }}</td>
                                <td class="text-muted small font-monospace">{{ $item->siswa->nis ?? '-' }}</td>

                                {{-- Status Badges --}}
                                @forelse($agendas as $agenda)
                                    @php
                                        $st = $item->statuses[$agenda->id] ?? null;
                                    @endphp
                                    <td class="py-2">
                                        @if($st === 'hadir')
                                            <span class="badge bg-success py-1 px-2 fw-bold" style="font-size: 0.78rem;" title="Hadir">H</span>
                                        @elseif($st === 'sakit')
                                            <span class="badge bg-primary py-1 px-2 fw-bold" style="font-size: 0.78rem;" title="Sakit">S</span>
                                        @elseif($st === 'izin')
                                            <span class="badge bg-warning text-dark py-1 px-2 fw-bold" style="font-size: 0.78rem;" title="Izin">I</span>
                                        @elseif($st === 'alpa')
                                            <span class="badge bg-danger py-1 px-2 fw-bold" style="font-size: 0.78rem;" title="Alpa">A</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                @empty
                                    <td class="text-muted small">-</td>
                                @endforelse

                                {{-- Total Columns --}}
                                <td class="fw-bold text-success">{{ $item->h }}</td>
                                <td class="fw-bold text-primary">{{ $item->s }}</td>
                                <td class="fw-bold text-warning">{{ $item->i }}</td>
                                <td class="fw-bold text-danger">{{ $item->a }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <span class="fw-bold small">{{ $item->persenHadir }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 8 + max(1, $agendas->count()) }}" class="text-center text-muted py-4">
                                    <i class="bi bi-people fs-4 d-block mb-2"></i>
                                    Tidak ada data siswa yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card Footer --}}
        <div class="card-footer bg-light py-2 px-3 border-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
                <div class="d-flex align-items-center gap-3 text-muted">
                    <span class="fw-bold text-dark">Keterangan:</span>
                    <span><span class="badge bg-success px-2 py-1 me-1">H</span> Hadir</span>
                    <span><span class="badge bg-primary px-2 py-1 me-1">S</span> Sakit</span>
                    <span><span class="badge bg-warning text-dark px-2 py-1 me-1">I</span> Izin</span>
                    <span><span class="badge bg-danger px-2 py-1 me-1">A</span> Alpa</span>
                </div>
                <div class="text-muted">
                    Total Siswa: <strong>{{ $siswaMatrix->count() }}</strong> orang
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
