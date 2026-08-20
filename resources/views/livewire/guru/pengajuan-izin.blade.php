<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-x text-primary me-2"></i>Pengajuan Izin Harian Guru</h4>
            <p class="text-muted small mb-0">Izin mendadak / harian langsung ke Waka Kurikulum & Admin tanpa perlu input tanggal</p>
        </div>
        <button wire:click="openForm" class="btn btn-primary d-flex align-items-center gap-2" style="min-height: 44px; border-radius: 10px;">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Ajukan Izin Hari Ini</span>
        </button>
    </div>

    {{-- Info Card --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-3 animate-fade-in-up" style="border-radius: 14px;">
        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
        <div class="small">
            <strong>Izin bersifat Harian / Mendesak:</strong> Izin yang diajukan otomatis berlaku untuk <strong>Hari Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }})</strong>. Anda cukup memilih apakah izin untuk <em>Seharian Penuh</em> atau <em>Jam Pelajaran Tertentu</em>.
        </div>
    </div>

    {{-- History Cards / Table --}}
    @if($riwayatIzin->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-calendar2-check fs-2"></i>
                </div>
                <h6 class="fw-bold">Belum Ada Riwayat Pengajuan Izin</h6>
                <p class="text-muted small mb-3">Jika Anda berhalangan hadir atau ada urusan mendesak hari ini, silakan klik tombol di bawah.</p>
                <button wire:click="openForm" class="btn btn-outline-primary" style="border-radius: 10px;">
                    <i class="bi bi-plus-lg me-1"></i> Buat Pengajuan Izin Hari Ini
                </button>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($riwayatIzin as $izin)
                @php
                    $badge = $izin->status_badge;
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 animate-fade-in-up" style="border-radius: 14px; transition: transform 0.2s;">
                        <div class="card-body p-3 d-flex flex-column">
                            {{-- Status & Jenis Izin --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge {{ $badge['class'] }} px-2 py-1 fw-semibold" style="font-size: 0.75rem; border-radius: 6px;">
                                    <i class="bi {{ $badge['icon'] }} me-1"></i>{{ $badge['text'] }}
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                    {{ $izin->jenis_izin_label }}
                                </span>
                            </div>

                            {{-- Tanggal & Waktu --}}
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                {{ $izin->tanggal_mulai->translatedFormat('l, d F Y') }}
                            </h6>
                            <div class="mb-2">
                                <span class="badge {{ $izin->is_seharian ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning border border-warning' }} px-2 py-1 small">
                                    <i class="bi bi-clock-fill me-1"></i> {{ $izin->waktu_display }}
                                </span>
                            </div>

                            {{-- Alasan --}}
                            <p class="text-muted small mb-2 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $izin->alasan }}
                            </p>

                            {{-- Guru Pengganti / Info --}}
                            @if($izin->guruPengganti)
                                <div class="bg-light rounded-3 p-2 small mb-2">
                                    <span class="text-muted">Guru Pengganti:</span>
                                    <div class="fw-semibold text-dark"><i class="bi bi-person-badge text-info me-1"></i>{{ $izin->guruPengganti->name }}</div>
                                </div>
                            @endif

                            @if($izin->catatan_waka)
                                <div class="alert alert-{{ $izin->status === 'disetujui' ? 'success' : 'danger' }} bg-opacity-10 py-1 px-2 mb-2 small" style="font-size: 0.75rem;">
                                    <strong>Catatan Waka:</strong> {{ $izin->catatan_waka }}
                                </div>
                            @endif

                            {{-- Footer Actions --}}
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto gap-2">
                                <button wire:click="showDetail('{{ $izin->id }}')" class="btn btn-sm btn-light border text-primary flex-fill" style="min-height: 38px; border-radius: 8px;">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </button>
                                @if($izin->status === 'menunggu')
                                    <button wire:click="batalkanIzin('{{ $izin->id }}')"
                                            wire:confirm="Yakin ingin membatalkan pengajuan izin ini?"
                                            class="btn btn-sm btn-outline-danger" style="min-height: 38px; border-radius: 8px;" title="Batalkan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- MODAL PENGAJUAN IZIN HARIAN --}}
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1a56db, #0d47a1); border-radius: 16px 16px 0 0;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0"><i class="bi bi-calendar-plus me-2"></i>Form Pengajuan Izin Harian</h5>
                        <small class="text-white-50">Izin berlaku untuk hari ini: {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeForm"></button>
                </div>
                <form wire:submit.prevent="submitIzin">
                    <div class="modal-body p-3 p-md-4">
                        <div class="row g-3">
                            {{-- Info Tanggal Otomatis --}}
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                                        <div>
                                            <div class="small text-muted fw-semibold">Tanggal Pengajuan Izin:</div>
                                            <div class="fw-bold fs-6 text-dark">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }} (Hari Ini)</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
                                        <i class="bi bi-lightning-charge-fill me-1"></i>Izin Mendadak / Harian
                                    </span>
                                </div>
                            </div>

                            {{-- Pilihan Jam Pelajaran / Waktu Izin --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Waktu / Jam Pelajaran Izin <span class="text-danger">*</span></label>
                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-6">
                                        <label class="card p-3 border cursor-pointer h-100 {{ $isSeharian ? 'border-primary bg-primary bg-opacity-10' : 'bg-white' }}" style="cursor: pointer; border-radius: 10px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="radio" wire:model.live="isSeharian" value="1" class="form-check-input mt-0" style="width: 20px; height: 20px;">
                                                <div>
                                                    <div class="fw-bold text-dark">Seharian Penuh</div>
                                                    <div class="small text-muted">Izin untuk seluruh jam pelajaran hari ini</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="card p-3 border cursor-pointer h-100 {{ !$isSeharian ? 'border-primary bg-primary bg-opacity-10' : 'bg-white' }}" style="cursor: pointer; border-radius: 10px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="radio" wire:model.live="isSeharian" value="0" class="form-check-input mt-0" style="width: 20px; height: 20px;">
                                                <div>
                                                    <div class="fw-bold text-dark">Jam / Sesi Tertentu</div>
                                                    <div class="small text-muted">Pilih jam atau jadwal mengajar tertentu</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Selector Jam Pelajaran jika tidak seharian --}}
                                @if(!$isSeharian)
                                    <div class="card border p-3 bg-light animate-fade-in-up" style="border-radius: 10px;">
                                        <h6 class="fw-bold small text-primary mb-2"><i class="bi bi-clock-history me-1"></i>Pilih Sesi Mengajar Hari Ini:</h6>
                                        @if($jadwalHariIni->isNotEmpty())
                                            <div class="row g-2 mb-3">
                                                @foreach($jadwalHariIni as $jadwal)
                                                    <div class="col-12 col-md-6">
                                                        <label class="d-flex align-items-start gap-2 p-2 bg-white rounded border cursor-pointer" style="cursor: pointer;">
                                                            <input type="checkbox" wire:model="selectedJadwalIds" value="{{ $jadwal->id }}" class="form-check-input mt-1" style="min-width: 18px; min-height: 18px;">
                                                            <div class="small">
                                                                <div class="fw-bold text-dark">Jam {{ $jadwal->jam_ke_mulai }} - {{ $jadwal->jam_ke_selesai }}</div>
                                                                <div class="text-primary">{{ $jadwal->rombel?->nama_kelas }} &bull; {{ $jadwal->mataPelajaran?->nama_mapel }}</div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning py-2 px-3 small mb-2">
                                                <i class="bi bi-exclamation-circle me-1"></i> Anda tidak memiliki jadwal mengajar terdaftar untuk hari ini. Silakan pilih nomor jam pelajaran di bawah:
                                            </div>
                                        @endif

                                        <label class="form-label small fw-bold text-muted mb-1">Atau Pilih Nomor Jam Pelajaran:</label>
                                        <div class="d-flex flex-wrap gap-1">
                                            @for($i = 0; $i <= 12; $i++)
                                                <label class="btn btn-sm btn-outline-secondary px-2 py-1 d-flex align-items-center gap-1 {{ in_array($i, $selectedJam) ? 'active bg-primary text-white border-primary' : '' }}" style="border-radius: 6px; cursor: pointer;">
                                                    <input type="checkbox" wire:model="selectedJam" value="{{ $i }}" class="d-none">
                                                    <span>{{ $i === 0 ? 'Jam 0 (Apel)' : 'Jam ' . $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                        @error('selectedJadwalIds') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Jenis Izin --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small">Jenis Izin <span class="text-danger">*</span></label>
                                <select wire:model="jenisIzin" class="form-select" style="min-height: 48px;">
                                    <option value="sakit">🤒 Sakit (Mendadak Sakit / Istirahat)</option>
                                    <option value="izin">🚗 Izin Pribadi / Keperluan Mendesak</option>
                                    <option value="dinas">🏛️ Perjalanan Dinas</option>
                                    <option value="tugas_luar">📚 Tugas Luar / Pelatihan / MGMP</option>
                                    <option value="cuti">🏖️ Cuti Mendesak</option>
                                </select>
                            </div>

                            {{-- Usulan Guru Pengganti --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small">Usulan Guru Pengganti / Piket (Opsional)</label>
                                <select wire:model="guruPenggantiId" class="form-select" style="min-height: 48px;">
                                    <option value="">— Tidak Ada / Ditentukan Waka / Guru Piket —</option>
                                    @foreach($daftarGuru as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Alasan / Keterangan --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Alasan / Keterangan Izin <span class="text-danger">*</span></label>
                                <textarea wire:model="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" placeholder="Tuliskan keterangan izin (misal: Demam tinggi sejak subuh / Urusan keluarga mendesak)..." style="border-radius: 10px;"></textarea>
                                @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Lampiran Surat / Bukti --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Upload File / Foto Surat Keterangan / Resep Dokter (Opsional)</label>
                                <input type="file" wire:model="fileLampiran" class="form-control @error('fileLampiran') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text small">Mendukung format JPG, PNG, PDF (Maks 5MB).</div>
                                @error('fileLampiran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-secondary px-4" wire:click="closeForm" style="min-height: 44px; border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2" style="min-height: 44px; border-radius: 10px;" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-send-fill me-1"></i>Kirim Pengajuan Izin</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL IZIN --}}
    @if($detailIzin)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Detail Pengajuan Izin</h5>
                    <button type="button" class="btn-close" wire:click="closeDetail"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="badge {{ $detailIzin->status_badge['class'] }} px-3 py-2 fs-6">
                            <i class="bi {{ $detailIzin->status_badge['icon'] }} me-1"></i>{{ $detailIzin->status_badge['text'] }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">{{ $detailIzin->jenis_izin_label }}</span>
                    </div>

                    <div class="list-group list-group-flush mb-3">
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Tanggal Izin</small>
                            <span class="fw-bold text-dark">
                                {{ $detailIzin->tanggal_mulai->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Waktu / Jam Pelajaran</small>
                            <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-2 py-1">
                                {{ $detailIzin->waktu_display }}
                            </span>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Alasan / Keterangan</small>
                            <span class="text-dark">{{ $detailIzin->alasan }}</span>
                        </div>
                        @if($detailIzin->guruPengganti)
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">Guru Pengganti</small>
                            <span class="fw-bold text-primary">{{ $detailIzin->guruPengganti->name }}</span>
                        </div>
                        @endif
                        @if($detailIzin->file_lampiran)
                        <div class="list-group-item px-0 py-2">
                            <small class="text-muted d-block">File Lampiran</small>
                            <a href="{{ asset('storage/' . $detailIzin->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="bi bi-paperclip me-1"></i> Lihat Dokumen Lampiran
                            </a>
                        </div>
                        @endif
                        @if($detailIzin->diverifikasiOleh)
                        <div class="list-group-item px-0 py-2 bg-light rounded-3 p-2 mt-2">
                            <small class="text-muted d-block">Diverifikasi oleh</small>
                            <span class="fw-bold text-dark">{{ $detailIzin->diverifikasiOleh->name }}</span>
                            <small class="text-muted d-block">Waktu: {{ $detailIzin->waktu_verifikasi?->translatedFormat('d F Y, H:i') }} WIB</small>
                            @if($detailIzin->catatan_waka)
                                <div class="mt-2 text-danger small"><strong>Catatan:</strong> {{ $detailIzin->catatan_waka }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary w-100" wire:click="closeDetail" style="min-height: 44px; border-radius: 10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
