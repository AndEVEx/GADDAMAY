<div>
    <div class="page-header">
        <h1><i class="bi bi-list-check me-2"></i>Kriteria Ketercapaian TP</h1>
        <p class="subtitle mb-0">
            {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
        </p>
    </div>

    <div class="card mb-4 animate-fade-in-up">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0 text-center align-middle" style="white-space: nowrap;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start" style="min-width: 150px; position: sticky; left: 0; z-index: 2; background-color: #f8f9fa;">Nama Siswa</th>
                            @foreach($tps as $tp)
                                <th style="min-width: 120px;" title="{{ $tp->deskripsi_tp ?? '' }}">{{ $tp->kode_tp }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaHadir as $siswa)
                            <tr>
                                <td class="text-start" style="position: sticky; left: 0; z-index: 1; background-color: #fff;">{{ $siswa->nama }}</td>
                                @foreach($tps as $tp)
                                    <td>
                                        @php
                                            $status = $kktpData[$siswa->id][$tp->id] ?? 'belum_tercapai';
                                        @endphp
                                        @if($status === 'tercapai')
                                            <button wire:click="toggleKktp('{{ $siswa->id }}', '{{ $tp->id }}')" 
                                                    class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center" 
                                                    style="min-height: 48px;">
                                                <i class="bi bi-check-circle-fill me-1"></i> Tercapai
                                            </button>
                                        @else
                                            <button wire:click="toggleKktp('{{ $siswa->id }}', '{{ $tp->id }}')" 
                                                    class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center" 
                                                    style="min-height: 48px;">
                                                <i class="bi bi-x-circle me-1"></i> Belum
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($tps) + 1 }}" class="text-muted py-3">Tidak ada siswa yang hadir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Refleksi Pembelajaran --}}
    <div class="card mt-3 mb-4 animate-fade-in-up">
        <div class="card-header bg-info bg-opacity-10">
            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2 text-info"></i>Refleksi Pembelajaran Guru <span class="text-danger">*</span></h6>
        </div>
        <div class="card-body">
            <textarea wire:model="refleksi" class="form-control @error('refleksi') is-invalid @enderror" rows="4" 
                      placeholder="Tuliskan refleksi pembelajaran hari ini (proses KBM, respon siswa, kendala, dan rencana tindak lanjut)..."
                      style="min-height: 120px;"></textarea>
            @error('refleksi')
                <div class="invalid-feedback fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
            <div class="form-text mt-2">Refleksi pembelajaran ini akan terekam secara otomatis di detail aktifitas Jurnal Guru.</div>
        </div>
    </div>

    <div class="d-flex flex-column gap-2 mb-4 animate-fade-in-up">
        <button wire:click="simpanDanSelesai" class="btn btn-primary btn-lg w-100 shadow-sm py-3" style="border-radius: 12px;" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="bi bi-save me-2 fs-5"></i>Simpan & Selesai Pembelajaran</span>
            <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
        </button>

        <div class="d-flex gap-2">
            <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary flex-fill" wire:navigate style="min-height: 44px; border-radius: 10px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <button type="button" wire:click="batalkanAgenda" 
                    wire:confirm="Yakin ingin membatalkan dan mereset agenda jam ini? (Gunakan ini jika Anda salah memilih jam pelajaran)"
                    class="btn btn-outline-danger flex-fill" style="min-height: 44px; border-radius: 10px;">
                <i class="bi bi-trash me-1"></i> Batalkan Sesi Ini (Salah Jam)
            </button>
        </div>
    </div>
</div>
