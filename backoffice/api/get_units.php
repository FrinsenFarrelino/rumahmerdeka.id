<?php
// File: backoffice/api/get_units.php
require_once '../../config.php';
header('Content-Type: application/json');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi database gagal.']);
    exit;
}

// Helper function untuk membangun klausa IN dengan aman
function build_in_clause($field, $values, &$where_clauses, &$params, &$types) {
    if (!empty($values)) {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $where_clauses[] = "$field IN ($placeholders)";
        foreach ($values as $value) {
            $params[] = $value;
            $types .= 's';
        }
    }
}

// Ambil semua filter dari request
$all_filters = [
    'tipe' => isset($_GET['tipes']) && !empty($_GET['tipes']) ? explode(',', $_GET['tipes']) : [],
    'status_unit' => isset($_GET['statuses']) && !empty($_GET['statuses']) ? explode(',', $_GET['statuses']) : [],
    'blok' => isset($_GET['bloks']) && !empty($_GET['bloks']) ? explode(',', $_GET['bloks']) : [],
];

$base_sql = "FROM unit_stock WHERE id_pendaftar IS NULL";

// --- Ambil OPSI FILTER yang tersedia untuk dropdown ---
$filter_options = [];
foreach (array_keys($all_filters) as $column_to_get) {
    $temp_where_clauses = [];
    $temp_params = [];
    $temp_types = '';

    // Bangun klausa WHERE menggunakan semua filter KECUALI yang sedang kita ambil opsinya
    foreach ($all_filters as $field => $values) {
        if ($field !== $column_to_get) {
            build_in_clause($field, $values, $temp_where_clauses, $temp_params, $temp_types);
        }
    }
    
    $sql_where = !empty($temp_where_clauses) ? " AND " . implode(" AND ", $temp_where_clauses) : "";
    
    $query = "SELECT DISTINCT $column_to_get " . $base_sql . $sql_where . " ORDER BY $column_to_get ASC";
    $stmt = $conn->prepare($query);
    if ($stmt === false) { continue; } // Lewati jika prepare gagal
    
    if (!empty($temp_params)) {
        $stmt->bind_param($temp_types, ...$temp_params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $filter_options[$column_to_get] = array_column($result, $column_to_get);
}

// --- Ambil daftar UNIT FINAL berdasarkan SEMUA filter yang aktif ---
$main_where_clauses = [];
$main_params = [];
$main_types = '';
foreach ($all_filters as $field => $values) {
    build_in_clause($field, $values, $main_where_clauses, $main_params, $main_types);
}
$sql_main_where = !empty($main_where_clauses) ? " AND " . implode(" AND ", $main_where_clauses) : "";

$unit_sql = "SELECT id, no_unit, lt_lb, tipe, status_unit, blok " . $base_sql . $sql_main_where . " ORDER BY blok, no_unit ASC";
$unit_stmt = $conn->prepare($unit_sql);
$available_units = [];
if ($unit_stmt) {
    if (!empty($main_params)) {
        $unit_stmt->bind_param($main_types, ...$main_params);
    }
    $unit_stmt->execute();
    $available_units = $unit_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $unit_stmt->close();
}

echo json_encode([
    'filter_options' => $filter_options,
    'units' => $available_units
]);

$conn->close();
?>