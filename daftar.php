<?php
header('Content-Type: application/json');

require_once 'config.php';

// Fungsi untuk mengirim response JSON dan menghentikan skrip
function send_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Fungsi untuk menangani upload file
function handle_upload($file_input_name, $upload_dir, $nik) {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File tidak diunggah atau terjadi error. Pastikan Anda telah memilih file.'];
    }

    $file = $_FILES[$file_input_name];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($file_ext, $allowed_ext)) {
        return ['error' => 'Format file tidak diizinkan. Hanya JPG, JPEG, PNG, dan PDF.'];
    }

    if ($file_size > 2 * 1024 * 1024) { // 2 MB
        return ['error' => 'Ukuran file tidak boleh lebih dari 2MB.'];
    }

    $new_file_name = $nik . '_' . uniqid() . '.' . $file_ext;
    $destination = $upload_dir . $new_file_name;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file_tmp, $destination)) {
        return ['path' => $destination];
    } else {
        return ['error' => 'Gagal memindahkan file yang diunggah.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response('error', 'Metode request tidak valid.');
}

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    send_response('error', 'Koneksi database gagal: ' . $conn->connect_error);
}

// Data Karyawan
$nama_karyawan = trim($_POST['nama_karyawan'] ?? '');
$nik_karyawan = trim($_POST['nik_karyawan'] ?? '');
$nomor_induk_karyawan = trim($_POST['nomor_induk_karyawan'] ?? '');
$no_hp_karyawan = trim($_POST['no_hp_karyawan'] ?? '');
$email_karyawan = filter_var(trim($_POST['email_karyawan'] ?? ''), FILTER_SANITIZE_EMAIL);
$alamat_karyawan = trim($_POST['alamat_karyawan'] ?? '');
$status_perkawinan = trim($_POST['status_perkawinan'] ?? '');
$penghasilan_sesuai = trim($_POST['penghasilan_sesuai'] ?? '');

// Validasi Data Wajib Karyawan
if (empty($nama_karyawan) || empty($nik_karyawan) || empty($nomor_induk_karyawan) || empty($no_hp_karyawan) || empty($alamat_karyawan) || empty($status_perkawinan) || empty($penghasilan_sesuai)) {
    send_response('error', 'Semua data karyawan wajib diisi.');
}
if (!preg_match('/^[0-9]{16}$/', $nik_karyawan)) {
    send_response('error', 'Format NIK Karyawan tidak valid (harus 16 digit angka).');
}

// Upload KTP Karyawan
$upload_karyawan = handle_upload('ktp_karyawan', UPLOAD_DIR_KARYAWAN, $nik_karyawan);
if (isset($upload_karyawan['error'])) {
    send_response('error', 'KTP Karyawan: ' . $upload_karyawan['error']);
}
$path_ktp_karyawan = $upload_karyawan['path'];

// Inisialisasi dan Proses Data Pasangan jika Menikah
$nama_pasangan = null;
$nik_pasangan = null;
$no_hp_pasangan = null;
$email_pasangan = null;
$alamat_pasangan = null;
$path_ktp_pasangan = null;

if ($status_perkawinan === 'menikah') {
    $nama_pasangan = trim($_POST['nama_pasangan'] ?? '');
    $nik_pasangan = trim($_POST['nik_pasangan'] ?? '');
    $no_hp_pasangan = trim($_POST['no_hp_pasangan'] ?? '');
    $email_pasangan = filter_var(trim($_POST['email_pasangan'] ?? ''), FILTER_SANITIZE_EMAIL);
    $alamat_pasangan = trim($_POST['alamat_pasangan'] ?? '');

    if (empty($nama_pasangan) || empty($nik_pasangan) || empty($no_hp_pasangan) || empty($alamat_pasangan)) {
        send_response('error', 'Semua data pasangan (kecuali email) wajib diisi jika status menikah.');
    }
    if (!preg_match('/^[0-9]{16}$/', $nik_pasangan)) {
        send_response('error', 'Format NIK Pasangan tidak valid (harus 16 digit angka).');
    }

    $upload_pasangan = handle_upload('ktp_pasangan', UPLOAD_DIR_PASANGAN, $nik_pasangan);
    if (isset($upload_pasangan['error'])) {
        send_response('error', 'KTP Pasangan: ' . $upload_pasangan['error']);
    }
    $path_ktp_pasangan = $upload_pasangan['path'];
}

// Insert ke Database
$sql = "INSERT INTO pendaftar (nama_karyawan, nik_karyawan, nomor_induk_karyawan, no_hp_karyawan, email_karyawan, alamat_karyawan, path_ktp_karyawan, status_perkawinan, penghasilan_sesuai, nama_pasangan, nik_pasangan, no_hp_pasangan, email_pasangan, alamat_pasangan, path_ktp_pasangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    send_response('error', 'Gagal menyiapkan statement database: ' . $conn->error);
}

$stmt->bind_param("sssssssssssssss", 
    $nama_karyawan, $nik_karyawan, $nomor_induk_karyawan, $no_hp_karyawan, $email_karyawan, $alamat_karyawan, $path_ktp_karyawan, 
    $status_perkawinan, $penghasilan_sesuai, 
    $nama_pasangan, $nik_pasangan, $no_hp_pasangan, $email_pasangan, $alamat_pasangan, $path_ktp_pasangan
);

if ($stmt->execute()) {
    send_response('success', 'Pendaftaran berhasil! Terima kasih telah mendaftar.');
} else {
    // Hapus file yang sudah terupload jika query gagal
    if (file_exists($path_ktp_karyawan)) unlink($path_ktp_karyawan);
    if ($path_ktp_pasangan && file_exists($path_ktp_pasangan)) unlink($path_ktp_pasangan);

    if ($conn->errno == 1062) {
        send_response('error', 'NIK Karyawan, NIK Perusahaan, atau NIK Pasangan sudah terdaftar.');
    } else {
        send_response('error', 'Gagal menyimpan data ke database: ' . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>
