<div class="max-w-5xl mx-auto px-4 py-8">
    @if(!$penempatan)
        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-8 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h2 class="text-xl font-black text-rose-900">Tautan Review DUDI Tidak Ditemukan</h2>
            <p class="text-sm text-rose-700 mt-2">Tautan akses yang Anda gunakan tidak valid atau telah diperbarui. Silakan hubungi Guru Pembimbing SMKN 2 Indramayu untuk mendapatkan tautan verifikasi terbaru.</p>
        </div>
    @else
        <!-- Header Banner Khusus Pembimbing DUDI -->
        <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                <div>
                    <span class="px-3 py-1 bg-sky-400/20 text-sky-300 border border-sky-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                        Portal Verifikasi DUDI Mitra PKL
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">
                        {{ $penempatan->dudi->nama_instansi ?? 'Mitra Industri' }}
                    </h1>
                    <p class="text-slate-300 text-sm mt-1">
                        Verifikasi digital jurnal & presensi PKL atas siswa: <strong class="text-white font-bold">{{ $penempatan->siswa->nama ?? 'Siswa' }}</strong>
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-xs">
                    <div class="text-slate-400 uppercase font-semibold">Guru Pembimbing Sekolah:</div>
                    <div class="text-base font-bold text-sky-300 mt-0.5">{{ $penempatan->guruPembimbing->name ?? '-' }}</div>
                    <div class="text-slate-300 mt-1">SMKN 2 Indramayu &bull; TA {{ $penempatan->tahun_ajaran }}</div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tab Navigasi DUDI -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-4 mb-6 overflow-x-auto">
            <button type="button" wire:click="setTab('jurnal')" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2 {{ $activeTab === 'jurnal' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Jurnal Kegiatan ({{ $jurnalList->count() }})</span>
            </button>

            <button type="button" wire:click="setTab('presensi')" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2 {{ $activeTab === 'presensi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Presensi Kehadiran ({{ $summary['persentase_kehadiran'] ?? 0 }}%)</span>
            </button>

            <button type="button" wire:click="setTab('nilai')" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2 {{ $activeTab === 'nilai' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                <span>Lembar Asesmen Akhir DUDI</span>
            </button>
        </div>

        <!-- TAB 1: JURNAL KEGIATAN & PARAF -->
        @if($activeTab === 'jurnal')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Jurnal Aktivitas Harian Siswa</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Silakan periksa ringkasan pekerjaan siswa dan bubuhkan paraf digital Anda.</p>
                    </div>

                    @if($jurnalList->where('paraf_dudi_status', 'pending')->count() > 0)
                        <button type="button" wire:click="setujuiSemuaJurnal" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Paraf Semua Jurnal Sekaligus ({{ $jurnalList->where('paraf_dudi_status', 'pending')->count() }})</span>
                        </button>
                    @endif
                </div>

                <div class="space-y-4">
                    @forelse($jurnalList as $jurnal)
                        <div class="p-5 rounded-2xl border {{ $jurnal->paraf_dudi_status === 'disetujui' ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/60' }} transition">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-2">
                                <div>
                                    <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold uppercase">
                                        {{ $jurnal->elemen_cp }}
                                    </span>
                                    <h3 class="font-bold text-slate-900 mt-1.5 text-base">{{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}</h3>
                                    <div class="text-xs text-slate-500">Pukul {{ substr($jurnal->jam_mulai, 0, 5) }} - {{ substr($jurnal->jam_selesai, 0, 5) }} WIB</div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($jurnal->paraf_dudi_status === 'disetujui')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Sudah Diparaf DUDI</span>
                                        </span>
                                    @else
                                        <button type="button" wire:click="setujuiJurnal('{{ $jurnal->id }}')" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Beri Paraf / Setujui</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <p class="text-sm text-slate-700 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-100 mt-2">{{ $jurnal->ringkasan_pekerjaan }}</p>

                            @if($jurnal->alat_dan_bahan)
                                <div class="mt-2 text-xs text-slate-500">
                                    <strong>Alat & Software:</strong> {{ $jurnal->alat_dan_bahan }}
                                </div>
                            @endif

                            @if($jurnal->catatan_dudi)
                                <div class="mt-3 p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-700">
                                    <strong>Catatan Anda:</strong> "{{ $jurnal->catatan_dudi }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400 text-sm">Belum ada jurnal pekerjaan yang diisi oleh siswa.</div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- TAB 2: PRESENSI & KEHADIRAN -->
        @if($activeTab === 'presensi')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Rekap Presensi & Kehadiran Siswa</h2>

                <!-- Ringkasan Statistik Kehadiran -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                        <div class="text-2xl font-black text-emerald-800">{{ $summary['hadir'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-emerald-600 mt-0.5 uppercase">Hadir</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100 text-center">
                        <div class="text-2xl font-black text-amber-800">{{ $summary['terlambat'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-amber-600 mt-0.5 uppercase">Terlambat</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100 text-center">
                        <div class="text-2xl font-black text-sky-800">{{ $summary['izin'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-sky-600 mt-0.5 uppercase">Izin / Sakit</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-center">
                        <div class="text-2xl font-black text-indigo-800">{{ $summary['persentase_kehadiran'] ?? 0 }}%</div>
                        <div class="text-xs font-bold text-indigo-600 mt-0.5 uppercase">Persentase Hadir</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase font-semibold">
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Jam Datang</th>
                                <th class="pb-3">Jam Pulang</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Posisi GPS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($presensiList as $p)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 font-bold text-slate-800">{{ \Carbon\Carbon::parse($p->tanggal)->isoFormat('dddd, D MMM Y') }}</td>
                                    <td class="py-3.5 font-mono text-emerald-700 font-semibold">{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-' }}</td>
                                    <td class="py-3.5 font-mono text-indigo-700 font-semibold">{{ $p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '-' }}</td>
                                    <td class="py-3.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $p->status_kehadiran === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $p->status_kehadiran }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-xs text-slate-600">
                                        {{ $p->jarak_masuk_meter !== null ? $p->jarak_masuk_meter . ' meter' : '-' }}
                                        @if($p->is_in_radius)
                                            <span class="text-emerald-600 font-bold ml-1">✓ Dalam Radius</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat presensi siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 3: LEMBAR ASESMEN AKHIR DUDI -->
        @if($activeTab === 'nilai')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="mb-6">
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-extrabold uppercase">
                        Bobot Penilaian DUDI: 50% (5 dari 10 Bobot Nilai Akhir)
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 mt-2">Lembar Penilaian Siswa oleh Pembimbing Industri</h2>
                    <p class="text-xs text-slate-500 mt-1">Skala nilai rentang 0 s.d. 100 berdasarkan performa riil siswa di tempat kerja.</p>
                </div>

                <form wire:submit.prevent="simpanPenilaianDudi" class="space-y-8">
                    <!-- Bagian A: Soft Skills Budaya Kerja -->
                    <div>
                        <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4 pb-2 border-b border-indigo-50">
                            A. Aspek Soft Skills & Budaya Kerja (5 Indikator)
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">1. Integritas & Kejujuran (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_soft_integritas" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">2. Etos Kerja & Tanggung Jawab (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_soft_etos_kerja" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">3. Kerjasama Tim & Komunikasi (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_soft_gotong_royong" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">4. Kemandirian & Inisiatif (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_soft_kemandirian" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">5. Kedisiplinan Waktu & Aturan Kerja (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_soft_disiplin" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian B: Hard Skills Teknis PPLG -->
                    <div>
                        <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4 pb-2 border-b border-indigo-50">
                            B. Aspek Hard Skills / Teknis Keahlian PPLG (4 Tujuan Pembelajaran)
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">TP 1: Pemahaman Alur Bisnis & Rekayasa Sistem (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_hard_tp1" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">TP 2: Penerapan Standar K3LH & Prosedur Industri (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_hard_tp2" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">TP 3: Desain, Pemrograman & Pemecahan Masalah (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_hard_tp3" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">TP 4: Pengujian, Pengemasan & Dokumentasi Hasil Kerja (0-100)</label>
                                <input type="number" min="0" max="100" wire:model="nilai_hard_tp4" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Catatan / Umpan Balik Khusus untuk Siswa & Sekolah</label>
                        <textarea rows="3" wire:model="catatanNilaiDudi" placeholder="Tuliskan apresiasi, masukan peningkatan kompetensi, atau rekomendasi kerja untuk siswa..." class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" class="px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-black text-sm shadow-xl shadow-emerald-600/20 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Nilai Akhir DUDI</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endif
</div>