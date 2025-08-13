<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 403 Forbidden'); exit;
}

$user_role = $_SESSION['role'] ?? 'viewRMP';
if ($user_role === 'viewRMP') {
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
$status_proses = $_POST['status_proses'];
$catatan_batal = ($status_proses === 'Batal') ? trim($_POST['catatan_batal']) : null;

// [UPDATE v2.3] Validasi catatan batal
if ($status_proses === 'Batal' && empty($catatan_batal)) {
    header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Catatan alasan batal wajib diisi jika status proses adalah Batal.');
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Koneksi DB gagal');
    exit;
}

// [UPDATE v2.3] Jika status diubah menjadi Batal, lepaskan unit yang terkait
if ($status_proses === 'Batal') {
    $conn->begin_transaction();
    try {
        // Ambil id_unit pendaftar
        $stmt_get_unit = $conn->prepare("SELECT id_unit FROM pendaftar WHERE id = ?");
        $stmt_get_unit->bind_param("i", $id);
        $stmt_get_unit->execute();
        $unit_data = $stmt_get_unit->get_result()->fetch_assoc();
        $stmt_get_unit->close();
        
        if ($unit_data && !empty($unit_data['id_unit'])) {
            $unit_id = $unit_data['id_unit'];
            // Lepaskan unit
            $stmt_release = $conn->prepare("UPDATE unit_stock SET id_pendaftar = NULL WHERE id = ?");
            $stmt_release->bind_param("i", $unit_id);
            $stmt_release->execute();
            $stmt_release->close();
            
            // Hapus id_unit dari pendaftar
            $stmt_unassign = $conn->prepare("UPDATE pendaftar SET id_unit = NULL WHERE id = ?");
            $stmt_unassign->bind_param("i", $id);
            $stmt_unassign->execute();
            $stmt_unassign->close();
        }
        $conn->commit();
        // Lanjutkan ke proses update utama
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: dashboard.php?page=edit_participant&id=' . $id . '&error=Gagal melepaskan unit: ' . $e->getMessage());
        exit;
    }
}

// Ambil path file saat ini sebelum update
$stmt = $conn->prepare("SELECT nik_karyawan, nik_pasangan, path_ktp_karyawan, path_ktp_pasangan, path_sikasep_1, path_sikasep_2 FROM pendaftar WHERE id = ?");
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

// [UPDATE v1.8] Handle upload file KTP jika superadmin
$path_ktp_karyawan = $current_data['path_ktp_karyawan'];
$path_ktp_pasangan = $current_data['path_ktp_pasangan'];
if ($user_role === 'superadmin') {
    $path_ktp_karyawan = handle_file_upload('path_ktp_karyawan', UPLOAD_DIR_KARYAWAN, $current_data['nik_karyawan'], $current_data['path_ktp_karyawan']);
    if (!empty($current_data['nik_pasangan'])) {
        $path_ktp_pasangan = handle_file_upload('path_ktp_pasangan', UPLOAD_DIR_PASANGAN, $current_data['nik_pasangan'], $current_data['path_ktp_pasangan']);
    }
}

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
        'path_ktp_karyawan' => $path_ktp_karyawan,
        'path_ktp_pasangan' => $path_ktp_pasangan,
        'catatan_batal' => $catatan_batal,
        'id' => $id
    ];
    
    $sql = "UPDATE pendaftar SET 
                nama_karyawan = ?, nik_karyawan = ?, nomor_induk_karyawan = ?, no_hp_karyawan = ?, email_karyawan = ?, alamat_karyawan = ?, 
                status_perkawinan = ?, penghasilan_sesuai = ?, 
                nama_pasangan = ?, nik_pasangan = ?, no_hp_pasangan = ?, email_pasangan = ?, alamat_pasangan = ?, 
                slik_bi_checking = ?, status_proses = ?, status_data = ?, 
                path_sikasep_1 = ?, path_sikasep_2 = ?,
                path_ktp_karyawan = ?, path_ktp_pasangan = ?, catatan_batal = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssssssi", 
        $data['nama_karyawan'], $data['nik_karyawan'], $data['nomor_induk_karyawan'], $data['no_hp_karyawan'], 
        $data['email_karyawan'], $data['alamat_karyawan'], $data['status_perkawinan'], $data['penghasilan_sesuai'],
        $data['nama_pasangan'], $data['nik_pasangan'], $data['no_hp_pasangan'], $data['email_pasangan'], 
        $data['alamat_pasangan'], $data['slik_bi_checking'], $data['status_proses'], $data['status_data'], 
        $data['path_sikasep_1'], $data['path_sikasep_2'], $data['path_ktp_karyawan'], $data['path_ktp_pasangan'], $data['catatan_batal'],
        $data['id']  // Keep this as the last parameter (for the WHERE clause)
    );

} elseif ($user_role === 'adminRMP') {
    // adminRMP hanya bisa mengedit data tambahan
    $data = [
        'slik_bi_checking' => !empty($_POST['slik_bi_checking']) ? $_POST['slik_bi_checking'] : null,
        'status_proses' => $_POST['status_proses'],
        'status_data' => 'active', // Paksa status data tetap active
        'path_sikasep_1' => $path_sikasep_1,
        'path_sikasep_2' => $path_sikasep_2,
        'catatan_batal' => $catatan_batal,
        'id' => $id
    ];

    $sql = "UPDATE pendaftar SET 
                slik_bi_checking = ?, status_proses = ?, status_data = ?, 
                path_sikasep_1 = ?, path_sikasep_2 = ?, catatan_batal = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi",
        $data['slik_bi_checking'], $data['status_proses'], $data['status_data'],
        $data['path_sikasep_1'], $data['path_sikasep_2'], $data['catatan_batal'], $data['id']
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