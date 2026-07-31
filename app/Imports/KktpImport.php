<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KktpImport
{
    public array $metadata = [];
    public array $tpData = [];
    public ?string $mapelId = null;
    public string $error = '';

    public function parse(string $filePath): bool
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Parse metadata
            $this->metadata = [
                'mapel' => trim($sheet->getCell('D4')->getValue() ?? ''),
                'tingkat' => trim($sheet->getCell('D5')->getValue() ?? ''),
                'kelas' => trim($sheet->getCell('D6')->getValue() ?? ''),
                'semester' => trim($sheet->getCell('D7')->getValue() ?? ''),
                'tahun' => trim($sheet->getCell('D8')->getValue() ?? ''),
                'guru' => trim($sheet->getCell('D9')->getValue() ?? ''),
            ];

            // Find mapel
            if (!empty($this->metadata['mapel'])) {
                $mapel = MataPelajaran::where('nama_mapel', 'like', '%' . $this->metadata['mapel'] . '%')->first();
                $this->mapelId = $mapel?->id;
            }

            // Parse TP data from row 12+
            $highestRow = $sheet->getHighestRow();
            $this->tpData = [];
            for ($row = 12; $row <= $highestRow; $row++) {
                $no = $sheet->getCell('B' . $row)->getValue();
                $pertemuan = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $cp = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                $tp = trim($sheet->getCell('E' . $row)->getValue() ?? '');

                if (empty($pertemuan) && empty($cp) && empty($tp)) continue;
                if (empty($cp) && empty($tp)) continue;

                $this->tpData[] = [
                    'no' => $no,
                    'pertemuan' => $pertemuan,
                    'cp' => $cp,
                    'tp' => $tp,
                ];
            }

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function import(string $mapelId): int
    {
        $count = 0;
        foreach ($this->tpData as $index => $data) {
            if (empty($data['tp'])) continue;

            $kodeTP = 'TP-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $deskripsi = $data['tp'];
            if (!empty($data['cp'])) {
                $deskripsi = 'CP: ' . $data['cp'] . ' | TP: ' . $data['tp'];
            }

            TujuanPembelajaran::updateOrCreate(
                ['mapel_id' => $mapelId, 'kode_tp' => $kodeTP],
                [
                    'deskripsi_tp' => $deskripsi,
                    'order_sequence' => $index + 1,
                ]
            );
            $count++;
        }
        return $count;
    }
}
