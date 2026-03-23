<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\WaGatewayService;

date_default_timezone_set('Asia/Jakarta');

/**
 * Daily Absent Student Notification Controller
 * 
 * Sends list of absent students to WhatsApp Channel at 10 AM
 * 
 * Cron: 0 10 * * 1-6 curl -s http://your-domain/DailyAbsentNotification/send
 */
class DailyAbsentNotification extends Controller
{
    protected $db;
    protected $waGateway;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->waGateway = new WaGatewayService();
    }

    /**
     * Main method: Collect absent students and send to WA Channel
     * Called via cron at 10 AM every school day (Mon-Sat)
     */
    public function send()
    {
        $today = date('Y-m-d');
        $dayOfWeek = date('N'); // 1=Mon, 7=Sun

        // Skip Sunday
        if ($dayOfWeek == 7) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Hari Minggu, tidak ada notifikasi'
            ]);
        }

        // Get active tapel
        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        if (!$tapelRow) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tahun pelajaran aktif tidak ditemukan'
            ]);
        }
        $id_tapel = $tapelRow->id_tapel;

        // Get all active students
        $allStudents = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel, r.id_rombel')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('sr.id_tapel', $id_tapel)
            ->where('s.sts_siswa', 1)
            ->orderBy('r.nm_rombel', 'ASC')
            ->orderBy('s.nm_siswa', 'ASC')
            ->get()
            ->getResultArray();

        // Get students who ARE present today
        $presentIds = $this->db->table('t_siswa_hadir')
            ->select('id_siswa')
            ->where('tgl_hadir', $today)
            ->where('sts_hadir', 0) // 0 = masuk
            ->get()
            ->getResultArray();

        $presentIdList = array_column($presentIds, 'id_siswa');

        // Filter to get absent students
        $absentStudents = array_filter($allStudents, function ($s) use ($presentIdList) {
            return !in_array($s['id_siswa'], $presentIdList);
        });

        // Format the message
        $message = $this->formatAbsentMessage($absentStudents, $today);

        // Get channel JIDs from settings
        $channelJid1 = $this->getSetting('channel_jid_1');
        $channelJid2 = $this->getSetting('channel_jid_2');

        $results = [];

        // Send to channel 1 via sender 1
        if (!empty($channelJid1)) {
            $result1 = $this->waGateway->sendToChannel($channelJid1, $message, 0);
            $results['channel_1'] = $result1;
        }

        // Send to channel 2 via sender 2
        if (!empty($channelJid2)) {
            $result2 = $this->waGateway->sendToChannel($channelJid2, $message, 1);
            $results['channel_2'] = $result2;
        }

        if (empty($channelJid1) && empty($channelJid2)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Channel JID belum dikonfigurasi. Atur di Pengaturan WhatsApp.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Notifikasi channel terkirim',
            'date' => $today,
            'total_siswa' => count($allStudents),
            'total_hadir' => count($presentIdList),
            'total_tidak_hadir' => count($absentStudents),
            'results' => $results,
        ]);
    }

    /**
     * Preview the message without sending (for testing)
     */
    public function preview()
    {
        $today = date('Y-m-d');

        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        if (!$tapelRow) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tapel aktif tidak ditemukan']);
        }
        $id_tapel = $tapelRow->id_tapel;

        $allStudents = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel, r.id_rombel')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('sr.id_tapel', $id_tapel)
            ->where('s.sts_siswa', 1)
            ->orderBy('r.nm_rombel', 'ASC')
            ->orderBy('s.nm_siswa', 'ASC')
            ->get()
            ->getResultArray();

        $presentIds = $this->db->table('t_siswa_hadir')
            ->select('id_siswa')
            ->where('tgl_hadir', $today)
            ->where('sts_hadir', 0)
            ->get()
            ->getResultArray();

        $presentIdList = array_column($presentIds, 'id_siswa');

        $absentStudents = array_filter($allStudents, function ($s) use ($presentIdList) {
            return !in_array($s['id_siswa'], $presentIdList);
        });

        $message = $this->formatAbsentMessage($absentStudents, $today);

        return $this->response->setJSON([
            'status' => true,
            'date' => $today,
            'total_siswa' => count($allStudents),
            'total_hadir' => count($presentIdList),
            'total_tidak_hadir' => count($absentStudents),
            'message' => $message,
            'absent_list' => array_values($absentStudents),
        ]);
    }

    /**
     * Format the absent student list message for WA Channel
     */
    private function formatAbsentMessage($absentStudents, $date)
    {
        $hari = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $dayName = $hari[(int)date('N', strtotime($date))];
        $d = date('j', strtotime($date));
        $m = $bulan[(int)date('n', strtotime($date))];
        $y = date('Y', strtotime($date));
        $formattedDate = "$dayName, $d $m $y";

        $totalAbsent = count($absentStudents);

        $message = "🏫 *SMKN 2 INDRAMAYU*\n";
        $message .= "📋 *DAFTAR SISWA TIDAK HADIR*\n\n";
        $message .= "📅 {$formattedDate}\n";
        $message .= "⏰ Pukul " . date('H:i') . " WIB\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($totalAbsent == 0) {
            $message .= "✅ *Alhamdulillah, semua siswa hadir!*\n\n";
        } else {
            $message .= "⚠️ Total tidak hadir: *{$totalAbsent} siswa*\n\n";

            // Group by class
            $grouped = [];
            foreach ($absentStudents as $s) {
                $kelas = $s['nm_rombel'];
                if (!isset($grouped[$kelas])) {
                    $grouped[$kelas] = [];
                }
                $grouped[$kelas][] = $s;
            }

            foreach ($grouped as $kelas => $students) {
                $jumlah = count($students);
                $message .= "📚 *{$kelas}* ({$jumlah} siswa)\n";
                $no = 0;
                foreach ($students as $s) {
                    $no++;
                    $message .= "  {$no}. {$s['nm_siswa']} ({$s['no_induk']})\n";
                }
                $message .= "\n";
            }
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "— *Sistem Absensi Digital*\n";
        $message .= "*SMKN 2 INDRAMAYU*";

        return $message;
    }

    /**
     * Get setting value from wa_settings
     */
    private function getSetting($key)
    {
        try {
            $row = $this->db->table('wa_settings')->where('key', $key)->get()->getRow();
            return $row->value ?? '';
        } catch (\Exception $e) {
            return '';
        }
    }
}
