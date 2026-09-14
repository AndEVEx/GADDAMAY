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

    public function parse(string $filePath, ?string $guruId = null): bool
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

            // Detect tingkat (10, 11, or 12)
            $detectedTingkat = self::detectTingkat($rawTingkat, $rawKelas);

            $this->metadata = [
                'mapel' => $rawMapel,
                'tingkat' => $rawTingkat,
                'detected_tingkat' => $detectedTingkat,
                'kelas' => $rawKelas,
                'semester' => $rawSemester,
                'tahun' => $rawTahun,
                'guru' => $rawGuru,
            ];

            // 2. Intelligent Mapel Matching (prioritize teacher's taught mapels)
            $mapel = $this->findMapel($rawMapel, $guruId, $rawKelas);
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
     * Detect integer tingkat (10, 11, 12) from raw metadata string.
     */
    public static function detectTingkat(?string $rawTingkat, ?string $rawKelas = null): ?int
    {
        $combined = strtolower(trim(($rawTingkat ?? '') . ' ' . ($rawKelas ?? '')));
        
        if (preg_match('/\b(xii|12|fase\s*f\s*2|kelas\s*12|kelas\s*xii)\b/i', $combined)) {
            return 12;
        }
        if (preg_match('/\b(xi|11|fase\s*f\s*1|kelas\s*11|kelas\s*xi)\b/i', $combined)) {
            return 11;
        }
        if (preg_match('/\b(x|10|fase\s*e|kelas\s*10|kelas\s*x)\b/i', $combined)) {
            return 10;
        }
        
        return null;
    }

    /**
     * Intelligently match a raw Mapel string against the database.
     * Prioritizes subjects taught by the teacher.
     */
    public function findMapel(string $raw, ?string $guruId = null, ?string $rawKelas = null): ?MataPelajaran
    {
        $cleaned = self::cleanValue($raw);
        if (empty($cleaned)) return null;

        try {
            // Normalized string for phonetic / fuzzy comparison
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleaned));
            $normTypo = str_replace(['software', 'softwere'], 'soft', $norm);

            // Priority 1: Check teacher's own taught subjects in JadwalPelajaran
            if ($guruId) {
                $teacherMapelIds = \App\Models\JadwalPelajaran::whereHas('jadwalGuru', fn($q) => $q->where('guru_id', $guruId))
                    ->whereNotNull('mapel_id')
                    ->pluck('mapel_id')
                    ->unique();

                $teacherMapels = MataPelajaran::whereIn('id', $teacherMapelIds)->get();

                foreach ($teacherMapels as $tm) {
                    $tmNamaNorm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tm->nama_mapel));
                    $tmKodeNorm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tm->kode_mapel ?? ''));
                    $tmNamaTypo = str_replace(['software', 'softwere'], 'soft', $tmNamaNorm);
                    $tmKodeTypo = str_replace(['software', 'softwere'], 'soft', $tmKodeNorm);

                    // Exact or substring or fuzzy match on teacher's subjects
                    if ($norm === $tmNamaNorm || $norm === $tmKodeNorm ||
                        $normTypo === $tmNamaTypo || $normTypo === $tmKodeTypo ||
                        str_contains($normTypo, $tmKodeTypo) || str_contains($tmKodeTypo, $normTypo) ||
                        str_contains($normTypo, $tmNamaTypo) || str_contains($tmNamaTypo, $normTypo)) {
                        return $tm;
                    }

                    // Check similarity percentage
                    similar_text($normTypo, $tmKodeTypo, $simPercent);
                    if ($simPercent > 65) {
                        return $tm;
                    }
                }
            }

            // Priority 2: Exact match on kode_mapel or nama_mapel in all DB
            $mapel = MataPelajaran::where('kode_mapel', $cleaned)
                ->orWhere('nama_mapel', $cleaned)
                ->first();
            if ($mapel) return $mapel;

            // Priority 3: Substring match on nama_mapel or kode_mapel
            $mapel = MataPelajaran::where('nama_mapel', 'like', '%' . $cleaned . '%')
                ->orWhere('kode_mapel', 'like', '%' . $cleaned . '%')
                ->first();
            if ($mapel) return $mapel;

            // Priority 4: Reverse substring: check if any DB mapel name is inside the cleaned string
            $allMapels = MataPelajaran::all();
            foreach ($allMapels as $m) {
                if (stripos($cleaned, $m->nama_mapel) !== false || (!empty($m->kode_mapel) && stripos($cleaned, $m->kode_mapel) !== false)) {
                    return $m;
                }
            }

            // Priority 5: Alias dictionary matching
            $upper = strtoupper($cleaned);
            $aliasExtMap = array_merge(self::$aliasMap, [
                'SOFTWARE DEVELOPMENT' => 'SOFTWERE DEVELOPMENT',
                'SOFTWERE DEVELOPMENT' => 'SD',
                'SD' => 'SOFTWERE DEVELOPMENT',
                'DIGITAL PRINTING' => 'DIGITAL PRINTING',
                'DP' => 'DIGITAL PRINTING',
                'KODING' => 'KODING DAN KECERDASAN ARTIFISIAL',
                'AI' => 'KODING DAN KECERDASAN ARTIFISIAL',
            ]);

            foreach ($aliasExtMap as $alias => $target) {
                if (str_contains($upper, $alias) || str_contains($alias, $upper)) {
                    $matched = MataPelajaran::where('nama_mapel', 'like', '%' . $target . '%')
                        ->orWhere('kode_mapel', 'like', '%' . $target . '%')
                        ->orWhere('nama_mapel', $alias)
                        ->orWhere('kode_mapel', $alias)
                        ->first();
                    if ($matched) return $matched;
                }
            }

            // Priority 6: Global fuzzy similarity on all mapels
            foreach ($allMapels as $m) {
                $mNorm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', ($m->kode_mapel ?? '') . ' ' . $m->nama_mapel));
                $mTypo = str_replace(['software', 'softwere'], 'soft', $mNorm);
                similar_text($normTypo, $mTypo, $sim);
                if ($sim > 70) {
                    return $m;
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully
        }

        return null;
    }

    /**
     * Import parsed TP data into the database for a given MataPelajaran ID.
     */
    public function import(string $mapelId, ?string $guruId = null, ?int $tingkat = null): int
    {
        $count = 0;
        $tingkat = $tingkat ?? ($this->metadata['detected_tingkat'] ?? null);

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
                ->where(function ($q) use ($tingkat) {
                    if ($tingkat) {
                        $q->where('tingkat', $tingkat)->orWhereNull('tingkat');
                    }
                })
                ->first();

            if ($existing) {
                $existing->update([
                    'kode_tp' => $kodeTP,
                    'deskripsi_tp' => $deskripsi,
                    'order_sequence' => $order,
                    'tingkat' => $tingkat ?? $existing->tingkat,
                    'ketua_mgmp_id' => $guruId ?: $existing->ketua_mgmp_id,
                ]);
            } else {
                TujuanPembelajaran::create([
                    'mapel_id' => $mapelId,
                    'tingkat' => $tingkat,
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
