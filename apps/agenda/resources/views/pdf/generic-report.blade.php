<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Resmi' }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.3;
            color: #222;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 10pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 2px 0;
            font-size: 13.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d47a1;
        }
        .kop-text p {
            margin: 0;
            font-size: 8pt;
            color: #444;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #444;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #777;
            padding: 5px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 45%;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI SMKN 2 INDRAMAYU --}}
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h4>Pemerintah Daerah Provinsi Jawa Barat</h4>
                <h3>Dinas Pendidikan</h3>
                <h3>Cabang Dinas Pendidikan Wilayah IX</h3>
                <h2>SMK Negeri 2 Indramayu</h2>
                <p>Jl. Umar Wirahadikusumah No. 1, Pabean Udik, Kec. Indramayu, Kab. Indramayu, Jawa Barat 45219</p>
                <p>Website: smkn2indramayu.sch.id | Aplikasi: AgenDAmay</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div class="doc-title">
        <h4>{{ $title }}</h4>
        @if(!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
    </div>

    {{-- METADATA INFO --}}
    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 35%;">: {{ $tanggalCetak ?? \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</td>
            <td style="width: 15%;"><strong>Dicetak Oleh</strong></td>
            <td style="width: 35%;">: {{ $dicetakOleh ?? auth()->user()?->name ?? 'Administrator' }}</td>
        </tr>
        @if(!empty($metaInfo))
            @foreach($metaInfo as $label => $val)
                <tr>
                    <td><strong>{{ $label }}</strong></td>
                    <td colspan="3">: {{ $val }}</td>
                </tr>
            @endforeach
        @endif
    </table>

    {{-- DATA TABLE --}}
    <table class="data-table">
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th style="{{ isset($h['width']) ? 'width:'.$h['width'].';' : '' }} {{ isset($h['align']) ? 'text-align:'.$h['align'].';' : '' }}">
                        {{ is_array($h) ? ($h['name'] ?? '') : $h }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $colIdx => $cell)
                        @php
                            $headerAlign = isset($headers[$colIdx]['align']) ? $headers[$colIdx]['align'] : 'left';
                            $cellClass = is_numeric($cell) && strlen((string)$cell) <= 4 ? 'text-center' : ($headerAlign === 'center' ? 'text-center' : 'text-left');
                        @endphp
                        <td class="{{ $cellClass }}">
                            {!! $cell !!}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center" style="padding: 15px; color: #888;">
                        Tidak ada data yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN RESMI --}}
    <table class="signature-table">
        <tr>
            <td>
                @if(!empty($leftSigneeRole))
                    Mengetahui,<br>
                    <strong>{{ $leftSigneeRole }}</strong>
                    <br><br><br><br><br>
                    <strong><u>{{ $leftSigneeName ?? '....................................' }}</u></strong><br>
                    @if(!empty($leftSigneeNip)) NIP. {{ $leftSigneeNip }} @endif
                @endif
            </td>
            <td style="width: 10%;"></td>
            <td>
                Indramayu, {{ $tanggalCetak ?? \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                <strong>{{ $signeeRole ?? 'Petugas / Administrator' }}</strong>
                <br><br><br><br><br>
                <strong><u>{{ $signeeName ?? auth()->user()?->name ?? '....................................' }}</u></strong><br>
                @if(!empty($signeeNip)) NIP. {{ $signeeNip }} @endif
            </td>
        </tr>
    </table>

</body>
</html>
