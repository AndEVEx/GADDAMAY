<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Logsheet Siswa Unit Teaching Factory</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Logsheet Produksi & Jam Terbang Siswa</h1>
            <p class="text-sm text-slate-500 mt-1">Catat jam pengerjaan riil proyek industri untuk sertifikat portofolio keahlian kerja.</p>
        </div>

        <!-- Kartu Total Jam Terbang Siswa -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-2xl p-4 shadow-lg shadow-emerald-600/20 flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold tracking-wider text-emerald-100">Total Jam Terbang Vokasi</div>
                <div class="text-2xl font-black leading-tight">{{ $totalJamLolos }} <span class="text-xs font-normal">Jam Lolos QC</span></div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Input Logsheet Baru -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span>Input Sesi Kerja Produksi Baru</span>
        </h2>

        <form wire:submit.prevent="simpanLog" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Proyek / Order TEFA</label>
                    <select wire:model="selectedOrderId" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white">
                        @foreach($myOrders as $ord)
                            <option value="{{ $ord->id }}">{{ $ord->kode_order }} - {{ $ord->judul_proyek }} ({{ $ord->nama_pemesan }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tanggal Pengerjaan</label>
                    <input type="date" wire:model="tanggal" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Jam Mulai</label>
                    <input type="time" wire:model="jamMulai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Jam Selesai</label>
                    <input type="time" wire:model="jamSelesai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Ringkasan Pekerjaan / Modul yang Dikerjakan</label>
                <textarea rows="3" wire:model="ringkasanPekerjaan" placeholder="Jelaskan secara spesifik apa yang Anda selesaikan pada sesi kerja ini..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200"></textarea>
                @error('ringkasanPekerjaan') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Alat / Software / Bahan yang Digunakan</label>
                    <input type="text" wire:model="alatDanBahan" placeholder="Contoh: Laravel, Tailwind, Docker, GitHub, Solder, Obeng" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Foto Bukti / Tangkapan Layar Hasil Pekerjaan</label>
                    <input type="file" wire:model="fotoProgres" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 border border-slate-200 rounded-xl p-1.5">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Sesi Kerja Produksi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Logsheet Jam Kerja Siswa -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Riwayat Logsheet Jam Kerja Produksi Anda</h2>

        <div class="space-y-4">
            @forelse($riwayatLog as $log)
                <div class="p-5 rounded-2xl border {{ $log->status_qc === 'lolos_qc' ? 'border-emerald-200 bg-emerald-50/20' : ($log->status_qc === 'perlu_revisi' ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-slate-50/50') }} transition">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                        <div>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-mono text-[10px] font-bold">
                                {{ $log->order->kode_order ?? '-' }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm mt-1">{{ $log->order->judul_proyek ?? 'Proyek TEFA' }}</h3>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('dddd, D MMMM Y') }} &bull; Durasi: <strong>{{ $log->durasi_menit }} Menit</strong> ({{ substr($log->jam_mulai, 0, 5) }} - {{ substr($log->jam_selesai, 0, 5) }})
                            </div>
                        </div>

                        <div>
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $log->status_qc === 'lolos_qc' ? 'bg-emerald-100 text-emerald-800' : ($log->status_qc === 'perlu_revisi' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                QC: {{ str_replace('_', ' ', $log->status_qc) }}
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100 leading-relaxed mt-2">
                        {{ $log->ringkasan_pekerjaan }}
                    </p>

                    @if($log->alat_dan_bahan)
                        <div class="mt-2 text-[11px] text-slate-500">
                            Alat: <strong>{{ $log->alat_dan_bahan }}</strong>
                        </div>
                    @endif

                    @if($log->catatan_instruktur)
                        <div class="mt-2 p-2.5 bg-rose-50 rounded-xl border border-rose-200 text-xs text-rose-900">
                            Catatan Instruktur: "{{ $log->catatan_instruktur }}"
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 text-slate-400 text-xs">Belum ada catatan logsheet jam kerja produksi yang Anda buat.</div>
            @endforelse
        </div>
    </div>
</div>