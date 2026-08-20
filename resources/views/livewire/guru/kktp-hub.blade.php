<div>
    <style>
        .hover-card {
            transition: all 0.2s ease-in-out;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 .5rem 1.25rem rgba(0,0,0,.12)!important;
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1.25rem;
        }
        .icon-circle i {
            font-size: 2.2rem;
        }
        .bg-emerald-soft {
            background-color: rgba(16, 185, 129, 0.12);
            color: #059669;
        }
        .bg-blue-soft {
            background-color: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }
        .bg-purple-soft {
            background-color: rgba(139, 92, 246, 0.12);
            color: #7c3aed;
        }
    </style>

    <div class="container py-3">
        <div class="text-center mb-4 animate-fade-in-up">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-list-check text-primary me-2"></i> Menu KKTP Guru
            </h3>
            <p class="text-muted small">Kelola Tujuan Pembelajaran, Import Format MGMP, & Nilai Ketercapaian Siswa</p>
        </div>

        <div class="row g-3">
            <!-- 1. Import KKTP Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card animate-fade-in-up border-top border-success border-3" style="border-radius: 14px;">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-circle bg-emerald-soft">
                            <i class="bi bi-file-earmark-excel"></i>
                        </div>
                        <h5 class="fw-bold card-title mb-2">Import KKTP (Excel)</h5>
                        <p class="card-text text-muted small mb-4 flex-grow-1">
                            Upload file Excel KKTP dari MGMP untuk mengimport daftar TP per pertemuan secara otomatis ke mata pelajaran Anda.
                        </p>
                        <a href="{{ route('guru.import-kktp') }}" wire:navigate class="btn btn-success w-100 d-flex align-items-center justify-content-center fw-semibold mt-auto" style="min-height: 48px; border-radius: 10px;">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Import KKTP
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Setting KKTP Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card animate-fade-in-up border-top border-primary border-3" style="border-radius: 14px; animation-delay: 0.05s;">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-circle bg-blue-soft">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h5 class="fw-bold card-title mb-2">Setting KKTP</h5>
                        <p class="card-text text-muted small mb-4 flex-grow-1">
                            Kelola Tujuan Pembelajaran (TP) untuk mata pelajaran yang Anda ampu. Tambah manual, edit deskripsi, atau hapus item TP.
                        </p>
                        <a href="{{ route('guru.kktp-setting') }}" wire:navigate class="btn btn-primary w-100 d-flex align-items-center justify-content-center fw-semibold mt-auto" style="min-height: 48px; border-radius: 10px;">
                            <i class="bi bi-gear-fill me-2"></i> Setting KKTP
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Nilai KKTP Murid Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card animate-fade-in-up border-top border-purple border-3" style="border-radius: 14px; animation-delay: 0.1s;">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-circle bg-purple-soft">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h5 class="fw-bold card-title mb-2">Nilai KKTP Murid</h5>
                        <p class="card-text text-muted small mb-4 flex-grow-1">
                            Input dan pantau ketercapaian nilai KKTP per siswa untuk setiap rombel/kelas yang Anda ajar.
                        </p>
                        <a href="{{ route('guru.kktp-nilai') }}" wire:navigate class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center fw-semibold mt-auto" style="min-height: 48px; border-radius: 10px;">
                            <i class="bi bi-clipboard-check-fill me-2"></i> Buka Nilai KKTP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
