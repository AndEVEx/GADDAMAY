-- ===========================================
-- Migration: Murid Monitoring System
-- Date: 2026-03-23
-- ===========================================

-- Add Guru BK column to t_rombel
ALTER TABLE `t_rombel` ADD `id_guru_bk` INT(11) DEFAULT NULL AFTER `id_walikelas`;

-- Student monitoring cases
CREATE TABLE `t_murid_monitoring` (
  `id_monitoring` INT(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` INT(11) NOT NULL,
  `id_rombel` INT(11) NOT NULL,
  `id_tapel` INT(11) NOT NULL,
  `alasan` TEXT NOT NULL,
  `current_step` INT(1) NOT NULL DEFAULT 1,
  `status` ENUM('active','resolved') NOT NULL DEFAULT 'active',
  `created_by` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_monitoring`),
  KEY `idx_siswa` (`id_siswa`),
  KEY `idx_rombel` (`id_rombel`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Progress steps (1-4) with proof files
CREATE TABLE `t_monitoring_progress` (
  `id_progress` INT(11) NOT NULL AUTO_INCREMENT,
  `id_monitoring` INT(11) NOT NULL,
  `step` INT(1) NOT NULL,
  `is_done` TINYINT(1) NOT NULL DEFAULT 0,
  `file_bukti` VARCHAR(300) DEFAULT NULL,
  `file_type` ENUM('image','pdf') DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `done_by` INT(11) DEFAULT NULL,
  `done_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_progress`),
  KEY `idx_monitoring` (`id_monitoring`),
  UNIQUE KEY `uk_monitoring_step` (`id_monitoring`, `step`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
