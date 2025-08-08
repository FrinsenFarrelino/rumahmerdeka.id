<?php
// File: backoffice/api/assign_unit.php
session_start();
require_once '../../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

$participant_id = $_POST['participant_id'] ?? null;
$unit_id = $_POST['unit_id'] ?? null;

if (!$participant_id || !$unit_id) {
    echo json_encode(['success' => false, 'message' => 'ID Peserta atau ID Unit tidak lengkap.']);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}

// Memulai transaksi
$conn->begin_transaction();

try {
    // 1. Lepaskan unit lama jika ada (untuk kasus ubah unit)
    $stmt_release = $conn->prepare("UPDATE unit_stock SET id_pendaftar = NULL, status_unit = 'Belum Dikerjakan' WHERE id_pendaftar = ?");
    $stmt_release->bind_param("i", $participant_id);
    $stmt_release->execute();
    $stmt_release->close();

    // 2. Cek apakah unit yang baru sudah diambil orang lain
    $stmt_check = $conn->prepare("SELECT id_pendaftar FROM unit_stock WHERE id = ?");
    $stmt_check->bind_param("i", $unit_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result()->fetch_assoc();
    $stmt_check->close();

    if ($result_check && $result_check['id_pendaftar'] !== null) {
        throw new Exception('Unit ini sudah dipilih oleh peserta lain. Silakan muat ulang halaman.');
    }

    // 3. Update tabel pendaftar dengan id_unit yang baru
    $stmt_pendaftar = $conn->prepare("UPDATE pendaftar SET id_unit = ? WHERE id = ?");
    $stmt_pendaftar->bind_param("ii", $unit_id, $participant_id);
    $stmt_pendaftar->execute();
    $stmt_pendaftar->close();

    // 4. Update tabel unit_stock dengan id_pendaftar dan status baru
    $stmt_unit = $conn->prepare("UPDATE unit_stock SET id_pendaftar = ?, status_unit = 'Dipesan' WHERE id = ?");
    $stmt_unit->bind_param("ii", $participant_id, $unit_id);
    $stmt_unit->execute();
    $stmt_unit->close();

    // Jika semua berhasil, commit transaksi
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Unit berhasil dipilih!']);

} catch (Exception $e) {
    // Jika ada error, rollback semua perubahan
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}

$conn->close();
?>