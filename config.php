<?php
/**
 * Konfigurasi Database
 *
 * Ganti nilai-nilai di bawah ini sesuai dengan
 * konfigurasi server database Anda.
 */

// Aktifkan pelaporan error untuk debugging (nonaktifkan di production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definisikan konstanta untuk koneksi database
define('DB_HOST', 'srv1983.hstgr.io');      // Biasanya 'localhost'
define('DB_USER', 'u127142362_rmerdeka');           // User database Anda
define('DB_PASS', 'Godistheking1#');               // Password database Anda
define('DB_NAME', 'u127142362_rumah_merdeka'); // Nama database Anda

// define('DB_HOST', 'localhost');      // Biasanya 'localhost'
// define('DB_USER', 'root');           // User database Anda
// define('DB_PASS', '');               // Password database Anda
// define('DB_NAME', 'db_rumah_merdeka'); // Nama database Anda

// Direktori untuk menyimpan file yang di-upload
define('UPLOAD_DIR_KARYAWAN', 'uploads/ktp_karyawan/');
define('UPLOAD_DIR_PASANGAN', 'uploads/ktp_pasangan/');

// Buat koneksi ke database menggunakan MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Periksa koneksi
if ($conn->connect_error) {
    // Hentikan eksekusi dan tampilkan pesan error jika koneksi gagal
    // Ini adalah respons darurat jika database tidak bisa diakses sama sekali.
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi ke database gagal: ' . $conn->connect_error
    ]);
    die();
}

// Set karakter set ke utf8mb4 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4");

?>
