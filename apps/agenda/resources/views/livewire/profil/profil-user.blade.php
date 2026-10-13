<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner Profil --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                {{-- Info Siswa / Pegawai --}}
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="position-relative flex-shrink-0">
                        <div class="rounded-4 overflow-hidden shadow-lg border border-3 border-white border-opacity-30 d-flex align-items-center justify-content-center bg-white" style="width: 90px; height: 110px;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($isViewingStudent ? ($siswa->nama ?? 'Siswa SMKN 2') : ($user->name ?? 'Pegawai SMKN 2')) }}&size=200&background=4f46e5&color=ffffff&bold=true" 
                                 alt="Avatar" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-2 border-white p-1.5" title="Status Aktif">
                            <span class="visually-hidden">Aktif</span>
                        </span>
                    </div>

                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill small">
                                <i class="bi {{ $isViewingStudent ? 'bi-mortarboard-fill' : 'bi-person-badge-fill' }} me-1"></i>
                                {{ $isViewingStudent ? 'PROFIL LENGKAP SISWA' : 'PROFIL PENDIDIK & PEGAWAI' }}
                            </span>
                            <span class="badge bg-white bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                                SMKN 2 Indramayu
                            </span>
                        </div>

                        <h3 class="fw-bold mb-1 text-white">
                            {{ $isViewingStudent ? ($siswa->nama ?? 'Siswa') : $user->name }}
                        </h3>

                        <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                            @if($isViewingStudent)
                                <span><i class="bi bi-person-vcard me-1"></i>NIS: <strong>{{ $siswa->nis ?? '001001' }}</strong></span>
                                <span>•</span>
                                <span><i class="bi bi-door-open me-1"></i>Kelas: <strong>{{ $siswa->rombel->nama_rombel ?? 'X Vokasi' }}</strong></span>
                            @else
                                <span><i class="bi bi-person-vcard me-1"></i>NIP: <strong>19850314 201001 1 008</strong></span>
                                <span>•</span>
                                <span><i class="bi bi-briefcase me-1"></i>Role: <strong>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Button: Buka Kartu Siswa / Pegawai & Dropdown Pemilih Siswa --}}
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                    @if(count($allSiswaList) > 0)
                        <div class="d-flex align-items-center gap-2">
                            <select wire:change="switchSiswa($event.target.value)" class="form-select form-select-sm rounded-pill bg-white bg-opacity-20 text-white border-white border-opacity-25" style="max-width: 240px;">
                                <option value="" class="text-dark">-- Pilih Siswa Binaan --</option>
                                @foreach($allSiswaList as $s)
                                    <option value="{{ $s->id }}" {{ $selectedSiswaId == $s->id ? 'selected' : '' }} class="text-dark">
                                        {{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <a href="{{ route('profil.kartu', ['siswa_id' => $isViewingStudent ? $selectedSiswaId : null]) }}" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                        <i class="bi bi-qr-code me-1.5"></i> {{ $isViewingStudent ? 'Kartu Pelajar Digital' : 'Kartu Pegawai Digital' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigasi Profil --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2 p-md-3">
            <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto no-scrollbar py-1">
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'hexagonal')" class="nav-link {{ $activeTab === 'hexagonal' ? 'active bg-indigo text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small" style="{{ $activeTab === 'hexagonal' ? 'background: #4338ca !important;' : '' }}">
                        <i class="bi bi-hexagon-fill me-1 text-warning"></i> Hexagonal Spektrum Kompetensi
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'fisik')" class="nav-link {{ $activeTab === 'fisik' ? 'active bg-danger text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                        <i class="bi bi-heart-pulse-fill me-1"></i> Data Fisik & Kebugaran PJOK
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'numerasi_literasi')" class="nav-link {{ $activeTab === 'numerasi_literasi' ? 'active bg-primary text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                        <i class="bi bi-calculator-fill me-1"></i> Nilai Numerasi & Literasi
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'biodata')" class="nav-link {{ $activeTab === 'biodata' ? 'active bg-dark text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                        <i class="bi bi-person-lines-fill me-1"></i> Biodata Sangat Lengkap
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- TAB 1: HEXAGONAL STATISTIK / SPIDER WEB RADAR CHART --}}
    @if($activeTab === 'hexagonal')
        <div class="row g-3 g-md-4">
            {{-- Visual Hexagonal Radar Chart --}}
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-indigo-subtle text-indigo fw-bold rounded-pill px-2.5 py-1 small" style="background: #e0e7ff; color: #4338ca;">
                                <i class="bi bi-diagram-3-fill me-1"></i>RADAR STATISTIK 6 PILAR
                            </span>
                            <h5 class="fw-bold text-dark mt-1 mb-0">Spektrum Kompetensi & Kebugaran</h5>
                        </div>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill small">
                            Status: Sangat Baik
                        </span>
                    </div>

                    <p class="text-muted small mb-4">
                        Pemetaan performa holistik siswa mencakup ketahanan jasmani fisik, penalaran logika matematis, literasi wawasan, kedisiplinan presensi, keahlian vokasi, dan budi pekerti.
                    </p>

                    {{-- Canvas Hexagonal Chart.js --}}
                    <div class="position-relative mx-auto my-auto d-flex justify-content-center align-items-center" style="max-width: 480px; min-height: 380px; width: 100%;">
                        <canvas id="hexagonalRadarChart"></canvas>
                    </div>

                    <div class="d-flex justify-content-center align-items-center gap-3 mt-3 pt-3 border-top small text-muted">
                        <span class="d-flex align-items-center gap-1.5"><span class="rounded-circle" style="width: 10px; height: 10px; background: #4f46e5;"></span> Skor Capaian Siswa</span>
                        <span class="d-flex align-items-center gap-1.5"><span class="rounded-circle" style="width: 10px; height: 10px; background: #e2e8f0;"></span> Target Maksimal (100)</span>
                    </div>
                </div>
            </div>

            {{-- Kartu Ringkasan 6 Spektrum --}}
            <div class="col-12 col-lg-5">
                <div class="d-flex flex-column gap-3">
                    {{-- 1. Kebugaran Fisik --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #ef4444; width: 44px; height: 44px;">
                                    <i class="bi bi-heart-pulse fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Kebugaran Fisik (PJOK)</h6>
                                    <div class="text-muted small">Daya tahan, BMI, dan kelincahan</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-danger">{{ $hexagonal['fisik'] }}</span>
                                <span class="text-muted small">/100</span>
                                <div><span class="badge bg-danger-subtle text-danger small">Kategori Bugar</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Nilai Numerasi --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #0284c7; width: 44px; height: 44px;">
                                    <i class="bi bi-calculator fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Penalaran Numerasi</h6>
                                    <div class="text-muted small">Logika matematika & simulasi TKA</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-primary">{{ $hexagonal['numerasi'] }}</span>
                                <span class="text-muted small">/100</span>
                                <div><span class="badge bg-primary-subtle text-primary small">Mahir</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Kemampuan Literasi --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #0d9488; width: 44px; height: 44px;">
                                    <i class="bi bi-book-half fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Kemampuan Literasi</h6>
                                    <div class="text-muted small">Perpus Digital & Literasi Lapangan</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-teal" style="color: #0d9488;">{{ $hexagonal['literasi'] }}</span>
                                <span class="text-muted small">/100</span>
                                <div><span class="badge bg-teal-subtle text-teal small" style="background: #ccfbf1; color: #0d9488;">Daya Serap Tinggi</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Kedisiplinan & Presensi --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #10b981; width: 44px; height: 44px;">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Kedisiplinan & Presensi</h6>
                                    <div class="text-muted small">Kehadiran KBM & Gate Gerbang</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-success">{{ $hexagonal['disiplin'] }}%</span>
                                <div><span class="badge bg-success-subtle text-success small">Sangat Tertib</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Kompetensi Vokasi --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #6366f1; width: 44px; height: 44px;">
                                    <i class="bi bi-tools fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Kompetensi Vokasi</h6>
                                    <div class="text-muted small">Ketercapaian TP & Praktik Industri</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-indigo" style="color: #6366f1;">{{ $hexagonal['vokasi'] }}</span>
                                <span class="text-muted small">/100</span>
                                <div><span class="badge bg-indigo-subtle small" style="background: #e0e7ff; color: #4338ca;">Kompeten</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Karakter & Soft Skills --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2.5 text-white d-flex align-items-center justify-content-center shadow-sm" style="background: #f59e0b; width: 44px; height: 44px;">
                                    <i class="bi bi-star-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Karakter & Budi Pekerti</h6>
                                    <div class="text-muted small">Profil Pelajar Pancasila & Sikap</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="h5 fw-bold mb-0 text-warning">{{ $hexagonal['karakter'] }}</span>
                                <span class="text-muted small">/100</span>
                                <div><span class="badge bg-warning-subtle text-dark small">Berintegritas</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: DATA FISIK & KEBUGARAN PJOK LENGKAP --}}
    @if($activeTab === 'fisik')
        <div class="row g-3 g-md-4">
            {{-- Indeks Massa Tubuh (BMI) Banner --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #e11d48, #be123c);">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8">
                            <span class="badge bg-white text-danger fw-bold rounded-pill px-3 py-1 mb-2">STATUS FISIK & KEBUGARAN JASMANI</span>
                            <h4 class="fw-bold mb-1">Indeks Massa Tubuh (BMI): {{ $tesFisikTerakhir ? $tesFisikTerakhir->bmi : '20.5' }}</h4>
                            <p class="mb-0 text-white-50 small">
                                Berdasarkan pengukuran antropometri terkini: Tinggi Badan <strong>{{ $tesFisikTerakhir ? $tesFisikTerakhir->tinggi_badan_cm : '168' }} cm</strong> dan Berat Badan <strong>{{ $tesFisikTerakhir ? $tesFisikTerakhir->berat_badan_kg : '58' }} kg</strong>. Status: <span class="badge bg-success text-white px-2 py-0.5 rounded-pill">{{ $tesFisikTerakhir ? $tesFisikTerakhir->kategori_bmi : 'Normal / Ideal' }}</span>.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="bg-black bg-opacity-25 p-3 rounded-4 d-inline-block text-start">
                                <div class="small text-white-50">Skor Kebugaran Total</div>
                                <div class="display-6 fw-bold text-white">{{ $tesFisikTerakhir ? $tesFisikTerakhir->skor_kebugaran : '85' }} <span class="fs-6 fw-normal">/ 100</span></div>
                                <div class="badge bg-warning text-dark fw-bold">Predikat: {{ $tesFisikTerakhir ? $tesFisikTerakhir->predikat : 'Sangat Baik (A)' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5 Parameter Tes Fisik --}}
            <div class="col-6 col-md-4 col-lg-2-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle mx-auto p-3 mb-2" style="width: 52px; height: 52px;">
                        <i class="bi bi-stopwatch-fill fs-5"></i>
                    </div>
                    <div class="small text-muted mb-1">Daya Tahan Jantung</div>
                    <h5 class="fw-bold text-dark mb-0">{{ $tesFisikTerakhir ? $tesFisikTerakhir->lari_1200m_detik . ' dtk' : '5m 20s' }}</h5>
                    <div class="small text-muted" style="font-size: 0.72rem;">Lari 1200 Meter</div>
                    <span class="badge bg-success-subtle text-success small mt-2">Baik Sekali</span>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto p-3 mb-2" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-arms-up fs-5"></i>
                    </div>
                    <div class="small text-muted mb-1">Kekuatan Lengan</div>
                    <h5 class="fw-bold text-dark mb-0">{{ $tesFisikTerakhir ? $tesFisikTerakhir->push_up_1min : '32' }} Kali</h5>
                    <div class="small text-muted" style="font-size: 0.72rem;">Push-Up 1 Menit</div>
                    <span class="badge bg-success-subtle text-success small mt-2">Daya Kuat</span>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle mx-auto p-3 mb-2" style="width: 52px; height: 52px;">
                        <i class="bi bi-activity fs-5"></i>
                    </div>
                    <div class="small text-muted mb-1">Kekuatan Perut</div>
                    <h5 class="fw-bold text-dark mb-0">{{ $tesFisikTerakhir ? $tesFisikTerakhir->sit_up_1min : '38' }} Kali</h5>
                    <div class="small text-muted" style="font-size: 0.72rem;">Sit-Up 1 Menit</div>
                    <span class="badge bg-success-subtle text-success small mt-2">Sangat Baik</span>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle mx-auto p-3 mb-2" style="width: 52px; height: 52px;">
                        <i class="bi bi-lightning-charge-fill fs-5"></i>
                    </div>
                    <div class="small text-muted mb-1">Kelincahan Gerak</div>
                    <h5 class="fw-bold text-dark mb-0">{{ $tesFisikTerakhir ? $tesFisikTerakhir->shuttle_run_detik . ' dtk' : '11.2 dtk' }}</h5>
                    <div class="small text-muted" style="font-size: 0.72rem;">Shuttle Run 4x10m</div>
                    <span class="badge bg-success-subtle text-success small mt-2">Responsif</span>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
                    <div class="bg-teal bg-opacity-10 text-teal rounded-circle mx-auto p-3 mb-2" style="width: 52px; height: 52px; color: #0d9488;">
                        <i class="bi bi-arrows-expand fs-5"></i>
                    </div>
                    <div class="small text-muted mb-1">Kelenturan Sendi</div>
                    <h5 class="fw-bold text-dark mb-0">{{ $tesFisikTerakhir ? $tesFisikTerakhir->sit_and_reach_cm . ' cm' : '18 cm' }}</h5>
                    <div class="small text-muted" style="font-size: 0.72rem;">Sit &amp; Reach</div>
                    <span class="badge bg-success-subtle text-success small mt-2">Fleksibel</span>
                </div>
            </div>

            {{-- Catatan Guru Olahraga --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-chat-square-quote-fill text-danger"></i> Catatan & Rekomendasi Guru Olahraga PJOK:
                    </h6>
                    <blockquote class="blockquote mb-0 p-3 bg-light rounded-3 text-muted small">
                        "{{ $tesFisikTerakhir->catatan_guru_olahraga ?? 'Tingkat kebugaran dan ketahanan jasmani siswa berada di atas rata-rata kelompok usia. Disarankan mempertahankan pola makan seimbang, istirahat cukup, dan rutin melakukan pendinginan setelah latihan intensif.' }}"
                    </blockquote>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 3: NILAI NUMERASI & LITERASI --}}
    @if($activeTab === 'numerasi_literasi')
        <div class="row g-3 g-md-4">
            {{-- Kolom Numerasi --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-3 p-2 bg-primary text-white">
                            <i class="bi bi-calculator-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Capaian Numerasi &amp; TKA</h5>
                            <span class="text-muted small">Simulasi Tes Kemampuan Akademik (CBT)</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 bg-primary bg-opacity-10 mb-3 border border-primary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Rata-rata Skor Numerasi</div>
                                <h3 class="fw-bold text-primary mb-0">{{ $hexagonal['numerasi'] }} / 100</h3>
                            </div>
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">Level 4: Mahir</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">Riwayat Paket Soal Numerasi</h6>
                    <div class="list-group list-group-flush mb-3">
                        @forelse($tkaHasilList as $hasil)
                            <div class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark small">{{ $hasil->paket->judul_paket ?? 'Simulasi TKA Matematika Vokasi' }}</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        Benar: {{ $hasil->jumlah_benar }} | Salah: {{ $hasil->jumlah_salah }}
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-2.5 py-1">{{ $hasil->nilai_skor }} Poin</span>
                            </div>
                        @empty
                            <div class="p-3 bg-light rounded-3 text-center text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Data simulasi TKA Numerasi terintegrasi. Skor estimasi penalaran: <strong>{{ $hexagonal['numerasi'] }}</strong>.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Perlu latihan lanjutan?</span>
                        <a href="{{ route('lms.tka.simulasi') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            Buka Simulasi TKA <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Kolom Literasi --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-3 p-2 bg-teal text-white" style="background: #0d9488;">
                            <i class="bi bi-book-half fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Capaian Literasi &amp; Membaca</h5>
                            <span class="text-muted small">Perpustakaan Digital &amp; Sesi Rabu Pagi</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 mb-3 border" style="background: #f0fdfa; border-color: #99f6e4 !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Skor Spektrum Literasi</div>
                                <h3 class="fw-bold mb-0" style="color: #0d9488;">{{ $hexagonal['literasi'] }} / 100</h3>
                            </div>
                            <span class="badge text-white px-3 py-2 rounded-pill fw-bold" style="background: #0d9488;">Sangat Aktif</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">Buku Terakhir Dibaca di Perpus PWA</h6>
                    <div class="list-group list-group-flush mb-3">
                        @forelse($riwayatBacaList as $baca)
                            <div class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark small">{{ $baca->buku->judul ?? 'E-Book Digital' }}</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        Sampai Hal. {{ $baca->halaman_terakhir }} • Waktu: {{ $baca->durasi_format }}
                                    </div>
                                </div>
                                <span class="badge bg-teal text-white rounded-pill px-2.5 py-1" style="background: #0d9488;">{{ $baca->persentase_baca }}%</span>
                            </div>
                        @empty
                            <div class="p-3 bg-light rounded-3 text-center text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Telah menyelesaikan pembacaan modul kurikulum dan literasi sekolah.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Jelajahi perpustakaan digital</span>
                        <a href="{{ route('perpus.katalog') }}" class="btn btn-outline-teal btn-sm rounded-pill px-3" style="color: #0d9488; border-color: #0d9488;">
                            Buka Perpus Digital <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 4: BIODATA SANGAT LENGKAP --}}
    @if($activeTab === 'biodata')
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-primary"></i> Data Identitas Lengkap Siswa / Pegawai
            </h5>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Nama Lengkap</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? ($siswa->nama ?? '-') : $user->name }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">{{ $isViewingStudent ? 'Nomor Induk Siswa (NIS)' : 'NIP / NUPTK' }}</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? ($siswa->nis ?? '001001') : '19850314 201001 1 008' }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">{{ $isViewingStudent ? 'NISN (Nasional)' : 'Pangkat / Golongan' }}</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? '0082918274' : 'Penata Tk. I / III-d' }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">{{ $isViewingStudent ? 'Rombel / Kelas' : 'Unit Kerja' }}</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? ($siswa->rombel->nama_rombel ?? '-') : 'SMKN 2 Indramayu' }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">{{ $isViewingStudent ? 'Program / Kompetensi Keahlian' : 'Tugas Tambahan' }}</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? 'Pengembangan Perangkat Lunak & Gim (PPLG)' : 'Pendidik & Pembina' }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Tempat, Tanggal Lahir</div>
                        <div class="fw-bold text-dark">Indramayu, 14 Mei 2008</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Jenis Kelamin / Agama</div>
                        <div class="fw-bold text-dark">Laki-laki / Islam</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Nomor Telepon / WhatsApp</div>
                        <div class="fw-bold text-dark">0812-3456-7890</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">{{ $isViewingStudent ? 'Nama Orang Tua / Wali' : 'Status Kepegawaian' }}</div>
                        <div class="fw-bold text-dark">{{ $isViewingStudent ? 'Bpk. H. Supardi / Ibu Siti Maryam' : 'PNS / ASN Guru Tetap' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Alamat Domisili</div>
                        <div class="fw-bold text-dark">Jl. Ki Hajar Dewantara, Lemahmekar, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45212</div>
                    </div>
                </div>

                @if($isViewingStudent)
                    <div class="col-12 mt-3">
                        <div class="p-3 rounded-3 border bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-trophy-fill text-warning me-1.5"></i>Ekstrakurikuler &amp; Pengembangan Minat Bakat
                                </h6>
                                <span class="badge bg-primary rounded-pill">{{ count($ekskulSiswaList) }} Ekskul Diikuti</span>
                            </div>

                            @if(count($ekskulSiswaList) > 0)
                                <div class="row g-2 mt-1">
                                    @foreach($ekskulSiswaList as $agt)
                                        <div class="col-md-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark small">{{ $agt->ekskul->nama_ekskul }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">
                                                            {{ $agt->jabatan_anggota }} • Pembina: {{ $agt->ekskul->nama_pembina_lengkap }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success small">{{ $agt->status_keanggotaan }}</span>
                                                    <div class="text-muted" style="font-size: 0.68rem;">Kehadiran: {{ $agt->persentase_kehadiran }}%</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small mb-0">Siswa belum terdaftar pada cabang ekstrakurikuler manapun semester ini.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Chart.js CDN & Hexagonal Radar Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let radarChart = null;

    function renderHexagonalRadar(dataValues) {
        const ctx = document.getElementById('hexagonalRadarChart');
        if (!ctx) return;

        if (radarChart) {
            radarChart.destroy();
        }

        radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: [
                    'Kebugaran Fisik',
                    'Penalaran Numerasi',
                    'Literasi Membaca',
                    'Kedisiplinan Presensi',
                    'Keahlian Vokasi',
                    'Karakter & Budi Pekerti'
                ],
                datasets: [{
                    label: 'Performa Holistik Siswa',
                    data: [
                        dataValues.fisik,
                        dataValues.numerasi,
                        dataValues.literasi,
                        dataValues.disiplin,
                        dataValues.vokasi,
                        dataValues.karakter
                    ],
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.25)',
                    borderColor: '#4f46e5',
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#4f46e5',
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            lineWidth: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.08)'
                        },
                        pointLabels: {
                            font: {
                                size: 12,
                                weight: '600',
                                family: 'system-ui, sans-serif'
                            },
                            color: '#1e293b'
                        },
                        ticks: {
                            backdropColor: 'transparent',
                            color: '#64748b',
                            stepSize: 20
                        },
                        suggestedMin: 30,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` Skor: ${context.raw} / 100`;
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const stats = @json($hexagonal);
        renderHexagonalRadar(stats);
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('hexagonalDataUpdated', (data) => {
            renderHexagonalRadar(data[0] || data);
        });

        Livewire.hook('morph.updated', ({ component, cleanup }) => {
            if (document.getElementById('hexagonalRadarChart')) {
                const stats = @json($hexagonal);
                renderHexagonalRadar(stats);
            }
        });
    });
</script>
