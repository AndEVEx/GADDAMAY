<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Teaching Factory (TEFA) SMKN 2 Indramayu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Order & Unit Produksi</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola Surat Perintah Kerja (SPK), penugasan tim kerja siswa, dan inspeksi Quality Control.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tefa.siswa.log') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Logsheet Siswa</span>
            </a>

            <button type="button" wire:click="$set('showCreateModal', true)" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Buat Order / SPK Baru</span>
            </button>
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

    <!-- Kartu Statistik Singkat TEFA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Total Pesanan Masuk</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $orderList->count() }}</div>
            <div class="text-xs text-slate-500 mt-1">SPK Resmi Konsumen</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Sedang Dalam Produksi</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $orderList->where('status', 'dalam_produksi')->count() }}</div>
            <div class="text-xs text-indigo-500 mt-1">Dikerjakan Tim Siswa</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Total Jam Kerja Tim</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $orderList->sum(fn($o) => $o->total_jam_produksi) }} Jam</div>
            <div class="text-xs text-emerald-600 mt-1 font-semibold">Jam Terbang Produksi Riil</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase">Omzet / Nilai Jasa TEFA</div>
            <div class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($orderList->sum('biaya_proyek'), 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Pemberdayaan Unit Vokasi</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button type="button" wire:click="$set('filterStatus', 'semua')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'semua' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $orderList->count() }})
            </button>
            <button type="button" wire:click="$set('filterStatus', 'dalam_produksi')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'dalam_produksi' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Dalam Produksi
            </button>
            <button type="button" wire:click="$set('filterStatus', 'selesai_diserahkan')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'selesai_diserahkan' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Selesai / Lolos QC
            </button>
        </div>

        <div class="w-full sm:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul proyek, pemesan..." class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
    </div>

    <!-- Tabel Daftar Order TEFA -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                        <th class="pb-3">Kode / Proyek</th>
                        <th class="pb-3">Pemesan & Kontak WA</th>
                        <th class="pb-3 text-center">Tim Siswa</th>
                        <th class="pb-3 text-center">Jam Kerja</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orderList as $order)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-mono text-[10px] font-bold">
                                    {{ $order->kode_order }}
                                </span>
                                <div class="font-bold text-slate-900 text-sm mt-1">{{ $order->judul_proyek }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Deadline: {{ \Carbon\Carbon::parse($order->target_selesai)->format('d/m/Y') }} &bull; Rp {{ number_format($order->biaya_proyek, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4">
                                <div class="font-semibold text-slate-800">{{ $order->nama_pemesan }}</div>
                                <div class="text-slate-500 text-[11px]">{{ $order->instansi_pemesan ?: 'Perorangan' }}</div>
                                <div class="text-emerald-700 font-mono text-[11px] font-bold">{{ $order->nomor_wa_pemesan }}</div>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full font-bold text-[11px]">
                                    {{ $order->timKerja->count() }} siswa
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                <div class="font-black text-slate-800 text-sm">{{ $order->total_jam_produksi }} Jam</div>
                                <div class="text-[10px] text-slate-400">{{ $order->logProduksi->count() }} logsheet</div>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] {{ $order->status === 'selesai_diserahkan' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'dalam_produksi' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol Detail / QC -->
                                    <a href="{{ route('tefa.admin.detail', ['id' => $order->id]) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-black text-white rounded-lg font-bold text-[11px] transition">
                                        Detail & QC
                                    </a>

                                    <!-- Tombol WhatsApp Update Pemesan -->
                                    <button type="button" wire:click="openWaModal('{{ $order->id }}', 'progres')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg font-bold text-[11px] transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span>Update WA</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">Belum ada order pesanan TEFA yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: FORM BUAT ORDER TEFA BARU -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Buat Surat Perintah Kerja (SPK) TEFA Baru</h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="simpanOrder" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Judul Proyek / Nama Pesanan</label>
                        <input type="text" wire:model="judul_proyek" placeholder="Contoh: Pembuatan Landing Page & Sistem Pendaftaran Siswa Baru" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        @error('judul_proyek') <span class="text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Nama Pemesan / Konsumen</label>
                            <input type="text" wire:model="nama_pemesan" placeholder="Nama lengkap..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp Pemesan</label>
                            <input type="text" wire:model="nomor_wa_pemesan" placeholder="08..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Instansi / Perusahaan Pemesan</label>
                            <input type="text" wire:model="instansi_pemesan" placeholder="Nama instansi/sekolah..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Jurusan Unit Produksi</label>
                            <select wire:model="kategori_kejuruan" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                                <option value="PPLG">PPLG (Software & Web House)</option>
                                <option value="TJKT">TJKT (Jaringan & Infrastruktur)</option>
                                <option value="Otomotif">Teknik Otomotif / Servis</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Biaya / Nilai Proyek (Rp)</label>
                            <input type="number" wire:model="biaya_proyek" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Target Deadline Selesai</label>
                            <input type="date" wire:model="target_selesai" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Deskripsi Spesifikasi Teknis Pesanan</label>
                        <textarea rows="3" wire:model="deskripsi_proyek" placeholder="Rincian fitur, teknologi yang digunakan, atau kebutuhan bahan..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/20 transition">
                            ✓ Terbitkan SPK & Masuk Antrean Produksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: KIRIM KABAR WHATSAPP KE PEMESAN -->
    @if($showWaModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Update WhatsApp ke Konsumen</h3>
                    <button type="button" wire:click="closeWaModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <!-- Pilihan Tahap Notifikasi -->
                <div class="flex p-1 bg-slate-100 rounded-xl mb-4">
                    <button type="button" wire:click="changeWaStage('konfirmasi')" class="flex-1 py-1.5 rounded-lg text-xs font-bold {{ $waStage === 'konfirmasi' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                        1. Konfirmasi SPK
                    </button>
                    <button type="button" wire:click="changeWaStage('progres')" class="flex-1 py-1.5 rounded-lg text-xs font-bold {{ $waStage === 'progres' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                        2. Update Progres
                    </button>
                    <button type="button" wire:click="changeWaStage('selesai')" class="flex-1 py-1.5 rounded-lg text-xs font-bold {{ $waStage === 'selesai' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
                        3. Siap Diambil
                    </button>
                </div>

                <div class="mb-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900">
                    <div>Penerima: <strong>{{ $targetName }}</strong></div>
                    <div>WhatsApp: <strong>{{ $targetPhone }}</strong></div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pratinjau Pesan Resmi:</label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 whitespace-pre-wrap max-h-48 overflow-y-auto">{{ $waMessage }}</div>
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