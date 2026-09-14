<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Header Banner Profil Anak -->
    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-rose-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
            <div>
                <span class="px-3 py-1 bg-rose-500/20 text-rose-300 border border-rose-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                    Buku Monitoring Parenting Digital
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">
                    {{ $siswa->nama ?? 'Siswa SMKN 2 Indramayu' }}
                </h1>
                <div class="text-slate-300 text-xs sm:text-sm mt-1 flex flex-wrap gap-x-4 gap-y-1">
                    <span>NISN: <strong>{{ $siswa->nisn ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>Kelas: <strong>{{ $siswa->rombel->nama_rombel ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>Tahun Ajaran: 2025/2026</span>
                </div>
            </div>

            <!-- Kartu Skor Kedisiplinan -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <div class="text-slate-400 uppercase text-[10px] font-bold tracking-wider">Skor Kedisiplinan Siswa</div>
                    <div class="text-2xl font-black text-white leading-tight">
                        {{ $summary['saldo_poin'] ?? 100 }} <span class="text-xs font-normal text-slate-300">Poin</span>
                    </div>
                    <div class="text-[11px] text-emerald-400 font-semibold">Predikat: Sangat Baik</div>
                </div>
            </div>
        </div>

        <!-- Banner Khusus jika Siswa Memiliki Izin atau Sedang PKL -->
        @if(!empty($summary['izin_aktif']))
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center gap-3 text-xs bg-amber-500/20 px-4 py-2.5 rounded-xl border border-amber-400/30 text-amber-200">
                <svg class="w-4 h-4 flex-shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Ananda memiliki <strong>Izin Sah Terverifikasi ({{ strtoupper($summary['izin_aktif']->kategori) }})</strong> untuk hari ini. Status KBM kelas dan gerbang telah disinkronkan otomatis.</span>
            </div>
        @endif

        @if(!empty($summary['status_pkl']))
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center gap-3 text-xs bg-sky-500/20 px-4 py-2.5 rounded-xl border border-sky-400/30 text-sky-200">
                <svg class="w-4 h-4 flex-shrink-0 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span>Ananda saat ini aktif dalam <strong>Program PKL Industri di {{ $summary['status_pkl']->dudi->nama_instansi ?? 'Mitra DUDI' }}</strong>. Presensi harian tercatat via GPS Geolocation.</span>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tab Navigasi Menu Orang Tua -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-4 mb-6 overflow-x-auto">
        <button type="button" wire:click="setTab('timeline')" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition flex items-center gap-2 {{ $activeTab === 'timeline' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Timeline Kehadiran Hari Ini</span>
        </button>

        <button type="button" wire:click="setTab('disiplin')" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition flex items-center gap-2 {{ $activeTab === 'disiplin' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Buku Disiplin & Prestasi ({{ $disiplinList->count() }})</span>
        </button>

        <button type="button" wire:click="setTab('izin')" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition flex items-center gap-2 {{ $activeTab === 'izin' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Ajukan Izin Sakit / Keperluan</span>
        </button>

        <button type="button" wire:click="setTab('konsultasi')" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition flex items-center gap-2 {{ $activeTab === 'konsultasi' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <span>Kontak Wali Kelas & BK</span>
        </button>
    </div>

    <!-- TAB 1: TIMELINE KEHADIRAN HARI INI -->
    @if($activeTab === 'timeline')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Alur Kehadiran Siswa Hari Ini</h2>
            <p class="text-xs text-slate-500 mb-6">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} &bull; Update otomatis dari scanner gerbang & ruang kelas.</p>

            <div class="relative pl-6 sm:pl-8 border-l-2 border-indigo-100 space-y-6">
                @foreach($timeline as $item)
                    <div class="relative">
                        <!-- Dot Indicator -->
                        <div class="absolute -left-[31px] sm:-left-[39px] top-1.5 w-4 h-4 rounded-full border-2 border-white {{ $item['tipe'] === 'gerbang' ? 'bg-emerald-500 shadow-md shadow-emerald-500/50' : 'bg-indigo-600 shadow-md shadow-indigo-600/50' }}"></div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-indigo-200 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $item['judul'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $item['status'] === 'hadir' || $item['status'] === 'hadir_tepat_waktu' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $item['status'] }}
                                    </span>
                                </div>
                                <span class="font-mono text-xs font-bold text-slate-500">{{ $item['jam'] }}</span>
                            </div>

                            <p class="text-xs text-slate-600 mt-1">{{ $item['keterangan'] }}</p>

                            @if(isset($item['guru']))
                                <div class="mt-2 pt-2 border-t border-slate-200/60 text-[11px] text-slate-400">
                                    Pengampu: <strong class="text-slate-700">{{ $item['guru'] }}</strong> &bull; Lokasi: {{ $item['lokasi'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- TAB 2: BUKU KEDISIPLINAN & PRESTASI -->
    @if($activeTab === 'disiplin')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Buku Catatan Kedisiplinan & Prestasi</h2>
            <p class="text-xs text-slate-500 mb-6">Rekaman pembinaan resmi dari Guru Bimbingan Konseling (BK), Wali Kelas, dan Guru Piket.</p>

            <div class="space-y-4">
                @forelse($disiplinList as $cat)
                    <div class="p-5 rounded-2xl border {{ $cat->poin >= 0 ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} transition">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase {{ $cat->poin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $cat->kategori }} &bull; {{ $cat->poin >= 0 ? '+' . $cat->poin : $cat->poin }} Poin
                                </span>
                                <h3 class="font-bold text-slate-900 text-base mt-2">{{ $cat->jenis_tindakan }}</h3>
                                <div class="text-xs text-slate-400 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($cat->tanggal_kejadian)->isoFormat('dddd, D MMMM Y') }}</div>
                            </div>
                            <div class="text-xs text-slate-500">
                                Dicatat oleh: <strong>{{ $cat->petugas->name ?? 'Petugas BK' }}</strong>
                            </div>
                        </div>

                        <p class="text-sm text-slate-700 bg-white p-3.5 rounded-xl border border-slate-100 mt-2 leading-relaxed">
                            {{ $cat->deskripsi }}
                        </p>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400 text-sm">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>Tidak ada catatan pelanggaran. Catatan kedisiplinan putra/putri Anda bersih.</div>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: FORM PERIZINAN LANGSUNG DARI ORTU -->
    @if($activeTab === 'izin')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Pengajuan Surat Izin / Sakit dari Rumah</h2>
            <p class="text-xs text-slate-500 mb-6">Orang tua dapat mengajukan izin ketidakhadiran anak secara resmi tanpa perlu surat kertas fisik.</p>

            <form wire:submit.prevent="ajukanIzinOrtu" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Izin</label>
                        <select wire:model="kategoriIzin" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="sakit">Sakit (Melampirkan Surat Dokter / Resep)</option>
                            <option value="izin_keperluan">Izin Keperluan Keluarga Mendesak</option>
                            <option value="dispensasi_sekolah">Dispensasi Kegiatan Lomba</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp Anda (Orang Tua)</label>
                        <input type="text" wire:model="nomorWaOrtu" placeholder="08..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                        @error('nomorWaOrtu') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Mulai Tanggal</label>
                        <input type="date" wire:model="tanggalMulai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model="tanggalSelesai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Alasan Izin / Keterangan Sakit</label>
                    <textarea rows="3" wire:model="alasanIzin" placeholder="Jelaskan alasan ketidakhadiran ananda..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200"></textarea>
                    @error('alasanIzin') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Unggah Foto Surat Keterangan Dokter / Surat Pernyataan</label>
                    <input type="file" wire:model="lampiranFoto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 border border-slate-200 rounded-xl p-1.5">
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-rose-600/20 transition">
                        ✓ Kirim Permohonan Izin ke Guru Piket & Walas
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 4: KONTAK WALI KELAS & BK (WHATSAPP) -->
    @if($activeTab === 'konsultasi')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Konsultasi Terpadu Sekolah & Orang Tua</h2>
            <p class="text-xs text-slate-500 mb-6">Hubungi langsung pendidik dan pembimbing anak Anda melalui layanan WhatsApp resmi.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Card Hubungi Walas -->
                <div class="p-6 rounded-2xl bg-emerald-50/60 border border-emerald-200 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Wali Kelas {{ $siswa->rombel->nama_rombel ?? '-' }}</h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Konsultasikan perkembangan belajar, kehadiran di kelas, serta koordinasi harian ananda dengan Wali Kelas.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-emerald-200">
                        <a href="{{ $walasWaLink }}" target="_blank" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs text-center transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Chat WhatsApp Wali Kelas</span>
                        </a>
                    </div>
                </div>

                <!-- Card Hubungi Guru BK -->
                <div class="p-6 rounded-2xl bg-indigo-50/60 border border-indigo-200 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Bimbingan Konseling (BK)</h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Konsultasi pendampingan psikologis, kedisiplinan, minat karir, dan penyelesaian kendala belajar anak di sekolah.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-indigo-200">
                        <a href="{{ $walasWaLink }}" target="_blank" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs text-center transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Hubungi Ruang Konseling BK</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>