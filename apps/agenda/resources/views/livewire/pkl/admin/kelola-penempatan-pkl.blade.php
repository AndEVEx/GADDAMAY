<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Pokja PKL SMKN 2 Indramayu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Mitra DUDI & Penempatan PKL</h1>
            <p class="text-sm text-slate-500 mt-1">Atur titik GPS DUDI, toleransi radius, dan penempatan siswa bimbingan.</p>
        </div>

        <div>
            <a href="{{ route('pkl.guru.monitoring') }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>Monitoring Jurnal</span>
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

    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Form Pendaftaran DUDI Baru -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Daftar Mitra DUDI Baru</span>
            </h2>

            <form wire:submit.prevent="simpanDudi" class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-slate-700 uppercase">Nama Perusahaan / DUDI</label>
                    <input type="text" wire:model="nama_instansi" placeholder="PT / CV / Studio..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                </div>

                <div>
                    <label class="font-bold text-slate-700 uppercase">Alamat Lengkap</label>
                    <textarea rows="2" wire:model="alamat" placeholder="Jl. Raya..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Nama Pembimbing</label>
                        <input type="text" wire:model="pembimbing_nama" placeholder="Bpk / Ibu..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 uppercase">No. WhatsApp DUDI</label>
                        <input type="text" wire:model="pembimbing_kontak" placeholder="08..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Latitude</label>
                        <input type="text" wire:model="latitude" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 mt-1 font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Longitude</label>
                        <input type="text" wire:model="longitude" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 mt-1 font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 uppercase">Radius (m)</label>
                        <input type="number" wire:model="radius_meter" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 mt-1 font-mono">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold transition">
                        + Tambah Mitra DUDI
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Penempatan Siswa (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Formulir Penempatan Siswa ke DUDI</span>
            </h2>

            <form wire:submit.prevent="simpanPenempatan" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Pilih Siswa</label>
                        <select wire:model="siswa_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            @foreach($siswaList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Pilih Mitra DUDI</label>
                        <select wire:model="dudi_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            @foreach($dudiList as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_instansi }} ({{ $d->kota }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Guru Pembimbing Sekolah</label>
                        <select wire:model="guru_pembimbing_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Nomor WhatsApp Pembimbing DUDI</label>
                        <input type="text" wire:model="nomor_wa_dudi" placeholder="08..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Nama Pembimbing DUDI</label>
                        <input type="text" wire:model="nama_pembimbing_dudi" placeholder="Bpk / Ibu..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Tanggal Mulai PKL</label>
                        <input type="date" wire:model="tanggal_mulai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 uppercase mb-1 block">Tanggal Selesai PKL</label>
                        <input type="date" wire:model="tanggal_selesai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                        ✓ Simpan Penempatan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Penempatan Aktif -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Penempatan Siswa PKL Aktif</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                        <th class="pb-3">Siswa</th>
                        <th class="pb-3">DUDI</th>
                        <th class="pb-3">Pembimbing Sekolah</th>
                        <th class="pb-3">Periode</th>
                        <th class="pb-3">Magic Link Token DUDI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penempatanList as $pen)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 font-bold text-slate-900">{{ $pen->siswa->nama ?? '-' }}</td>
                            <td class="py-3 text-slate-800">{{ $pen->dudi->nama_instansi ?? '-' }}</td>
                            <td class="py-3 text-slate-700">{{ $pen->guruPembimbing->name ?? '-' }}</td>
                            <td class="py-3 text-slate-500">
                                {{ \Carbon\Carbon::parse($pen->tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($pen->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="py-3 font-mono text-[11px] text-sky-700">
                                <a href="{{ $pen->magic_link_url }}" target="_blank" class="hover:underline flex items-center gap-1">
                                    <span>/pkl/review-dudi/{{ substr($pen->token_magic_link_dudi, 0, 10) }}...</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada data penempatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>