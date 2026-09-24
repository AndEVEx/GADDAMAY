<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-person-lines-fill text-primary me-2"></i>Monitoring Kelas Binaan (Wali Kelas)</h1>
            <p class="text-muted small mb-0">Pantau rekam kemampuan fisik &amp; kebugaran jasmani siswa serta perkembangan hasil skor latihan TKA.</p>
        </div>
        <div class="d-flex gap-2">
            <select wire:model.live="rombelId" class="form-select fw-bold border-primary text-primary" style="min-height: 42px;">
                @foreach($allRombels as $r)
                    <option value="{{ $r->id }}">Kelas: {{ $r->nama_kelas }} ({{ $r->siswa->count() }} Siswa)</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <button wire:click="$set('activeTab', 'fisik')" class="nav-link {{ $activeTab === 'fisik' ? 'active bg-success' : 'bg-light text-dark' }} fw-bold rounded-pill px-4">
                <i class="bi bi-heart-pulse-fill me-1"></i> Data Kebugaran Fisik (PJOK)
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('activeTab', 'tka')" class="nav-link {{ $activeTab === 'tka' ? 'active bg-primary' : 'bg-light text-dark' }} fw-bold rounded-pill px-4">
                <i class="bi bi-mortarboard-fill me-1"></i> Hasil Latihan Soal TKA
            </button>
        </li>
    </ul>

    {{-- Search Bar --}}
    <div class="card mb-3 border-0 shadow-sm rounded-4">
        <div class="card-body p-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control border-0" placeholder="Cari nama atau NIS siswa di kelas ini...">
            </div>
        </div>
    </div>

    <!-- TAB 1: Kebugaran Fisik Siswa Binaan -->
    @if($activeTab === 'fisik')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-success text-white py-3 fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-heart-pulse me-2"></i>Laporan Kebugaran Fisik &amp; Antropometri Siswa (PJOK)</span>
            <span class="badge bg-white text-success">{{ $siswaList->count() }} Siswa Terdaftar</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Siswa</th>
                        <th>NIS</th>
                        <th>Tinggi / Berat</th>
                        <th>Indeks BMI</th>
                        <th>Push / Sit Up</th>
                        <th>Ketahanan Lari</th>
                        <th>Predikat Kebugaran</th>
                        <th class="pe-4">Catatan Guru PJOK</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $s)
                        @php
                            $fisik = $tesFisik->get($s->id)?->first();
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $s->nama }}</div>
                            </td>
                            <td><span class="small font-monospace text-muted">{{ $s->nis ?: '-' }}</span></td>
                            <td>
                                @if($fisik && $fisik->tinggi_badan_cm)
                                    <span class="small"><strong>{{ $fisik->tinggi_badan_cm }}</strong> cm / <strong>{{ $fisik->berat_badan_kg }}</strong> kg</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($fisik && $fisik->bmi)
                                    <div class="small fw-bold">{{ $fisik->bmi }}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">{{ $fisik->kategori_bmi }}</div>
                                @else
                                    <span class="text-muted small">Belum tes</span>
                                @endif
                            </td>
                            <td>
                                @if($fisik)
                                    <span class="small">P: <strong>{{ $fisik->push_up_1min ?? 0 }}</strong> | S: <strong>{{ $fisik->sit_up_1min ?? 0 }}</strong></span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($fisik && $fisik->lari_1200m_detik)
                                    <span class="small font-monospace">{{ gmdate("i:s", $fisik->lari_1200m_detik) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($fisik)
                                    @php
                                        $predBadge = match(true) {
                                            str_contains($fisik->predikat, 'Prima') || str_contains($fisik->predikat, 'Sangat Baik') => 'bg-success',
                                            str_contains($fisik->predikat, 'Baik') => 'bg-primary',
                                            str_contains($fisik->predikat, 'Cukup') => 'bg-warning text-dark',
                                            default => 'bg-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $predBadge }} rounded-pill px-2 py-1 small">
                                        {{ $fisik->predikat }} ({{ $fisik->skor_kebugaran }})
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Belum Ada Rekam</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <span class="small text-muted fst-italic">{{ Str::limit($fisik?->catatan_guru_olahraga ?: 'Belum ada catatan evaluasi fisik.', 40) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data siswa di rombel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 2: Hasil Latihan Soal TKA Siswa Binaan -->
    @if($activeTab === 'tka')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-mortarboard me-2"></i>Rekap Hasil Latihan Tes Kemampuan Akademik (TKA)</span>
            <span class="badge bg-white text-primary">Akademik &amp; Skolastik</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Siswa</th>
                        <th>NIS</th>
                        <th>Total Ujian Diikuti</th>
                        <th>Rata-rata Skor TKA</th>
                        <th>Nilai Tertinggi</th>
                        <th>Ujian Terakhir Dikerjakan</th>
                        <th class="pe-4 text-end">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $s)
                        @php
                            $hasil = $tkaHasil->get($s->id) ?? collect();
                            $avg = $hasil->isNotEmpty() ? round($hasil->avg('nilai_skor'), 1) : 0;
                            $max = $hasil->isNotEmpty() ? $hasil->max('nilai_skor') : 0;
                            $latest = $hasil->first();
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $s->nama }}</div>
                            </td>
                            <td><span class="small font-monospace text-muted">{{ $s->nis ?: '-' }}</span></td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $hasil->count() }} Paket
                                </span>
                            </td>
                            <td>
                                @if($hasil->isNotEmpty())
                                    <span class="fs-6 fw-bold {{ $avg >= 75 ? 'text-success' : 'text-primary' }}">{{ $avg }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($hasil->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success fw-bold">{{ $max }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($latest)
                                    <div class="small fw-semibold">{{ $latest->paket?->judul_paket }}</div>
                                    <div class="text-[10px] text-muted">{{ $latest->created_at->format('d/m/Y H:i') }} • Skor: <strong>{{ $latest->nilai_skor }}</strong></div>
                                @else
                                    <span class="text-muted small italic">Belum pernah mencoba</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($hasil->isNotEmpty())
                                    <span class="badge bg-primary rounded-pill px-3 py-1.5 small">Aktif Belajar</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5 small">Belum Ada Tes</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada data siswa di rombel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
