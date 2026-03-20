-- ============================================================
-- Complete SQL Import for absen_smkn2_indramayu
-- Generated: 2026-03-20
-- Description: All tables with dummy data filled (1 row each)
--              INCLUDING guru BK in t_rombel
-- ============================================================
-- USAGE: Import via phpMyAdmin or CLI:
--   mysql -u root -p absen_smkn2_indramayu < absen_smkn2_indramayu_complete.sql
--
-- NOTE: This script uses TRUNCATE to clear existing data first.
--       Make sure you have a backup before importing!
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- STEP 1: CLEAR ALL EXISTING DATA
-- ============================================================

TRUNCATE TABLE `t_siswa_rombel`;
TRUNCATE TABLE `t_siswa_hadir`;
TRUNCATE TABLE `t_siswa_absen`;
TRUNCATE TABLE `t_point_siswa`;
TRUNCATE TABLE `t_total_point_siswa`;
TRUNCATE TABLE `attendance_alerts`;
TRUNCATE TABLE `wa_message_queue`;
TRUNCATE TABLE `t_rombel`;
TRUNCATE TABLE `t_siswa`;
TRUNCATE TABLE `t_point`;
TRUNCATE TABLE `t_total_point`;
TRUNCATE TABLE `t_anggota_shift`;
TRUNCATE TABLE `t_lembur`;
TRUNCATE TABLE `t_ptk`;
TRUNCATE TABLE `absen_mengajar`;
TRUNCATE TABLE `tweb_pegawai_hadir`;
TRUNCATE TABLE `tweb_pegawai_absen`;
TRUNCATE TABLE `jadwal_absen`;
TRUNCATE TABLE `jadwal_khusus`;
TRUNCATE TABLE `libur`;
TRUNCATE TABLE `libur_besar`;
TRUNCATE TABLE `tweb_command_adms`;
TRUNCATE TABLE `tweb_log_command_adms`;

-- ============================================================
-- STEP 2: REFERENCE / LOOKUP TABLES
-- ============================================================

-- r_hari (Hari & jam sekolah)
-- Already has data from original dump, but we re-insert to be safe
DELETE FROM `r_hari`;
INSERT INTO `r_hari` (`id_hari`, `nm_hari`, `sts_hari`, `jammasuk`, `jampulang`) VALUES
(1, 'Senin', 1, '07:10:00', '14:05:00'),
(2, 'Selasa', 1, '07:10:00', '14:05:00'),
(3, 'Rabu', 1, '07:10:00', '14:05:00'),
(4, 'Kamis', 1, '07:10:00', '14:05:00'),
(5, 'Jumat', 1, '07:10:00', '11:30:00'),
(6, 'Sabtu', 2, '08:10:00', '14:05:00'),
(7, 'Minggu', 2, '07:10:00', '14:05:00');

-- r_jenis_ptk (Jenis PTK: Guru / Pegawai)
DELETE FROM `r_jenis_ptk`;
INSERT INTO `r_jenis_ptk` (`id_jenis_ptk`, `nama_jenis_ptk`) VALUES
(1, 'Guru'),
(2, 'Pegawai');

-- r_shift (Shift kerja)
DELETE FROM `r_shift`;
INSERT INTO `r_shift` (`id_shift`, `nm_shift`, `jam_masuk`, `jam_pulang`) VALUES
(4, 'Shift Pagi Satpam', '07:00:00', '19:00:00'),
(5, 'Shift Regular', '07:00:00', '15:30:00'),
(7, 'Shift Ramadhan', '07:35:00', '14:00:00'),
(8, 'Shift Malam Satpam', '19:00:00', '07:00:00');

-- r_tapel (Tahun Pelajaran)
DELETE FROM `r_tapel`;
INSERT INTO `r_tapel` (`id_tapel`, `nm_tapel`, `sts_aktif`) VALUES
(2, '2024/2025', 0),
(3, '2025/2026', 1);

