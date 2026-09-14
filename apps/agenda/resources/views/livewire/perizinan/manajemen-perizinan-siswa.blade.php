<div class="container-fluid py-3">
    {{-- Header & Stat Cards --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-check text-success me-2"></i>Pusat Perizinan Siswa Terpadu
            </h4>
            <p class="text-muted small mb-0">
                Persetujuan Izin Siswa, Guru Piket & Wali Kelas dengan Sinkronisasi Otomatis ke Agenda KBM & Gate
            </p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('perizinan.pengajuan') }}" class="btn btn-primary px-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Entri Izin / Dispensasi Baru
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-semibold mb-1 text-uppercase">Menunggu Verifikasi</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $countMenunggu }} <span class="fs-6 text-muted fw-normal">pengajuan</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-semibold mb-1 text-uppercase">Siswa Izin Hari Ini</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $countHariIni }} <span class="fs-6 text-muted fw-normal">siswa sah</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                        <i class="bi bi-whatsapp fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-semibold mb-1 text-uppercase">Multi-Driver Gateway</h6>
                        <h6 class="fw-bold mb-0 text-dark">GOWA / WA-AKG <span class="badge bg-success-subtle text-success ms-1">Auto + wa.me</span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari nama atau NIS siswa...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterStatus" class="form-select form-select-sm">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="disetujui">Disetujui (Semua)</option>
                        <option value="disetujui_walas">Disetujui Walas</option>
                        <option value="disetujui_piket">Disetujui Piket</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterKategori" class="form-select form-select-sm">
                        <option value="semua">Semua Kategori</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin_keperluan">Izin Keperluan</option>
                        <option value="dispensasi_sekolah">Dispensasi Lomba/Sekolah</option>
                        <option value="izin_keluar_kampus">Izin Keluar Kampus</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterRombel" class="form-select form-select-sm">
                        <option value="semua">Semua Kelas / Rombel</option>
                        @foreach($rombelList as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="filterTanggal" class="form-control form-control-sm" title="Filter Tanggal Izin">
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Siswa & Kelas</th>
                        <th>Kategori & Alasan</th>
                        <th>Tanggal / Jam</th>
                        <th>Status Verifikasi</th>
                        <th>Notif WhatsApp</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinanList as $izin)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $izin->siswa?->nama_siswa ?? 'Siswa' }}</div>
                                <div class="text-muted small">
                                    NIS: {{ $izin->siswa?->nis ?? '-' }} | 
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $izin->siswa?->rombel?->nama_kelas ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($izin->kategori === 'sakit')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>
                                    @elseif($izin->kategori === 'izin_keperluan')
                                        <span class="badge bg-info text-dark"><i class="bi bi-envelope-open me-1"></i>Izin Ortu</span>
                                    @elseif($izin->kategori === 'dispensasi_sekolah')
                                        <span class="badge bg-primary"><i class="bi bi-trophy me-1"></i>Dispensasi</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-door-open me-1"></i>Izin Keluar</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1 text-truncate" style="max-width: 250px;" title="{{ $izin->alasan }}">
                                    {{ $izin->alasan }}
                                </div>
                                @if($izin->file_lampiran)
                                    <a href="{{ asset('storage/' . $izin->file_lampiran) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none mt-1">
                                        <i class="bi bi-paperclip"></i> Lihat Berkas
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small">
                                    {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') }}
                                    @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                        s.d. {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d/m/Y') }}
                                    @endif
                                </div>
                                @if($izin->jam_mulai)
                                    <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $izin->jam_mulai }} - {{ $izin->jam_selesai ?? 'Selesai' }}</div>
                                @endif
                            </td>
                            <td>
                                @if($izin->status === 'menunggu')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">
                                        <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                    </span>
                                @elseif(in_array($izin->status, ['disetujui_walas', 'disetujui_piket', 'disetujui_bk']))
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>{{ $izin->status_label }}
                                    </span>
                                    @if($izin->auto_locked_agenda)
                                        <div class="text-success small mt-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-lock-fill"></i> Agenda Terkunci
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($izin->wa_notif_status === 'sent_auto')
                                    <span class="badge bg-success" title="Terkirim otomatis via gateway"><i class="bi bi-check-all"></i> Auto Sent</span>
                                @elseif($izin->wa_notif_status === 'sent_manual')
                                    <span class="badge bg-info text-dark" title="Terkirim manual via wa.me"><i class="bi bi-person-check"></i> Manual Sent</span>
                                @elseif($izin->wa_notif_status === 'failed')
                                    <span class="badge bg-danger" title="Gateway gagal kirim"><i class="bi bi-exclamation-triangle"></i> Gagal</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif

                                @if($izin->nomor_wa_pemohon)
                                    <button wire:click="openWaModal('{{ $izin->id }}')" class="btn btn-sm btn-outline-success border-0 p-1 ms-1" title="Buka WhatsApp Failover">
                                        <i class="bi bi-whatsapp"></i>
                                    </button>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="openDetail('{{ $izin->id }}')" class="btn btn-outline-secondary" title="Detail Pengajuan">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($izin->status === 'menunggu')
                                        <button wire:click="openApprove('{{ $izin->id }}')" class="btn btn-success" title="Setujui Izin">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button wire:click="openReject('{{ $izin->id }}')" class="btn btn-outline-danger" title="Tolak Izin">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Belum ada data perizinan siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $perizinanList->links() }}
        </div>
    </div>

    {{-- ==================== MODAL APPROVE ==================== --}}
    @if($showApproveModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-check-circle me-2"></i>Persetujuan Izin Siswa</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            Apakah Anda yakin menyetujui izin untuk <strong>{{ $selectedIzin?->siswa?->nama_siswa }}</strong>?
                            Status kehadiran pada seluruh jam pelajaran di agenda kelas akan <strong>otomatis terkunci</strong>.
                        </p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Persetujuan Sebagai:</label>
                            <select wire:model="peranVerifikator" class="form-select form-select-sm">
                                <option value="guru_piket">Guru Piket / Petugas Piket</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="guru_bk">Guru Bimbingan Konseling (BK)</option>
                                <option value="waka_kesiswaan">Waka Kesiswaan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Verifikator (Opsional):</label>
                            <textarea wire:model="catatanVerifikator" class="form-control form-control-sm" rows="2" placeholder="Contoh: Surat dokter telah diverifikasi sah."></textarea>
                        </div>
                        <div class="alert alert-light border small text-muted mb-0">
                            <i class="bi bi-whatsapp text-success me-1"></i> Sistem akan secara otomatis mencoba mengirimkan pesan konfirmasi ke nomor orang tua siswa jika WhatsApp Gateway aktif.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModals">Batal</button>
                        <button type="button" class="btn btn-success btn-sm px-3 fw-bold" wire:click="approveIzin">
                            <i class="bi bi-check-lg me-1"></i> Ya, Setujui Izin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== MODAL REJECT ==================== --}}
    @if($showRejectModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Tolak Pengajuan Izin</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            Tolak pengajuan izin atas nama <strong>{{ $selectedIzin?->siswa?->nama_siswa }}</strong>?
                        </p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Alasan Penolakan:</label>
                            <textarea wire:model="catatanVerifikator" class="form-control form-control-sm" rows="3" placeholder="Sebutkan alasan penolakan agar pemohon dapat mengetahuinya..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModals">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm px-3 fw-bold" wire:click="rejectIzin">
                            <i class="bi bi-x-lg me-1"></i> Tolak Izin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== MODAL WA FAILOVER (MANUAL DISPATCHER) ==================== --}}
    @if($showWaModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-whatsapp text-success me-2"></i>Pusat Pengiriman WhatsApp Orang Tua / Pemohon
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Flash Status --}}
                        @if (session()->has('wa_feedback'))
                            <div class="alert alert-success small py-2">{{ session('wa_feedback') }}</div>
                        @endif
                        @if (session()->has('wa_feedback_error'))
                            <div class="alert alert-danger small py-2">{{ session('wa_feedback_error') }}</div>
                        @endif

                        <div class="alert alert-success bg-success-subtle border-0 mb-3 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill text-success fs-4 me-3"></i>
                            <div class="small">
                                <strong>Fitur Failover / Backup Anti-Gagal:</strong> Jika WhatsApp Gateway otomatis sedang offline, kuota habis, atau diblokir, guru/piket dapat langsung mengirim pesan resmi di bawah ini menggunakan <strong>WhatsApp Web / HP pribadi</strong> hanya dengan 1-klik!
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Tujuan Pesan (Orang Tua / Wali):</label>
                                <div class="fw-bold text-dark fs-6">{{ $selectedIzin?->nama_pemohon ?? $selectedIzin?->siswa?->nama_siswa }}</div>
                                <div class="text-primary font-monospace">{{ $waPhone ?: 'Nomor belum dicantumkan' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Status Pengiriman Saat Ini:</label>
                                <div>
                                    @if($waStatusText === 'sent_auto')
                                        <span class="badge bg-success fs-6"><i class="bi bi-check-all"></i> Terkirim Otomatis</span>
                                    @elseif($waStatusText === 'sent_manual')
                                        <span class="badge bg-info text-dark fs-6"><i class="bi bi-person-check"></i> Terkirim Manual via wa.me</span>
                                    @elseif($waStatusText === 'failed')
                                        <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle"></i> Gagal Kirim Gateway</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Belum Terkirim</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Template Pesan Resmi (Pre-filled):</label>
                            <textarea id="waMessageText" class="form-control font-monospace bg-light" rows="8" readonly style="font-size: 0.85rem;">{{ $waMessagePreview }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <button type="button" wire:click="retrySendAutoWa" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i> Coba Kirim Ulang Otomatis (Gateway)
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('waMessageText').value); alert('Teks pesan berhasil disalin ke clipboard!');" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-clipboard me-1"></i> Salin Pesan
                                </button>
                                @if($waMeLink)
                                    <a href="{{ $waMeLink }}" target="_blank" class="btn btn-success btn-sm fw-bold px-3 shadow-sm">
                                        <i class="bi bi-whatsapp me-1"></i> Buka WhatsApp (wa.me)
                                    </a>
                                @endif
                                <button type="button" wire:click="markWaSentManual" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Terkirim
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModals">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== MODAL DETAIL ==================== --}}
    @if($showDetailModal && $selectedIzin)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-text me-2"></i>Rincian Pengajuan Izin Siswa</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <h6 class="fw-bold text-primary mb-2">Identitas Siswa</h6>
                                    <div><strong>Nama:</strong> {{ $selectedIzin->siswa?->nama_siswa }}</div>
                                    <div><strong>NIS / NISN:</strong> {{ $selectedIzin->siswa?->nis }} / {{ $selectedIzin->siswa?->nisn ?? '-' }}</div>
                                    <div><strong>Kelas:</strong> {{ $selectedIzin->siswa?->rombel?->nama_kelas }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <h6 class="fw-bold text-success mb-2">Data Pemohon</h6>
                                    <div><strong>Nama Pemohon:</strong> {{ $selectedIzin->nama_pemohon ?: 'Mandiri' }}</div>
                                    <div><strong>Hubungan:</strong> {{ ucwords(str_replace('_', ' ', $selectedIzin->hubungan_pemohon)) }}</div>
                                    <div><strong>No. WhatsApp:</strong> {{ $selectedIzin->nomor_wa_pemohon ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded-3">
                                    <h6 class="fw-bold mb-2">Informasi Izin</h6>
                                    <div><strong>Kategori:</strong> {{ $selectedIzin->kategori_label }}</div>
                                    <div><strong>Tanggal:</strong> {{ $selectedIzin->tanggal_mulai->format('d/m/Y') }} s.d. {{ $selectedIzin->tanggal_selesai->format('d/m/Y') }}</div>
                                    @if($selectedIzin->jam_mulai)
                                        <div><strong>Jam:</strong> {{ $selectedIzin->jam_mulai }} - {{ $selectedIzin->jam_selesai ?? 'Selesai' }}</div>
                                    @endif
                                    <div class="mt-2"><strong>Alasan:</strong> {{ $selectedIzin->alasan }}</div>
                                    @if($selectedIzin->catatan_verifikator)
                                        <div class="mt-2 text-muted"><strong>Catatan Verifikator:</strong> {{ $selectedIzin->catatan_verifikator }} (Oleh: {{ $selectedIzin->verifikator?->name }})</div>
                                    @endif
                                </div>
                            </div>
                            @if($selectedIzin->file_lampiran)
                                <div class="col-12">
                                    <h6 class="fw-bold mb-2">Lampiran Bukti (Surat Dokter / Tugas / Izin):</h6>
                                    <div class="text-center p-2 bg-light rounded border">
                                        <img src="{{ asset('storage/' . $selectedIzin->file_lampiran) }}" class="img-fluid rounded shadow-sm" style="max-height: 350px;" alt="Lampiran Izin">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModals">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>