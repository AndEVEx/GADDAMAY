<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Siswa Belum Mencapai KKTP - {{ $rombel->nama_kelas }}</title>
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
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        table.data-table th { background-color: #f0f4f8; border: 1px solid #777; padding: 6px 4px; text-align: center; font-weight: bold; }
        table.data-table td { border: 1px solid #999; padding: 5px 4px; vertical-align: top; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8pt; font-weight: bold; border-radius: 3px; }
        .badge-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .badge-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
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
        <h4>REKAPITULASI SISWA BELUM MENCAPAI KKTP (PROGRAM REMEDIAL)</h4>
        <p>Kelas: {{ $rombel->nama_kelas }} &bull; Periode: {{ $namaBulan }}</p>
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
            <td class="fw-bold">Status Siswa</td>
            <td>: {{ $siswaBelumTuntasOnly->count() }} Siswa Belum Tuntas / {{ $rekapSiswaKktp->count() }} Total Siswa</td>
        </tr>
    </table>

    @if($siswaBelumTuntasOnly->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 75px;">NIS</th>
                    <th style="width: 150px;">Nama Siswa</th>
                    <th>Tujuan Pembelajaran (TP) yang Belum Tuntas</th>
                    <th style="width: 80px;">Ketuntasan</th>
                    <th style="width: 95px;">Rencana Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaBelumTuntasOnly as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $s->siswa->nis ?? '—' }}</td>
                    <td class="fw-bold">{{ $s->siswa->nama }}</td>
                    <td>
                        @foreach($s->uncompleted_tps as $tp)
                            <div style="margin-bottom: 4px;">
                                <strong style="color: #c62828;">[{{ $tp->kode_tp }}]</strong> {{ $tp->deskripsi_tp }}
                            </div>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <span class="badge badge-danger">{{ $s->completed_count }}/{{ $s->total_tp }} TP ({{ $s->percentage }}%)</span>
                    </td>
                    <td class="text-center" style="font-size: 8pt; color: #444;">
                        Tugas Remedial & Pendampingan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 20px; text-align: center; background-color: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 4px; color: #2e7d32;">
            <strong>✓ Luar Biasa</strong>: Seluruh siswa di kelas {{ $rombel->nama_kelas }} telah 100% mencapai Kriteria Ketercapaian Tujuan Pembelajaran (KKTP).
        </div>
    @endif

    {{-- LEMBAR PENGESAHAN --}}
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Waka Kurikulum</strong>
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