-- r_tingkat_kelas (Tingkat kelas: 10, 11, 12)
DELETE FROM `r_tingkat_kelas`;
INSERT INTO `r_tingkat_kelas` (`id_tingkat_kelas`, `nm_tingkat_kelas`) VALUES
(1, '10'),
(2, '11'),
(3, '12');

-- ============================================================
-- STEP 3: MESIN ABSENSI
-- ============================================================

DELETE FROM `tweb_mesin`;
INSERT INTO `tweb_mesin` (`ID`, `NOMOR_IP`, `NAMA`, `SERIAL_NUMBER`, `ERRORDELAY`, `DELAY`, `TRANSTIMES1`, `TRANSTIMES2`, `TANGGAL_SINC`, `MEREK_MESIN`) VALUES
(15, '192.168.0.1', 'Mesin 1', 'CKO9232060131', 60, 60, NULL, NULL, '2023-09-02 12:53:35', 2),
(19, '192.168.0.2', 'Mesin 2', 'ADGE233560712', 60, 60, NULL, NULL, NULL, 1);

-- ============================================================
-- STEP 4: SETTING APLIKASI
-- ============================================================

DELETE FROM `t_setting_aplikasi`;
INSERT INTO `t_setting_aplikasi` (`id_setting`, `nm_aplikasi`, `file`, `nm_sekolah`, `alamat`, `nm_kepsek`) VALUES
(1, 'Absensi Online', 'logo_smkn2.png', 'SMKN 2 INDRAMAYU', 'Pabean Udik 15, Indramayu', 'Yeti Sumiyati, S.Pd., M.M.Pd.');

-- ============================================================
-- STEP 5: USERS (Admin & Kepsek)
-- ============================================================
-- Password: admin123 (bcrypt hash)

DELETE FROM `t_user`;
INSERT INTO `t_user` (`id_user`, `username`, `password`, `nama`, `level`, `foto`) VALUES
(1, 'admin', '$2y$10$O1Ux6CUSlZ0XqSXJYkGLf.P0.tPkZeQBIGBnPQPBq2JdpJYkNKmG.', 'Super Admin', 1, '1721215046_327c8620e13c6f99cd18.png'),
(25, 'kepsek', '$2y$10$uz2sF2EIJw4saQyPjvM7/ueeRvYnO1ChVsAdeSjANvt5lKRsQaxUa', 'Kepala Sekolah', 4, '1725176043_fcada036b0b7e1d4b49b.png');

-- ============================================================
-- STEP 6: GURU / PTK (2 orang: 1 Wali Kelas + 1 Guru BK)
-- ============================================================
-- Password for both: password123
-- id_ptk=1 => Wali Kelas (Agung Saputra)
-- id_ptk=2 => Guru BK (Siti Rahayu)

INSERT INTO `t_ptk` (`id_ptk`, `nip`, `nik`, `nuptk`, `nama_ptk`, `nama_panggilan`, `id_jenis_ptk`, `id_divisi`, `no_hp`, `email`, `status_ptk`, `kode_tahun_ajaran`, `kd_jenis_kelamin`, `alamat`, `nomor_rfid`, `nomor_absensi`, `token_chat`, `urut`, `nominal`, `photo`, `password`, `KETERANGAN_ABSEN`, `status_absensi`, `tempat_lahir`, `tgl_lahir`, `tgl_join`, `batas_cuti`) VALUES
(1, '198501012010011001', '3211010101850001', '1234567890123456', 'Agung Saputra', 'Agung', 1, NULL, '082142986420', 'agung@smkn2indramayu.sch.id', 1, NULL, '1', 'Jl. Merdeka No. 10, Indramayu', NULL, '601', NULL, NULL, NULL, '1761539126_64f541fda82067a4c0ac.jpg', '$2y$10$bAK242Es82RHfoYBHa203.zEPbWWg5BfzxHSWp7aqM8v0ypf.uLNi', NULL, '0', 'Indramayu', '1985-01-01', '2010-01-01', 12),
(2, '199002152012022002', '3211021502900002', '6543210987654321', 'Siti Rahayu', 'Siti', 1, NULL, '081234567890', 'siti.bk@smkn2indramayu.sch.id', 1, NULL, '2', 'Jl. Pahlawan No. 25, Indramayu', NULL, '602', NULL, NULL, NULL, NULL, '$2y$10$bAK242Es82RHfoYBHa203.zEPbWWg5BfzxHSWp7aqM8v0ypf.uLNi', NULL, '0', 'Cirebon', '1990-02-15', '2012-02-01', 12);

