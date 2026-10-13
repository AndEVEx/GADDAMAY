<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner Kartu Identitas --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #0f172a, #1e293b, #334155);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-qr-code-scan me-1"></i>SMART CARD DIGITAL (2 MUKA)
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">
                        {{ $isStudentCard ? 'Kartu Pelajar Siswa Digital' : 'Kartu Identitas Pegawai Digital' }}
                    </h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 680px;">
                        Kartu identitas resmi dilengkapi QR Code encoding <strong>{{ $isStudentCard ? 'Nomor Induk Siswa (NIS)' : 'Nomor Induk Pegawai (NIP)' }}</strong> untuk scanner gerbang, perpustakaan, dan presensi. Menampilkan dua sisi (muka depan &amp; belakang) siap cetak dan diunduh.
                    </p>
                </div>

                {{-- Action Switch Kartu Siswa vs Pegawai --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('profil.index', ['siswa_id' => $selectedSiswaId]) }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                    </a>

                    @if(in_array($user->role, ['admin', 'guru', 'waka', 'kepsek', 'guru_piket']))
                        <div class="btn-group btn-group-sm rounded-pill p-1 bg-black bg-opacity-30 border border-white border-opacity-20">
                            <button wire:click="toggleCardType('siswa')" class="btn {{ $isStudentCard ? 'btn-warning text-dark fw-bold' : 'btn-link text-white' }} rounded-pill px-3 py-1.5 text-decoration-none">
                                Kartu Siswa
                            </button>
                            <button wire:click="toggleCardType('pegawai')" class="btn {{ !$isStudentCard ? 'btn-warning text-dark fw-bold' : 'btn-link text-white' }} rounded-pill px-3 py-1.5 text-decoration-none">
                                Kartu Pegawai
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Pemilih Siswa (Bagi Guru/Admin) & Toolbar Download --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if($isStudentCard && count($siswaList) > 0)
                        <label class="small text-muted fw-bold mb-0 me-1">Pilih Siswa:</label>
                        <select wire:change="switchSiswa($event.target.value)" class="form-select form-select-sm rounded-pill bg-light border-0" style="min-width: 250px;">
                            @foreach($siswaList as $s)
                                <option value="{{ $s->id }}" {{ $selectedSiswaId == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }} - {{ $s->rombel->nama_rombel ?? 'X' }} (NIS: {{ $s->nis }})
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <div class="btn-group btn-group-sm">
                        <button type="button" wire:click="$set('viewMode', 'side-by-side')" class="btn {{ $viewMode === 'side-by-side' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }} rounded-start-pill px-3">
                            <i class="bi bi-layout-split me-1"></i> Berdampingan
                        </button>
                        <button type="button" wire:click="$set('viewMode', '3d-flip')" class="btn {{ $viewMode === '3d-flip' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }} rounded-end-pill px-3">
                            <i class="bi bi-arrow-repeat me-1"></i> Putar Kartu (3D Flip)
                        </button>
                    </div>
                </div>

                {{-- Action Buttons: Download & Cetak --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" onclick="downloadFrontCard()" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-download me-1"></i> Unduh Muka Depan
                    </button>
                    <button type="button" onclick="downloadBackCard()" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-download me-1"></i> Unduh Muka Belakang
                    </button>
                    <button type="button" onclick="downloadBothCards()" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 shadow-sm">
                        <i class="bi bi-images me-1"></i> Unduh 2 Muka (HD)
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Kartu
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- AREA KARTU TAMPILAN: BERDAMPINGAN ATAU 3D FLIP --}}
    @if($viewMode === 'side-by-side')
        <div class="row g-4 justify-content-center mb-4">
            {{-- MUKA DEPAN (FRONT) --}}
            <div class="col-12 col-md-6 col-xl-5 d-flex flex-column align-items-center">
                <div class="badge bg-primary px-3 py-1 rounded-pill mb-2 fw-bold shadow-sm">
                    <i class="bi bi-front me-1"></i> MUKA DEPAN (FRONT FACE)
                </div>

                <div id="card-front-face" class="id-card-wrapper position-relative rounded-4 overflow-hidden shadow-lg border">
                    {{-- Watermark Logo Sekolah di Background --}}
                    <div class="id-card-watermark position-absolute top-50 start-50 translate-middle pointer-events-none opacity-10">
                        <i class="bi bi-mortarboard-fill" style="font-size: 15rem; color: #4338ca;"></i>
                    </div>

                    {{-- Kop Header Kartu --}}
                    <div class="id-card-header p-3 position-relative z-1 d-flex align-items-center gap-2.5 text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca); border-bottom: 3px solid #f59e0b;">
                        <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-uppercase fw-semibold" style="font-size: 0.58rem; letter-spacing: 0.5px; opacity: 0.85;">Pemerintah Daerah Provinsi Jawa Barat</div>
                            <div class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">SMK NEGERI 2 INDRAMAYU</div>
                            <div class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.60rem; letter-spacing: 0.8px;">
                                {{ $isStudentCard ? 'KARTU TANDA PELAJAR' : 'KARTU IDENTITAS PEGAWAI' }}
                            </div>
                        </div>
                    </div>

                    {{-- Body Kartu Depan --}}
                    <div class="id-card-body p-3 position-relative z-1 d-flex flex-column justify-content-between" style="background: #ffffff; min-height: 220px;">
                        <div class="d-flex gap-3">
                            {{-- Foto Siswa / Pegawai & Chip IC --}}
                            <div class="d-flex flex-column align-items-center gap-2 flex-shrink-0">
                                {{-- Pas Foto Formal --}}
                                <div class="rounded-3 overflow-hidden shadow-sm border border-2 border-primary border-opacity-40" style="width: 95px; height: 120px; background: #e2e8f0;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($namaLengkap) }}&size=250&background=1e1b4b&color=ffffff&bold=true" alt="Foto" class="w-100 h-100" style="object-fit: cover;">
                                </div>

                                {{-- Chip Logam IC Smart Card Realistis --}}
                                <div class="smart-card-chip rounded-1 p-1 d-flex align-items-center justify-content-center border" style="width: 42px; height: 30px; background: linear-gradient(135deg, #d4af37, #f7d070, #aa820a); border-color: #8c6d02 !important;">
                                    <div class="w-100 h-100 rounded-1 border border-dark border-opacity-25 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);">
                                        <div style="width: 14px; height: 10px; border: 1px solid rgba(0,0,0,0.3); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Biodata Singkat & QR Code Muka Depan --}}
                            <div class="flex-grow-1 overflow-hidden d-flex flex-column justify-content-between">
                                <div>
                                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.62rem;">Nama Lengkap:</div>
                                    <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.95rem;">{{ $namaLengkap }}</h6>

                                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.62rem;">{{ $isStudentCard ? 'NIS / NISN:' : 'NIP / NUPTK:' }}</div>
                                    <div class="fw-bold text-primary mb-1" style="font-size: 0.85rem;">{{ $nomorInduk }}</div>

                                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.62rem;">{{ $isStudentCard ? 'Kelas / Kompetensi Keahlian:' : 'Jabatan / Peran:' }}</div>
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.78rem;">{{ $jabatanAtauKelas }}</div>
                                </div>

                                {{-- QR Code Depan --}}
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div id="qrcode-front-box" class="p-1 bg-white rounded border shadow-sm"></div>
                                        <div>
                                            <div class="text-muted fw-bold" style="font-size: 0.58rem;">SCANNER GATE RFID</div>
                                            <div class="fw-bold text-dark" style="font-size: 0.68rem;">{{ $nomorInduk }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end text-muted" style="font-size: 0.58rem;">
                                        <div>Berlaku s/d:</div>
                                        <div class="fw-bold text-dark">{{ date('Y') + 3 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Kartu Depan --}}
                    <div class="id-card-footer px-3 py-1.5 text-center text-white" style="background: #1e1b4b; font-size: 0.60rem; letter-spacing: 0.5px;">
                        SMKN 2 INDRAMAYU • PUSAT KEUNGGULAN VOKASI JAWA BARAT
                    </div>
                </div>
            </div>

            {{-- MUKA BELAKANG (BACK) --}}
            <div class="col-12 col-md-6 col-xl-5 d-flex flex-column align-items-center">
                <div class="badge bg-secondary px-3 py-1 rounded-pill mb-2 fw-bold shadow-sm">
                    <i class="bi bi-back me-1"></i> MUKA BELAKANG (BACK FACE)
                </div>

                <div id="card-back-face" class="id-card-wrapper position-relative rounded-4 overflow-hidden shadow-lg border">
                    {{-- Header Kartu Belakang --}}
                    <div class="id-card-header p-2.5 text-center text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81); border-bottom: 3px solid #f59e0b;">
                        <div class="fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                            KETENTUAN &amp; TATA TERTIB KARTU PINTAR
                        </div>
                    </div>

                    {{-- Body Kartu Belakang --}}
                    <div class="id-card-body p-3 position-relative z-1 d-flex flex-column justify-content-between" style="background: #ffffff; min-height: 250px;">
                        {{-- Aturan Pemilik Kartu --}}
                        <ol class="ps-3 mb-2 text-dark small" style="font-size: 0.65rem; line-height: 1.45;">
                            <li>Kartu ini adalah identitas resmi siswa/pegawai SMKN 2 Indramayu.</li>
                            <li>Wajib dibawa setiap hari dan digunakan saat presensi Gate, KBM, dan perpustakaan.</li>
                            <li>Dilarang meminjamkan, menggandakan, atau menyalahgunakan kartu ini kepada orang lain.</li>
                            <li>Bila kartu hilang atau rusak, segera melapor ke bagian Kesiswaan / Tata Usaha.</li>
                            <li>Barang siapa menemukan kartu ini, harap mengembalikan ke alamat sekolah tertera di bawah.</li>
                        </ol>

                        {{-- Tanda Tangan Digital Kepala Sekolah & QR Code Scanner Besar --}}
                        <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                            {{-- QR Code Scanner Belakang --}}
                            <div class="d-flex flex-column align-items-center">
                                <div id="qrcode-back-box" class="p-1.5 bg-white rounded border shadow-sm mb-1"></div>
                                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.58rem;">SCAN ID VERIFIKASI</div>
                            </div>

                            {{-- Tanda Tangan & Cap Kepala Sekolah --}}
                            <div class="text-center position-relative" style="min-width: 160px;">
                                <div class="text-muted" style="font-size: 0.62rem;">Indramayu, {{ date('d F Y') }}</div>
                                <div class="fw-bold text-dark" style="font-size: 0.65rem;">Kepala SMKN 2 Indramayu</div>
                                
                                {{-- Simulasi Cap & TTD --}}
                                <div class="position-relative my-1" style="height: 48px;">
                                    {{-- Cap Stempel Resmi Biru --}}
                                    <div class="position-absolute start-0 top-0 translate-middle-y rounded-circle border border-2 border-primary d-flex align-items-center justify-content-center opacity-75" 
                                         style="width: 52px; height: 52px; color: #1d4ed8; font-size: 0.45rem; transform: rotate(-15deg); border-style: double !important;">
                                        <div class="text-center fw-bold" style="line-height: 1.1;">SMKN 2<br>INDRAMAYU</div>
                                    </div>
                                    {{-- TTD Digital --}}
                                    <div class="position-absolute end-0 top-0 d-flex align-items-center justify-content-center w-100 h-100">
                                        <span class="fst-italic fw-bold text-dark font-monospace" style="font-size: 1.1rem; opacity: 0.85;">Dra. Hj. Sri Lestari</span>
                                    </div>
                                </div>

                                <div class="fw-bold text-dark text-decoration-underline" style="font-size: 0.68rem;">Dra. Hj. Sri Lestari, M.Pd.</div>
                                <div class="text-muted" style="font-size: 0.58rem;">NIP. 19680512 199303 2 004</div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Kartu Belakang: Alamat & Kontak --}}
                    <div class="id-card-footer px-3 py-1.5 text-center text-white" style="background: #0f172a; font-size: 0.58rem; line-height: 1.3;">
                        Jl. Pahlawan No. 47 Indramayu, Jawa Barat 45212 • Telp: (0234) 272018 • Web: smkn2indramayu.sch.id
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- VIEW MODE: 3D CARD FLIP --}}
        <div class="d-flex flex-column align-items-center mb-5">
            <div class="text-center mb-3">
                <span class="badge bg-primary px-3 py-1 rounded-pill small">Sentuh / Klik tombol untuk membalik kartu</span>
            </div>

            <div class="flip-card-container" onclick="toggleCardFlip()">
                <div class="flip-card-inner" id="flip-card-inner-box">
                    {{-- Sisi Depan --}}
                    <div class="flip-card-front">
                        <div class="id-card-wrapper position-relative rounded-4 overflow-hidden shadow-lg border">
                            <div class="id-card-header p-3 text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca); border-bottom: 3px solid #f59e0b;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-uppercase fw-semibold" style="font-size: 0.58rem;">Pemerintah Daerah Provinsi Jawa Barat</div>
                                        <div class="fw-bold text-uppercase" style="font-size: 0.78rem;">SMK NEGERI 2 INDRAMAYU</div>
                                        <div class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-0.5" style="font-size: 0.60rem;">
                                            {{ $isStudentCard ? 'KARTU TANDA PELAJAR' : 'KARTU IDENTITAS PEGAWAI' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="id-card-body p-3 bg-white" style="min-height: 220px;">
                                <div class="d-flex gap-3">
                                    <div class="d-flex flex-column align-items-center gap-2 flex-shrink-0">
                                        <div class="rounded-3 overflow-hidden shadow-sm border border-2 border-primary" style="width: 95px; height: 120px; background: #e2e8f0;">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($namaLengkap) }}&size=250&background=1e1b4b&color=ffffff&bold=true" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                        <div class="rounded-1 p-1 border" style="width: 42px; height: 28px; background: linear-gradient(135deg, #d4af37, #f7d070);"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted fw-bold" style="font-size: 0.62rem;">NAMA LENGKAP:</div>
                                        <h6 class="fw-bold text-dark text-truncate mb-1">{{ $namaLengkap }}</h6>
                                        <div class="text-muted fw-bold" style="font-size: 0.62rem;">{{ $isStudentCard ? 'NIS / NISN:' : 'NIP / NUPTK:' }}</div>
                                        <div class="fw-bold text-primary mb-1">{{ $nomorInduk }}</div>
                                        <div class="text-muted fw-bold" style="font-size: 0.62rem;">{{ $isStudentCard ? 'KELAS / KOMPETENSI:' : 'JABATAN:' }}</div>
                                        <div class="fw-semibold text-dark text-truncate">{{ $jabatanAtauKelas }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="id-card-footer px-3 py-1.5 text-center text-white" style="background: #1e1b4b; font-size: 0.60rem;">
                                KLIK UNTUK MEMBALIK KARTU KE SISI BELAKANG <i class="bi bi-arrow-repeat ms-1"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Belakang --}}
                    <div class="flip-card-back">
                        <div class="id-card-wrapper position-relative rounded-4 overflow-hidden shadow-lg border">
                            <div class="id-card-header p-2.5 text-center text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81); border-bottom: 3px solid #f59e0b;">
                                <div class="fw-bold text-uppercase" style="font-size: 0.72rem;">KETENTUAN KARTU PINTAR</div>
                            </div>
                            <div class="id-card-body p-3 bg-white" style="min-height: 250px;">
                                <ol class="ps-3 mb-2 text-dark small" style="font-size: 0.65rem; line-height: 1.45;">
                                    <li>Kartu identitas resmi siswa/pegawai SMKN 2 Indramayu.</li>
                                    <li>Wajib dibawa setiap hari untuk presensi Gate &amp; Perpus.</li>
                                    <li>Dilarang dipinjamkan kepada orang lain.</li>
                                </ol>
                                <div class="text-center pt-3 border-top">
                                    <div class="text-muted" style="font-size: 0.62rem;">Kepala SMKN 2 Indramayu</div>
                                    <div class="fw-bold text-dark text-decoration-underline" style="font-size: 0.68rem;">Dra. Hj. Sri Lestari, M.Pd.</div>
                                    <div class="text-muted" style="font-size: 0.58rem;">NIP. 19680512 199303 2 004</div>
                                </div>
                            </div>
                            <div class="id-card-footer px-3 py-1.5 text-center text-white" style="background: #0f172a; font-size: 0.58rem;">
                                KLIK UNTUK MEMBALIK KE SISI DEPAN <i class="bi bi-arrow-repeat ms-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-3 px-4 shadow-sm" onclick="toggleCardFlip()">
                <i class="bi bi-arrow-repeat me-1"></i> Putar Kartu Sekarang
            </button>
        </div>
    @endif
