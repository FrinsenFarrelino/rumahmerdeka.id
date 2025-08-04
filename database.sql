-- Pastikan Anda sudah membuat database dengan nama `db_rumah_merdeka`
-- CREATE DATABASE db_rumah_merdeka;
-- USE db_rumah_merdeka;

-- Hapus tabel jika sudah ada (untuk setup ulang)
DROP TABLE IF EXISTS `pendaftar`;

-- Buat tabel `pendaftar`
CREATE TABLE `pendaftar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(255) NOT NULL,
  `nik_karyawan` varchar(16) NOT NULL,
  `nomor_induk_karyawan` varchar(100) NOT NULL,
  `no_hp_karyawan` varchar(20) NOT NULL,
  `email_karyawan` varchar(255) DEFAULT NULL,
  `alamat_karyawan` text NOT NULL,
  `path_ktp_karyawan` varchar(255) NOT NULL,
  `status_perkawinan` enum('lajang','menikah') NOT NULL,
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
