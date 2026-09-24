<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Personalized Learning Track Siswa</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Portal Pembelajaran & Training Camp</h1>
            <p class="text-sm text-slate-500 mt-1">Jalur materi personal, bimbingan intensif Ujikom (UKK), dan persiapan kontingen LKS.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('ujikom.siswa.daftar') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Pendaftaran Ujikom (UKK)</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Kartu Statistik Belajar Siswa -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Total Modul Ditugaskan</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $summary['total_materi'] ?? 0 }}</div>
            <div class="text-xs text-blue-600 mt-1 font-semibold">Progres: {{ $summary['persentase_selesai'] ?? 0 }}% Tuntas</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Pelatihan Ujikom (UKK)</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $summary['modul_ujikom'] ?? 0 }} Modul</div>
            <div class="text-xs text-indigo-500 mt-1">Kisi-kisi Proyek Asesmen</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Bimbingan Kontingen LKS</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $summary['modul_lks'] ?? 0 }} Modul</div>
            <div class="text-xs text-amber-600 mt-1">Standar Lomba Vokasi</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Modul Selesai</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $summary['selesai'] ?? 0 }}</div>
            <div class="text-xs text-emerald-600 mt-1">Kompetensi Dikuasai</div>
        </div>
    </div>

    <!-- Layout 2 Kolom: Daftar Modul (Kiri) & Konten Belajar (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Daftar Penugasan Belajar -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900">Jalur Belajar Saya</h2>
                    <span class="text-xs text-slate-400">{{ count($summary['daftar_penugasan'] ?? []) }} Modul</span>
                </div>

                <div class="space-y-2.5">
                    @forelse(($summary['daftar_penugasan'] ?? []) as $p)
                        <button type="button" wire:click="selectMateri('{{ $p->id }}')" class="w-full text-left p-3.5 rounded-2xl border transition {{ $selectedPenugasan && $selectedPenugasan->id === $p->id ? 'border-blue-500 bg-blue-50/50 shadow-sm' : 'border-slate-200 hover:border-blue-200 bg-slate-50/50' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $p->materi->kategori === 'ujikom_intensif' ? 'bg-indigo-100 text-indigo-800' : ($p->materi->kategori === 'lks_khusus' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ str_replace('_', ' ', $p->materi->kategori) }}
                                </span>
                                <span class="text-[10px] font-bold uppercase {{ $p->status_progres === 'selesai' ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $p->status_progres === 'selesai' ? '✓ Tuntas' : 'Belum' }}
                                </span>
                            </div>
                            <div class="font-bold text-xs text-slate-900 line-clamp-1">{{ $p->materi->judul }}</div>
                            <div class="text-[10px] text-slate-400 mt-1">Batas: {{ \Carbon\Carbon::parse($p->target_selesai)->format('d M Y') }}</div>
                        </button>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">Belum ada modul yang ditugaskan khusus untuk Anda.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Materi Terpilih & Pengumpulan Proyek (2 Kolom) -->
        <div class="lg:col-span-2">
            @if($selectedPenugasan)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase {{ $selectedPenugasan->materi->kategori === 'ujikom_intensif' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ str_replace('_', ' ', $selectedPenugasan->materi->kategori) }}
                            </span>
                            <h2 class="text-xl font-black text-slate-900 mt-2">{{ $selectedPenugasan->materi->judul }}</h2>
                            <div class="text-xs text-slate-400 mt-1">Pembimbing: <strong class="text-slate-700">{{ $selectedPenugasan->materi->guru->name ?? '-' }}</strong> &bull; Bidang: {{ $selectedPenugasan->materi->bidang_keahlian }}</div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($selectedPenugasan->status_progres !== 'selesai')
                                <button type="button" wire:click="tandaiSelesai('{{ $selectedPenugasan->id }}')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md transition">
                                    ✓ Tandai Selesai
                                </button>
                            @else
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs">
                                    ✓ Modul Tuntas
                                </span>
                            @endif

                            <button type="button" wire:click="openSubmitModal('{{ $selectedPenugasan->id }}')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Kumpul Proyek Latihan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Video Tutorial Jika Ada -->
                    @if($selectedPenugasan->materi->link_video)
                        <div class="mb-6 p-4 bg-slate-900 text-white rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-600 text-white rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold">Video Panduan Praktikum</div>
                                    <div class="text-[11px] text-slate-400">Tonton tutorial pengerjaan dari pembimbing</div>
                                </div>
                            </div>
                            <a href="{{ $selectedPenugasan->materi->link_video }}" target="_blank" class="px-3.5 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg font-bold text-xs transition">
                                Tonton di YouTube &nearr;
                            </a>
                        </div>
                    @endif

                    <!-- Konten Materi / Jobsheet Praktikum -->
                    <div class="prose max-w-none text-slate-800 text-sm leading-relaxed mb-6">
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 font-mono text-xs whitespace-pre-wrap">
                            {{ $selectedPenugasan->materi->konten_materi }}
                        </div>
                    </div>

                    <!-- Status Pengumpulan Proyek Jika Sudah Mengumpulkan -->
                    @if($selectedPenugasan->proyekLatihan)
                        <div class="p-5 rounded-2xl border border-blue-200 bg-blue-50/30">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-xs text-blue-900 uppercase tracking-wider">Status Pengumpulan Proyek Latihan Anda:</h4>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase {{ $selectedPenugasan->proyekLatihan->status_review === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($selectedPenugasan->proyekLatihan->status_review === 'revisi' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    Review: {{ $selectedPenugasan->proyekLatihan->status_review }}
                                </span>
                            </div>

                            @if($selectedPenugasan->proyekLatihan->link_repository_git)
                                <div class="text-xs text-slate-600">
                                    Repository: <a href="{{ $selectedPenugasan->proyekLatihan->link_repository_git }}" target="_blank" class="text-blue-600 hover:underline font-mono">{{ $selectedPenugasan->proyekLatihan->link_repository_git }}</a>
                                </div>
                            @endif

                            <p class="text-xs text-slate-700 mt-2 bg-white p-3 rounded-xl border border-slate-200">{{ $selectedPenugasan->proyekLatihan->deskripsi_pekerjaan }}</p>

                            @if($selectedPenugasan->proyekLatihan->catatan_pembimbing)
                                <div class="mt-2 p-3 bg-white rounded-xl border border-blue-200 text-xs text-blue-900">
                                    <strong>Catatan Pembimbing:</strong> "{{ $selectedPenugasan->proyekLatihan->catatan_pembimbing }}"
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-sm">Pilih salah satu modul di samping untuk mulai belajar atau mengerjakan latihan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Section Kebugaran Jasmani & Hasil Latihan TKA Siswa -->
    <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Panel 1: Catatan Kebugaran Jasmani Siswa (Dari Guru Olahraga) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </span>
                    <div>
                        <h3 class="font-black text-slate-900 text-base">Kebugaran Fisik Saya</h3>
                        <p class="text-xs text-slate-400">Dicatat Guru Olahraga &amp; Terhubung ke Wali Kelas</p>
                    </div>
                </div>
                <a href="{{ route('lms.fisik.kelola') }}" class="text-xs font-bold text-emerald-600 hover:underline">Kelola &rarr;</a>
            </div>

            @forelse($riwayatTesFisik as $f)
                <div class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-100 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-emerald-800">Semester {{ $f->semester }} ({{ $f->tahun_ajaran }})</span>
                            <div class="text-[11px] text-slate-400">Penguji: {{ $f->guruOlahraga?->name ?? 'Guru PJOK' }} • {{ $f->tanggal_tes->format('d M Y') }}</div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-200 text-emerald-900">
                            {{ $f->predikat }} ({{ $f->skor_kebugaran }})
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="p-2 bg-white rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block">TB / BB</span>
                            <strong>{{ $f->tinggi_badan_cm ?: '-' }}cm / {{ $f->berat_badan_kg ?: '-' }}kg</strong>
                        </div>
                        <div class="p-2 bg-white rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block">BMI</span>
                            <strong>{{ $f->bmi }} ({{ $f->kategori_bmi }})</strong>
                        </div>
                        <div class="p-2 bg-white rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block">Push/Sit Up</span>
                            <strong>{{ $f->push_up_1min ?: 0 }} / {{ $f->sit_up_1min ?: 0 }}</strong>
                        </div>
                    </div>

                    @if($f->catatan_guru_olahraga)
                        <p class="text-xs text-slate-600 bg-white p-2.5 rounded-xl border border-slate-100 italic">"{{ $f->catatan_guru_olahraga }}"</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Belum ada rekaman tes fisik dari guru olahraga.
                </div>
            @endforelse
        </div>

        <!-- Panel 2: Simulasi Latihan Soal TKA Siswa -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <div>
                        <h3 class="font-black text-slate-900 text-base">Latihan Soal TKA</h3>
                        <p class="text-xs text-slate-400">Skolastik &amp; Penalaran Terpantau Wali Kelas</p>
                    </div>
                </div>
                <a href="{{ route('lms.tka.simulasi') }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                    Mulai Tes &rarr;
                </a>
            </div>

            @forelse($riwayatTka as $tka)
                <div class="p-3.5 rounded-2xl bg-indigo-50/30 border border-indigo-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-900">{{ $tka->paket?->judul_paket }}</h4>
                        <div class="text-[11px] text-slate-400">
                            {{ $tka->created_at->format('d M Y H:i') }} • Benar: <strong class="text-emerald-600">{{ $tka->jumlah_benar }}</strong> / Salah: <strong class="text-rose-600">{{ $tka->jumlah_salah }}</strong>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-indigo-700">{{ $tka->nilai_skor }}</span>
                        <div class="text-[10px] text-slate-400">Skor TKA</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Belum ada riwayat pengerjaan tes TKA. Klik "Mulai Tes" untuk mencoba simulasi.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL KUMPUL PROYEK LATIHAN -->
    @if($showSubmitModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900">Kumpulkan Proyek / Latihan Simulasi</h3>
                    <button type="button" wire:click="$set('showSubmitModal', false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="submitProyekLatihan" class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Link Repository GitHub / GitLab (Opsional)</label>
                        <input type="text" wire:model="linkRepoGit" placeholder="https://github.com/username/project" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1 font-mono">
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Unggah Berkas Proyek (ZIP / PDF)</label>
                        <input type="file" wire:model="fileProyek" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 border border-slate-200 rounded-xl p-1 mt-1">
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Ringkasan Hasil Pekerjaan & Pembahasan Solusi</label>
                        <textarea rows="4" wire:model="deskripsiPekerjaan" placeholder="Jelaskan fitur yang telah Anda selesaikan sesuai kisi-kisi jobsheet..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 mt-1"></textarea>
                        @error('deskripsiPekerjaan') <span class="text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">
                            ✓ Kirim ke Guru Pembimbing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>