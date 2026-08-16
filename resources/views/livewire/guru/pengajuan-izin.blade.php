<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-x text-primary me-2"></i>Pengajuan Izin Guru</h4>
            <p class="text-muted small mb-0">Ajukan izin tidak mengajar dan pantau status verifikasi oleh Waka Kurikulum</p>
        </div>
        <button wire:click="openForm" class="btn btn-primary d-flex align-items-center gap-2" style="min-height: 44px; border-radius: 10px;">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Ajukan Izin Baru</span>
        </button>
    </div>

    {{-- History Cards / Table --}}
    @if($riwayatIzin->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-calendar2-check fs-2"></i>
                </div>
                <h6 class="fw-bold">Belum Ada Riwayat Pengajuan Izin</h6>
                <p class="text-muted small mb-3">Jika Anda berhalangan hadir atau ada tugas luar, silakan klik tombol di bawah.</p>
                <button wire:click="openForm" class="btn btn-outline-primary" style="border-radius: 10px;">
                    <i class="bi bi-plus-lg me-1"></i> Buat Pengajuan Izin
                </button>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($riwayatIzin as $izin)
                @php
                    $badge = $izin->status_badge;
                    $isSelesai = Carbon\Carbon::parse($izin->tanggal_selesai)->isPast();
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

                            {{-- Tanggal --}}
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                @if($izin->tanggal_mulai->format('Y-m-d') === $izin->tanggal_selesai->format('Y-m-d'))
                                    {{ $izin->tanggal_mulai->translatedFormat('d F Y') }}
                                @else
                                    {{ $izin->tanggal_mulai->translatedFormat('d M') }} — {{ $izin->tanggal_selesai->translatedFormat('d M Y') }}
                                @endif
                            </h6>

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

    {{-- MODAL PENGAJUAN IZIN --}}
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1a56db, #0d47a1); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Form Pengajuan Izin Guru</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeForm"></button>
                </div>
                <form wire:submit.prevent="submitIzin">
                    <div class="modal-body p-3 p-md-4">
                        <div class="row g-3">
                            {{-- Jenis Izin --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small">Jenis Izin <span class="text-danger">*</span></label>
                                <select wire:model="jenisIzin" class="form-select" style="min-height: 48px;">
                                    <option value="izin">Izin Pribadi / Keperluan Mendesak</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="cuti">Cuti</option>
                                    <option value="dinas">Perjalanan Dinas</option>
                                    <option value="tugas_luar">Tugas Luar / Pelatihan / MGMP</option>
                                </select>
                            </div>

                            {{-- Guru Pengganti (Opsional) --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small">Usulan Guru Pengganti (Opsional)</label>
                                <select wire:model="guruPenggantiId" class="form-select" style="min-height: 48px;">
                                    <option value="">— Tidak Ada / Ditentukan Waka —</option>
                                    @foreach($daftarGuru as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal Mulai --}}
                            <div class="col-6">
                                <label class="form-label fw-bold small">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" wire:model.live="tanggalMulai" class="form-control @error('tanggalMulai') is-invalid @enderror" style="min-height: 48px;">
                                @error('tanggalMulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div class="col-6">
                                <label class="form-label fw-bold small">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" wire:model.live="tanggalSelesai" class="form-control @error('tanggalSelesai') is-invalid @enderror" style="min-height: 48px;">
                                @error('tanggalSelesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Alasan / Keterangan --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Alasan / Keterangan Izin <span class="text-danger">*</span></label>
                                <textarea wire:model="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" placeholder="Tuliskan keterangan keperluan izin secara jelas..." style="border-radius: 10px;"></textarea>
                                @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Lampiran Surat / Bukti --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Upload File Lampiran (Surat Dokter / Surat Tugas / Surat Permohonan)</label>
                                <input type="file" wire:model="fileLampiran" class="form-control @error('fileLampiran') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text small">Format: PDF, JPG, PNG (Maks 5MB). Opsional.</div>
                                @error('fileLampiran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Jadwal Terdampak Preview --}}
                            @if($jadwalTerdampak->isNotEmpty())
                                <div class="col-12">
                                    <div class="card border bg-light">
                                        <div class="card-header bg-light py-2">
                                            <span class="small fw-bold text-dark"><i class="bi bi-clock-history me-1 text-primary"></i>Jadwal Mengajar yang Terdampak ({{ $jadwalTerdampak->count() }} Sesi):</span>
                                        </div>
                                        <div class="card-body p-2" style="max-height: 180px; overflow-y: auto;">
                                            <div class="list-group list-group-flush">
                                                @foreach($jadwalTerdampak as $j)
                                                    <div class="list-group-item bg-transparent px-2 py-1 small d-flex justify-content-between align-items-center">
                                                        <span><strong>{{ $j->hari_label }}</strong> (Jam {{ $j->jam_ke_mulai }}-{{ $j->jam_ke_selesai }})</span>
                                                        <span class="text-primary fw-semibold">{{ $j->rombel?->nama_kelas }} &bull; {{ $j->mataPelajaran?->nama_mapel }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-secondary px-4" wire:click="closeForm" style="min-height: 44px; border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2" style="min-height: 44px; border-radius: 10px;" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-send-fill me-1"></i>Kirim Pengajuan</span>
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
                            <small class="text-muted d-block">Periode Izin</small>
                            <span class="fw-bold text-dark">
                                {{ $detailIzin->tanggal_mulai->translatedFormat('d F Y') }} — {{ $detailIzin->tanggal_selesai->translatedFormat('d F Y') }}
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
