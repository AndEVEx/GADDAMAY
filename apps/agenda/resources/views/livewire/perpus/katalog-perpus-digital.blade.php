<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner Siswa --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #0d9488, #0284c7);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-book-fill me-1"></i>PERPUS DIGITAL PWA
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Perpustakaan Digital Vokasi</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 650px;">
                        Jelajahi ribuan e-book materi kejuruan, modul kurikulum merdeka, dan novel literasi. Dukungan membaca offline serta penanda otomatis halaman terakhir.
                    </p>
                </div>

                {{-- Status Akun Siswa --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-black bg-opacity-25 p-3 rounded-4 border border-white border-opacity-20 d-flex align-items-center gap-3">
                        <div class="bg-white text-teal rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color: #0d9488;">
                            <i class="bi bi-bookmark-star-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-white-50">Koleksi Aktif Saya</div>
                            <div class="h5 fw-bold mb-0 text-white">{{ count($bukuSayaList) }} Judul</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigasi PWA: Katalog vs Buku Saya vs Rak Offline --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto no-scrollbar py-1">
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'katalog')" class="nav-link {{ $activeTab === 'katalog' ? 'active bg-primary fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-grid-fill me-1"></i> Semua Koleksi Buku
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'buku_saya')" class="nav-link {{ $activeTab === 'buku_saya' ? 'active bg-teal fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small" style="{{ $activeTab === 'buku_saya' ? 'background: #0d9488 !important;' : '' }}">
                            <i class="bi bi-bookmark-check-fill me-1"></i> Buku Sedang Dibaca ({{ count($bukuSayaList) }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'offline')" class="nav-link {{ $activeTab === 'offline' ? 'active bg-success fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-cloud-arrow-down-fill me-1"></i> Rak Offline PWA
                        </button>
                    </li>
                </ul>

                @if(Auth::user()?->isKepalaPerpustakaan())
                    <a href="{{ route('perpus.kelola') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 text-nowrap">
                        <i class="bi bi-gear-fill me-1"></i> Panel Kepala Perpustakaan
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- TAB 1: SEMUA KOLEKSI BUKU --}}
    @if($activeTab === 'katalog')
        {{-- Filter & Kategori Chips --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
            <div class="row g-2 align-items-center mb-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari judul buku pelajaran, penulis, atau topik...">
                    </div>
                </div>
                <div class="col-md-5">
                    <select wire:model.live="filterKategori" class="form-select bg-light border-0">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }} ({{ $k->buku_count }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Kategori Pills Cepat --}}
            <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <button wire:click="$set('filterKategori', '')" class="btn btn-sm rounded-pill text-nowrap {{ empty($filterKategori) ? 'btn-primary fw-bold' : 'btn-light border text-secondary' }}">
                    Semua
                </button>
                @foreach($kategoriList as $k)
                    <button wire:click="$set('filterKategori', '{{ $k->id }}')" class="btn btn-sm rounded-pill text-nowrap {{ $filterKategori === $k->id ? 'btn-primary fw-bold' : 'btn-light border text-secondary' }}">
                        <i class="bi {{ $k->ikon }} me-1"></i>{{ $k->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Grid Kartu Buku --}}
        <div class="row g-3 g-md-4">
            @forelse($bukuList as $buku)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column justify-content-between transition-all hover-shadow bg-white">
                        <div>
                            {{-- Cover Buku --}}
                            <div class="position-relative" style="height: 220px; background: #f1f5f9; overflow: hidden;">
                                <img src="{{ $buku->cover_asset }}" alt="{{ $buku->judul }}" class="w-100 h-100" style="object-fit: cover;">
                                
                                <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small" style="font-size: 0.68rem;">
                                    {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>

                                <span class="position-absolute bottom-0 end-0 m-2 badge bg-primary text-white rounded-pill px-2 py-1 small" style="font-size: 0.68rem;">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>{{ $buku->jumlah_halaman }} Hal
                                </span>
                            </div>

                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-1 line-clamp-2" title="{{ $buku->judul }}" style="font-size: 0.88rem; min-height: 2.4rem;">
                                    {{ $buku->judul }}
                                </h6>
                                <p class="text-muted small mb-2 text-truncate" style="font-size: 0.75rem;">
                                    <i class="bi bi-pen me-1"></i>{{ $buku->penulis }}
                                </p>

                                @if($buku->sinopsis)
                                    <p class="text-muted small mb-2 line-clamp-2" style="font-size: 0.72rem; line-height: 1.4;">
                                        {{ $buku->sinopsis }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top p-2 p-md-3 d-flex flex-column gap-1.5">
                            <a href="{{ route('perpus.baca', $buku->id) }}" class="btn btn-primary btn-sm fw-bold rounded-pill w-100 py-1.5 shadow-sm">
                                <i class="bi bi-book-half me-1"></i> Baca Sekarang
                            </a>

                            @if($buku->allow_offline_cache)
                                <button type="button" onclick="saveBookForOffline('{{ $buku->id }}', '{{ addslashes($buku->judul) }}', '{{ addslashes($buku->penulis) }}', '{{ $buku->cover_asset }}', '{{ $buku->file_pdf_url }}', {{ $buku->jumlah_halaman }})" class="btn btn-outline-success btn-sm rounded-pill w-100 py-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-cloud-arrow-down me-1"></i> Simpan Offline
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                        <i class="bi bi-search display-3 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Tidak Ada Buku Ditemukan</h5>
                        <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $bukuList->links() }}
        </div>
    @endif

    {{-- TAB 2: BUKU SAYA (SEDANG DIBACA & PENANDA TERAKHIR) --}}
    @if($activeTab === 'buku_saya')
        <div class="row g-3 g-md-4">
            @forelse($bukuSayaList as $riwayat)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-column justify-content-between transition-all hover-shadow">
                        <div class="d-flex gap-3 mb-3">
                            <div class="rounded-3 overflow-hidden flex-shrink-0 shadow-sm" style="width: 80px; height: 110px;">
                                <img src="{{ $riwayat->buku->cover_asset }}" alt="{{ $riwayat->buku->judul }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <span class="badge bg-light text-secondary border small mb-1">
                                    {{ $riwayat->buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                                <h6 class="fw-bold text-dark mb-1 line-clamp-2" title="{{ $riwayat->buku->judul }}">
                                    {{ $riwayat->buku->judul }}
                                </h6>
                                <p class="text-muted small mb-2"><i class="bi bi-pen me-1"></i>{{ $riwayat->buku->penulis }}</p>

                                <div class="d-flex align-items-center gap-2 small">
                                    <span class="badge bg-primary-subtle text-primary fw-bold">
                                        <i class="bi bi-bookmark-fill me-1"></i>Hal. {{ $riwayat->halaman_terakhir }}
                                    </span>
                                    <span class="text-muted" style="font-size: 0.72rem;">dari {{ $riwayat->buku->jumlah_halaman }} hal</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            {{-- Progress Bar Membaca --}}
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-muted" style="font-size: 0.72rem;">Progres Membaca:</span>
                                    <span class="fw-bold text-teal" style="color: #0d9488; font-size: 0.75rem;">{{ $riwayat->persentase_baca }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="background: #0d9488; width: {{ $riwayat->persentase_baca }}%;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center text-muted small mb-3" style="font-size: 0.72rem;">
                                <span><i class="bi bi-clock-history me-1"></i>Waktu: {{ $riwayat->durasi_format }}</span>
                                <span>Terakhir: {{ $riwayat->waktu_terakhir_baca ? $riwayat->waktu_terakhir_baca->diffForHumans() : '-' }}</span>
                            </div>

                            <a href="{{ route('perpus.baca', $riwayat->buku->id) }}" class="btn btn-teal text-white btn-sm fw-bold rounded-pill w-100 py-2 shadow-sm" style="background: #0d9488;">
                                <i class="bi bi-arrow-right-circle-fill me-1"></i> Lanjutkan Membaca (Hal. {{ $riwayat->halaman_terakhir }})
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                        <i class="bi bi-book-half display-3 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Belum Ada Buku Yang Sedang Dibaca</h5>
                        <p class="text-muted small mx-auto" style="max-width: 440px;">
                            Buka tab <strong>"Semua Koleksi Buku"</strong> dan pilih buku yang menarik minat belajar Anda untuk mulai membaca.
                        </p>
                        <button wire:click="$set('activeTab', 'katalog')" class="btn btn-primary btn-sm px-4 rounded-pill mx-auto">
                            Jelajahi Katalog Buku Sekarang
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    {{-- TAB 3: RAK OFFLINE TERSIMPAN DI PWA --}}
    @if($activeTab === 'offline')
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-cloud-check-fill fs-3 text-success"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Rak Buku Tersimpan Offline di HP Anda</h6>
                    <p class="text-muted small mb-0">
                        Buku di bawah ini tersimpan di memori lokal peramban PWA (IndexedDB). Anda dapat membacanya kapan saja di mana saja tanpa memerlukan kuota internet.
                    </p>
                </div>
            </div>
        </div>

        <div id="offline-books-container" class="row g-3 g-md-4">
            {{-- Rendered dynamically via JavaScript IndexedDB --}}
            <div class="col-12 text-center py-5 text-muted" id="offline-loading-spinner">
                <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                <span>Memuat koleksi buku offline dari memori perangkat...</span>
            </div>
        </div>
    @endif
</div>

{{-- SCRIPT PWA INDEXEDDB: OFFLINE BOOK STORAGE --}}
<script>
    const DB_NAME = 'PerpusDigitalSMKN2';
    const DB_VERSION = 1;
    const STORE_NAME = 'offline_books';

    function openPerpusDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);
            request.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                }
            };
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async function saveBookForOffline(bookId, title, author, coverUrl, pdfUrl, totalPages) {
        try {
            alert('Sedang mengunduh buku untuk penyimpanan offline di HP Anda...');
            const response = await fetch(pdfUrl);
            const blob = await response.blob();

            const db = await openPerpusDB();
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);

            const bookData = {
                id: bookId,
                title: title,
                author: author,
                coverUrl: coverUrl,
                pdfBlob: blob,
                totalPages: totalPages,
                savedAt: new Date().toISOString()
            };

            store.put(bookData);
            tx.oncomplete = () => {
                alert('Buku "' + title + '" berhasil disimpan ke Rak Offline! Anda bisa membacanya tanpa internet.');
            };
        } catch (err) {
            console.error('Offline save error:', err);
            alert('Gagal menyimpan offline. Pastikan izin penyimpanan aktif.');
        }
    }

    async function loadOfflineBooks() {
        const container = document.getElementById('offline-books-container');
        if (!container) return;

        try {
            const db = await openPerpusDB();
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const request = store.getAll();

            request.onsuccess = () => {
                const books = request.result;
                container.innerHTML = '';

                if (!books || books.length === 0) {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                                <i class="bi bi-cloud-arrow-down display-3 text-muted mb-3"></i>
                                <h5 class="fw-bold text-dark">Belum Ada Buku Tersimpan Offline</h5>
                                <p class="text-muted small mx-auto" style="max-width: 440px;">
                                    Klik tombol <strong>"Simpan Offline"</strong> pada salah satu buku di katalog untuk menyimpannya ke memori ponsel Anda.
                                </p>
                            </div>
                        </div>
                    `;
                    return;
                }

                books.forEach(b => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4 col-lg-3';
                    col.innerHTML = `
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column justify-content-between bg-white">
                            <div>
                                <div class="position-relative" style="height: 200px; background: #f1f5f9; overflow: hidden;">
                                    <img src="${b.coverUrl}" alt="${b.title}" class="w-100 h-100" style="object-fit: cover;">
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-success text-white rounded-pill px-2 py-1 small">
                                        <i class="bi bi-cloud-check-fill me-1"></i>Offline
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-1 line-clamp-2" style="font-size: 0.88rem;">${b.title}</h6>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">${b.author}</p>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top p-2.5 d-flex gap-1.5">
                                <a href="/perpus/baca/${b.id}?offline=true" class="btn btn-success btn-sm fw-bold rounded-pill flex-fill py-1.5">
                                    <i class="bi bi-book-half me-1"></i> Buka Offline
                                </a>
                                <button onclick="deleteOfflineBook('${b.id}')" class="btn btn-outline-danger btn-sm rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus dari offline">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });
            };
        } catch (err) {
            console.error('Load offline error:', err);
        }
    }

    async function deleteOfflineBook(id) {
        if (!confirm('Hapus buku ini dari memori offline?')) return;
        const db = await openPerpusDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        tx.objectStore(STORE_NAME).delete(id);
        tx.oncomplete = () => {
            loadOfflineBooks();
        };
    }

    // Auto-load saat tab offline dibuka
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('morph.updated', ({ component, cleanup }) => {
            if (document.getElementById('offline-books-container')) {
                loadOfflineBooks();
            }
        });
        if (document.getElementById('offline-books-container')) {
            loadOfflineBooks();
        }
    });
</script>