-- ============================================================
-- STEP 7: ROMBEL (WITH GURU BK!) ← KEY FIX
-- ============================================================
-- id_walikelas=1 (Agung Saputra)
-- id_guru_bk=2  (Siti Rahayu) ← NOW FILLED!

INSERT INTO `t_rombel` (`id_rombel`, `id_tingkat_kelas`, `nm_rombel`, `id_tapel`, `id_walikelas`, `id_guru_bk`) VALUES
(1, 1, 'X-1', 3, 1, 2);

-- ============================================================
-- STEP 8: SISWA (1 siswa contoh)
-- ============================================================
-- Password: siswa123

INSERT INTO `t_siswa` (`id_siswa`, `no_induk`, `nisn`, `rfid`, `nm_siswa`, `alamat`, `tempat_lahir`, `tgl_lahir`, `jk`, `hp`, `sts_siswa`, `file`, `password`) VALUES
(1, 15001, 15001, '34242', 'Sintia Dewi', 'Jl. Anggrek No. 8, Indramayu', 'Indramayu', '2008-10-07', 2, '082142986420', 1, '1761539568_d866f2dedba6b022981d.png', '$2y$10$MH74qK5Us8twGHEcL4EBAeyxBH5M489rzIWdYZXE3hOXk6vq4vAF2');

-- ============================================================
-- STEP 9: SISWA ROMBEL (assign siswa ke rombel)
-- ============================================================

INSERT INTO `t_siswa_rombel` (`id_siswa_rombel`, `id_tapel`, `id_siswa`, `id_rombel`) VALUES
(1, 3, 1, 1);

-- ============================================================
-- STEP 10: ABSENSI GURU (pegawai hadir & absen)
-- ============================================================

-- Guru hadir (clock in & clock out)
INSERT INTO `tweb_pegawai_hadir` (`NO_INDUK`, `ID_PEGAWAI`, `TANGGAL`, `JAM`, `STATUS`, `STS`, `NO_MESIN`, `LON`, `LAT`, `JAM_SETTING`, `KETERANGAN`, `KETERANGAN2`, `FILENAME`, `SUHU`) VALUES
('601', '1', '2026-03-17', '07:05:00', '0', NULL, NULL, '108.3243', '-6.3271', NULL, 'Hadir tepat waktu', NULL, NULL, 36.5),
('601', '1', '2026-03-17', '14:10:00', '1', NULL, NULL, '108.3243', '-6.3271', NULL, 'Pulang', NULL, NULL, NULL),
('602', '2', '2026-03-17', '07:08:00', '0', NULL, NULL, '108.3243', '-6.3271', NULL, 'Hadir tepat waktu', NULL, NULL, 36.3),
('602', '2', '2026-03-17', '14:05:00', '1', NULL, NULL, '108.3243', '-6.3271', NULL, 'Pulang', NULL, NULL, NULL);

