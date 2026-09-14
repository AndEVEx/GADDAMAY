<?php

namespace App\Services;

use App\Helpers\LogoHelper;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfReportService
{
    /**
     * Generate and stream download a standardized PDF report with official SMKN 2 Indramayu Kop Surat.
     */
    public static function download(
        string $title,
        string $subtitle,
        array $headers,
        array $rows,
        string $filename,
        string $paper = 'A4',
        string $orientation = 'portrait',
        array $metaInfo = [],
        ?string $signeeName = null,
        ?string $signeeRole = null,
        ?string $leftSigneeName = null,
        ?string $leftSigneeRole = null
    ): StreamedResponse {
        $data = [
            'title' => $title,
            'subtitle' => $subtitle,
            'headers' => $headers,
            'rows' => $rows,
            'metaInfo' => $metaInfo,
            'logoBase64' => LogoHelper::getBase64(),
            'tanggalCetak' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
            'dicetakOleh' => auth()->user()?->name ?? 'Administrator',
            'signeeName' => $signeeName ?? auth()->user()?->name ?? 'Administrator',
            'signeeRole' => $signeeRole ?? 'Petugas / Administrator',
            'leftSigneeName' => $leftSigneeName,
            'leftSigneeRole' => $leftSigneeRole,
        ];

        $html = view('pdf.generic-report', $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $safeFilename = str_ends_with(strtolower($filename), '.pdf') ? $filename : $filename . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $safeFilename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
