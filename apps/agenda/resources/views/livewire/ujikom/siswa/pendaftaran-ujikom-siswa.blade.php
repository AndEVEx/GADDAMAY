<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>LSP-P1 SMKN 2 INDRAMAYU</span>
                <span>&bull;</span>
                <span>Sertifikasi Kompetensi Keahlian (UKK)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pendaftaran & Asesmen Ujikom</h1>
            <p class="text-sm text-slate-500 mt-1">Registrasi asesi, pengunggahan formulir APL-01/02, dan penerbitan Kartu Peserta Uji.</p>
        </div>

        <div>
            <a href="{{ route('lms.siswa.dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Buka Modul Latihan Ujikom</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(!$pendaftaranAktif)
        <!-- Form Pendaftaran Pertama Kali -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <div class="mb-6">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold text-xs uppercase">Tahun Ajaran {{ $tahun_ajaran }}</span>
                <h2 class="text-xl font-black text-slate-900 mt-2">Formulir Permohonan Sertifikasi Ujikom</h2>
                <p class="text-xs text-slate-500 mt-1">Pastikan data diri Anda telah sesuai dengan database kesiswaan sekolah.</p>
            </div>

            <!-- Data Diri Siswa -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <div class="text-slate-400 font-bold uppercase text-[10px]">Nama Lengkap Asesi</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $siswa->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-bold uppercase text-[10px]">NIS / NISN</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $siswa->nis ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-bold uppercase text-[10px]">Rombel Kelas</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $siswa->rombel->nama_rombel ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="daftarUjikom" class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 uppercase mb-1 block">Pilih Skema Sertifikasi Kompetensi</label>
                    <select wire:model="skema_id" class="w-full px-4 py-3 text-xs rounded-xl border border-slate-200 bg-white">
                        @foreach($skemaList as $skema)
                            <option value="{{ $skema->id }}">{{ $skema->kode_skema }} - {{ $skema->nama_skema }} ({{ $skema->jurusan }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs shadow-lg shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Daftar Sebagai Peserta Ujikom Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Kartu Tanda Peserta Ujikom (Digital Card) -->
        <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-blue-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                <div>
                    <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        KARTU ASESI RESMI LSP-P1 SMKN 2 INDRAMAYU
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">{{ $pendaftaranAktif->siswa->nama ?? 'Asesi' }}</h2>
                    <div class="text-xs text-slate-300 mt-1">Nomor Registrasi: <strong class="text-indigo-400 font-mono text-sm">{{ $pendaftaranAktif->nomor_pendaftaran }}</strong></div>
                    <div class="text-xs text-slate-300 mt-0.5">Skema: <strong>{{ $pendaftaranAktif->skema->nama_skema ?? '-' }}</strong></div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-xs flex flex-col justify-between">
                    <div>
                        <div class="text-slate-400 uppercase font-semibold text-[10px]">Status Verifikasi Berkas:</div>
                        <span class="px-2.5 py-0.5 rounded-full font-black uppercase text-[10px] inline-block mt-1 {{ $pendaftaranAktif->status_verifikasi === 'lolos_administrasi' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30' }}">
                            {{ str_replace('_', ' ', $pendaftaranAktif->status_verifikasi) }}
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-white/10">
                        <div class="text-[10px] text-slate-400">Tempat Uji Kompetensi (TUK):</div>
                        <div class="font-bold text-white text-xs mt-0.5">{{ $pendaftaranAktif->tempat_uji_kompetensi_tuk }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unggah Berkas Pra-Asesmen (APL-01 & APL-02) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <h2 class="text-base font-bold text-slate-900 mb-1">Unggah Kelengkapan Berkas Pra-Asesmen</h2>
            <p class="text-xs text-slate-500 mb-4">Lampirkan formulir APL-01, formulir APL-02 Asesmen Mandiri, sertifikat PKL, atau bukti portofolio TEFA.</p>

            <form wire:submit.prevent="uploadBerkas" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs mb-6">
                <div>
                    <label class="font-bold text-slate-700 uppercase mb-1 block">Jenis Berkas</label>
                    <select wire:model="jenis_berkas" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                        <option value="apl_01_permohonan">Formulir APL-01 (Permohonan Sertifikasi)</option>
                        <option value="apl_02_mandiri">Formulir APL-02 (Asesmen Mandiri)</option>
                        <option value="portofolio_proyek">Portofolio Hasil Proyek TEFA</option>
                        <option value="sertifikat_pkl">Sertifikat / Nilai PKL Industri</option>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase mb-1 block">Keterangan / Nama Dokumen</label>
                    <input type="text" wire:model="nama_berkas" placeholder="Contoh: APL-02 Pemrograman Web" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase mb-1 block">Pilih Berkas (PDF / ZIP)</label>
                    <input type="file" wire:model="fileBerkas" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 border border-slate-200 rounded-xl p-1">
                </div>

                <div class="sm:col-span-3 pt-1">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Unggah Dokumen Pra-Asesmen</span>
                    </button>
                </div>
            </form>

            <!-- Tabel Berkas Terunggah -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                            <th class="pb-2">Jenis Berkas</th>
                            <th class="pb-2">Nama Dokumen</th>
                            <th class="pb-2">Tanggal Unggah</th>
                            <th class="pb-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendaftaranAktif->berkas as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 font-bold uppercase text-[11px] text-indigo-700">{{ str_replace('_', ' ', $b->jenis_berkas) }}</td>
                                <td class="py-2.5 text-slate-800">{{ $b->nama_file }}</td>
                                <td class="py-2.5 text-slate-400">{{ \Carbon\Carbon::parse($b->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 text-right">
                                    <span class="text-emerald-600 font-bold">✓ Terlampir</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada berkas pra-asesmen yang diunggah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>