<div class="container-fluid px-3 py-3" style="max-width: 1200px;">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 fw-bold rounded-pill small">
                    <i class="bi bi-speedometer me-1"></i> Performa Saya
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-people text-info"></i> Daftar Nama Siswa Yang Diajar
            </h4>
            <p class="text-muted small mb-0">Daftar siswa dikelompokkan per Kelas dan Mata Pelajaran.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.performa.export-iki') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-semibold" style="border-radius: 10px;" wire:navigate>
                <i class="bi bi-file-earmark-pdf-fill"></i> Export IKI (PDF)
            </a>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari nama siswa atau NIS..." style="min-height: 42px;">
                        @if(!empty($search))
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary border-start-0" type="button"><i class="bi bi-x"></i></button>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select wire:model.live="selectedRombelMapel" class="form-select" style="min-height: 42px;">
                        <option value="all">-- Semua Kelas & Mata Pelajaran ({{ count($dropdownOptions) }} Tabel) --</option>
                        @foreach($dropdownOptions as $opt)
                            <option value="{{ $opt->key }}">{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 text-md-end">
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                        <i class="bi bi-person-check me-1 text-primary"></i> Total {{ $totalSiswaUnik }} Siswa
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Info jika kelas sama diajar mapel berbeda --}}
    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-4 py-2.5 px-3 mb-4 d-flex align-items-center gap-2 small text-primary">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <span>Satu kelas yang mempelajari lebih dari 1 mata pelajaran akan ditampilkan dalam <strong>tabel terpisah</strong> sesuai mata pelajarannya.</span>
    </div>

    {{-- List of Tables grouped by Rombel + Mapel --}}
    @forelse($tables as $table)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="tabel-{{ $table->group_key }}">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-extrabold text-primary">{{ $table->rombel_nama }}</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small">
                            Tingkat {{ $table->tingkat }}
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        Mata Pelajaran: <strong class="text-dark">{{ $table->mapel_nama }}</strong>
                        @if($table->kode_mapel) <span class="badge bg-light text-secondary border ms-1">{{ $table->kode_mapel }}</span> @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill px-3 py-2 fw-bold fs-7">
                        <i class="bi bi-people-fill me-1"></i> {{ $table->total_siswa }} Murid
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-2.5" style="width: 50px;">No</th>
                                <th class="py-2.5" style="width: 140px;">NIS</th>
                                <th class="py-2.5">Nama Lengkap Siswa</th>
                                <th class="py-2.5" style="width: 130px;">Jenis Kelamin</th>
                                <th class="pe-4 py-2.5 text-center" style="width: 120px;">Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($table->siswa_list as $idx => $s)
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">{{ $idx + 1 }}</td>
                                    <td class="font-monospace text-primary fw-semibold">{{ $s->nis ?? '-' }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $s->nama }}</span>
                                    </td>
                                    <td class="text-muted small">
                                        @if(isset($s->jenis_kelamin))
                                            {{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : ($s->jenis_kelamin === 'P' ? 'Perempuan' : $s->jenis_kelamin) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $table->rombel_nama }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Tidak ada siswa yang cocok dengan pencarian "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="ps-4 py-2.5 text-muted small">
                                    Total Siswa Terdaftar: <strong>{{ $table->total_siswa }} orang</strong>
                                </td>
                                <td colspan="2" class="pe-4 py-2.5 text-end text-muted small">
                                    Mata Pelajaran: <strong>{{ $table->mapel_nama }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
            <h5>Tidak ditemukan daftar siswa</h5>
            <p class="small mb-0">Pastikan akun guru Anda telah memiliki jadwal pelajaran aktif di menu jadwal.</p>
        </div>
    @endforelse
</div>