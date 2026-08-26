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
    public ?string $detectedMapelName = null;
    public ?string $detectedKodeMapel = null;
    public string $error = '';

    private static array $aliasMap = [
        'KKA' => 'Koding dan Kecerdasan Artifisial',
        'MTK' => 'Matematika',
        'INF' => 'Informatika',
        'INFORMATIKA' => 'Informatika',
        'PJOK' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
        'PAI' => 'Pendidikan Agama Islam dan Budi Pekerti',
        'PAI-PBP' => 'Pendidikan Agama Islam dan Budi Pekerti',
        'PAI & BP' => 'Pendidikan Agama Islam dan Budi Pekerti',
        'PAI&BP' => 'Pendidikan Agama Islam dan Budi Pekerti',
        'IPAS' => 'Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)',
        'SEJARAH' => 'Sejarah',
        'B. IND' => 'Bahasa Indonesia',
        'B. ING' => 'Bahasa Inggris',
        'ENGLISH' => 'Bahasa Inggris',
        'B. JEPANG' => 'Bahasa Jepang',
        'B. ARAB' => 'Bahasa Arab',
        'DDK' => 'Dasar-Dasar Kejuruan',
        'KKNKPI' => 'Konsentrasi Keahlian NKPI',
        'KKAPHPI' => 'Konsentrasi Keahlian APHP',
        'KKK' => 'Konsentrasi Keahlian Kuliner',
    ];

    public static function cleanValue(?string $val): string
    {
        if ($val === null) return '';
        $val = trim($val);
        // Remove enclosing brackets, braces, parentheses, quotes
        $val = trim($val, "[](){}\"'` \t\n\r\0\x0B");
        return trim($val);
    }

    public function parse(string $filePath): bool
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // 1. Parse and clean metadata
            $rawMapel = self::cleanValue($sheet->getCell('D4')->getValue());
            $rawTingkat = self::cleanValue($sheet->getCell('D5')->getValue());
            $rawKelas = self::cleanValue($sheet->getCell('D6')->getValue());
            $rawSemester = self::cleanValue($sheet->getCell('D7')->getValue());
            $rawTahun = self::cleanValue($sheet->getCell('D8')->getValue());
            $rawGuru = self::cleanValue($sheet->getCell('D9')->getValue());

            $this->metadata = [
                'mapel' => $rawMapel,
                'tingkat' => $rawTingkat,
                'kelas' => $rawKelas,
                'semester' => $rawSemester,
                'tahun' => $rawTahun,
                'guru' => $rawGuru,
            ];

            // 2. Intelligent Mapel Matching
            $mapel = $this->findMapel($rawMapel);
            if ($mapel) {
                $this->mapelId = $mapel->id;
                $this->detectedMapelName = $mapel->nama_mapel;
                $this->detectedKodeMapel = $mapel->kode_mapel;
            }

            // 3. Find Header Row (scan rows 10-15)
            $headerRow = 11;
            for ($r = 10; $r <= 15; $r++) {
                $b = strtolower(self::cleanValue($sheet->getCell('B' . $r)->getValue()));
                $c = strtolower(self::cleanValue($sheet->getCell('C' . $r)->getValue()));
                $d = strtolower(self::cleanValue($sheet->getCell('D' . $r)->getValue()));
                $e = strtolower(self::cleanValue($sheet->getCell('E' . $r)->getValue()));

                if (str_contains($b, 'no') || str_contains($c, 'pertemuan') || str_contains($d, 'capaian') || str_contains($e, 'tujuan')) {
                    $headerRow = $r;
                    break;
                }
            }

            // 4. Parse TP Data Rows
            $highestRow = $sheet->getHighestRow();
            $this->tpData = [];
            $itemIndex = 1;

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $no = self::cleanValue($sheet->getCell('B' . $row)->getValue());
                $pertemuanRaw = self::cleanValue($sheet->getCell('C' . $row)->getValue());
                $cp = self::cleanValue($sheet->getCell('D' . $row)->getValue());
                $tp = self::cleanValue($sheet->getCell('E' . $row)->getValue());

                if (empty($pertemuanRaw) && empty($cp) && empty($tp)) continue;
                if (empty($cp) && empty($tp)) continue;

                // Fallback: If TP is empty but CP is present, treat CP as TP
                if (empty($tp)) {
                    $tp = $cp;
                }

                // Format Pertemuan label
                $pertemuan = $pertemuanRaw;
                if (!empty($pertemuanRaw) && is_numeric($pertemuanRaw)) {
                    $pertemuan = 'Pertemuan ' . $pertemuanRaw;
                } elseif (empty($pertemuanRaw)) {
                    $pertemuan = 'Pertemuan ' . $itemIndex;
                }

                $kodeTp = 'TP-' . str_pad($itemIndex, 2, '0', STR_PAD_LEFT);
                $deskripsiTp = '[' . $pertemuan . '] ' . (!empty($cp) && $cp !== $tp ? 'CP: ' . $cp . ' | TP: ' . $tp : $tp);

                $this->tpData[] = [
                    'no' => !empty($no) ? $no : $itemIndex,
                    'pertemuan' => $pertemuan,
                    'cp' => $cp,
                    'tp' => $tp,
                    'kode_tp' => $kodeTp,
                    'deskripsi_tp' => $deskripsiTp,
                    'order_sequence' => $itemIndex,
                ];

                $itemIndex++;
            }

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Intelligently match a raw Mapel string against the database.
     */
    public function findMapel(string $raw): ?MataPelajaran
    {
        $cleaned = self::cleanValue($raw);
        if (empty($cleaned)) return null;

        try {
            // 1. Exact match on kode_mapel
            $mapel = MataPelajaran::where('kode_mapel', $cleaned)->first();
            if ($mapel) return $mapel;

            // 2. Exact match on nama_mapel
            $mapel = MataPelajaran::where('nama_mapel', $cleaned)->first();
            if ($mapel) return $mapel;

            // 3. Substring match on nama_mapel or kode_mapel
            $mapel = MataPelajaran::where('nama_mapel', 'like', '%' . $cleaned . '%')
                ->orWhere('kode_mapel', 'like', '%' . $cleaned . '%')
                ->first();
            if ($mapel) return $mapel;

            // 4. Reverse substring: check if any DB mapel name is inside the cleaned string
            $allMapels = MataPelajaran::all();
            foreach ($allMapels as $m) {
                if (stripos($cleaned, $m->nama_mapel) !== false || (!empty($m->kode_mapel) && stripos($cleaned, $m->kode_mapel) !== false)) {
                    return $m;
                }
            }

            // 5. Alias dictionary matching
            $upper = strtoupper($cleaned);
            foreach (self::$aliasMap as $alias => $fullName) {
                if ($upper === $alias || str_starts_with($upper, $alias) || str_contains($upper, $alias)) {
                    $matched = MataPelajaran::where('nama_mapel', 'like', '%' . $fullName . '%')
                        ->orWhere('nama_mapel', 'like', '%' . $alias . '%')
                        ->orWhere('kode_mapel', 'like', '%' . $alias . '%')
                        ->first();
                    if ($matched) return $matched;
                }
            }
        } catch (\Throwable $e) {
            // DB might not be connected in isolated test context
        }

        return null;
    }

    /**
     * Import parsed TP data into the database for a given MataPelajaran ID.
     */
    public function import(string $mapelId, ?string $guruId = null): int
    {
        $count = 0;
        foreach ($this->tpData as $index => $data) {
            if (empty($data['tp']) && empty($data['deskripsi_tp'])) continue;

            $kodeTP = $data['kode_tp'] ?? ('TP-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT));
            $deskripsi = $data['deskripsi_tp'] ?? $data['tp'];
            $order = (int) ($data['order_sequence'] ?? ($index + 1));

            // Check if exact matching TP already exists for this mapel and guru (or any guru for this mapel)
            $existing = TujuanPembelajaran::where('mapel_id', $mapelId)
                ->where(function ($q) use ($kodeTP, $deskripsi) {
                    $q->where('kode_tp', $kodeTP)
                      ->orWhere('deskripsi_tp', $deskripsi);
                })
                ->where(function ($q) use ($guruId) {
                    if ($guruId) {
                        $q->where('ketua_mgmp_id', $guruId)->orWhereNull('ketua_mgmp_id');
                    }
                })
                ->first();

            if ($existing) {
                $existing->update([
                    'kode_tp' => $kodeTP,
                    'deskripsi_tp' => $deskripsi,
                    'order_sequence' => $order,
                    'ketua_mgmp_id' => $guruId ?: $existing->ketua_mgmp_id,
                ]);
            } else {
                TujuanPembelajaran::create([
                    'mapel_id' => $mapelId,
                    'kode_tp' => $kodeTP,
                    'deskripsi_tp' => $deskripsi,
                    'order_sequence' => $order,
                    'ketua_mgmp_id' => $guruId,
                ]);
            }
            $count++;
        }
        return $count;
    }
}
