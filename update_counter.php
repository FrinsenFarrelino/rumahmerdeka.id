<?php
require_once 'config.php';

header('Content-Type: application/json');

// Mendapatkan ID tombol dari request POST
$buttonId = $_POST['button_id'] ?? null;

if (!$buttonId) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Button ID tidak ditemukan.']);
    exit;
}

// Menyiapkan koneksi database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
    exit;
}

// Query SQL yang efisien untuk insert atau update
$sql = "INSERT INTO button_clicks (button_id, click_count) VALUES (?, 1)
        ON DUPLICATE KEY UPDATE click_count = click_count + 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement SQL.']);
    $conn->close();
    exit;
}

// Bind parameter ke statement
$stmt->bind_param("s", $buttonId);

// Eksekusi statement
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Counter berhasil diperbarui.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui counter.']);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>