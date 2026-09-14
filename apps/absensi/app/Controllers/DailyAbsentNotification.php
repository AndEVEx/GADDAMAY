<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\WaGatewayService;

date_default_timezone_set('Asia/Jakarta');

/**
 * Daily Absent Student Notification Controller
 * 
 * Sends list of absent students to WhatsApp Channel
 * Split by tingkat: Sender/JID 1 for Tingkat 10, Sender/JID 2 for Tingkat 11+
 * 
 * Cron: 
 *   15 8 * * 1-6 curl -s http://domain/DailyAbsentNotification/send      (8:15 AM - absent students)
 *   50 15 * * 1-6 curl -s http://domain/DailyAbsentNotification/sendPulang (3:50 PM - not departed)
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
     * Check if WA is globally enabled
     */
    private function isWaEnabled()
    {
        try {
            $row = $this->db->table('wa_settings')->where('key', 'wa_enabled')->get()->getRow();
            return ($row && $row->value == '1');
        } catch (\Exception $e) {
            return true; // default enabled if setting doesn't exist
        }
    }

    /**
     * Send absent student list at 8:15 AM to WA Channel
     * Split by tingkat: JID 1 for tingkat 10, JID 2 for tingkat 11+
     */
    public function send()
    {
        if (!$this->isWaEnabled()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pengiriman WA dinonaktifkan oleh admin'
            ]);
        }

        $today = date('Y-m-d');
        $dayOfWeek = date('N');

        if ($dayOfWeek == 7) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Hari Minggu, tidak ada notifikasi'
            ]);
        }

        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        if (!$tapelRow) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tahun pelajaran aktif tidak ditemukan'
            ]);
        }
        $id_tapel = $tapelRow->id_tapel;

        // Get all active students with tingkat info
        $allStudents = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel, r.id_rombel, tk.id_tingkat_kelas, tk.nm_tingkat_kelas')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->join('r_tingkat_kelas tk', 'tk.id_tingkat_kelas = r.id_tingkat_kelas')
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
            ->where('sts_hadir', 0)
            ->get()
            ->getResultArray();

        $presentIdList = array_column($presentIds, 'id_siswa');

        // Filter to get absent students
        $absentStudents = array_filter($allStudents, function ($s) use ($presentIdList) {
            return !in_array($s['id_siswa'], $presentIdList);
        });

        // Split by tingkat
        $absentTingkat10 = array_filter($absentStudents, function ($s) {
            return (stripos($s['nm_tingkat_kelas'], '10') !== false || stripos($s['nm_tingkat_kelas'], 'X') === 0);
        });
        $absentTingkat11 = array_filter($absentStudents, function ($s) {
            return (stripos($s['nm_tingkat_kelas'], '10') === false && stripos($s['nm_tingkat_kelas'], 'X') !== 0);
        });

        // Get channel JIDs and message template
        $channelJid1 = $this->getSetting('channel_jid_1');
        $channelJid2 = $this->getSetting('channel_jid_2');
        $template = $this->getSetting('channel_message_template');

        $results = [];

        // Send tingkat 10 to channel 1 via sender 0
        if (!empty($channelJid1)) {
            $message10 = $this->formatAbsentMessage($absentTingkat10, $today, 'Tingkat 10', $template);
            $result1 = $this->waGateway->sendToChannel($channelJid1, $message10, 0);
            $results['channel_1_tingkat10'] = $result1;
        }

        // Send tingkat 11+ to channel 2 via sender 1
        if (!empty($channelJid2)) {
            $message11 = $this->formatAbsentMessage($absentTingkat11, $today, 'Tingkat 11', $template);
            $result2 = $this->waGateway->sendToChannel($channelJid2, $message11, 1);
            $results['channel_2_tingkat11'] = $result2;
        }

        if (empty($channelJid1) && empty($channelJid2)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Channel JID belum dikonfigurasi.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Notifikasi channel terkirim',
            'date' => $today,
            'total_siswa' => count($allStudents),
            'total_hadir' => count($presentIdList),
            'total_tidak_hadir' => count($absentStudents),
            'tidak_hadir_tingkat10' => count($absentTingkat10),
            'tidak_hadir_tingkat11' => count($absentTingkat11),
            'results' => $results,
        ]);
    }

    /**
     * Send NOT-YET-DEPARTED student list at 15:50 to WA Channel
     * Only includes students who checked IN but haven't checked OUT.
     * Students who were absent (never checked in) are EXCLUDED.
     */
    public function sendPulang()
    {
        if (!$this->isWaEnabled()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pengiriman WA dinonaktifkan oleh admin'
            ]);
        }

        $today = date('Y-m-d');
        $dayOfWeek = date('N');

        if ($dayOfWeek == 7) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Hari Minggu'
            ]);
        }

        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        if (!$tapelRow) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tapel aktif tidak ditemukan']);
        }
        $id_tapel = $tapelRow->id_tapel;

        // Get students who checked IN today (masuk)
        $masukStudents = $this->db->table('t_siswa_hadir h')
            ->select('h.id_siswa')
            ->where('h.tgl_hadir', $today)
            ->where('h.sts_hadir', 0)
            ->get()
            ->getResultArray();
        $masukIds = array_column($masukStudents, 'id_siswa');

        if (empty($masukIds)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Tidak ada siswa yang masuk hari ini'
            ]);
        }

        // Get students who already checked OUT (pulang)
        $pulangStudents = $this->db->table('t_siswa_hadir h')
            ->select('h.id_siswa')
            ->where('h.tgl_hadir', $today)
            ->where('h.sts_hadir', 1)
            ->get()
            ->getResultArray();
        $pulangIds = array_column($pulangStudents, 'id_siswa');

        // Students who checked in but NOT yet checked out
        $notDepartedIds = array_diff($masukIds, $pulangIds);

        if (empty($notDepartedIds)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Semua siswa sudah pulang'
            ]);
        }

        // Get full student data with tingkat
        $notDepartedStudents = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel, tk.id_tingkat_kelas, tk.nm_tingkat_kelas')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->join('r_tingkat_kelas tk', 'tk.id_tingkat_kelas = r.id_tingkat_kelas')
            ->where('sr.id_tapel', $id_tapel)
            ->whereIn('s.id_siswa', $notDepartedIds)
            ->orderBy('r.nm_rombel', 'ASC')
            ->orderBy('s.nm_siswa', 'ASC')
            ->get()
            ->getResultArray();

        // Split by tingkat
        $ndTingkat10 = array_filter($notDepartedStudents, function ($s) {
            return (stripos($s['nm_tingkat_kelas'], '10') !== false || stripos($s['nm_tingkat_kelas'], 'X') === 0);
        });
        $ndTingkat11 = array_filter($notDepartedStudents, function ($s) {
            return (stripos($s['nm_tingkat_kelas'], '10') === false && stripos($s['nm_tingkat_kelas'], 'X') !== 0);
        });

        $channelJid1 = $this->getSetting('channel_jid_1');
        $channelJid2 = $this->getSetting('channel_jid_2');

        $results = [];

        if (!empty($channelJid1) && !empty($ndTingkat10)) {
            $msg10 = $this->formatNotDepartedMessage($ndTingkat10, $today, 'Tingkat 10');
            $results['channel_1'] = $this->waGateway->sendToChannel($channelJid1, $msg10, 0);
        }

        if (!empty($channelJid2) && !empty($ndTingkat11)) {
            $msg11 = $this->formatNotDepartedMessage($ndTingkat11, $today, 'Tingkat 11');
            $results['channel_2'] = $this->waGateway->sendToChannel($channelJid2, $msg11, 1);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Notifikasi belum pulang terkirim',
            'total_belum_pulang' => count($notDepartedStudents),
            'results' => $results,
        ]);
    }

    /**
     * Preview the message without sending
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
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel, r.id_rombel, tk.nm_tingkat_kelas')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->join('r_tingkat_kelas tk', 'tk.id_tingkat_kelas = r.id_tingkat_kelas')
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

        $template = $this->getSetting('channel_message_template');
        $message = $this->formatAbsentMessage($absentStudents, $today, 'Semua Tingkat', $template);

        return $this->response->setJSON([
            'status' => true,
            'date' => $today,
            'total_siswa' => count($allStudents),
            'total_hadir' => count($presentIdList),
            'total_tidak_hadir' => count($absentStudents),
            'message' => $message,
        ]);
    }

    /**
     * Format absent student list message
     */
    private function formatAbsentMessage($absentStudents, $date, $tingkatLabel = '', $template = '')
    {
        $formattedDate = $this->formatDateIndo($date);
        $totalAbsent = count($absentStudents);

        $message = "🏫 *SMKN 2 INDRAMAYU*\n";
        $message .= "📋 *DAFTAR SISWA TIDAK HADIR*\n";
        if ($tingkatLabel) $message .= "📚 *{$tingkatLabel}*\n";
        $message .= "\n📅 {$formattedDate}\n";
        $message .= "⏰ Pukul " . date('H:i') . " WIB\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($totalAbsent == 0) {
            $message .= "✅ *Alhamdulillah, semua siswa hadir!*\n\n";
        } else {
            $message .= "⚠️ Total tidak hadir: *{$totalAbsent} siswa*\n\n";

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
     * Format not-departed student list message
     */
    private function formatNotDepartedMessage($students, $date, $tingkatLabel = '')
    {
        $formattedDate = $this->formatDateIndo($date);
        $total = count($students);

        $message = "🏫 *SMKN 2 INDRAMAYU*\n";
        $message .= "📋 *DAFTAR SISWA BELUM PULANG*\n";
        if ($tingkatLabel) $message .= "📚 *{$tingkatLabel}*\n";
        $message .= "\n📅 {$formattedDate}\n";
        $message .= "⏰ Pukul " . date('H:i') . " WIB\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($total == 0) {
            $message .= "✅ *Semua siswa sudah pulang!*\n\n";
        } else {
            $message .= "⚠️ Total belum pulang: *{$total} siswa*\n\n";

            $grouped = [];
            foreach ($students as $s) {
                $kelas = $s['nm_rombel'];
                if (!isset($grouped[$kelas])) $grouped[$kelas] = [];
                $grouped[$kelas][] = $s;
            }

            foreach ($grouped as $kelas => $studentList) {
                $jumlah = count($studentList);
                $message .= "📚 *{$kelas}* ({$jumlah} siswa)\n";
                $no = 0;
                foreach ($studentList as $s) {
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

    private function formatDateIndo($date)
    {
        $hari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                  7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $dayName = $hari[(int)date('N', strtotime($date))];
        $d = date('j', strtotime($date));
        $m = $bulan[(int)date('n', strtotime($date))];
        $y = date('Y', strtotime($date));
        return "$dayName, $d $m $y";
    }

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
