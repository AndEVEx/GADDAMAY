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
                            <th class="text-start" style="min-width: 150px;">Nama Siswa</th>
                            @foreach($tps as $tp)
                                <th style="min-width: 120px;" title="{{ $tp->deskripsi_tp ?? '' }}">{{ $tp->kode_tp }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaHadir as $siswa)
                            <tr>
                                <td class="text-start">{{ $siswa->nama }}</td>
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
            <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>Refleksi Pembelajaran</h6>
        </div>
        <div class="card-body">
            <textarea wire:model="refleksi" class="form-control" rows="4" 
                      placeholder="Tuliskan refleksi pembelajaran hari ini..."
                      style="min-height: 120px;"></textarea>
            <div class="form-text">Refleksi tentang proses pembelajaran, kendala, dan rencana tindak lanjut.</div>
        </div>
    </div>

    <button wire:click="simpanDanSelesai" class="btn btn-primary btn-lg w-100 mb-4 animate-fade-in-up" style="min-height: 48px; animation-delay: 0.1s;" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="bi bi-save me-2"></i>Simpan & Selesai</span>
        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
    </button>
</div>
