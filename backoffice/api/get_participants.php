<?php
// File: backoffice/api/get_participants.php
require_once '../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nilai filter. Defaultnya string kosong jika tidak ada.
$filter_status = $_GET['status_proses'] ?? '';
$filter_status_data = $_GET['status_data'] ?? '';

$sql = "SELECT * FROM pendaftar";
$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_status)) {
    $where_clauses[] = "status_proses = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($filter_status_data)) {
    $where_clauses[] = "status_data = ?";
    $params[] = $filter_status_data;
    $types .= 's';
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY tanggal_daftar DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Koneksi tidak ditutup agar $result bisa digunakan di file pemanggil.
?>