<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY PARENTING</span>
                <span>&bull;</span>
                <span>Bimbingan Konseling & Kesiswaan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Kedisiplinan & Prestasi Siswa</h1>
            <p class="text-sm text-slate-500 mt-1">Pencatatan kredit poin pembinaan BK, pelanggaran tata tertib, serta notifikasi resmi WhatsApp ke Orang Tua.</p>
        </div>

        <div>
            <a href="{{ route('parenting.login') }}" target="_blank" class="px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Buka Portal Orang Tua</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Form Pencatatan Baru -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Input Catatan Pembinaan / Prestasi</span>
            </h2>

            <form wire:submit.prevent="simpanCatatan" class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-slate-700 uppercase">Pilih Siswa</label>
                    <select wire:model="siswa_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1 bg-white">
                        @foreach($siswaList as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Kategori</label>
                        <select wire:model="kategori" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 mt-1 bg-white">
                            <option value="pelanggaran">Pelanggaran Tata Tertib</option>
                            <option value="pembinaan_bk">Pembinaan Konseling BK</option>
                            <option value="prestasi">Prestasi Akademik/Lomba</option>
                            <option value="apresiasi">Apresiasi Sikap Positif</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Poin (Kredit)</label>
                        <input type="number" wire:model="poin" placeholder="Contoh: 5, 10, 20" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase">Jenis Tindakan / Nama Prestasi</label>
                    <input type="text" wire:model="jenis_tindakan" placeholder="Contoh: Terlambat Masuk Kelas / Juara 1 LKS" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase">Deskripsi & Catatan Pembinaan</label>
                    <textarea rows="3" wire:model="deskripsi" placeholder="Tuliskan kronologi dan saran pembinaan untuk orang tua..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase">Tanggal Kejadian</label>
                    <input type="date" wire:model="tanggal_kejadian" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase">Nomor WhatsApp Orang Tua</label>
                    <input type="text" wire:model="nomorWaOrtu" placeholder="08..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition">
                        ✓ Simpan & Siapkan Pesan WA
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Catatan Terkini (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4">Riwayat Catatan Kedisiplinan & Prestasi Terbaru</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                            <th class="pb-3">Siswa</th>
                            <th class="pb-3">Tindakan / Prestasi</th>
                            <th class="pb-3 text-center">Poin</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3 text-right">Notifikasi WA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatCatatan as $rc)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $rc->siswa->nama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $rc->siswa->rombel->nama_rombel ?? '-' }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-slate-800">{{ $rc->jenis_tindakan }}</div>
                                    <div class="text-[10px] text-slate-500 line-clamp-1">{{ $rc->deskripsi }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-black text-[11px] {{ $rc->poin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $rc->poin >= 0 ? '+' . $rc->poin : $rc->poin }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($rc->tanggal_kejadian)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" wire:click="openWaModal('{{ $rc->id }}')" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span>Kirim WA</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada catatan pembinaan siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL KIRIM WA KE ORANG TUA -->
    @if($showWaModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Kirim Pemberitahuan WA ke Orang Tua</h3>
                    <button type="button" wire:click="closeWaModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <div class="mb-4 p-3 bg-rose-50 rounded-2xl border border-rose-200 text-xs text-rose-900">
                    <div>Nama Siswa: <strong>{{ $namaSiswaTarget }}</strong></div>
                    <div>Nomor WhatsApp Orang Tua: <strong>{{ $targetPhone }}</strong></div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teks Pesan Resmi (Pre-filled):</label>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 whitespace-pre-wrap max-h-56 overflow-y-auto">{{ $waMessage }}</div>
                </div>

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
</div>