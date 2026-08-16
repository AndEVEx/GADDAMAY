<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Ketidakhadiran Siswa - {{ $rombel->nama_kelas }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #222; }
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px double #000; margin-bottom: 15px; padding-bottom: 8px; }
        .header-table td { vertical-align: middle; }
        .logo-img { width: 65px; height: 65px; object-fit: contain; }
        .kop-text { text-align: center; }
        .kop-text h4 { margin: 0; font-size: 10pt; font-weight: normal; text-transform: uppercase; }
        .kop-text h3 { margin: 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .kop-text h2 { margin: 2px 0; font-size: 14pt; font-weight: bold; color: #0d47a1; text-transform: uppercase; }
        .kop-text p { margin: 0; font-size: 8pt; color: #444; }
        .doc-title { text-align: center; margin: 12px 0 10px 0; }
        .doc-title h4 { margin: 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .doc-title p { margin: 2px 0 0 0; font-size: 9pt; font-weight: bold; color: #333; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        .info-table td { padding: 2px 4px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        table.data-table th { background-color: #f0f4f8; border: 1px solid #777; padding: 6px 4px; text-align: center; font-weight: bold; }
        table.data-table td { border: 1px solid #999; padding: 5px 4px; vertical-align: middle; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8pt; font-weight: bold; border-radius: 3px; }
        .badge-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .badge-warning { background-color: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
        .badge-info { background-color: #e1f5fe; color: #0277bd; border: 1px solid #81d4fa; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 9pt; page-break-inside: avoid; }
        .signature-table td { vertical-align: top; text-align: center; width: 50%; }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h4>Pemerintah Daerah Provinsi Jawa Barat</h4>
                <h3>Dinas Pendidikan &bull; Cabang Dinas Wilayah IX</h3>
                <h2>SMK Negeri 2 Indramayu</h2>
                <p>Jl. Umar Rasadi, Singaraja, Kec. Indramayu, Kab. Indramayu, Jawa Barat 45218</p>
                <p>Website: smkn2indramayu.sch.id &bull; Email: info@smkn2indramayu.sch.id</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h4>REKAPITULASI KETIDAKHADIRAN SISWA SAAT PEMBELAJARAN (KBM)</h4>
        <p>Periode: {{ $namaBulan }}</p>
    </div>

    {{-- IDENTITAS --}}
    <table class="info-table">
        <tr>
            <td style="width: 18%;" class="fw-bold">Nama Guru</td>
            <td style="width: 32%;">: {{ $guru->name }}</td>
            <td style="width: 18%;" class="fw-bold">Kelas / Rombel</td>
            <td style="width: 32%;">: {{ $rombel->nama_kelas }} (Tingkat {{ $rombel->tingkat }})</td>
        </tr>
        <tr>
            <td class="fw-bold">Mata Pelajaran</td>
            <td>: {{ $agendas->first()?->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? '-' }}</td>
            <td class="fw-bold">Total Absensi</td>
            <td>: Sakit: {{ $absenStats['sakit'] }}, Izin: {{ $absenStats['izin'] }}, Alpha: {{ $absenStats['alpha'] }} (Total: {{ $absenStats['total'] }})</td>
        </tr>
    </table>

    @if($absensiRecords->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 85px;">Hari, Tanggal</th>
                    <th style="width: 50px;">Ptm Ke-</th>
                    <th style="width: 80px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 75px;">Status</th>
                    <th>Materi KBM Saat Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absensiRecords as $idx => $absen)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $absen->agendaHarian?->tanggal?->translatedFormat('d/m/Y') }}</div>
                        <small style="color: #666;">{{ $absen->agendaHarian?->tanggal?->translatedFormat('l') }}</small>
                    </td>
                    <td class="text-center">{{ $absen->agendaHarian?->pertemuan_ke ?? '-' }}</td>
                    <td class="text-center">{{ $absen->siswa?->nis ?? '—' }}</td>
                    <td class="fw-bold">{{ $absen->siswa?->nama }}</td>
                    <td class="text-center">
                        @if($absen->status === 'sakit')
                            <span class="badge badge-warning">Sakit</span>
                        @elseif($absen->status === 'izin')
                            <span class="badge badge-info">Izin</span>
                        @else
                            <span class="badge badge-danger">Alpha</span>
                        @endif
                    </td>
                    <td style="font-size: 8.5pt;">{{ $absen->agendaHarian?->materi_diajarkan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 20px; text-align: center; background-color: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 4px; color: #2e7d32;">
            <strong>✓ Nihil</strong>: Seluruh siswa hadir 100% pada semua sesi KBM di kelas {{ $rombel->nama_kelas }} selama bulan {{ $namaBulan }}.
        </div>
    @endif

    {{-- LEMBAR PENGESAHAN --}}
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Waka Kurikulum / Kesiswaan</strong>
                <br><br><br><br><br>
                <strong>___________________________________</strong><br>
                NIP. ....................................................
            </td>
            <td>
                Indramayu, {{ $tanggalCetak }}<br>
                <strong>Guru Mata Pelajaran</strong>
                <br><br><br><br><br>
                <strong>{{ $guru->name }}</strong><br>
                NIP/ID. {{ $guru->email }}
            </td>
        </tr>
    </table>

</body>
</html>