</div>

{{-- Styles Khusus ID Card --}}
<style>
    .id-card-wrapper {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    /* 3D Card Flip Styling */
    .flip-card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 420px;
        cursor: pointer;
    }
    .flip-card-inner {
        position: relative;
        width: 100%;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }
    .flip-card-inner.is-flipped {
        transform: rotateY(180deg);
    }
    .flip-card-front, .flip-card-back {
        width: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .flip-card-back {
        position: absolute;
        top: 0;
        left: 0;
        transform: rotateY(180deg);
    }
    @media print {
        header, footer, nav, .btn, .card:not(.id-card-wrapper), select {
            display: none !important;
        }
        .id-card-wrapper {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            page-break-inside: avoid;
            margin: 10px auto;
        }
    }
</style>

{{-- Libraries: QRCode.js & html2canvas --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    let isCardFlipped = false;
    function toggleCardFlip() {
        const box = document.getElementById('flip-card-inner-box');
        if (!box) return;
        isCardFlipped = !isCardFlipped;
        if (isCardFlipped) {
            box.classList.add('is-flipped');
        } else {
            box.classList.remove('is-flipped');
        }
    }

    function generateQRCodes(qrText) {
        const frontBox = document.getElementById('qrcode-front-box');
        const backBox = document.getElementById('qrcode-back-box');

        if (frontBox) {
            frontBox.innerHTML = '';
            new QRCode(frontBox, {
                text: qrText,
                width: 46,
                height: 46,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        if (backBox) {
            backBox.innerHTML = '';
            new QRCode(backBox, {
                text: qrText,
                width: 64,
                height: 64,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    }

    // Download Functionality via html2canvas
    async function downloadFrontCard() {
        const el = document.getElementById('card-front-face');
        if (!el) {
            alert('Silakan gunakan mode Berdampingan untuk mengunduh gambar kartu.');
            return;
        }
        const canvas = await html2canvas(el, { scale: 3, useCORS: true });
        const link = document.createElement('a');
        link.download = `Kartu_Depan_{{ preg_replace('/[^A-Za-z0-9_-]/', '_', $namaLengkap) }}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    async function downloadBackCard() {
        const el = document.getElementById('card-back-face');
        if (!el) {
            alert('Silakan gunakan mode Berdampingan untuk mengunduh gambar kartu.');
            return;
        }
        const canvas = await html2canvas(el, { scale: 3, useCORS: true });
        const link = document.createElement('a');
        link.download = `Kartu_Belakang_{{ preg_replace('/[^A-Za-z0-9_-]/', '_', $namaLengkap) }}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    async function downloadBothCards() {
        await downloadFrontCard();
        setTimeout(async () => {
            await downloadBackCard();
        }, 800);
    }

    document.addEventListener('DOMContentLoaded', () => {
        generateQRCodes('{{ $qrCodeValue }}');
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('qrCodeRegenerate', (data) => {
            generateQRCodes(data[0]?.code || data?.code || '{{ $qrCodeValue }}');
        });

        Livewire.hook('morph.updated', ({ component, cleanup }) => {
            generateQRCodes('{{ $qrCodeValue }}');
        });
    });
</script>
