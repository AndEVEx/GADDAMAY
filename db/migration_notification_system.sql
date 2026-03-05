-- ================================================================
-- MIGRATION: Notification System for SMKN 2 Indramayu Absensi
-- Jalankan SQL ini di phpMyAdmin atau MySQL CLI
-- ================================================================

-- 1. Tabel attendance_alerts
-- Menyimpan peringatan ketidakhadiran siswa
CREATE TABLE IF NOT EXISTS `attendance_alerts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` INT(11) NOT NULL,
  `id_rombel` INT(11) NOT NULL,
  `alert_type` ENUM('consecutive','weekly','monthly') NOT NULL COMMENT 'consecutive=2+ hari berturut, weekly=3+ kali/minggu, monthly=10+ hari/bulan',
  `alert_date` DATE NOT NULL,
  `absence_count` INT(11) NOT NULL DEFAULT 0,
  `absence_dates` TEXT DEFAULT NULL,
  `status` ENUM('new','notified_walikelas','notified_bk','resolved') NOT NULL DEFAULT 'new',
  `notified_walikelas_at` DATETIME DEFAULT NULL,
  `notified_bk_at` DATETIME DEFAULT NULL,
  `resolved_at` DATETIME DEFAULT NULL,
  `resolved_by` INT(11) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_siswa` (`id_siswa`),
  KEY `idx_rombel` (`id_rombel`),
  KEY `idx_type_date` (`alert_type`, `alert_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabel wa_message_queue
-- Antrian pesan WhatsApp (pending -> processing -> sent/failed)
CREATE TABLE IF NOT EXISTS `wa_message_queue` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` INT(11) DEFAULT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `recipient_name` VARCHAR(100) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `message_type` VARCHAR(50) DEFAULT NULL COMMENT 'weekly_report, alert_walikelas, alert_bk, etc',
  `status` ENUM('pending','processing','sent','failed') NOT NULL DEFAULT 'pending',
  `scheduled_date` DATE DEFAULT NULL,
  `week_start` DATE DEFAULT NULL,
  `week_end` DATE DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `retry_count` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_scheduled` (`scheduled_date`),
  KEY `idx_message_type` (`message_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabel wa_settings
-- Konfigurasi & template pesan WhatsApp
CREATE TABLE IF NOT EXISTS `wa_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Tambah kolom id_guru_bk di t_rombel
-- Guru BK per kelas, FK ke t_ptk.id_ptk
ALTER TABLE `t_rombel` ADD COLUMN `id_guru_bk` INT(11) DEFAULT NULL AFTER `id_walikelas`;

-- 5. Default settings
INSERT INTO `wa_settings` (`key`, `value`) VALUES
('alert_template_walikelas', '🏫 *SMKN 2 INDRAMAYU*\n\n⚠️ *Peringatan Kehadiran Siswa*\n\nKepada Wali Kelas {kelas},\n\nSiswa berikut memerlukan perhatian:\n👤 Nama: {nama_siswa}\n🆔 NIS: {nis}\n🏫 Kelas: {kelas}\n\n{alert_description}\n\nMohon segera ditindaklanjuti.\nTerima kasih 🙏'),
('alert_template_bk', '🏫 *SMKN 2 INDRAMAYU*\n\n🚨 *Laporan Guru BK*\n\nSiswa berikut memerlukan perhatian khusus:\n👤 Nama: {nama_siswa}\n🆔 NIS: {nis}\n🏫 Kelas: {kelas}\n🧑‍🏫 Wali Kelas: {wali_kelas}\n\n{alert_description}\n\nMohon segera ditindaklanjuti.\nTerima kasih 🙏'),
('notification_paused', '0'),
('notification_pause_reason', '')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- 6. Update logo di database
-- Logo di halaman ScanQR dan Dashboard dibaca dari tabel ini
UPDATE `t_setting_aplikasi` SET `file` = 'logo_smkn2.png';
