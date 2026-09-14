<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb & Navigasi Balik -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('tefa.admin.orders') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Order TEFA</span>
        </a>

        @if($order->status !== 'selesai_diserahkan')
            <button type="button" wire:click="selesaikanOrder" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Tandai Proyek Selesai & Lolos QC</span>
            </button>
        @else
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-black text-xs uppercase flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Status: Selesai & Lolos QC</span>
            </span>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header SPK Proyek -->
    <div class="bg-gradient-to-br from-slate-900 to-emerald-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded font-mono text-xs font-bold">
                        {{ $order->kode_order }}
                    </span>
                    <span class="px-2.5 py-0.5 bg-white/10 rounded text-xs font-bold text-slate-300 uppercase">
                        Unit: {{ $order->kategori_kejuruan }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $order->judul_proyek }}</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">{{ $order->deskripsi_proyek ?: 'Tidak ada deskripsi spesifikasi tambahan.' }}</p>

                <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap gap-4 text-xs text-slate-300">
                    <div>Pemesan: <strong class="text-white">{{ $order->nama_pemesan }}</strong> ({{ $order->instansi_pemesan ?: 'Perorangan' }})</div>
                    <div>&bull;</div>
                    <div>WhatsApp: <strong class="text-emerald-400 font-mono">{{ $order->nomor_wa_pemesan }}</strong></div>
                    <div>&bull;</div>
                    <div>Deadline: <strong class="text-white">{{ \Carbon\Carbon::parse($order->target_selesai)->format('d F Y') }}</strong></div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
                <div>
                    <div class="text-xs text-slate-300 uppercase font-semibold">Total Jam Kerja Tim Produksi:</div>
                    <div class="text-3xl font-black text-white mt-1">{{ $order->total_jam_produksi }} <span class="text-base font-normal text-slate-300">Jam Riil</span></div>
                    <div class="text-xs text-slate-400 mt-1">{{ $order->logProduksi->where('status_qc', 'lolos_qc')->count() }} sesi kerja terverifikasi QC</div>
                </div>

                <div class="mt-4 pt-4 border-t border-white/10">
                    <div class="text-xs text-slate-300">Instruktur Supervisi:</div>
                    <div class="text-sm font-bold text-emerald-300">{{ $order->instruktur->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout 2 Kolom: Tim Kerja Siswa & Logsheet Produksi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Tim Kerja Siswa Pelaksana -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900">Tim Kerja Siswa</h2>
                    <button type="button" wire:click="$set('showAddMemberModal', true)" class="text-xs text-emerald-600 font-bold hover:underline">
                        + Tambah Siswa
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($order->timKerja as $tim)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="font-bold text-xs text-slate-900">{{ $tim->siswa->nama ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $tim->siswa->rombel->nama_rombel ?? '-' }}</div>
                            <div class="mt-2 flex items-center justify-between text-[11px]">
                                <span class="px-2 py-0.5 rounded font-bold uppercase {{ $tim->peran_dalam_tim === 'project_manager' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                    {{ str_replace('_', ' ', $tim->peran_dalam_tim) }}
                                </span>
                                <span class="text-slate-500 font-mono">{{ $order->logProduksi->where('siswa_id', $tim->siswa_id)->sum('durasi_menit') }} mnt</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">Belum ada siswa yang ditugaskan ke proyek ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Logsheet Produksi & Inspeksi Quality Control (QC) (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Logsheet Jam Kerja & Quality Control (QC)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan standar hasil kerja siswa sebelum diserahkan ke pemesan.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($order->logProduksi as $log)
                        <div class="p-5 rounded-2xl border {{ $log->status_qc === 'lolos_qc' ? 'border-emerald-200 bg-emerald-50/20' : ($log->status_qc === 'perlu_revisi' ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-slate-50/50') }} transition">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $log->siswa->nama ?? 'Siswa' }}</div>
                                    <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('dddd, D MMMM Y') }} &bull; Pukul {{ substr($log->jam_mulai, 0, 5) }} - {{ substr($log->jam_selesai, 0, 5) }} ({{ $log->durasi_menit }} Menit)</div>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    @if($log->status_qc === 'lolos_qc')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[11px] font-bold uppercase">
                                            ✓ Lolos QC
                                        </span>
                                    @else
                                        <button type="button" wire:click="setujuiQc('{{ $log->id }}')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition">
                                            ✓ Setujui QC
                                        </button>
                                        <button type="button" wire:click="tolakQc('{{ $log->id }}')" class="px-3 py-1 bg-slate-200 hover:bg-rose-100 hover:text-rose-700 text-slate-600 rounded-lg font-bold text-xs transition">
                                            Revisi
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100 leading-relaxed mt-2">
                                {{ $log->ringkasan_pekerjaan }}
                            </p>

                            @if($log->alat_dan_bahan)
                                <div class="mt-2 text-[11px] text-slate-500">
                                    Alat/Software: <strong>{{ $log->alat_dan_bahan }}</strong>
                                </div>
                            @endif

                            @if($log->catatan_instruktur)
                                <div class="mt-2 p-2.5 bg-rose-50 rounded-xl border border-rose-200 text-xs text-rose-900">
                                    Catatan Instruktur: "{{ $log->catatan_instruktur }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400 text-xs">Belum ada catatan logsheet produksi dari siswa pelaksana.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH ANGGOTA TIM SISWA -->
    @if($showAddMemberModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900">Tugaskan Siswa ke Tim Proyek</h3>
                    <button type="button" wire:click="$set('showAddMemberModal', false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="tambahAnggotaTim" class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Pilih Siswa</label>
                        <select wire:model="siswa_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white mt-1">
                            @foreach($siswaPilihan as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->nama }} ({{ $sp->rombel->nama_rombel ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Peran dalam Tim</label>
                        <select wire:model="peran_dalam_tim" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white mt-1">
                            <option value="project_manager">Project Manager / Koordinator Siswa</option>
                            <option value="teknisi_utama">Teknisi / Programmer Utama</option>
                            <option value="quality_tester">Quality Tester / QC Siswa</option>
                            <option value="operator">Operator Produksi / Asisten</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase">Job Sheet / Tugas Spesifik</label>
                        <input type="text" wire:model="job_desc" placeholder="Contoh: Modul Autentikasi & Database" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition">
                            ✓ Tugaskan Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>