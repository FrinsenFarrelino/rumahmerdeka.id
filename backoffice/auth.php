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

$sql = "SELECT password FROM admin_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // PERINGATAN KEAMANAN: SHA1 tidak direkomendasikan untuk hashing password.
    // Metode ini rentan terhadap serangan. Gunakan password_verify() untuk keamanan yang lebih baik.
    if ($user['password'] === sha1($password)) {
        // Password benar
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit;
    }
}

// Password salah atau user tidak ditemukan
header('Location: index.php?error=Username atau password salah.');

$stmt->close();
$conn->close();
?>