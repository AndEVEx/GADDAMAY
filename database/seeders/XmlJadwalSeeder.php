<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\JadwalGuru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class XmlJadwalSeeder extends Seeder
{
    // Non-guru keywords - these entities are stored as keterangan, not as users
    private array $nonGuruKeywords = ['LPK', 'TIM ', 'Kesiswaan', 'DIDI 2', 'NINO 2', 'EGAR 2',
        'NINA 2', 'RUDI 2', 'GATOT 2', 'DEDE 2', 'FAHMI 2', 'PERMANA 2', 'GABRIEL 2',
        'ZAENAL 2', 'FARIDA 2', 'NENI 2', 'HANIEF', 'DIAN 2 TP'];

    // Non-mapel keywords - stored as kegiatan_khusus
    private array $nonMapelKeywords = ['UPACARA', 'ISTIRAHAT', 'ESKUL', 'APEL PAGI', 'HAPPY DAY',
        'GURU BELAJAR', 'GURU BERKARYA'];

    private array $teacherMap = []; // asc_id => user_id or null (non-guru)
    private array $teacherNames = []; // asc_id => name (for keterangan)
    private array $classMap = []; // asc_id => rombel_id
    private array $subjectMap = []; // asc_id => mapel_id or null (non-mapel)
    private array $subjectNames = []; // asc_id => name (for kegiatan_khusus)
    private array $lessonMap = []; // lesson_id => {classids, subjectid, teacherids}
    private ?string $cachedGuruPassword = null;
    private ?string $cachedKetuaPassword = null;

    public function run(): void
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        DB::disableQueryLog();

        // Pre-compute bcrypt hashes ONCE to avoid hundreds of slow Bcrypt hashing calls
        $this->cachedGuruPassword = Hash::make('password123');
        $this->cachedKetuaPassword = Hash::make('ketua123');

        $xmlPath = config('app.xml_jadwal_path', storage_path('app/temp_jadwal.xml'));

        if (!file_exists($xmlPath)) {
            $message = "XML file not found: {$xmlPath}";
            $this->command?->error($message);
            throw new \RuntimeException($message);
        }

        $content = file_get_contents($xmlPath);
        // Convert from windows-1252 to UTF-8 and fix XML declaration
        $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        $content = preg_replace('/encoding="[^"]+"/', 'encoding="UTF-8"', $content);
        $xml = simplexml_load_string($content);

        if (!$xml) {
            $message = 'Gagal membaca format XML. File XML tidak valid atau corrupt.';
            $this->command?->error($message);
            throw new \RuntimeException($message);
        }

        DB::transaction(function () use ($xml) {
            $this->seedPeriods($xml);
            $this->seedTeachers($xml);
            $this->seedClasses($xml);
            $this->seedSubjects($xml);
            $this->seedLessonsAndCards($xml);
            $this->seedKetuaKelas();
        });

        $this->command?->info('XML jadwal seeded successfully!');
    }

    private function seedPeriods($xml): void
    {
        // Standard SMKN 2 Indramayu 12-Period Schedule Slots (Including Jam 0 Apel & Breaks)
        $standardPeriods = [
            0  => ['mulai' => '07:00', 'selesai' => '07:30'], // Jam 0: Apel / Upacara / Pra-KBM
            1  => ['mulai' => '07:30', 'selesai' => '08:15'], // Jam 1
            2  => ['mulai' => '08:15', 'selesai' => '09:00'], // Jam 2
            3  => ['mulai' => '09:00', 'selesai' => '09:45'], // Jam 3
            4  => ['mulai' => '09:45', 'selesai' => '10:30'], // Jam 4
            5  => ['mulai' => '10:30', 'selesai' => '11:15'], // Jam 5
            6  => ['mulai' => '11:15', 'selesai' => '12:00'], // Jam 6
            7  => ['mulai' => '12:00', 'selesai' => '12:45'], // Jam 7 (Ishoma / Sholat / Lunch)
            8  => ['mulai' => '12:45', 'selesai' => '13:30'], // Jam 8
            9  => ['mulai' => '13:30', 'selesai' => '14:15'], // Jam 9
            10 => ['mulai' => '14:15', 'selesai' => '15:00'], // Jam 10 (14:46 PM is HERE!)
            11 => ['mulai' => '15:00', 'selesai' => '15:45'], // Jam 11
            12 => ['mulai' => '15:45', 'selesai' => '16:30'], // Jam 12
        ];

        foreach ($xml->periods->period as $period) {
            $jamKe = (int) $period['period'];
            $mulai = $standardPeriods[$jamKe]['mulai'] ?? (string) $period['starttime'];
            $selesai = $standardPeriods[$jamKe]['selesai'] ?? (string) $period['endtime'];

            JamPelajaran::updateOrCreate(
                ['jam_ke' => $jamKe],
                ['waktu_mulai' => $mulai, 'waktu_selesai' => $selesai]
            );
        }
        $this->command?->info('Periods seeded: ' . count($xml->periods->period));
    }

    private function seedTeachers($xml): void
    {
        $guruCount = 0;
        $nonGuruCount = 0;

        foreach ($xml->teachers->teacher as $teacher) {
            $ascId = (string) $teacher['id'];
            $name = trim((string) $teacher['name']);
            $short = (string) $teacher['short'];

            $this->teacherNames[$ascId] = $name;

            if ($this->isNonGuru($name)) {
                $this->teacherMap[$ascId] = null;
                $nonGuruCount++;
                continue;
            }

            $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $short)) . '@smkn2indramayu.sch.id';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $this->cachedGuruPassword,
                    'role' => 'guru',
                ]
            );

            $this->teacherMap[$ascId] = $user->id;
            $guruCount++;
        }

        $this->command?->info("Teachers seeded: {$guruCount} guru, {$nonGuruCount} non-guru (keterangan)");
    }

    private function seedClasses($xml): void
    {
        foreach ($xml->classes->class as $class) {
            $ascId = (string) $class['id'];
            $name = trim((string) $class['name']);
            $tingkat = $this->detectTingkat($name);

            $rombel = Rombel::updateOrCreate(
                ['asc_id' => $ascId],
                ['nama_kelas' => $name, 'tingkat' => $tingkat]
            );

            $this->classMap[$ascId] = $rombel->id;
        }

        $this->command?->info('Classes seeded: ' . count($xml->classes->class));
    }

    private function seedSubjects($xml): void
    {
        $mapelCount = 0;
        $nonMapelCount = 0;

        foreach ($xml->subjects->subject as $subject) {
            $ascId = (string) $subject['id'];
            $name = trim((string) $subject['name']);
            $short = trim((string) $subject['short']);

            $this->subjectNames[$ascId] = $name;

            if ($this->isNonMapel($name, $short)) {
                $this->subjectMap[$ascId] = null;
                $nonMapelCount++;
                continue;
            }

            $mapel = MataPelajaran::updateOrCreate(
                ['asc_id' => $ascId],
                ['nama_mapel' => $name, 'kode_mapel' => $short]
            );

            $this->subjectMap[$ascId] = $mapel->id;
            $mapelCount++;
        }

        $this->command?->info("Subjects seeded: {$mapelCount} mapel, {$nonMapelCount} non-mapel (kegiatan_khusus)");
    }

    private function seedLessonsAndCards($xml): void
    {
        // Build lesson map
        foreach ($xml->lessons->lesson as $lesson) {
            $lessonId = (string) $lesson['id'];
            $this->lessonMap[$lessonId] = [
                'classids' => (string) $lesson['classids'],
                'subjectid' => (string) $lesson['subjectid'],
                'teacherids' => (string) $lesson['teacherids'],
            ];
        }

        // Process cards - group by lesson+day to merge consecutive periods
        $cardGroups = [];
        foreach ($xml->cards->card as $card) {
            $lessonId = (string) $card['lessonid'];
            $period = (int) $card['period'];
            $dayBitmask = (string) $card['days'];
            $day = $this->decodeDayBitmask($dayBitmask);

            if ($day === 0) continue;

            $key = "{$lessonId}_{$day}";
            if (!isset($cardGroups[$key])) {
                $cardGroups[$key] = [
                    'lessonid' => $lessonId,
                    'day' => $day,
                    'periods' => [],
                ];
            }
            $cardGroups[$key]['periods'][] = $period;
        }

        // Create jadwal_pelajaran from grouped cards
        $jadwalCount = 0;
        foreach ($cardGroups as $group) {
            $lesson = $this->lessonMap[$group['lessonid']] ?? null;
            if (!$lesson) continue;

            sort($group['periods']);
            $jamMulai = min($group['periods']);
            $jamSelesai = max($group['periods']);

            $classId = $lesson['classids'];
            $subjectId = $lesson['subjectid'];
            $teacherIds = $lesson['teacherids'];

            // Determine rombel
            $rombelId = $this->classMap[$classId] ?? null;

            // Determine mapel or kegiatan_khusus
            $mapelId = $this->subjectMap[$subjectId] ?? null;
            $kegiatanKhusus = ($mapelId === null && isset($this->subjectNames[$subjectId]))
                ? $this->subjectNames[$subjectId] : null;

            // Determine guru or keterangan
            $guruIds = [];
            $keterangan = null;

            if (!empty($teacherIds)) {
                foreach (explode(',', $teacherIds) as $tid) {
                    $tid = trim($tid);
                    if (isset($this->teacherMap[$tid])) {
                        if ($this->teacherMap[$tid] !== null) {
                            $guruIds[] = $this->teacherMap[$tid];
                        } else {
                            $keterangan = $this->teacherNames[$tid] ?? 'Non-guru';
                        }
                    }
                }
            }

            $jadwal = JadwalPelajaran::create([
                'hari' => $group['day'],
                'jam_ke_mulai' => $jamMulai,
                'jam_ke_selesai' => $jamSelesai,
                'rombel_id' => $rombelId,
                'mapel_id' => $mapelId,
                'keterangan' => $keterangan,
                'kegiatan_khusus' => $kegiatanKhusus,
                'asc_lesson_id' => $group['lessonid'],
            ]);

            // Create jadwal_guru pivot entries
            foreach ($guruIds as $guruId) {
                JadwalGuru::firstOrCreate([
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'guru_id' => $guruId,
                ]);
            }

            $jadwalCount++;
        }

        $this->command?->info("Schedule entries created: {$jadwalCount}");
    }

    private function seedKetuaKelas(): void
    {
        $rombels = Rombel::all();
        $count = 0;
        foreach ($rombels as $rombel) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rombel->nama_kelas));
            User::updateOrCreate(
                ['email' => "ketua.{$slug}@smkn2indramayu.sch.id"],
                [
                    'name' => "Ketua Kelas {$rombel->nama_kelas}",
                    'password' => $this->cachedKetuaPassword,
                    'role' => 'ketua_kelas',
                    'rombel_id' => $rombel->id,
                ]
            );
            $count++;
        }
        $this->command?->info("Ketua kelas created: {$count}");
    }

    private function isNonGuru(string $name): bool
    {
        foreach ($this->nonGuruKeywords as $keyword) {
            if (stripos($name, $keyword) !== false) return true;
        }
        return false;
    }

    private function isNonMapel(string $name, string $short): bool
    {
        foreach ($this->nonMapelKeywords as $keyword) {
            if (stripos($name, $keyword) !== false || stripos($short, $keyword) !== false) return true;
        }
        return false;
    }

    private function detectTingkat(string $namaKelas): int
    {
        if (preg_match('/^XII\b/i', $namaKelas)) return 12;
        if (preg_match('/^XI\b/i', $namaKelas)) return 11;
        return 10;
    }

    private function decodeDayBitmask(string $bitmask): int
    {
        // "10000" = 1 (Senin), "01000" = 2 (Selasa), etc.
        $bitmask = trim($bitmask);
        if (strlen($bitmask) < 5) return 0;

        for ($i = 0; $i < 5; $i++) {
            if ($bitmask[$i] === '1') return $i + 1;
        }
        return 0;
    }
}
