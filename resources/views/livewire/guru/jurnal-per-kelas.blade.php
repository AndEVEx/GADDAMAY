<div x-data="{ 
    showModal: false, 
    modalAgenda: null,
    openDetail(agenda) { 
        if (!agenda || !agenda.id) return;
        this.modalAgenda = agenda; 
        this.showModal = true; 
    },
    closeModal() {
        this.showModal = false;
        this.modalAgenda = null;
    }
}">
    <div class="page-header mb-3">
        <h1><i class="bi bi-journal-text me-2"></i>Jurnal Mengajar {{ $rombel->nama_kelas }}</h1>
        <p class="subtitle mb-0">Riwayat mengajar perbulan & perminggu lengkap dengan foto & materi</p>
    </div>

    {{-- Month & Week Filter Section --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold text-dark small mb-1">
                        <i class="bi bi-calendar-month text-primary me-1"></i>Pilih Bulan Jurnal:
                    </label>
                    <input type="month" wire:model.live="bulan" class="form-control fw-semibold" style="min-height: 44px; border-radius: 10px;">
                </div>

                <div class="col-12 col-md-7">
                    <label class="form-label fw-bold text-dark small mb-1">
                        <i class="bi bi-funnel text-primary me-1"></i>Filter Minggu Ke- ({{ $namaBulan }}):
                    </label>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" wire:click="setMinggu('semua')" 
                                class="btn btn-sm flex-fill {{ $minggu === 'semua' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}" style="border-radius: 8px; min-height: 38px;">
                            Semua Minggu ({{ $totalMonth }})
                        </button>
                        @for($w = 1; $w <= 5; $w++)
                        <button type="button" wire:click="setMinggu('{{ $w }}')" 
                                class="btn btn-sm flex-fill {{ $minggu == $w ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}" style="border-radius: 8px; min-height: 38px;">
                            Minggu {{ $w }}
                        </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pertemuan Cards List --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fw-bold text-dark mb-0">Daftar Pertemuan ({{ $agendas->count() }} Pertemuan):</h6>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold" style="font-size: 0.8rem; border-radius: 8px;">
            {{ $namaBulan }} {{ $minggu !== 'semua' ? '• Minggu ke-' . $minggu : '' }}
        </span>
    </div>

    @forelse($agendas as $agenda)
    <div class="card mb-3 animate-fade-in-up shadow-sm border cursor-pointer hover-shadow" 
         style="animation-delay: {{ $loop->index * 0.03 }}s; border-radius: 12px; cursor: pointer;"
         @click="openDetail({{ json_encode([
             'id' => $agenda->id,
             'pertemuan_ke' => $agenda->pertemuan_ke,
             'week_of_month' => $agenda->week_of_month,
             'tanggal' => $agenda->tanggal?->translatedFormat('l, d F Y'),
             'mapel' => $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel,
             'materi' => $agenda->materi_diajarkan,
             'refleksi' => $agenda->refleksi,
             'waktu_masuk' => ($agenda->waktu_mulai?->format('H:i') ?? $agenda->created_at?->format('H:i')) . ' WIB',
             'guru' => $agenda->guru?->name ?? '-',
             'status' => ucfirst($agenda->status),
             'tps' => $agenda->tujuanPembelajaran->map(fn($t) => ['kode' => $t->kode_tp, 'deskripsi' => $t->deskripsi_tp]),
             'foto_murid' => $agenda->foto_bukti_path ? Storage::url($agenda->foto_bukti_path) : null,
             'foto_guru' => $agenda->foto_guru_path ? Storage::url($agenda->foto_guru_path) : null,
         ]) }})">
        
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary px-2 py-1 fw-bold" style="font-size: 0.75rem; border-radius: 6px;">
                            📌 Pertemuan Ke-{{ $agenda->pertemuan_ke }}
                        </span>
                        <span class="badge bg-secondary bg-opacity-15 text-dark px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
                            Minggu ke-{{ $agenda->week_of_month }}
                        </span>
                        <span class="badge bg-info bg-opacity-15 text-info fw-bold px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="bi bi-clock-history me-1"></i>Masuk pkl {{ $agenda->waktu_mulai?->format('H:i') ?? $agenda->created_at?->format('H:i') }} WIB
                        </span>
                    </div>
                    <div class="fw-bold fs-6 text-dark">{{ $agenda->tanggal?->translatedFormat('l, d F Y') }}</div>
                    <div class="text-primary small fw-semibold">{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</div>
                </div>

                <span class="status-badge status-{{ $agenda->status === 'selesai' ? 'hijau' : ($agenda->status === 'berjalan' ? 'hijau' : 'abu') }}">
                    {{ ucfirst($agenda->status) }}
                </span>
            </div>

            @if($agenda->materi_diajarkan)
            <div class="mt-2 small bg-light rounded p-2 border text-dark">
                <strong>Materi:</strong> {{ Str::limit($agenda->materi_diajarkan, 120) }}
            </div>
            @endif

            @if($agenda->tujuanPembelajaran->count())
            <div class="mt-2 d-flex flex-wrap gap-1">
                @foreach($agenda->tujuanPembelajaran as $tp)
                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">{{ $tp->kode_tp }}</span>
                @endforeach
            </div>
            @endif

            {{-- Photo Badges & Click Action --}}
            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    @if($agenda->foto_bukti_path)
                        <span class="badge bg-primary bg-opacity-10 text-primary small">
                            <i class="bi bi-camera-fill me-1"></i>Foto Murid
                        </span>
                    @endif

                    @if($agenda->foto_guru_path)
                        <span class="badge bg-success bg-opacity-10 text-success small">
                            <i class="bi bi-person-bounding-box me-1"></i>Foto Guru
                        </span>
                    @endif
                </div>

                <div class="text-primary small fw-bold">
                    Buka Pertemuan Ini <i class="bi bi-chevron-right ms-1"></i>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
            <p class="mb-0 fw-medium">Belum ada riwayat pertemuan mengajar pada bulan/minggu ini.</p>
        </div>
    </div>
    @endforelse

    {{-- Detail Modal Viewer --}}
    <div x-show="showModal" class="modal fade" :class="{ 'show d-block': showModal }" tabindex="-1" style="background: rgba(0,0,0,0.75); z-index: 1060;" @click.self="closeModal()" x-cloak>
        <template x-if="showModal && modalAgenda">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header bg-primary text-white border-0">
                        <div>
                            <span class="badge bg-white text-primary fw-bold me-2" x-text="'📌 Pertemuan Ke-' + modalAgenda.pertemuan_ke"></span>
                            <span class="modal-title fw-bold" x-text="modalAgenda.tanggal + ' &bull; ' + modalAgenda.mapel"></span>
                            <div class="small text-white-50 mt-1">Kelas {{ $rombel->nama_kelas }} &bull; <span x-text="'Minggu ke-' + modalAgenda.week_of_month"></span></div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" @click="closeModal()"></button>
                    </div>
                    
                    <div class="modal-body p-3 text-dark">
                        <div>
                            {{-- Status & Jam Masuk Badge --}}
                            <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="badge bg-success px-3 py-2 fw-bold" x-text="'Status: ' + modalAgenda.status"></span>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                                    <i class="bi bi-clock-history me-1"></i>Waktu Guru Masuk: <span x-text="modalAgenda.waktu_masuk"></span>
                                </span>
                            </div>

                            {{-- Materi --}}
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1"><i class="bi bi-book text-primary me-1"></i>Materi yang Diajarkan:</div>
                                <div class="p-3 bg-light rounded-3 border text-dark fw-medium" x-text="modalAgenda.materi || 'Tidak ada materi dicatat'"></div>
                            </div>

                            {{-- TP --}}
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1"><i class="bi bi-list-check text-primary me-1"></i>Tujuan Pembelajaran (TP/KKTP):</div>
                                <div class="d-flex flex-column gap-1">
                                    <template x-for="tp in modalAgenda.tps" :key="tp.kode">
                                        <div class="p-2 border rounded-3 bg-primary bg-opacity-10 text-primary small">
                                            <strong x-text="tp.kode + ':'"></strong> <span x-text="tp.deskripsi"></span>
                                        </div>
                                    </template>
                                    <template x-if="!modalAgenda.tps || modalAgenda.tps.length === 0">
                                        <div class="text-muted small">Tidak ada TP dicatat</div>
                                    </template>
                                </div>
                            </div>

                            {{-- Refleksi --}}
                            <template x-if="modalAgenda.refleksi">
                                <div class="mb-3">
                                    <div class="fw-bold text-muted small mb-1"><i class="bi bi-journal-text text-info me-1"></i>Refleksi Guru:</div>
                                    <div class="p-3 bg-info bg-opacity-10 text-dark rounded-3 border border-info border-opacity-25" x-text="modalAgenda.refleksi"></div>
                                </div>
                            </template>

                            {{-- 2 Foto Dokumentasi (Foto Murid & Foto Guru) --}}
                            <div class="mt-3">
                                <div class="fw-bold text-muted small mb-2"><i class="bi bi-camera-fill text-primary me-1"></i>Dokumentasi Foto Bukti:</div>
                                <div class="row g-2">
                                    {{-- Foto Murid --}}
                                    <div class="col-6">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <div class="fw-bold small mb-1">Foto Murid / Ketua Kelas</div>
                                            <template x-if="modalAgenda.foto_murid">
                                                <div>
                                                    <img :src="modalAgenda.foto_murid" class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: cover; cursor: pointer;"
                                                         onclick="window.openPhotoModal(this.src, 'Foto Murid')">
                                                    <a :href="modalAgenda.foto_murid" download class="btn btn-outline-primary btn-sm w-100 py-1" style="font-size: 0.72rem;">
                                                        <i class="bi bi-download me-1"></i>Unduh Foto
                                                    </a>
                                                </div>
                                            </template>
                                            <template x-if="!modalAgenda.foto_murid">
                                                <div class="text-muted small py-3">Belum ada foto</div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Foto Guru --}}
                                    <div class="col-6">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <div class="fw-bold small mb-1">Foto Guru / Suasana Kelas</div>
                                            <template x-if="modalAgenda.foto_guru">
                                                <div>
                                                    <img :src="modalAgenda.foto_guru" class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: cover; cursor: pointer;"
                                                         onclick="window.openPhotoModal(this.src, 'Foto Guru')">
                                                    <a :href="modalAgenda.foto_guru" download class="btn btn-outline-success btn-sm w-100 py-1" style="font-size: 0.72rem;">
                                                        <i class="bi bi-download me-1"></i>Unduh Foto
                                                    </a>
                                                </div>
                                            </template>
                                            <template x-if="!modalAgenda.foto_guru">
                                                <div class="text-muted small py-3">Belum ada foto</div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light justify-content-between">
                        <a x-show="modalAgenda?.id" :href="'/guru/detail/' + modalAgenda.id" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 8px;" wire:navigate>
                            <i class="bi bi-eye me-1"></i>Buka Halaman Detail Penuh
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="closeModal()" style="border-radius: 8px;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="mt-3">
        <a href="{{ route('guru.jurnal', ['bulan' => $bulan]) }}" class="btn btn-outline-secondary" wire:navigate style="border-radius: 10px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Jurnal Mengajar
        </a>
    </div>
</div>
