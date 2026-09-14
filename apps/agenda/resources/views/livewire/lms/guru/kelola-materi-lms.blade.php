<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY AKADEMIK & VOKASI</span>
                <span>&bull;</span>
                <span>LMS & Training Camp SMKN 2 Indramayu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen LMS & Modul Ujikom/LKS</h1>
            <p class="text-sm text-slate-500 mt-1">Diferensiasi pembelajaran per siswa, materi intensif asesmen Ujikom, dan bimbingan kontingen LKS.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('lms.siswa.dashboard') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Portal Belajar Siswa</span>
            </a>

            <button type="button" wire:click="$set('showCreateModal', true)" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-xs shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Terbitkan Modul / Materi Baru</span>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Tab Kategori Modul -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button type="button" wire:click="$set('filterKategori', 'semua')" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filterKategori === 'semua' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Modul ({{ $materiList->count() }})
            </button>
            <button type="button" wire:click="$set('filterKategori', 'ujikom_intensif')" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filterKategori === 'ujikom_intensif' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🎯 Bimbingan Ujikom (UKK)
            </button>
            <button type="button" wire:click="$set('filterKategori', 'lks_khusus')" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filterKategori === 'lks_khusus' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🏆 Pelatihan Kontingen LKS
            </button>
            <button type="button" wire:click="$set('filterKategori', 'kbm_reguler')" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filterKategori === 'kbm_reguler' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                📚 KBM Kelas Reguler
            </button>
        </div>

        <div class="w-full sm:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul modul atau materi..." class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Grid Kartu Modul Materi -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($materiList as $materi)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $materi->kategori === 'ujikom_intensif' ? 'bg-indigo-100 text-indigo-800' : ($materi->kategori === 'lks_khusus' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ str_replace('_', ' ', $materi->kategori) }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $materi->bidang_keahlian }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 text-base group-hover:text-blue-600 transition">{{ $materi->judul }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $materi->deskripsi ?: 'Tidak ada deskripsi singkat.' }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <div>Pembimbing: <strong class="text-slate-700">{{ $materi->guru->name ?? '-' }}</strong></div>
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full font-bold text-[10px]">
                            {{ $materi->penugasanSiswa->count() }} Siswa
                        </span>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="openAssignModal('{{ $materi->id }}')" class="w-full py-2.5 px-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Tugaskan per Siswa / Rombel</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 text-sm bg-white rounded-3xl border border-slate-200">
                Belum ada modul materi LMS. Klik tombol "+ Terbitkan Modul" di atas.
            </div>
        @endforelse
    </div>

    <!-- MODAL 1: FORM TERBITKAN MODUL BARU -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Terbitkan Modul Pembelajaran / Training Camp</h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="simpanMateri" class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Judul Modul / Materi Pembelajaran</label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Bimbingan Intensif Ujikom: Restful API Laravel" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                        @error('judul') <span class="text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Kategori Program Belajar</label>
                            <select wire:model="kategori" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1 bg-white">
                                <option value="ujikom_intensif">🎯 Bimbingan Intensif Ujikom (UKK)</option>
                                <option value="lks_khusus">🏆 Pelatihan Khusus Kontingen LKS</option>
                                <option value="kbm_reguler">📚 Modul KBM Reguler Kelas</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Bidang Kejuruan</label>
                            <select wire:model="bidang_keahlian" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1 bg-white">
                                <option value="PPLG">PPLG (Software & Web)</option>
                                <option value="TJKT">TJKT (Jaringan Komputer)</option>
                                <option value="Otomotif">Teknik Otomotif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Deskripsi Singkat / Sasaran Kompetensi</label>
                        <textarea rows="2" wire:model="deskripsi" placeholder="Kisi-kisi atau sasaran proyek..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Konten Materi & Lembar Kerja (Job Sheet)</label>
                        <textarea rows="6" wire:model="konten_materi" placeholder="Tuliskan petunjuk teknis langkah kerja, spesifikasi tugas, dan kriteria penilaian..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1 font-mono"></textarea>
                        @error('konten_materi') <span class="text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Link Video Pembahasan (YouTube)</label>
                            <input type="text" wire:model="link_video" placeholder="https://youtube.com/..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Unggah File Modul / PDF Jobsheet</label>
                            <input type="file" wire:model="file_lampiran" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 border border-slate-200 rounded-xl p-1 mt-1">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-blue-600/20 transition">
                            ✓ Simpan & Terbitkan Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: PENUGASAN DIFERENSIASI BELAJAR (PER SISWA / ROMBEL) -->
    @if($showAssignModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900">Penugasan Belajar Diferensiasi</h3>
                    <button type="button" wire:click="$set('showAssignModal', false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <!-- Pilihan Target: Per Siswa atau Per Rombel -->
                <div class="flex p-1 bg-slate-100 rounded-xl mb-4 text-xs">
                    <button type="button" wire:click="$set('targetType', 'siswa')" class="flex-1 py-1.5 rounded-lg font-bold transition {{ $targetType === 'siswa' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                        1. Khusus Siswa Tertentu
                    </button>
                    <button type="button" wire:click="$set('targetType', 'rombel')" class="flex-1 py-1.5 rounded-lg font-bold transition {{ $targetType === 'rombel' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                        2. Satu Rombel Kelas
                    </button>
                </div>

                <form wire:submit.prevent="submitAssignment" class="space-y-3 text-xs">
                    @if($targetType === 'siswa')
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Pilih Siswa</label>
                            <select wire:model="selectedSiswaId" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white mt-1">
                                @foreach($siswaList as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="font-bold text-slate-700 uppercase">Pilih Rombel Kelas</label>
                            <select wire:model="selectedRombelId" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white mt-1">
                                @foreach($rombelList as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Tipe Jalur Belajar</label>
                        <select wire:model="tipeJalur" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white mt-1">
                            <option value="peserta_ujikom">🎯 Peserta Persiapan Ujikom (UKK)</option>
                            <option value="peserta_lks">🏆 Peserta Bimbingan Intensif LKS</option>
                            <option value="pengayaan">⭐ Pengayaan / Akselerasi Siswa</option>
                            <option value="remedial">💡 Remedial / Pendampingan Khusus</option>
                            <option value="reguler">📚 Pembelajaran Reguler</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Batas Waktu Selesai</label>
                        <input type="date" wire:model="targetSelesai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">
                            ✓ Konfirmasi Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>