-- Guru absen (record absensi harian)
INSERT INTO `tweb_pegawai_absen` (`NO_INDUK`, `ID_PEGAWAI`, `TANGGAL_ABSEN`, `TGL_ABSEN`, `STATUS`, `STATUS2`, `KETERANGAN`, `POIN`, `JAM_MASUK_SETTING`, `JAM_PULANG_SETTING`, `STS`, `TAHUN`, `TANGGAL_AKSES`, `FILE`, `TANGGAL_APPROVE`) VALUES
('601', '1', '2026-03-17', '2026-03-17', 'H', NULL, 'Hadir', '10', '07:10:00', '14:05:00', NULL, '2026', NOW(), NULL, NULL),
('602', '2', '2026-03-17', '2026-03-17', 'H', NULL, 'Hadir', '10', '07:10:00', '14:05:00', NULL, '2026', NOW(), NULL, NULL);

-- ============================================================
-- STEP 11: ABSENSI SISWA (hadir & absen)
-- ============================================================

-- Siswa hadir (masuk & pulang)
INSERT INTO `t_siswa_hadir` (`id_siswa_hadir`, `id_siswa`, `id_tapel`, `tgl_hadir`, `sts_hadir`, `jam`) VALUES
(1, 1, 3, '2026-03-17', 0, '07:08:00'),
(2, 1, 3, '2026-03-17', 1, '14:05:00');

-- Siswa absen (sakit pada hari lain)
INSERT INTO `t_siswa_absen` (`id_siswa_absen`, `id_siswa`, `tgl_absen`, `sts_absen`, `ket_absen`, `id_tapel`, `tgl_entri`, `sts_approve`, `tgl_approve`, `file`) VALUES
(1, 1, '2026-03-18', 2, 'Flu dan demam', 3, '2026-03-18 08:00:00', 1, NULL, NULL);

-- ============================================================
-- STEP 12: ABSEN MENGAJAR (kegiatan mengajar)
-- ============================================================

INSERT INTO `absen_mengajar` (`id_mengajar`, `id_dosen`, `nm_kegiatan`, `catatan`, `foto`, `longtitude`, `tgl_entri`) VALUES
(1, 1, 'Mengajar Matematika X-1', 'Materi: Persamaan Linear', NULL, '108.3243,-6.3271', '2026-03-17');

-- ============================================================
-- STEP 13: JADWAL ABSEN & JADWAL KHUSUS
-- ============================================================

INSERT INTO `jadwal_absen` (`id_jadwal`, `id_ptk`, `tgl_jadwal`, `id_shift`, `bulan`) VALUES
(1, 1, '2026-03-17', 5, 3),
(2, 2, '2026-03-17', 5, 3);

INSERT INTO `jadwal_khusus` (`id_jadwal`, `id_ptk`, `Senin`, `Selasa`, `Rabu`, `Kamis`, `Jumat`, `Sabtu`, `Minggu`, `tgl_entri`) VALUES
(1, 1, 5, 5, 5, 5, 5, NULL, NULL, '2026-03-01');

-- ============================================================
-- STEP 14: LIBUR & LIBUR BESAR
-- ============================================================

