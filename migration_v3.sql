-- Migration v3
-- Memperbaiki tabel t_ptk (Hapus RFID/Finger)
ALTER TABLE `t_ptk` DROP COLUMN IF EXISTS `nomor_absensi`;
ALTER TABLE `t_ptk` DROP COLUMN IF EXISTS `jenis_rfid`;
ALTER TABLE `t_ptk` DROP COLUMN IF EXISTS `rfid`;
-- Pastikan tabel wa_settings dan t_murid_monitoring ada (gabungan dari migrasi sebelumnya agar aman jika belum jalan)
CREATE TABLE IF NOT EXISTS `wa_settings` (
  `key` varchar(50) NOT NULL,
  `value` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `t_murid_monitoring` (
  `id_monitoring` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) NOT NULL,
  `id_tapel` int(11) NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('Aktif','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id_monitoring`),
  KEY `id_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `t_murid_monitoring_progress` (
  `id_progress` int(11) NOT NULL AUTO_INCREMENT,
  `id_monitoring` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  `catatan` text,
  `is_done` tinyint(1) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_progress`),
  KEY `id_monitoring` (`id_monitoring`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Hapus kolom password pada t_siswa sesuai permintaan
ALTER TABLE `t_siswa` DROP COLUMN `password`;
-- Menghapus system Point
DROP TABLE IF EXISTS `t_point`;
DROP TABLE IF EXISTS `t_point_siswa`;
DROP TABLE IF EXISTS `t_total_point_siswa`;
DROP TABLE IF EXISTS `t_total_point`;
