<?php
// File ini berfungsi sebagai penyedia data untuk daftar peserta.
// Ia tidak menghasilkan output langsung, tetapi menyediakan variabel $result.

require_once '../config.php'; // Path disesuaikan

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT * FROM pendaftar ORDER BY tanggal_daftar DESC";
$result = $conn->query($sql);

// Koneksi tidak ditutup di sini agar $result tetap bisa digunakan di file pemanggil.
// Jika hanya butuh data array, proses menjadi array di sini dan tutup koneksi.
// Contoh:
// $participants = [];
// if ($result && $result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $participants[] = $row;
//     }
// }
// $conn->close();
?>
