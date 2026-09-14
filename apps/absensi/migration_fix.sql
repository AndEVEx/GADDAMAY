-- Migration fix for missing tables that cause SNAG errors
-- Run this on the production database

-- Create t_point_siswa table if it doesn't exist
CREATE TABLE IF NOT EXISTS `t_point_siswa` (
  `id_point_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) DEFAULT NULL,
  `tgl_point` date DEFAULT NULL,
  `nilai` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `id_tapel` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_point_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create t_siswa_absen table if it doesn't exist (for izin/sakit records)
CREATE TABLE IF NOT EXISTS `t_siswa_absen` (
  `id_siswa_absen` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) DEFAULT NULL,
  `tgl_absen` date DEFAULT NULL,
  `sts_absen` int(11) DEFAULT NULL COMMENT '2=Sakit, 3=Izin',
  `ket_absen` text DEFAULT NULL,
  `id_tapel` int(11) DEFAULT NULL,
  `tgl_entri` datetime DEFAULT NULL,
  `sts_approve` int(11) DEFAULT 0 COMMENT '0=Menunggu, 1=Disetujui, 2=Ditolak',
  PRIMARY KEY (`id_siswa_absen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
