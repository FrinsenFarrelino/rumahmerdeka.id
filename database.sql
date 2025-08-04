CREATE DATABASE IF NOT EXISTS db_rumah_merdeka;
USE db_rumah_merdeka;

-- Tabel untuk data pendaftar
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik_karyawan` (`nik_karyawan`),
  UNIQUE KEY `nomor_induk_karyawan` (`nomor_induk_karyawan`),
  UNIQUE KEY `nik_pasangan` (`nik_pasangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [NEW v1.12] Tabel untuk penghitung klik tombol
CREATE TABLE IF NOT EXISTS `button_clicks` (
  `button_id` varchar(50) NOT NULL,
  `click_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`button_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [NEW v1.12] Memasukkan data awal untuk tombol (opsional, tapi direkomendasikan)
INSERT INTO `button_clicks` (`button_id`, `click_count`) VALUES
('daftar_sekarang_btn', 0),
('saya_mau_daftar_btn', 0),
('submitBtn', 0)
ON DUPLICATE KEY UPDATE button_id=button_id; -- Lakukan apa-apa jika sudah ada


-- Tabel untuk pengguna admin backoffice
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Menambahkan pengguna admin default
-- Passwordnya adalah 'admin123', di-hash menggunakan SHA1 (tidak aman)
INSERT INTO `admin_users` (`username`, `password`) VALUES
('admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef')
ON DUPLICATE KEY UPDATE password='40bd001563085fc35165329ea1ff5c5ecbdbbeef';