<div>
    {{-- Hero Section --}}
    <section class="hero-section py-5">
        <div class="hero-pattern"></div>
        <div class="container position-relative py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-white bg-opacity-20 text-white border border-white-50 px-3 py-2 rounded-pill mb-3">
                        <i class="bi bi-stars text-warning me-1"></i> PLATFORM DIGITAL VOKASI TERPADU SMKN 2 INDRAMAYU
                    </span>
                    <h1 class="display-4 fw-extrabold text-white mb-3">
                        GADDAMAY
                    </h1>
                    <p class="lead text-white-50 mb-4" style="max-width: 650px;">
                        Ekosistem satu pintu yang menyatukan <strong>Presensi Gerbang</strong>, <strong>Jurnal Agenda KBM</strong>, <strong>Perizinan Terpadu</strong>, <strong>Jurnal PKL Vokasi</strong>, <strong>Monitoring Parenting</strong>, dan <strong>Teaching Factory</strong>.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning px-4 py-2 fw-bold shadow">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                                </a>
                            @else
                                <a href="{{ route('guru.dashboard') }}" class="btn btn-warning px-4 py-2 fw-bold shadow">
                                    <i class="bi bi-house-door me-1"></i> Beranda Guru
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning px-4 py-2 fw-bold shadow">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sistem
                            </a>
                        @endauth
                        <a href="{{ route('perizinan.pengajuan') }}" class="btn btn-outline-light px-4 py-2 fw-bold">
                            <i class="bi bi-file-earmark-plus me-1"></i> Ajukan Izin Siswa
                        </a>
                        <a href="{{ route('perizinan.index') }}" class="btn btn-success px-4 py-2 fw-bold shadow">
                            <i class="bi bi-shield-check me-1"></i> Meja Guru Piket
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="card bg-white bg-opacity-10 border border-white-50 rounded-4 p-4 text-white shadow-lg backdrop-blur">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-uppercase fw-bold text-warning">Waktu Sekolah (WIB)</span>
                            <span class="badge bg-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> AKTIF</span>
                        </div>
                        <h2 class="fw-bold mb-1 font-monospace">{{ $today->format('H:i') }} <span class="fs-6 fw-normal">WIB</span></h2>
                        <div class="small text-white-50 mb-3">{{ $today->translatedFormat('l, d F Y') }}</div>
                        <hr class="border-white-50 my-2">
                        <div class="row g-2 text-center pt-2">
                            <div class="col-6">
                                <div class="p-2 bg-white bg-opacity-10 rounded-3">
                                    <div class="fs-5 fw-bold">{{ $stats['totalRombel'] }}</div>
                                    <div class="small text-white-50" style="font-size: 0.75rem;">Kelas Aktif</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-white bg-opacity-10 rounded-3">
                                    <div class="fs-5 fw-bold text-warning">{{ $stats['izinHariIni'] }}</div>
                                    <div class="small text-white-50" style="font-size: 0.75rem;">Izin Sah Hari Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6 Modul Layanan Utama --}}
    <section class="container py-5" id="modul-layanan">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill text-uppercase">
                Pusat Navigasi Layanan
            </span>
            <h2 class="fw-bold mt-2 text-dark">6 Modul Terpadu GADDAMAY</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                Satu sistem terintegrasi yang melayani seluruh siklus kegiatan siswa, guru, orang tua, dan mitra industri sekolah.
            </p>
        </div>

        <div class="row g-4">
            {{-- Modul 1: Presensi Gerbang --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-door-open-fill fs-3"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i>ONLINE (Port 8080)
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">1. Presensi Gerbang (Gate)</h5>
                        <p class="text-muted small mb-3">
                            Pencatatan presensi kedatangan & kepulangan siswa via Scanner RFID / QR Kartu Pelajar, mesin sidik jari ADMS, dan sistem pergantian shift pegawai.
                        </p>
                    </div>
                    <div class="pt-3 border-top">
                        <a href="http://localhost:8080" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                            Buka Layanan Gerbang <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 2: Agenda KBM Guru --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-journal-check fs-3"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i>ONLINE (Port 8000)
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">2. Agenda & Jurnal Guru</h5>
                        <p class="text-muted small mb-3">
                            Jurnal KBM harian, presensi siswa per jam mata pelajaran, evaluasi ketercapaian KKTP Kurikulum Merdeka, dan ekspor laporan kinerja IKI.
                        </p>
                    </div>
                    <div class="pt-3 border-top">
                        <a href="{{ route('login') }}" class="btn btn-outline-info btn-sm w-100 fw-semibold text-dark">
                            Buka Agenda Guru <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 3: Perizinan Terpadu --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-success border-2 shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-success text-white rounded-3 p-3 shadow-sm">
                                <i class="bi bi-shield-check fs-3"></i>
                            </div>
                            <span class="badge bg-success text-white px-2 py-1 small">
                                <i class="bi bi-star-fill me-1"></i>FASE 1 LENGKAP
                            </span>
                        </div>
                        <h5 class="fw-bold text-success mb-2">3. Perizinan Terpadu</h5>
                        <p class="text-muted small mb-3">
                            Pengajuan izin sakit, keperluan ortu, dispensasi lomba, verifikasi Guru Piket & Walas, auto-lock KBM kelas, serta failover WhatsApp pribadi (wa.me).
                        </p>
                    </div>
                    <div class="pt-3 border-top">
                        <a href="{{ route('perizinan.index') }}" class="btn btn-success btn-sm w-100 fw-bold shadow-sm">
                            Buka Meja Perizinan <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 4: Jurnal PKL Vokasi --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-primary border-2 shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-primary text-white rounded-3 p-3 shadow-sm">
                                <i class="bi bi-briefcase-fill fs-3"></i>
                            </div>
                            <span class="badge bg-primary text-white px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i>FASE 2 AKTIF
                            </span>
                        </div>
                        <h5 class="fw-bold text-primary mb-2">4. Jurnal PKL Vokasi</h5>
                        <p class="text-muted small mb-3">
                            Presensi Geolocation radius DUDI + swafoto, logbook harian 11 elemen CP PPLG 2025, paraf WhatsApp Magic Link mentor industri, dan asesmen bobot 5:3:2.
                        </p>
                    </div>
                    <div class="pt-3 border-top d-flex gap-2">
                        <a href="{{ route('pkl.guru.monitoring') }}" class="btn btn-primary btn-sm flex-fill fw-bold shadow-sm">
                            Monitoring Guru <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('pkl.siswa.presensi') }}" class="btn btn-outline-primary btn-sm flex-fill fw-semibold">
                            Presensi Siswa
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 5: Buku Parenting Digital --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-danger border-2 shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-danger text-white rounded-3 p-3 shadow-sm">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                            <span class="badge bg-danger text-white px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i>FASE 3 AKTIF
                            </span>
                        </div>
                        <h5 class="fw-bold text-danger mb-2">5. Buku Parenting Digital</h5>
                        <p class="text-muted small mb-3">
                            Portal pengawasan orang tua tanpa kata sandi (login via NISN / OTP WhatsApp), timeline presensi gerbang & KBM, dan buku poin kedisiplinan BK.
                        </p>
                    </div>
                    <div class="pt-3 border-top d-flex gap-2">
                        <a href="{{ route('parenting.login') }}" class="btn btn-danger btn-sm flex-fill fw-bold shadow-sm">
                            Portal Orang Tua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('parenting.guru.disiplin') }}" class="btn btn-outline-danger btn-sm flex-fill fw-semibold">
                            Disiplin & BK
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 6: Teaching Factory (TEFA) --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-dark border-2 shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-dark text-white rounded-3 p-3 shadow-sm">
                                <i class="bi bi-gear-wide-connected fs-3"></i>
                            </div>
                            <span class="badge bg-dark text-white px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i>FASE 4 AKTIF
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">6. Teaching Factory (TEFA)</h5>
                        <p class="text-muted small mb-3">
                            Pencatatan order pesanan konsumen (SPK), pembagian job sheet tim siswa vokasi, log jam kerja mesin produksi, lembar QC instruktur, dan portofolio keahlian.
                        </p>
                    </div>
                    <div class="pt-3 border-top d-flex gap-2">
                        <a href="{{ route('tefa.admin.orders') }}" class="btn btn-dark btn-sm flex-fill fw-bold shadow-sm">
                            Manajemen TEFA <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('tefa.siswa.log') }}" class="btn btn-outline-dark btn-sm flex-fill fw-semibold">
                            Logsheet Siswa
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 7: LMS Vokasi & Pembelajaran Diferensiasi --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-info border-2 shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-info text-white rounded-3 p-3 shadow-sm">
                                <i class="bi bi-mortarboard-fill fs-3"></i>
                            </div>
                            <span class="badge bg-info text-white px-2 py-1 small">
                                <i class="bi bi-stars me-1"></i>LMS DIFERENSIASI
                            </span>
                        </div>
                        <h5 class="fw-bold text-info mb-2">7. LMS Vokasi &amp; Pusat Belajar</h5>
                        <p class="text-muted small mb-3">
                            Pembelajaran personalisasi per siswa terkoneksi Capaian Pembelajaran (CP) &amp; TP. Dilengkapi modul intensif bimbingan <strong>LKS</strong> (Lomba Keterampilan Siswa) dan persiapan Ujikom.
                        </p>
                    </div>
                    <div class="pt-3 border-top d-flex gap-2">
                        <a href="{{ route('lms.guru.materi') }}" class="btn btn-info text-white btn-sm flex-fill fw-bold shadow-sm">
                            Kelola Materi &amp; Tugas <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('lms.siswa.dashboard') }}" class="btn btn-outline-info btn-sm flex-fill fw-semibold">
                            Portal Siswa
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modul 8: Pendaftaran & Sertifikasi Ujikom LSP-P1 --}}
            <div class="col-md-6 col-lg-4">
                <div class="module-card p-4 h-100 d-flex flex-column justify-content-between border-secondary border-2 shadow-sm" style="border-color: #6366f1 !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-indigo text-white rounded-3 p-3 shadow-sm" style="background-color: #6366f1;">
                                <i class="bi bi-patch-check-fill fs-3"></i>
                            </div>
                            <span class="badge text-white px-2 py-1 small" style="background-color: #6366f1;">
                                <i class="bi bi-award-fill me-1"></i>LSP-P1 RESMI
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #6366f1;">8. Pendaftaran &amp; Asesmen Ujikom</h5>
                        <p class="text-muted small mb-3">
                            Aplikasi pendaftaran mandiri UKK / LSP-P1. Unggah bukti portofolio APL-01 &amp; APL-02, verifikasi berkas oleh asesor, kartu peserta digital, jadwal TUK, dan penetapan kompetensi.
                        </p>
                    </div>
                    <div class="pt-3 border-top d-flex gap-2">
                        <a href="{{ route('ujikom.daftar') }}" class="btn text-white btn-sm flex-fill fw-bold shadow-sm" style="background-color: #6366f1;">
                            Daftar Asesi <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('ujikom.admin.verifikasi') }}" class="btn btn-outline-primary btn-sm flex-fill fw-semibold">
                            Meja Asesor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Panduan Deployment & Topologi Server --}}
    <section class="bg-white py-5 border-top">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="badge bg-dark text-white px-3 py-2 rounded-pill small mb-2">
                        <i class="bi bi-server me-1"></i> INFRASTRUKTUR & DEPLOYMENT
                    </span>
                    <h3 class="fw-bold text-dark mb-3">Satu Server Fisik Sekolah + Tunnel VPS</h3>
                    <p class="text-muted">
                        Arsitektur GADDAMAY dirancang sangat hemat biaya dan tangguh untuk operasional sekolah:
                    </p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Lokal LAN Super Cepat:</strong> Scan RFID & scanner USB di gerbang beroperasi sub-detik (&lt;300ms) tanpa bergantung koneksi internet luar.</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Tunnel VPS Terenkripsi:</strong> Diekspos ke publik menggunakan Cloudflare Tunnel atau Nginx Reverse Proxy via VPS dengan domain resmi sekolah (SSL otomatis).</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Multi-Driver WhatsApp:</strong> Mendukung GOWA / WA-AKG dengan fallback manual via tautan wa.me pribadi guru.</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card bg-light border p-3 rounded-4 shadow-sm font-monospace small">
                        <div class="text-muted mb-2">// Skrip Cepat Menjalankan Layanan Lokal:</div>
                        <div class="text-success fw-bold">scripts\run-all.bat</div>
                        <div class="text-muted mt-2">// Port Aktif:</div>
                        <div>- Agenda &amp; Perizinan (Laravel): <span class="text-primary">http://localhost:8000</span></div>
                        <div>- Gate Absensi (CI4): <span class="text-info">http://localhost:8080</span></div>
                        <div>- MySQL Database: <span class="text-warning">localhost:3306 (db_gaddamay)</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>