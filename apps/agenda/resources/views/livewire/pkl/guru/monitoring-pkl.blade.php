<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Fase 2: Monitoring Pokja & Pembimbing PKL</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Monitoring Jurnal & Asesmen PKL</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau presensi GPS, verifikasi jurnal 11 Elemen CP PPLG, dan sinkronisasi nilai akhir DUDI.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pkl.admin.penempatan') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Kelola Penempatan DUDI</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="mb-6 p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Kartu Statistik Singkat -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Total Siswa PKL</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $penempatanList->count() }}</div>
            <div class="text-xs text-emerald-600 mt-1 font-semibold">Tahun Ajaran {{ $filterTahun }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Mitra Industri (DUDI)</div>
            <div class="text-2xl font-black text-indigo-900 mt-1">{{ $penempatanList->pluck('dudi_id')->unique()->count() }}</div>
            <div class="text-xs text-slate-500 mt-1">Terhubung Magic Link WA</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Total Log Jurnal Masuk</div>
            <div class="text-2xl font-black text-sky-900 mt-1">{{ $penempatanList->sum(fn($p) => $p->jurnalHarian->count()) }}</div>
            <div class="text-xs text-slate-500 mt-1">11 Elemen CP PPLG</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Rumus Bobot NA</div>
            <div class="text-xl font-black text-emerald-700 mt-1">5 : 3 : 2</div>
            <div class="text-[11px] text-slate-400 mt-1">DUDI (5) : Penguji (3) : Laporan (2)</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari siswa, NISN, atau nama DUDI..." class="w-full px-4 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="text-xs text-slate-500">
                Menampilkan <strong>{{ $penempatanList->count() }}</strong> penempatan siswa
            </div>
        </div>
    </div>

    <!-- Tabel Monitoring Siswa PKL -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3">Siswa</th>
                        <th class="pb-3">Tempat DUDI</th>
                        <th class="pb-3">Pembimbing Industri & WA</th>
                        <th class="pb-3 text-center">Jurnal</th>
                        <th class="pb-3 text-center">Nilai Akhir (NA)</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penempatanList as $p)
                        @php
                            $na = $p->penilaian?->nilai_akhir_angka ?? 0;
                            $predikat = $p->penilaian?->predikat_huruf ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4">
                                <div class="font-bold text-slate-900">{{ $p->siswa->nama ?? 'Siswa' }}</div>
                                <div class="text-xs text-slate-500">NISN: {{ $p->siswa->nisn ?? '-' }} | {{ $p->siswa->rombel->nama_rombel ?? '-' }}</div>
                            </td>
                            <td class="py-4">
                                <div class="font-semibold text-slate-800">{{ $p->dudi->nama_instansi ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $p->dudi->kota ?? 'Indramayu' }}</div>
                            </td>
                            <td class="py-4">
                                <div class="font-medium text-slate-700">{{ $p->nama_pembimbing_dudi ?? $p->dudi->pembimbing_nama ?? 'Pembimbing DUDI' }}</div>
                                <div class="text-xs font-mono text-emerald-700 font-semibold">{{ $p->nomor_wa_dudi }}</div>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 bg-sky-50 text-sky-700 border border-sky-200 rounded-full font-bold text-xs">
                                    {{ $p->jurnalHarian->count() }} log
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                @if($na > 0)
                                    <div>
                                        <span class="text-base font-black text-slate-900">{{ $na }}</span>
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-black {{ $na >= 75 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            Predikat {{ $predikat }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">DUDI: {{ $p->penilaian->nilai_total_dudi }} | Sidang: {{ $p->penilaian->nilai_sidang_sekolah }}</div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Tombol Kirim WA ke DUDI -->
                                    <button type="button" wire:click="openWaModal('{{ $p->id }}')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span>Kirim Link WA</span>
                                    </button>

                                    <!-- Tombol Input Nilai Sidang & Laporan -->
                                    <button type="button" wire:click="openNilaiModal('{{ $p->id }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Nilai Sidang/Laporan</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">Tidak ada data penempatan PKL yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: KIRIM MAGIC LINK WA KE DUDI -->
    @if($showWaModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Kirim Link Review ke DUDI</h3>
                    <button type="button" wire:click="closeWaModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <div class="mb-4 p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                    <div>Penerima: <strong>{{ $dudiName }}</strong></div>
                    <div>Nomor WhatsApp: <strong>{{ $dudiPhone }}</strong></div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teks Pesan Resmi (Pre-filled):</label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 whitespace-pre-wrap max-h-48 overflow-y-auto">{{ $waMessage }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tautan Langsung DUDI (Tanpa Password):</label>
                    <input type="text" readonly value="{{ $magicLinkUrl }}" class="w-full px-3 py-2 text-xs bg-slate-100 rounded-xl border border-slate-200 text-slate-600 font-mono">
                </div>

                <!-- Tombol Aksi: Failover Manual wa.me & Gateway -->
                <div class="space-y-2">
                    <a href="{{ $waMeLink }}" target="_blank" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs text-center shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span>[🟢 Buka WhatsApp Pribadi Guru] (Kirim Manual wa.me)</span>
                    </a>

                    <button type="button" wire:click="kirimWaOtomatis" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Kirim Otomatis via Gateway Server (GOWA / WA-AKG)</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: INPUT NILAI SEKOLAH (SIDANG & LAPORAN) -->
    @if($showNilaiModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Penilaian Sidang & Laporan Sekolah</h3>
                    <button type="button" wire:click="closeNilaiModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <p class="text-xs text-slate-500 mb-4">
                    Asesmen penguji sekolah berbobot 30% (sidang) dan 20% (buku laporan PKL) sesuai panduan resmi SMKN 2 Indramayu.
                </p>

                <form wire:submit.prevent="simpanNilaiSekolah" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nilai Ujian Sidang PKL (Bobot 30%)</label>
                        <input type="number" min="0" max="100" wire:model="nilaiSidang" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nilai Portofolio & Buku Laporan (Bobot 20%)</label>
                        <input type="number" min="0" max="100" wire:model="nilaiLaporan" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Penguji Sekolah (Opsional)</label>
                        <textarea rows="2" wire:model="catatanPenguji" placeholder="Catatan penguasaan materi / revisi laporan..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                            Simpan & Hitung Nilai Akhir (NA) Otomatis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>