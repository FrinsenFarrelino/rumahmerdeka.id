<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: index.php?error=Username dan password wajib diisi.');
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    header('Location: index.php?error=Koneksi database gagal.');
    exit;
}

// [UPDATE v1.5] Mengambil password dan role
$sql = "SELECT password, role FROM admin_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Peringatan: SHA1 tidak aman. Gunakan password_hash() dan password_verify() di proyek nyata.
    if ($user['password'] === sha1($password)) {
        // Password benar
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role']; // [UPDATE v1.5] Simpan role ke session

        // Redirect ke halaman default setelah login
        header('Location: dashboard.php');
        exit;
    }
}

// Password salah atau user tidak ditemukan
header('Location: index.php?error=Username atau password salah.');

$stmt->close();
$conn->close();
?>