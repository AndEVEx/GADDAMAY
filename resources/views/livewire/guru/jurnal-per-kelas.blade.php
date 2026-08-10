<div x-data="{ 
    showModal: false, 
    modalAgenda: null,
    openDetail(agenda) { 
        this.modalAgenda = agenda; 
        this.showModal = true; 
    } 
}">
    <div class="page-header mb-3">
        <h1><i class="bi bi-journal-text me-2"></i>Jurnal Mengajar {{ $rombel->nama_kelas }}</h1>
        <p class="subtitle mb-0">Klik pada card agenda untuk melihat riwayat mengajar, foto & materi lengkap</p>
    </div>

    @forelse($agendas as $agenda)
    <div class="card mb-3 animate-fade-in-up shadow-sm cursor-pointer border" 
         style="animation-delay: {{ $loop->index * 0.03 }}s; border-radius: 12px; cursor: pointer;"
         @click="openDetail({{ json_encode([
             'id' => $agenda->id,
             'tanggal' => $agenda->tanggal?->translatedFormat('l, d F Y'),
             'mapel' => $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel,
             'materi' => $agenda->materi_diajarkan,
             'refleksi' => $agenda->refleksi,
             'status' => ucfirst($agenda->status),
             'tps' => $agenda->tujuanPembelajaran->map(fn($t) => ['kode' => $t->kode_tp, 'deskripsi' => $t->deskripsi_tp]),
             'foto_murid' => $agenda->foto_bukti_path ? Storage::url($agenda->foto_bukti_path) : null,
             'foto_guru' => $agenda->foto_guru_path ? Storage::url($agenda->foto_guru_path) : null,
         ]) }})">
        
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
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

            {{-- Photo Thumbnails Row --}}
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
                    Klik Card untuk Detail <i class="bi bi-chevron-right ms-1"></i>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
            <p class="mb-0 fw-medium">Belum ada riwayat mengajar di kelas ini.</p>
        </div>
    </div>
    @endforelse

    {{-- Detail Modal Viewer --}}
    <div x-show="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.75); z-index: 1060;" x-cloak>
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-primary text-white border-0">
                    <div>
                        <h6 class="modal-title fw-bold" x-text="modalAgenda?.tanggal + ' &bull; ' + modalAgenda?.mapel">Riwayat Mengajar</h6>
                        <div class="small text-white-50">Kelas {{ $rombel->nama_kelas }}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" @click="showModal = false"></button>
                </div>
                
                <div class="modal-body p-3 text-dark">
                    <template x-if="modalAgenda">
                        <div>
                            {{-- Materi --}}
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1"><i class="bi bi-book text-primary me-1"></i>Materi yang Diajarkan:</div>
                                <div class="p-3 bg-light rounded-3 border text-dark fw-medium" x-text="modalAgenda.materi || 'Tidak ada materi dicatat'"></div>
                            </div>

                            {{-- TP --}}
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1"><i class="bi bi-list-check text-primary me-1"></i>Tujuan Pembelajaran (TP):</div>
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
                                            <div class="fw-bold small mb-1">Foto Murid</div>
                                            <template x-if="modalAgenda.foto_murid">
                                                <div>
                                                    <img :src="modalAgenda.foto_murid" class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: cover;">
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
                                            <div class="fw-bold small mb-1">Foto Guru</div>
                                            <template x-if="modalAgenda.foto_guru">
                                                <div>
                                                    <img :src="modalAgenda.foto_guru" class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: cover;">
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
                    </template>
                </div>

                <div class="modal-footer bg-light justify-content-between">
                    <a :href="'/guru/detail/' + modalAgenda?.id" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-eye me-1"></i>Buka Halaman Detail Penuh
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="showModal = false" style="border-radius: 8px;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('guru.jurnal') }}" class="btn btn-outline-secondary" wire:navigate style="border-radius: 10px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Jurnal Mengajar
        </a>
    </div>
</div>