INSERT INTO `libur` (`id_libur`, `id_ptk`, `Senin`, `Selasa`, `Rabu`, `Kamis`, `Jumat`, `Sabtu`, `Minggu`, `tgl_entri`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-01');

INSERT INTO `libur_besar` (`id_liburbesar`, `keterangan`, `tgl_libur`) VALUES
(1, 'Hari Raya Nyepi', '2026-03-29');

-- ============================================================
-- STEP 15: SHIFT ANGGOTA
-- ============================================================

INSERT INTO `t_anggota_shift` (`id_anggota_shift`, `id_ptk`, `id_shift`) VALUES
(1, 1, 5),
(2, 2, 5);

-- ============================================================
-- STEP 16: LEMBUR
-- ============================================================

INSERT INTO `t_lembur` (`id_lembur`, `id_ptk`, `tgl_lembur`, `tgl_approve`, `id_user`) VALUES
(1, 1, '2026-03-15', '2026-03-16', 1);

-- ============================================================
-- STEP 17: POINT GURU & SISWA
-- ============================================================

INSERT INTO `t_point` (`id_point`, `id_ptk`, `tgl_point`, `nilai`) VALUES
(1, 1, '2026-03-17', 10);

INSERT INTO `t_point_siswa` (`id_point`, `id_siswa`, `tgl_point`, `nilai`) VALUES
(1, 1, '2026-03-17', 8);

INSERT INTO `t_total_point` (`id_total_point`, `id_ptk`, `bln`, `thn`, `jml_point`) VALUES
(1, 1, 3, 2026, 10);

INSERT INTO `t_total_point_siswa` (`id_total_point`, `id_siswa`, `bln`, `id_tapel`, `jml_point`) VALUES
(1, 1, 3, 3, 8);

-- ============================================================
-- STEP 18: ATTENDANCE ALERTS (notifikasi kehadiran)
-- ============================================================

INSERT INTO `attendance_alerts` (`id`, `id_siswa`, `id_rombel`, `alert_type`, `alert_date`, `absence_count`, `absence_dates`, `status`, `notified_walikelas_at`, `notified_bk_at`, `resolved_at`, `resolved_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'consecutive', '2026-03-19', 2, '2026-03-18,2026-03-19', 'notified_walikelas', '2026-03-19 09:00:00', NULL, NULL, NULL, 'Siswa sakit flu 2 hari berturut-turut', '2026-03-19 08:30:00', '2026-03-19 09:00:00');

-- ============================================================
-- STEP 19: WA MESSAGE QUEUE (antrian pesan WA)
-- ============================================================

INSERT INTO `wa_message_queue` (`id`, `id_siswa`, `phone_number`, `recipient_name`, `message`, `message_type`, `status`, `scheduled_date`, `week_start`, `week_end`, `sent_at`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(1, 1, '082142986420', 'Orang Tua Sintia', '🏫 *SMKN 2 INDRAMAYU*\n\n📋 *LAPORAN KEHADIRAN MINGGUAN*\n\nYth. Orang Tua dari:\n👤 Sintia Dewi\n🆔 NIS: 15001\n🏫 Kelas: X-1\n\n📅 Periode: 17-21 Mar 2026\n\n✅ Senin 17 Mar - Hadir\n❌ Selasa 18 Mar - Sakit\n\nTerima kasih 🙏', 'weekly_report', 'pending', '2026-03-21', '2026-03-17', '2026-03-21', NULL, NULL, 0, NOW(), NOW());

-- ============================================================
-- STEP 20: WA SETTINGS
-- ============================================================

DELETE FROM `wa_settings`;
INSERT INTO `wa_settings` (`id`, `key`, `value`, `updated_at`) VALUES
(1, 'alert_template_walikelas', '🏫 *SMKN 2 INDRAMAYU*\n\n⚠️ *Peringatan Kehadiran Siswa*\n\nKepada Wali Kelas {kelas},\n\nSiswa berikut memerlukan perhatian:\n👤 Nama: {nama_siswa}\n🆔 NIS: {nis}\n🏫 Kelas: {kelas}\n\n{alert_description}\n\nMohon segera ditindaklanjuti.\nTerima kasih 🙏', NOW()),
(2, 'alert_template_bk', '🏫 *SMKN 2 INDRAMAYU*\n\n🚨 *Laporan Guru BK*\n\nSiswa berikut memerlukan perhatian khusus:\n👤 Nama: {nama_siswa}\n🆔 NIS: {nis}\n🏫 Kelas: {kelas}\n🧑‍🏫 Wali Kelas: {wali_kelas}\n\n{alert_description}\n\nMohon segera ditindaklanjuti.\nTerima kasih 🙏', NOW()),
(3, 'notification_paused', '0', NOW()),
(4, 'notification_pause_reason', '', NOW()),
(5, 'gateway_url', 'http://192.168.2.10:3000/', NOW()),
(6, 'basic_auth_user', 'admin', NOW()),
(7, 'basic_auth_pass', 'Passwordabsen', NOW()),
(8, 'sender_number_1', '628976660772', NOW()),
(9, 'sender_number_2', '628976664772', NOW()),
(10, 'device_id_1', '1ba08f71-31f9-4770-8f87-e9703aa45e8d', NOW()),
(11, 'device_id_2', '1ca08f71-31f9-4770-8f87-e9703aa45e8d', NOW()),
(12, 'message_delay', '30', NOW()),
(13, 'distribution_days', '7', NOW()),
(14, 'schedule_day', 'Friday', NOW()),
(15, 'schedule_time', '16:00', NOW()),
(16, 'message_template', '📋 *LAPORAN KEHADIRAN MINGGUAN*\n🏫 SMKN 2 INDRAMAYU\n\nYth. Orang Tua/Wali dari:\n👤 *{nama_siswa}*\n🆔 NIS: {nis}\n🏫 Kelas: *{kelas}*\n\n📅 Periode: {periode}\n\n{attendance_list}\n\n⚠️ = Terlambat\n❌ = Tidak Hadir\n\nTerima kasih atas perhatian Bapak/Ibu 🙏\n— *Sistem Absensi Digital*\n*SMKN 2 INDRAMAYU*', NOW()),
(29, 'gateway_status', 'disconnected', NOW());

-- ============================================================
-- STEP 21: COMMAND TABLES (1 dummy each)
-- ============================================================

INSERT INTO `tweb_command_adms` (`id`, `isi_command`, `status_command`, `id_user`, `ip_mesin`, `tanggal_input`, `tanggal_return`) VALUES
(1, 'RESTART', 'done', '1', '192.168.0.1', '2026-03-17 08:00:00', '2026-03-17 08:00:05');

INSERT INTO `tweb_log_command_adms` (`id`, `url`, `isi_command`, `return`, `cmd`, `serial_number`, `tanggal_akses`) VALUES
(1, 'http://192.168.0.1/iclock/getrequest', 'INFO', 'OK', 'INFO', 'CKO9232060131', '2026-03-17 08:00:00');

-- ============================================================
-- DONE! Reset AUTO_INCREMENT values
-- ============================================================

ALTER TABLE `absen_mengajar` MODIFY `id_mengajar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `attendance_alerts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `jadwal_absen` MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `jadwal_khusus` MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `libur` MODIFY `id_libur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `libur_besar` MODIFY `id_liburbesar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `r_hari` MODIFY `id_hari` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `r_jenis_ptk` MODIFY `id_jenis_ptk` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `r_shift` MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `r_tapel` MODIFY `id_tapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `r_tingkat_kelas` MODIFY `id_tingkat_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tweb_command_adms` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tweb_log_command_adms` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tweb_mesin` MODIFY `ID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `t_anggota_shift` MODIFY `id_anggota_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `t_lembur` MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_point` MODIFY `id_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_point_siswa` MODIFY `id_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_ptk` MODIFY `id_ptk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `t_rombel` MODIFY `id_rombel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_setting_aplikasi` MODIFY `id_setting` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_siswa` MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_siswa_absen` MODIFY `id_siswa_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_siswa_hadir` MODIFY `id_siswa_hadir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `t_siswa_rombel` MODIFY `id_siswa_rombel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_total_point` MODIFY `id_total_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_total_point_siswa` MODIFY `id_total_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `t_user` MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
ALTER TABLE `wa_message_queue` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `wa_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================
-- DATA SUMMARY
-- ============================================================
-- t_ptk (Guru):
--   id=1: Agung Saputra (Wali Kelas X-1, NIP: 198501012010011001)
--   id=2: Siti Rahayu   (Guru BK,        NIP: 199002152012022002)
--
-- t_rombel:
--   id=1: X-1, tapel 2025/2026, walikelas=Agung(1), guru_bk=Siti(2) ✅
--
-- t_siswa:
--   id=1: Sintia Dewi (NIS: 15001, Kelas X-1)
--
-- t_user:
--   admin (level 1), kepsek (level 4)
--
-- All other tables filled with 1 dummy row each.
-- ============================================================
