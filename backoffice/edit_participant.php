<?php
// File: backoffice/edit_participant.php
require_once '../config.php';

$participant_id = $_GET['id'] ?? null;
if (!$participant_id) {
    die("ID Peserta tidak valid.");
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM pendaftar WHERE id = ?");
$stmt->bind_param("i", $participant_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Peserta tidak ditemukan.");
}
$participant = $result->fetch_assoc();
$stmt->close();

// Opsi untuk dropdown
$status_proses_options = [
    'Proses BI Checking', 'Reject BI Checking', 'Pemberkasan', 'Pengajuan Kredit Bank',
    'Terbit SP3K', 'Reject Bank', 'Siap AKAD', 'Sudah AKAD'
];
$status_data_options = ['active', 'inactive'];
$status_perkawinan_options = ['lajang', 'menikah'];
$penghasilan_options = ['ya', 'tidak'];

$user_role = $_SESSION['role'] ?? 'viewMRP';

// [UPDATE v1.5] Tentukan apakah form bisa diedit penuh
$is_fully_editable = ($user_role === 'superadmin');
$is_partially_editable = ($user_role === 'adminMRP');

?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-semibold text-gray-700">
        <?php echo ($user_role === 'viewMRP') ? 'Lihat' : 'Edit'; ?> Data Peserta: <?php echo htmlspecialchars($participant['nama_karyawan']); ?>
    </h2>
    <!-- [UPDATE v1.7] Tombol Kembali -->
    <a href="dashboard.php?page=participants" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Kembali
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
    </div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
    </div>
<?php endif; ?>

<form action="update_participant.php" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow-md space-y-8">
    <input type="hidden" name="id" value="<?php echo $participant['id']; ?>">

    <!-- Data Tambahan -->
    <div class="p-6 border border-gray-200 rounded-lg">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Data Tambahan (Admin)</h3>
        <fieldset <?php echo ($user_role === 'viewMRP') ? 'disabled' : ''; ?>>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status_proses" class="block font-semibold mb-1">Status Proses</label>
                    <select id="status_proses" name="status_proses" class="w-full p-3 border border-gray-300 rounded-lg">
                        <?php foreach ($status_proses_options as $option): ?>
                            <option value="<?php echo $option; ?>" <?php echo ($participant['status_proses'] === $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="status_data" class="block font-semibold mb-1">Status Data</label>
                    <select id="status_data" name="status_data" class="w-full p-3 border border-gray-300 rounded-lg" <?php echo ($user_role === 'adminMRP' || $user_role === 'viewMRP') ? 'disabled' : ''; ?>>
                        <?php foreach ($status_data_options as $option): ?>
                            <option value="<?php echo $option; ?>" <?php echo ($participant['status_data'] === $option) ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="slik_bi_checking" class="block font-semibold mb-1">Catatan SLIK / BI Checking</label>
                    <textarea id="slik_bi_checking" name="slik_bi_checking" rows="4" class="w-full p-3 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($participant['slik_bi_checking'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label for="path_sikasep_1" class="block font-semibold mb-1">Upload SIKASEP (File 1)</label>
                    <input type="file" id="path_sikasep_1" name="path_sikasep_1" class="w-full text-sm">
                    <?php if (!empty($participant['path_sikasep_1'])): ?>
                        <a href="../<?php echo htmlspecialchars($participant['path_sikasep_1']); ?>" target="_blank" class="text-xs text-blue-600">Lihat file saat ini</a>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="path_sikasep_2" class="block font-semibold mb-1">Upload SIKASEP (File 2)</label>
                    <input type="file" id="path_sikasep_2" name="path_sikasep_2" class="w-full text-sm">
                    <?php if (!empty($participant['path_sikasep_2'])): ?>
                        <a href="../<?php echo htmlspecialchars($participant['path_sikasep_2']); ?>" target="_blank" class="text-xs text-blue-600">Lihat file saat ini</a>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>
    </div>

    <!-- Data Awal Karyawan -->
    <div class="p-6 border border-gray-200 rounded-lg">
        <h3 class="text-xl font-bold text-red-600 border-b pb-2 mb-4">Data Awal Karyawan</h3>
        <fieldset <?php echo (!$is_fully_editable) ? 'disabled' : ''; ?>>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label for="nama_karyawan" class="block font-semibold mb-1">Nama Lengkap</label><input type="text" id="nama_karyawan" name="nama_karyawan" value="<?php echo htmlspecialchars($participant['nama_karyawan']); ?>" class="w-full p-3 border border-gray-300 rounded-lg" required></div>
                <div><label for="nik_karyawan" class="block font-semibold mb-1">NIK</label><input type="text" id="nik_karyawan" name="nik_karyawan" value="<?php echo htmlspecialchars($participant['nik_karyawan']); ?>" class="w-full p-3 border border-gray-300 rounded-lg" required></div>
                <div><label for="nomor_induk_karyawan" class="block font-semibold mb-1">Nomor Induk Karyawan</label><input type="text" id="nomor_induk_karyawan" name="nomor_induk_karyawan" value="<?php echo htmlspecialchars($participant['nomor_induk_karyawan']); ?>" class="w-full p-3 border border-gray-300 rounded-lg" required></div>
                <div><label for="no_hp_karyawan" class="block font-semibold mb-1">No. HP</label><input type="tel" id="no_hp_karyawan" name="no_hp_karyawan" value="<?php echo htmlspecialchars($participant['no_hp_karyawan']); ?>" class="w-full p-3 border border-gray-300 rounded-lg" required></div>
                <div class="md:col-span-2"><label for="email_karyawan" class="block font-semibold mb-1">Email</label><input type="email" id="email_karyawan" name="email_karyawan" value="<?php echo htmlspecialchars($participant['email_karyawan'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg"></div>
                <div class="md:col-span-2"><label for="alamat_karyawan" class="block font-semibold mb-1">Alamat</label><textarea id="alamat_karyawan" name="alamat_karyawan" rows="3" class="w-full p-3 border border-gray-300 rounded-lg" required><?php echo htmlspecialchars($participant['alamat_karyawan']); ?></textarea></div>
                <div>
                    <label for="status_perkawinan" class="block font-semibold mb-1">Status Perkawinan</label>
                    <select name="status_perkawinan" class="w-full p-3 border border-gray-300 rounded-lg">
                        <?php foreach ($status_perkawinan_options as $option): ?>
                            <option value="<?php echo $option; ?>" <?php echo ($participant['status_perkawinan'] === $option) ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="penghasilan_sesuai" class="block font-semibold mb-1">Penghasilan Sesuai</label>
                    <select name="penghasilan_sesuai" class="w-full p-3 border border-gray-300 rounded-lg">
                        <?php foreach ($penghasilan_options as $option): ?>
                            <option value="<?php echo $option; ?>" <?php echo ($participant['penghasilan_sesuai'] === $option) ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>
    </div>

    <!-- Data Pasangan -->
    <div class="p-6 border border-gray-200 rounded-lg">
        <h3 class="text-xl font-bold text-red-600 border-b pb-2 mb-4">Data Pasangan</h3>
        <fieldset <?php echo (!$is_fully_editable) ? 'disabled' : ''; ?>>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label for="nama_pasangan" class="block font-semibold mb-1">Nama Pasangan</label><input type="text" name="nama_pasangan" value="<?php echo htmlspecialchars($participant['nama_pasangan'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg"></div>
                <div><label for="nik_pasangan" class="block font-semibold mb-1">NIK Pasangan</label><input type="text" name="nik_pasangan" value="<?php echo htmlspecialchars($participant['nik_pasangan'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg"></div>
                <div><label for="no_hp_pasangan" class="block font-semibold mb-1">No. HP Pasangan</label><input type="tel" name="no_hp_pasangan" value="<?php echo htmlspecialchars($participant['no_hp_pasangan'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg"></div>
                <div><label for="email_pasangan" class="block font-semibold mb-1">Email Pasangan</label><input type="email" name="email_pasangan" value="<?php echo htmlspecialchars($participant['email_pasangan'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg"></div>
                <div class="md:col-span-2"><label for="alamat_pasangan" class="block font-semibold mb-1">Alamat Pasangan</label><textarea name="alamat_pasangan" rows="3" class="w-full p-3 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($participant['alamat_pasangan'] ?? ''); ?></textarea></div>
            </div>
        </fieldset>
    </div>

    <?php if ($user_role !== 'viewMRP'): ?>
    <div class="text-right">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
            Simpan Perubahan
        </button>
    </div>
    <?php endif; ?>
</form>

<?php $conn->close(); ?>