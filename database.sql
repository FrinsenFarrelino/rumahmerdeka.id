CREATE DATABASE IF NOT EXISTS db_rumah_merdeka;
USE db_rumah_merdeka;

-- Tabel untuk menyimpan data pendaftar program
CREATE TABLE IF NOT EXISTS `pendaftar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(255) NOT NULL,
  `nik_karyawan` varchar(16) NOT NULL,
  `nomor_induk_karyawan` varchar(100) NOT NULL,
  `no_hp_karyawan` varchar(20) NOT NULL,
  `email_karyawan` varchar(255) DEFAULT NULL,
  `alamat_karyawan` text NOT NULL,
  `path_ktp_karyawan` varchar(255) NOT NULL,
  `status_perkawinan` enum('lajang','menikah') NOT NULL,
  `penghasilan_sesuai` enum('ya','tidak') NOT NULL,
  `nama_pasangan` varchar(255) DEFAULT NULL,
  `nik_pasangan` varchar(16) DEFAULT NULL,
  `no_hp_pasangan` varchar(20) DEFAULT NULL,
  `email_pasangan` varchar(255) DEFAULT NULL,
  `alamat_pasangan` text DEFAULT NULL,
  `path_ktp_pasangan` varchar(255) DEFAULT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  
  -- [KOLOM BARU v1.2] --
  `path_sikasep_1` varchar(255) DEFAULT NULL,
  `path_sikasep_2` varchar(255) DEFAULT NULL,
  `slik_bi_checking` text DEFAULT NULL,
  `status_proses` enum(
      '', 'Proses BI Checking', 'Reject BI Checking', 'Pemberkasan', 
      'Pengajuan Kredit Bank', 'Terbit SP3K', 'Reject Bank', 
      'Siap AKAD', 'Sudah AKAD'
  ) NOT NULL DEFAULT '',
  `status_data` enum('active','inactive') NOT NULL DEFAULT 'active',
  ------------------------

  PRIMARY KEY (`id`),
  UNIQUE KEY `nik_karyawan` (`nik_karyawan`),
  UNIQUE KEY `nomor_induk_karyawan` (`nomor_induk_karyawan`),
  UNIQUE KEY `nik_pasangan` (`nik_pasangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel untuk menghitung jumlah klik dan page views
CREATE TABLE IF NOT EXISTS `button_clicks` (
  `button_id` varchar(50) NOT NULL,
  `click_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`button_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel untuk pengguna admin backoffice
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','adminRMP','viewRMP') NOT NULL DEFAULT 'viewRMP',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABEL BARU UNTUK STOK UNIT
CREATE TABLE IF NOT EXISTS `unit_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blok` varchar(10) NOT NULL,
  `no_unit` varchar(20) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `lt_lb` varchar(20) NOT NULL COMMENT 'Luas Tanah / Luas Bangunan',
  `status_unit` varchar(100) NOT NULL,
  `id_pendaftar` int(11) DEFAULT NULL COMMENT 'FK ke tabel pendaftar',
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_unit` (`no_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- MENGUBAH TABEL PENDAFTAR
-- Cek jika kolom belum ada sebelum menambahkan
ALTER TABLE `pendaftar` ADD COLUMN IF NOT EXISTS `id_unit` INT(11) DEFAULT NULL AFTER `status_data`;
