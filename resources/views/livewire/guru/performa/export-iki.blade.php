<div class="container-fluid px-3 py-3" style="max-width: 900px;">
    {{-- Breadcrumb & Title --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 fw-bold rounded-pill small">
                <i class="bi bi-speedometer me-1"></i> Performa Saya
            </span>
        </div>
        <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Export Laporan IKI Guru (PDF)
        </h4>
        <p class="text-muted small mb-0">Cetak dokumen resmi Indikator Kinerja Individu (IKI) Guru berkop SMKN 2 Indramayu.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-check2-square text-primary"></i> Pilih Komponen Laporan IKI yang Akan Diexport
            </h6>
        </div>
        <div class="card-body p-4">
            {{-- Checkboxes for 3 components --}}
            <div class="list-group list-group-flush mb-4">
                <label class="list-group-item d-flex gap-3 py-3 rounded-3 border mb-2 cursor-pointer {{ $includeJadwal ? 'bg-light border-primary' : '' }}">
                    <input class="form-control-input flex-shrink-0 mt-1" type="checkbox" wire:model.live="includeJadwal" style="width: 20px; height: 20px;">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-week text-primary"></i> 1. Tabel Jam Mengajar Guru Dalam Seminggu
                        </div>
                        <small class="text-muted d-block mt-1">
                            Memuat jadwal mengajar mingguan (Hari, Jam Ke, Waktu, Kelas, Mata Pelajaran, dan Total Beban JP seminggu).
                        </small>
                    </div>
                </label>

                <label class="list-group-item d-flex gap-3 py-3 rounded-3 border mb-2 cursor-pointer {{ $includeSiswa ? 'bg-light border-info' : '' }}">
                    <input class="form-control-input flex-shrink-0 mt-1" type="checkbox" wire:model.live="includeSiswa" style="width: 20px; height: 20px;">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-people text-info"></i> 2. Tabel Daftar Nama Siswa yang Diajar
                        </div>
                        <small class="text-muted d-block mt-1">
                            Daftar siswa dikelompokkan per Kelas dan Mata Pelajaran (terpisah per mapel) beserta total siswa yang diampu.
                        </small>
                    </div>
                </label>

                <label class="list-group-item d-flex gap-3 py-3 rounded-3 border mb-2 cursor-pointer {{ $includeKehadiran ? 'bg-light border-warning' : '' }}">
                    <input class="form-control-input flex-shrink-0 mt-1" type="checkbox" wire:model.live="includeKehadiran" style="width: 20px; height: 20px;">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-warning"></i> 3. Tabel Realisasi Jam Masuk Kelas Bulanan
                        </div>
                        <small class="text-muted d-block mt-1">
                            Perbandingan total jam (JP) masuk kelas realisasi dibanding semestinya (Target vs Realisasi & Ketercapaian %).
                        </small>
                    </div>
                </label>
            </div>

            {{-- Period Filter for Kehadiran --}}
            @if($includeKehadiran)
                <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <label class="form-label fw-bold small text-dark mb-2">
                        <i class="bi bi-calendar-month me-1 text-primary"></i> Periode Bulan & Tahun Realisasi:
                    </label>
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-6">
                            <input type="month" wire:model.live="bulan" class="form-control" style="min-height: 42px;">
                        </div>
                        <div class="col-12 col-md-6 text-muted small">
                            Laporan realisasi kehadiran akan dihitung untuk bulan <strong>{{ $namaBulan }}</strong>.
                        </div>
                    </div>
                </div>
            @endif

            {{-- Signature Option --}}
            <div class="mb-4">
                <label class="form-label fw-bold small text-dark">
                    <i class="bi bi-pen me-1 text-secondary"></i> Pengesahan Mengetahui:
                </label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="penandatangan" value="kepsek" id="ttdKepsek">
                        <label class="form-check-label" for="ttdKepsek">
                            Kepala Sekolah
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="penandatangan" value="waka" id="ttdWaka">
                        <label class="form-check-label" for="ttdWaka">
                            Wakasek Bidang Kurikulum
                        </label>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                <button wire:click="downloadPdf" class="btn btn-danger flex-fill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; min-height: 48px;" {{ (!$includeJadwal && !$includeSiswa && !$includeKehadiran) ? 'disabled' : '' }}>
                    <i class="bi bi-download fs-5"></i> Download PDF Laporan IKI
                </button>
                <button wire:click="previewPdf" class="btn btn-outline-danger py-2.5 px-4 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; min-height: 48px;" {{ (!$includeJadwal && !$includeSiswa && !$includeKehadiran) ? 'disabled' : '' }}>
                    <i class="bi bi-eye fs-5"></i> Preview PDF
                </button>
            </div>
        </div>
    </div>
</div>