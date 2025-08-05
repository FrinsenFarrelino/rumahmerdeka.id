<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$user_role = $_SESSION['role'] ?? 'viewMRP';
if ($user_role === 'viewMRP') {
    header('HTTP/1.1 403 Forbidden');
    exit('Anda tidak memiliki izin untuk melakukan aksi ini.');
}

require_once '../config.php';

// Fungsi untuk menangani upload file
function handle_file_upload($file_input_name, $upload_dir, $nik, $current_path = null) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        // Hapus file lama jika ada
        if ($current_path && file_exists('../' . $current_path)) {
            unlink('../' . $current_path);
        }

        $file = $_FILES[$file_input_name];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_file_name = $nik . '_' . uniqid() . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;

        if (!is_dir('../' . $upload_dir)) {
            mkdir('../' . $upload_dir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], '../' . $destination)) {
            return $destination;
        }
    }
    return $current_path; // Kembalikan path lama jika tidak ada file baru
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php?page=participants');
    exit;
}

$id = $_POST['id'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Koneksi DB gagal');
    exit;
}

// Ambil path file saat ini sebelum update
$stmt = $conn->prepare("SELECT path_sikasep_1, path_sikasep_2, nik_karyawan FROM pendaftar WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$current_data) {
     header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Data tidak ditemukan');
    exit;
}

// Handle upload file SIKASEP
$upload_dir_sikasep = 'uploads/sikasep/';
$path_sikasep_1 = handle_file_upload('path_sikasep_1', $upload_dir_sikasep, $current_data['nik_karyawan'], $current_data['path_sikasep_1']);
$path_sikasep_2 = handle_file_upload('path_sikasep_2', $upload_dir_sikasep, $current_data['nik_karyawan'], $current_data['path_sikasep_2']);

// Kumpulkan semua data dari form.
// Gunakan ternary operator untuk mengubah string kosong menjadi NULL untuk data opsional.

// [UPDATE v1.5] Logika pembaruan data berdasarkan role
if ($user_role === 'superadmin') {
    // Superadmin dapat mengedit semua data
    $data = [
        'nama_karyawan' => $_POST['nama_karyawan'],
        'nik_karyawan' => $_POST['nik_karyawan'],
        'nomor_induk_karyawan' => $_POST['nomor_induk_karyawan'],
        'no_hp_karyawan' => $_POST['no_hp_karyawan'],
        'email_karyawan' => $_POST['email_karyawan'],
        'alamat_karyawan' => $_POST['alamat_karyawan'],
        'status_perkawinan' => $_POST['status_perkawinan'],
        'penghasilan_sesuai' => $_POST['penghasilan_sesuai'],
        'nama_pasangan' => !empty($_POST['nama_pasangan']) ? $_POST['nama_pasangan'] : null,
        'nik_pasangan' => !empty($_POST['nik_pasangan']) ? $_POST['nik_pasangan'] : null,
        'no_hp_pasangan' => !empty($_POST['no_hp_pasangan']) ? $_POST['no_hp_pasangan'] : null,
        'email_pasangan' => !empty($_POST['email_pasangan']) ? $_POST['email_pasangan'] : null,
        'alamat_pasangan' => !empty($_POST['alamat_pasangan']) ? $_POST['alamat_pasangan'] : null,
        'slik_bi_checking' => !empty($_POST['slik_bi_checking']) ? $_POST['slik_bi_checking'] : null,
        'status_proses' => $_POST['status_proses'],
        'status_data' => $_POST['status_data'],
        'path_sikasep_1' => $path_sikasep_1,
        'path_sikasep_2' => $path_sikasep_2,
        'id' => $id
    ];
    
    $sql = "UPDATE pendaftar SET 
                nama_karyawan = ?, nik_karyawan = ?, nomor_induk_karyawan = ?, no_hp_karyawan = ?, email_karyawan = ?, alamat_karyawan = ?, 
                status_perkawinan = ?, penghasilan_sesuai = ?, 
                nama_pasangan = ?, nik_pasangan = ?, no_hp_pasangan = ?, email_pasangan = ?, alamat_pasangan = ?, 
                slik_bi_checking = ?, status_proses = ?, status_data = ?, path_sikasep_1 = ?, path_sikasep_2 = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssssssi",
        $data['nama_karyawan'], $data['nik_karyawan'], $data['nomor_induk_karyawan'], // ... (semua parameter)
        $data['status_data'], $data['path_sikasep_1'], $data['path_sikasep_2'], $data['id']
    );

} elseif ($user_role === 'adminMRP') {
    // adminMRP hanya bisa mengedit data tambahan
    $data = [
        'slik_bi_checking' => !empty($_POST['slik_bi_checking']) ? $_POST['slik_bi_checking'] : null,
        'status_proses' => $_POST['status_proses'],
        'status_data' => 'active', // Paksa status data tetap active
        'path_sikasep_1' => $path_sikasep_1,
        'path_sikasep_2' => $path_sikasep_2,
        'id' => $id
    ];

    $sql = "UPDATE pendaftar SET 
                slik_bi_checking = ?, status_proses = ?, status_data = ?, 
                path_sikasep_1 = ?, path_sikasep_2 = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi",
        $data['slik_bi_checking'], $data['status_proses'], $data['status_data'],
        $data['path_sikasep_1'], $data['path_sikasep_2'], $data['id']
    );
}

if ($stmt->execute()) {
    header('Location: dashboard.php?page=edit_participant&id=' . $id . '&success=Data berhasil diperbarui.');
} else {
    header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Gagal memperbarui data: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>