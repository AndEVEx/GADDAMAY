<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Fase 2: PKL SMKN 2 Indramayu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Jurnal Aktivitas Harian PKL</h1>
            <p class="text-sm text-slate-500 mt-1">Pencatatan kompetensi dan pekerjaan riil industri mengacu 11 Elemen CP PPLG 2025.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pkl.siswa.presensi') }}" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Presensi GPS</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($penempatan)
        <!-- Form Pengisian Jurnal Baru -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Input Log Jurnal Pekerjaan Baru</span>
            </h2>

            <form wire:submit.prevent="simpanJurnal" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Kegiatan</label>
                        <input type="date" wire:model="tanggal" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Mulai</label>
                        <input type="time" wire:model="jamMulai" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Selesai</label>
                        <input type="time" wire:model="jamSelesai" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Elemen Capaian Pembelajaran (CP) PPLG 2025</label>
                    <select wire:model="elemenCp" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        @foreach($elemenCpList as $cp)
                            <option value="{{ $cp }}">{{ $cp }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Ringkasan Pekerjaan / Aktivitas Nyata</label>
                    <textarea wire:model="ringkasanPekerjaan" rows="3" placeholder="Jelaskan secara detail apa yang Anda kerjakan hari ini di industri..." class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('ringkasanPekerjaan') <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alat & Bahan / Software yang Digunakan</label>
                        <input type="text" wire:model="alatDanBahan" placeholder="Contoh: VS Code, Git, Figma, Docker, Laravel" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Foto Bukti / Dokumentasi Pekerjaan</label>
                        <input type="file" wire:model="fotoDokumentasi" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl p-1.5">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Jurnal & Notifikasi Pembimbing DUDI</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Histori Jurnal Harian Siswa -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Riwayat Jurnal PKL Tersimpan</h2>

            <div class="space-y-4">
                @forelse($daftarJurnal as $jurnal)
                    <div class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-200 transition bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                            <div>
                                <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold uppercase tracking-wider">
                                    {{ $jurnal->elemen_cp }}
                                </span>
                                <h4 class="font-bold text-slate-900 mt-1.5 text-base">{{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}</h4>
                                <div class="text-xs text-slate-500">Pukul {{ substr($jurnal->jam_mulai, 0, 5) }} - {{ substr($jurnal->jam_selesai, 0, 5) }} WIB</div>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Status Paraf DUDI -->
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase {{ $jurnal->paraf_dudi_status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($jurnal->paraf_dudi_status === 'perlu_perbaikan' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    Paraf DUDI: {{ $jurnal->paraf_dudi_status }}
                                </span>
                            </div>
                        </div>

                        <p class="text-sm text-slate-700 mt-2 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-100">{{ $jurnal->ringkasan_pekerjaan }}</p>

                        @if($jurnal->alat_dan_bahan)
                            <div class="mt-2 text-xs text-slate-500">
                                <strong>Alat/Software:</strong> {{ $jurnal->alat_dan_bahan }}
                            </div>
                        @endif

                        @if($jurnal->catatan_dudi)
                            <div class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                                <strong>Catatan Pembimbing DUDI:</strong> "{{ $jurnal->catatan_dudi }}"
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">Belum ada catatan jurnal PKL. Silakan isi form di atas.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>