-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 11, 2026 at 05:01 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absen_smk2_indramayu`
--

-- --------------------------------------------------------

--
-- Table structure for table `absen_mengajar`
--

CREATE TABLE `absen_mengajar` (
  `id_mengajar` int(11) NOT NULL,
  `id_dosen` int(11) NOT NULL,
  `nm_kegiatan` varchar(200) NOT NULL,
  `catatan` longtext NOT NULL,
  `foto` varchar(300) DEFAULT NULL,
  `longtitude` longtext NOT NULL,
  `tgl_entri` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_absen`
--

CREATE TABLE `jadwal_absen` (
  `id_jadwal` int(11) NOT NULL,
  `id_ptk` int(11) DEFAULT NULL,
  `tgl_jadwal` date DEFAULT NULL,
  `id_shift` int(11) DEFAULT NULL,
  `bulan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_khusus`
--

CREATE TABLE `jadwal_khusus` (
  `id_jadwal` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `Senin` int(11) DEFAULT NULL,
  `Selasa` int(11) DEFAULT NULL,
  `Rabu` int(11) DEFAULT NULL,
  `Kamis` int(11) DEFAULT NULL,
  `Jumat` int(11) DEFAULT NULL,
  `Sabtu` int(11) DEFAULT NULL,
  `Minggu` int(11) DEFAULT NULL,
  `tgl_entri` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libur`
--

CREATE TABLE `libur` (
  `id_libur` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `Senin` int(11) DEFAULT NULL,
  `Selasa` int(11) DEFAULT NULL,
  `Rabu` int(11) DEFAULT NULL,
  `Kamis` int(11) DEFAULT NULL,
  `Jumat` int(11) DEFAULT NULL,
  `Sabtu` int(11) DEFAULT NULL,
  `Minggu` int(11) DEFAULT NULL,
  `tgl_entri` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libur_besar`
--

CREATE TABLE `libur_besar` (
  `id_liburbesar` int(11) NOT NULL,
  `keterangan` varchar(100) DEFAULT NULL,
  `tgl_libur` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `r_hari`
--

CREATE TABLE `r_hari` (
  `id_hari` int(11) NOT NULL,
  `nm_hari` varchar(20) NOT NULL,
  `sts_hari` int(11) NOT NULL,
  `jammasuk` time DEFAULT NULL,
  `jampulang` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `r_hari`
--

INSERT INTO `r_hari` (`id_hari`, `nm_hari`, `sts_hari`, `jammasuk`, `jampulang`) VALUES
(1, 'Senin', 1, '07:10:00', '14:05:00'),
(2, 'Selasa', 1, '07:10:00', '14:05:00'),
(3, 'Rabu', 1, '07:10:00', '14:05:00'),
(4, 'Kamis', 1, '07:10:00', '14:05:00'),
(5, 'Jumat', 2, '07:10:00', '00:00:00'),
(6, 'Sabtu', 1, '08:10:00', '14:05:00'),
(7, 'Minggu', 1, '07:10:00', '14:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `r_jenis_ptk`
--

CREATE TABLE `r_jenis_ptk` (
  `id_jenis_ptk` tinyint(4) NOT NULL,
  `nama_jenis_ptk` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `r_jenis_ptk`
--

INSERT INTO `r_jenis_ptk` (`id_jenis_ptk`, `nama_jenis_ptk`) VALUES
(1, 'Guru'),
(2, 'Pegawai');

-- --------------------------------------------------------

--
-- Table structure for table `r_shift`
--

CREATE TABLE `r_shift` (
  `id_shift` int(11) NOT NULL,
  `nm_shift` varchar(20) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `r_shift`
--

INSERT INTO `r_shift` (`id_shift`, `nm_shift`, `jam_masuk`, `jam_pulang`) VALUES
(4, 'Shift Pagi Satpam', '07:00:00', '19:00:00'),
(5, 'Shift Regular', '07:00:00', '15:30:00'),
(7, 'Shift Ramadhan', '07:35:00', '14:00:00'),
(8, 'Shift Malam Satpam', '19:00:00', '07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `r_tapel`
--

CREATE TABLE `r_tapel` (
  `id_tapel` int(11) NOT NULL,
  `nm_tapel` varchar(20) NOT NULL,
  `sts_aktif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `r_tapel`
--

INSERT INTO `r_tapel` (`id_tapel`, `nm_tapel`, `sts_aktif`) VALUES
(2, '2024/2025', 0),
(3, '2025/2026', 1);

-- --------------------------------------------------------

--
-- Table structure for table `r_tingkat_kelas`
--

CREATE TABLE `r_tingkat_kelas` (
  `id_tingkat_kelas` int(11) NOT NULL,
  `nm_tingkat_kelas` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `r_tingkat_kelas`
--

INSERT INTO `r_tingkat_kelas` (`id_tingkat_kelas`, `nm_tingkat_kelas`) VALUES
(1, '10'),
(2, '11'),
(3, '12');

-- --------------------------------------------------------

--
-- Table structure for table `tweb_command_adms`
--

CREATE TABLE `tweb_command_adms` (
  `id` int(11) NOT NULL,
  `isi_command` text DEFAULT NULL,
  `status_command` varchar(50) DEFAULT NULL,
  `id_user` varchar(50) DEFAULT NULL,
  `ip_mesin` varchar(50) DEFAULT NULL,
  `tanggal_input` datetime DEFAULT NULL,
  `tanggal_return` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tweb_log_command_adms`
--

CREATE TABLE `tweb_log_command_adms` (
  `id` int(11) NOT NULL,
  `url` text DEFAULT NULL,
  `isi_command` text DEFAULT NULL,
  `return` varchar(50) DEFAULT NULL,
  `cmd` varchar(50) DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `tanggal_akses` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tweb_mesin`
--

CREATE TABLE `tweb_mesin` (
  `ID` smallint(6) NOT NULL,
  `NOMOR_IP` varchar(16) NOT NULL,
  `NAMA` varchar(30) DEFAULT NULL,
  `SERIAL_NUMBER` varchar(100) DEFAULT NULL,
  `ERRORDELAY` int(11) DEFAULT 0,
  `DELAY` int(11) DEFAULT 0,
  `TRANSTIMES1` varchar(10) DEFAULT NULL,
  `TRANSTIMES2` varchar(10) DEFAULT NULL,
  `TANGGAL_SINC` datetime DEFAULT NULL,
  `MEREK_MESIN` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tweb_mesin`
--

INSERT INTO `tweb_mesin` (`ID`, `NOMOR_IP`, `NAMA`, `SERIAL_NUMBER`, `ERRORDELAY`, `DELAY`, `TRANSTIMES1`, `TRANSTIMES2`, `TANGGAL_SINC`, `MEREK_MESIN`) VALUES
(15, '192.168.0.1', 'Mesin 1', 'CKO9232060131', 60, 60, NULL, NULL, '2023-09-02 12:53:35', 2),
(19, '192.168.0.2', 'Mesin 2', 'ADGE233560712', 60, 60, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tweb_pegawai_absen`
--

CREATE TABLE `tweb_pegawai_absen` (
  `NO_INDUK` varchar(30) NOT NULL,
  `ID_PEGAWAI` varchar(40) DEFAULT NULL,
  `TANGGAL_ABSEN` date NOT NULL,
  `TGL_ABSEN` date DEFAULT NULL,
  `STATUS` varchar(1) NOT NULL,
  `STATUS2` char(1) DEFAULT NULL,
  `KETERANGAN` varchar(50) DEFAULT NULL,
  `POIN` varchar(50) DEFAULT NULL,
  `JAM_MASUK_SETTING` varchar(50) DEFAULT NULL,
  `JAM_PULANG_SETTING` varchar(50) DEFAULT NULL,
  `STS` char(1) DEFAULT NULL,
  `TAHUN` varchar(10) DEFAULT NULL,
  `TANGGAL_AKSES` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FILE` longtext DEFAULT NULL,
  `TANGGAL_APPROVE` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tweb_pegawai_hadir`
--

CREATE TABLE `tweb_pegawai_hadir` (
  `NO_INDUK` varchar(40) NOT NULL,
  `ID_PEGAWAI` varchar(100) DEFAULT NULL,
  `TANGGAL` date NOT NULL,
  `JAM` time NOT NULL,
  `STATUS` char(2) NOT NULL COMMENT '1:Hadir 2:Tidak Hadir',
  `STS` char(1) DEFAULT NULL,
  `NO_MESIN` varchar(50) DEFAULT NULL,
  `LON` varchar(150) DEFAULT NULL,
  `LAT` varchar(150) DEFAULT NULL,
  `JAM_SETTING` varchar(150) DEFAULT NULL,
  `KETERANGAN` text DEFAULT NULL,
  `KETERANGAN2` text DEFAULT NULL,
  `FILENAME` text DEFAULT NULL,
  `SUHU` decimal(10,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tweb_pegawai_hadir`
--

INSERT INTO `tweb_pegawai_hadir` (`NO_INDUK`, `ID_PEGAWAI`, `TANGGAL`, `JAM`, `STATUS`, `STS`, `NO_MESIN`, `LON`, `LAT`, `JAM_SETTING`, `KETERANGAN`, `KETERANGAN2`, `FILENAME`, `SUHU`) VALUES
('601', '1', '2025-10-27', '07:00:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_anggota_shift`
--

CREATE TABLE `t_anggota_shift` (
  `id_anggota_shift` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `id_shift` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_lembur`
--

CREATE TABLE `t_lembur` (
  `id_lembur` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `tgl_lembur` date NOT NULL,
  `tgl_approve` date NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_point`
--

CREATE TABLE `t_point` (
  `id_point` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `tgl_point` date NOT NULL,
  `nilai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_point`
--

INSERT INTO `t_point` (`id_point`, `id_ptk`, `tgl_point`, `nilai`) VALUES
(1, 1, '2025-10-27', 10);

-- --------------------------------------------------------

--
-- Table structure for table `t_point_siswa`
--

CREATE TABLE `t_point_siswa` (
  `id_point` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `tgl_point` date NOT NULL,
  `nilai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_ptk`
--

CREATE TABLE `t_ptk` (
  `id_ptk` int(11) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nik` varchar(18) DEFAULT NULL,
  `nuptk` varchar(18) DEFAULT NULL,
  `nama_ptk` varchar(60) NOT NULL,
  `nama_panggilan` varchar(60) NOT NULL,
  `id_jenis_ptk` int(11) DEFAULT NULL,
  `id_divisi` int(11) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `status_ptk` int(11) DEFAULT NULL,
  `kode_tahun_ajaran` varchar(50) DEFAULT NULL,
  `kd_jenis_kelamin` enum('1','2') NOT NULL,
  `alamat` text NOT NULL,
  `nomor_rfid` varchar(50) DEFAULT NULL,
  `nomor_absensi` varchar(50) DEFAULT NULL,
  `token_chat` text DEFAULT NULL,
  `urut` varchar(50) DEFAULT NULL,
  `nominal` int(11) DEFAULT NULL,
  `photo` varchar(150) DEFAULT NULL,
  `password` varchar(300) NOT NULL,
  `KETERANGAN_ABSEN` text DEFAULT NULL,
  `status_absensi` enum('0','1') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `tgl_join` date DEFAULT NULL,
  `batas_cuti` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `t_ptk`
--

INSERT INTO `t_ptk` (`id_ptk`, `nip`, `nik`, `nuptk`, `nama_ptk`, `nama_panggilan`, `id_jenis_ptk`, `id_divisi`, `no_hp`, `email`, `status_ptk`, `kode_tahun_ajaran`, `kd_jenis_kelamin`, `alamat`, `nomor_rfid`, `nomor_absensi`, `token_chat`, `urut`, `nominal`, `photo`, `password`, `KETERANGAN_ABSEN`, `status_absensi`, `tempat_lahir`, `tgl_lahir`, `tgl_join`, `batas_cuti`) VALUES
(1, '321321', NULL, NULL, 'Agung', 'Saputra', 1, NULL, '082142986420', NULL, 1, NULL, '1', 'Jln. Dr. Sutomo III A No. 35 Gresik', NULL, '601', NULL, NULL, NULL, '1761539126_64f541fda82067a4c0ac.jpg', '$2y$10$bAK242Es82RHfoYBHa203.zEPbWWg5BfzxHSWp7aqM8v0ypf.uLNi', NULL, '0', 'GRESIK', '2025-09-30', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_rombel`
--

CREATE TABLE `t_rombel` (
  `id_rombel` int(11) NOT NULL,
  `id_tingkat_kelas` int(11) DEFAULT NULL,
  `nm_rombel` varchar(10) DEFAULT NULL,
  `id_tapel` int(11) DEFAULT NULL,
  `id_walikelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_rombel`
--

INSERT INTO `t_rombel` (`id_rombel`, `id_tingkat_kelas`, `nm_rombel`, `id_tapel`, `id_walikelas`) VALUES
(1, 1, 'X-1', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `t_setting_aplikasi`
--

CREATE TABLE `t_setting_aplikasi` (
  `id_setting` int(2) NOT NULL,
  `nm_aplikasi` varchar(50) NOT NULL,
  `file` longtext DEFAULT NULL,
  `nm_sekolah` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `nm_kepsek` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_setting_aplikasi`
--

INSERT INTO `t_setting_aplikasi` (`id_setting`, `nm_aplikasi`, `file`, `nm_sekolah`, `alamat`, `nm_kepsek`) VALUES
(1, 'Absensi Online', '1761538057_e8c7375f345f0de3e9ec.png', 'SMKN 4 Penajam Paser Utara', 'Penajam Paser Utara, Provinsi Kalimantan Timur', 'Drs. Arjuno Wibisono');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id_siswa` int(11) NOT NULL,
  `no_induk` int(11) DEFAULT NULL,
  `nisn` int(11) DEFAULT NULL,
  `rfid` varchar(100) DEFAULT NULL,
  `nm_siswa` varchar(100) DEFAULT NULL,
  `alamat` longtext DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jk` int(11) DEFAULT NULL,
  `hp` varchar(100) DEFAULT NULL,
  `sts_siswa` int(11) DEFAULT NULL,
  `file` longtext DEFAULT NULL,
  `password` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa`
--

INSERT INTO `t_siswa` (`id_siswa`, `no_induk`, `nisn`, `rfid`, `nm_siswa`, `alamat`, `tempat_lahir`, `tgl_lahir`, `jk`, `hp`, `sts_siswa`, `file`, `password`) VALUES
(1, 15001, 15001, '34242', 'Sintia Dewi', 'Jl. Anggresk 8 Nganjuk', 'Surabaya', '2025-10-07', 2, '082142986420', 1, '1761539568_d866f2dedba6b022981d.png', '$2y$10$MH74qK5Us8twGHEcL4EBAeyxBH5M489rzIWdYZXE3hOXk6vq4vAF2');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa_absen`
--

CREATE TABLE `t_siswa_absen` (
  `id_siswa_absen` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `tgl_absen` date DEFAULT NULL,
  `sts_absen` int(11) DEFAULT NULL,
  `ket_absen` longtext DEFAULT NULL,
  `id_tapel` int(11) DEFAULT NULL,
  `tgl_entri` datetime DEFAULT NULL,
  `sts_approve` int(11) DEFAULT NULL,
  `tgl_approve` datetime DEFAULT NULL,
  `file` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa_absen`
--

INSERT INTO `t_siswa_absen` (`id_siswa_absen`, `id_siswa`, `tgl_absen`, `sts_absen`, `ket_absen`, `id_tapel`, `tgl_entri`, `sts_approve`, `tgl_approve`, `file`) VALUES
(1, 1, '2025-10-27', 2, 'flu', 3, '2025-10-27 04:39:39', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa_hadir`
--

CREATE TABLE `t_siswa_hadir` (
  `id_siswa_hadir` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_tapel` int(11) DEFAULT NULL,
  `tgl_hadir` date DEFAULT NULL,
  `sts_hadir` int(11) DEFAULT NULL,
  `jam` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa_hadir`
--

INSERT INTO `t_siswa_hadir` (`id_siswa_hadir`, `id_siswa`, `id_tapel`, `tgl_hadir`, `sts_hadir`, `jam`) VALUES
(1, 1, 3, '2025-10-29', 0, '16:40:59'),
(2, 1, 3, '2025-10-30', 0, '19:24:33'),
(3, 1, 3, '2025-10-30', 1, '19:24:45'),
(4, 1, 3, '2025-10-31', 0, '10:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa_rombel`
--

CREATE TABLE `t_siswa_rombel` (
  `id_siswa_rombel` int(11) NOT NULL,
  `id_tapel` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_rombel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa_rombel`
--

INSERT INTO `t_siswa_rombel` (`id_siswa_rombel`, `id_tapel`, `id_siswa`, `id_rombel`) VALUES
(1, 3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `t_total_point`
--

CREATE TABLE `t_total_point` (
  `id_total_point` int(11) NOT NULL,
  `id_ptk` int(11) NOT NULL,
  `bln` int(11) NOT NULL,
  `thn` int(11) NOT NULL,
  `jml_point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_total_point`
--

INSERT INTO `t_total_point` (`id_total_point`, `id_ptk`, `bln`, `thn`, `jml_point`) VALUES
(1, 1, 10, 2025, 10);

-- --------------------------------------------------------

--
-- Table structure for table `t_total_point_siswa`
--

CREATE TABLE `t_total_point_siswa` (
  `id_total_point` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `bln` int(11) NOT NULL,
  `id_tapel` int(11) NOT NULL,
  `jml_point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_user`
--

CREATE TABLE `t_user` (
  `id_user` int(5) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(300) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `level` int(2) NOT NULL,
  `foto` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_user`
--

INSERT INTO `t_user` (`id_user`, `username`, `password`, `nama`, `level`, `foto`) VALUES
(1, 'admin', '$2y$10$xsgHJP9JI4gj5LBGSIZrrOVm26kNYMC19uQrR7vvDqYskzwAZ5qka', 'Super Admin', 1, '1721215046_327c8620e13c6f99cd18.png'),
(25, 'kepsek', '$2y$10$xsgHJP9JI4gj5LBGSIZrrOVm26kNYMC19uQrR7vvDqYskzwAZ5qka', 'Kepala Madrasah', 4, '1725176043_fcada036b0b7e1d4b49b.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absen_mengajar`
--
ALTER TABLE `absen_mengajar`
  ADD PRIMARY KEY (`id_mengajar`);

--
-- Indexes for table `jadwal_absen`
--
ALTER TABLE `jadwal_absen`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indexes for table `jadwal_khusus`
--
ALTER TABLE `jadwal_khusus`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indexes for table `libur`
--
ALTER TABLE `libur`
  ADD PRIMARY KEY (`id_libur`);

--
-- Indexes for table `libur_besar`
--
ALTER TABLE `libur_besar`
  ADD PRIMARY KEY (`id_liburbesar`);

--
-- Indexes for table `r_hari`
--
ALTER TABLE `r_hari`
  ADD PRIMARY KEY (`id_hari`);

--
-- Indexes for table `r_jenis_ptk`
--
ALTER TABLE `r_jenis_ptk`
  ADD PRIMARY KEY (`id_jenis_ptk`);

--
-- Indexes for table `r_shift`
--
ALTER TABLE `r_shift`
  ADD PRIMARY KEY (`id_shift`);

--
-- Indexes for table `r_tapel`
--
ALTER TABLE `r_tapel`
  ADD PRIMARY KEY (`id_tapel`);

--
-- Indexes for table `r_tingkat_kelas`
--
ALTER TABLE `r_tingkat_kelas`
  ADD PRIMARY KEY (`id_tingkat_kelas`);

--
-- Indexes for table `tweb_command_adms`
--
ALTER TABLE `tweb_command_adms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tweb_log_command_adms`
--
ALTER TABLE `tweb_log_command_adms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tweb_mesin`
--
ALTER TABLE `tweb_mesin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tweb_pegawai_absen`
--
ALTER TABLE `tweb_pegawai_absen`
  ADD PRIMARY KEY (`NO_INDUK`,`TANGGAL_ABSEN`,`STATUS`);

--
-- Indexes for table `tweb_pegawai_hadir`
--
ALTER TABLE `tweb_pegawai_hadir`
  ADD PRIMARY KEY (`NO_INDUK`,`TANGGAL`,`STATUS`) USING BTREE;

--
-- Indexes for table `t_anggota_shift`
--
ALTER TABLE `t_anggota_shift`
  ADD PRIMARY KEY (`id_anggota_shift`);

--
-- Indexes for table `t_lembur`
--
ALTER TABLE `t_lembur`
  ADD PRIMARY KEY (`id_lembur`),
  ADD UNIQUE KEY `id_ptk` (`id_ptk`,`tgl_lembur`);

--
-- Indexes for table `t_point`
--
ALTER TABLE `t_point`
  ADD PRIMARY KEY (`id_point`),
  ADD UNIQUE KEY `id_ptk` (`id_ptk`,`tgl_point`);

--
-- Indexes for table `t_point_siswa`
--
ALTER TABLE `t_point_siswa`
  ADD PRIMARY KEY (`id_point`);

--
-- Indexes for table `t_ptk`
--
ALTER TABLE `t_ptk`
  ADD PRIMARY KEY (`id_ptk`);

--
-- Indexes for table `t_rombel`
--
ALTER TABLE `t_rombel`
  ADD PRIMARY KEY (`id_rombel`);

--
-- Indexes for table `t_setting_aplikasi`
--
ALTER TABLE `t_setting_aplikasi`
  ADD PRIMARY KEY (`id_setting`);

--
-- Indexes for table `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indexes for table `t_siswa_absen`
--
ALTER TABLE `t_siswa_absen`
  ADD PRIMARY KEY (`id_siswa_absen`);

--
-- Indexes for table `t_siswa_hadir`
--
ALTER TABLE `t_siswa_hadir`
  ADD PRIMARY KEY (`id_siswa_hadir`),
  ADD UNIQUE KEY `id_siswa` (`id_siswa`,`id_tapel`,`tgl_hadir`,`sts_hadir`);

--
-- Indexes for table `t_siswa_rombel`
--
ALTER TABLE `t_siswa_rombel`
  ADD PRIMARY KEY (`id_siswa_rombel`);

--
-- Indexes for table `t_total_point`
--
ALTER TABLE `t_total_point`
  ADD PRIMARY KEY (`id_total_point`);

--
-- Indexes for table `t_total_point_siswa`
--
ALTER TABLE `t_total_point_siswa`
  ADD PRIMARY KEY (`id_total_point`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absen_mengajar`
--
ALTER TABLE `absen_mengajar`
  MODIFY `id_mengajar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_absen`
--
ALTER TABLE `jadwal_absen`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_khusus`
--
ALTER TABLE `jadwal_khusus`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libur`
--
ALTER TABLE `libur`
  MODIFY `id_libur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libur_besar`
--
ALTER TABLE `libur_besar`
  MODIFY `id_liburbesar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `r_hari`
--
ALTER TABLE `r_hari`
  MODIFY `id_hari` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `r_jenis_ptk`
--
ALTER TABLE `r_jenis_ptk`
  MODIFY `id_jenis_ptk` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `r_shift`
--
ALTER TABLE `r_shift`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `r_tapel`
--
ALTER TABLE `r_tapel`
  MODIFY `id_tapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `r_tingkat_kelas`
--
ALTER TABLE `r_tingkat_kelas`
  MODIFY `id_tingkat_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tweb_command_adms`
--
ALTER TABLE `tweb_command_adms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tweb_log_command_adms`
--
ALTER TABLE `tweb_log_command_adms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tweb_mesin`
--
ALTER TABLE `tweb_mesin`
  MODIFY `ID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `t_anggota_shift`
--
ALTER TABLE `t_anggota_shift`
  MODIFY `id_anggota_shift` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_lembur`
--
ALTER TABLE `t_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `t_point`
--
ALTER TABLE `t_point`
  MODIFY `id_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_point_siswa`
--
ALTER TABLE `t_point_siswa`
  MODIFY `id_point` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_ptk`
--
ALTER TABLE `t_ptk`
  MODIFY `id_ptk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_rombel`
--
ALTER TABLE `t_rombel`
  MODIFY `id_rombel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_setting_aplikasi`
--
ALTER TABLE `t_setting_aplikasi`
  MODIFY `id_setting` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_siswa_absen`
--
ALTER TABLE `t_siswa_absen`
  MODIFY `id_siswa_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_siswa_hadir`
--
ALTER TABLE `t_siswa_hadir`
  MODIFY `id_siswa_hadir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_siswa_rombel`
--
ALTER TABLE `t_siswa_rombel`
  MODIFY `id_siswa_rombel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_total_point`
--
ALTER TABLE `t_total_point`
  MODIFY `id_total_point` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_total_point_siswa`
--
ALTER TABLE `t_total_point_siswa`
  MODIFY `id_total_point` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_user`
--
ALTER TABLE `t_user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
