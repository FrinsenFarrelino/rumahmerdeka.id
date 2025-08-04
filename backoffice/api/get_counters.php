<?php
// File ini berfungsi sebagai penyedia data untuk jumlah klik tombol.
// Ia tidak menghasilkan output langsung, tetapi menyediakan variabel $counters.

require_once '../config.php'; // Path disesuaikan karena berada di dalam subfolder

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    // Sebaiknya ditangani dengan logging, bukan die() di produksi
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT button_id, click_count FROM button_clicks";
$result = $conn->query($sql);

$counters = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $counters[$row['button_id']] = $row['click_count'];
    }
}
$conn->close();
?>
