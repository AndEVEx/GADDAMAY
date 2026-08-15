<div>
    <style>
        .hover-card {
            transition: all 0.2s ease-in-out;
        }
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
        }
        .icon-circle i {
            font-size: 2.5rem;
        }
        .bg-blue-soft {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .bg-green-soft {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }
    </style>

    <div class="container py-4">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="h3 mb-2">
                <i class="bi bi-list-check text-primary me-2"></i> Menu KKTP
            </h1>
            <p class="text-muted">Kelola Tujuan Pembelajaran & Nilai Ketercapaian Siswa</p>
        </div>

        <div class="row g-4 max-w-4xl mx-auto">
            <!-- Setting KKTP Card -->
            <div class="col-12 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card animate-fade-in-up" style="border-radius: 12px;">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-blue-soft">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3 class="h5 card-title mb-3">Setting KKTP</h3>
                        <p class="card-text text-muted mb-4">
                            Kelola Tujuan Pembelajaran (TP) untuk mata pelajaran yang Anda ampu. Tambah, edit, hapus, atau import dari file Excel.
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('guru.kktp-setting') }}" wire:navigate class="btn btn-primary w-100 d-flex align-items-center justify-content-center fw-medium" style="min-height: 48px; border-radius: 10px;">
                            <i class="bi bi-gear-fill me-2"></i> Buka Setting KKTP
                        </a>
                    </div>
                </div>
            </div>

            <!-- Nilai KKTP Murid Card -->
            <div class="col-12 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card animate-fade-in-up" style="border-radius: 12px; animation-delay: 0.1s;">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-green-soft">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h3 class="h5 card-title mb-3">Nilai KKTP Murid</h3>
                        <p class="card-text text-muted mb-4">
                            Input dan lihat ketercapaian KKTP per siswa untuk setiap kelas yang Anda ampu.
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('guru.kktp-nilai') }}" wire:navigate class="btn btn-success w-100 d-flex align-items-center justify-content-center fw-medium" style="min-height: 48px; border-radius: 10px;">
                            <i class="bi bi-clipboard-check-fill me-2"></i> Buka Nilai KKTP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
