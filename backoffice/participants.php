<?php
// File: backoffice/participants.php

// Ambil nilai filter dari URL
$filter_status = $_GET['status_proses'] ?? '';
$filter_status_data = $_GET['status_data'] ?? '';

// Memuat koneksi database dan mengambil semua data peserta dari tabel 'pendaftar'
require_once 'api/get_participants.php';
$user_role = $_SESSION['role'] ?? 'viewRMP';

// Daftar opsi untuk dropdown filter
$status_proses_options = [
    'Proses BI Checking', 'Reject BI Checking', 'Pemberkasan', 'Pengajuan Kredit Bank',
    'Terbit SP3K', 'Reject Bank', 'Siap AKAD', 'Sudah AKAD'
];
$status_data_options = ['active', 'inactive'];
?>

<div class="flex flex-wrap justify-between items-center mb-6 gap-4">
    <h2 class="text-3xl font-semibold text-gray-700">Data Lengkap Peserta</h2>
</div>

<!-- Form Filter -->
<form method="GET" action="dashboard.php" class="bg-white p-4 rounded-lg shadow-md mb-6">
    <input type="hidden" name="page" value="participants">
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label for="status_proses" class="block text-sm font-medium text-gray-700">Filter Status Proses</label>
            <select name="status_proses" id="status_proses" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Status</option>
                <?php foreach ($status_proses_options as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ($filter_status === $option) ? 'selected' : ''; ?>>
                        <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status_data" class="block text-sm font-medium text-gray-700">Filter Status Data</label>
            <select name="status_data" id="status_data" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Semua</option>
                <?php foreach ($status_data_options as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ($filter_status_data === $option) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>Filter
            </button>
            <a href="dashboard.php?page=participants" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Reset
            </a>
        </div>
        <div class="md:col-start-4 flex justify-end gap-2">
            <a href="export_pdf.php?status_proses=<?php echo urlencode($filter_status); ?>&status_data=<?php echo urlencode($filter_status_data); ?>" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                <i data-lucide="file-down" class="w-4 h-4 mr-2"></i>PDF
            </a>
            <a href="export_excel.php?status_proses=<?php echo urlencode($filter_status); ?>&status_data=<?php echo urlencode($filter_status_data); ?>" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                <i data-lucide="sheet" class="w-4 h-4 mr-2"></i>Excel
            </a>
        </div>
    </div>
</form>

<!-- Container untuk membuat tabel bisa di-scroll pada layar kecil -->
<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full leading-normal" id="participants-table">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Info Karyawan
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Kontak & Alamat Karyawan
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Status & Penghasilan
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Info Pasangan
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Alamat & Kontak Pasangan
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Dokumen
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    SIKASEP
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    SLIK / BI Checking
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Status Proses
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Status Data
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <p class="text-gray-900 font-bold whitespace-no-wrap"><?php echo htmlspecialchars($row['nama_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap">NIK: <?php echo htmlspecialchars($row['nik_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap">NIP: <?php echo htmlspecialchars($row['nomor_induk_karyawan']); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo ($row['email_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo htmlspecialchars($row['no_hp_karyawan']); ?></p>
                            <p class="text-gray-500 whitespace-no-wrap mt-1 text-xs"><?php echo htmlspecialchars($row['alamat_karyawan']); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <span class="relative inline-block px-3 py-1 font-semibold <?php echo ($row['status_perkawinan'] == 'menikah') ? 'text-green-900' : 'text-yellow-900'; ?> leading-tight">
                                <span aria-hidden class="absolute inset-0 <?php echo ($row['status_perkawinan'] == 'menikah') ? 'bg-green-200' : 'bg-yellow-200'; ?> opacity-50 rounded-full"></span>
                                <span class="relative capitalize"><?php echo htmlspecialchars($row['status_perkawinan']); ?></span>
                            </span>
                            <p class="text-gray-600 whitespace-no-wrap mt-2">Penghasilan Sesuai: <span class="font-semibold"><?php echo ucfirst($row['penghasilan_sesuai']); ?></span></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah') ? htmlspecialchars($row['nama_pasangan']) : '-'; ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah') ? 'NIK: ' . htmlspecialchars($row['nik_pasangan']) : ''; ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['email_pasangan']) ? htmlspecialchars($row['email_pasangan']) : '-'; ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['no_hp_pasangan']) ? htmlspecialchars($row['no_hp_pasangan']) : ''; ?></p>
                            <p class="text-gray-500 whitespace-no-wrap mt-1 text-xs"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['alamat_pasangan']) ? htmlspecialchars($row['alamat_pasangan']) : ''; ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center align-top">
                            <?php
                            // --- PERBAIKAN ---
                            // Mendefinisikan direktori root proyek di dalam loop untuk memastikan variabel selalu ada.
                            $project_server_root = dirname(__DIR__);

                            // Jalur untuk web (URL di browser)
                            $ktp_karyawan_web_path = '../' . $row['path_ktp_karyawan'];
                            // Jalur untuk server (untuk file_exists)
                            $ktp_karyawan_server_path = $project_server_root . '/' . ltrim($row['path_ktp_karyawan'], '/');

                            if (!empty($row['path_ktp_karyawan']) && file_exists($ktp_karyawan_server_path)): ?>
                                <a href="<?php echo htmlspecialchars($ktp_karyawan_web_path); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($ktp_karyawan_web_path); ?>" alt="KTP Karyawan" class="mx-auto w-40 h-auto rounded-md shadow-sm hover:shadow-lg transition-shadow duration-300">
                                </a>
                                <p class="text-xs text-gray-500 mt-1">KTP Karyawan</p>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">KTP Karyawan tidak tersedia</span>
                            <?php endif; ?>

                            <?php if ($row['status_perkawinan'] == 'menikah'):
                                // Jalur untuk web (URL di browser)
                                $ktp_pasangan_web_path = '../' . $row['path_ktp_pasangan'];
                                // Jalur untuk server (untuk file_exists)
                                $ktp_pasangan_server_path = $project_server_root . '/' . ltrim($row['path_ktp_pasangan'], '/');

                                if (!empty($row['path_ktp_pasangan']) && file_exists($ktp_pasangan_server_path)): ?>
                                    <a href="<?php echo htmlspecialchars($ktp_pasangan_web_path); ?>" target="_blank" class="mt-4 inline-block">
                                        <img src="<?php echo htmlspecialchars($ktp_pasangan_web_path); ?>" alt="KTP Pasangan" class="mx-auto w-40 h-auto rounded-md shadow-sm hover:shadow-lg transition-shadow duration-300">
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">KTP Pasangan</p>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 mt-4 inline-block">KTP Pasangan tidak tersedia</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <?php if (!empty($row['path_sikasep_1'])): ?>
                                <a href="../<?php echo htmlspecialchars($row['path_sikasep_1']); ?>" target="_blank" class="text-blue-600 hover:underline">
                                    <img src="../<?php echo htmlspecialchars($row['path_sikasep_1']); ?>" alt="KTP Pasangan" class="mx-auto w-40 h-auto rounded-md shadow-sm hover:shadow-lg transition-shadow duration-300">
                                </a>
                                <p class="text-xs text-gray-500 mt-1">File 1</p>
                                <br>
                            <?php endif; ?>
                            <?php if (!empty($row['path_sikasep_2'])): ?>
                                <a href="../<?php echo htmlspecialchars($row['path_sikasep_2']); ?>" target="_blank" class="text-blue-600 hover:underline">
                                    <img src="../<?php echo htmlspecialchars($row['path_sikasep_2']); ?>" alt="KTP Pasangan" class="mx-auto w-40 h-auto rounded-md shadow-sm hover:shadow-lg transition-shadow duration-300">
                                </a>
                                <p class="text-xs text-gray-500 mt-1">File 2</p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <p class="text-gray-600 whitespace-normal text-xs"><?php echo htmlspecialchars($row['slik_bi_checking'] ?? '-'); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <span class="font-semibold"><?php echo htmlspecialchars($row['status_proses']); ?></span>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <span class="relative inline-block px-3 py-1 font-semibold <?php echo ($row['status_data'] == 'active') ? 'text-green-900' : 'text-red-900'; ?> leading-tight">
                                <span aria-hidden class="absolute inset-0 <?php echo ($row['status_data'] == 'active') ? 'bg-green-200' : 'bg-red-200'; ?> opacity-50 rounded-full"></span>
                                <span class="relative capitalize"><?php echo htmlspecialchars($row['status_data']); ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm align-top">
                            <?php // [UPDATE v1.5] Sembunyikan tombol Edit untuk viewRMP ?>
                            <?php if ($user_role !== 'viewRMP'): ?>
                                <a href="dashboard.php?page=edit_participant&id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <?php else: ?>
                                <span class="text-gray-400">View Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center py-10 text-gray-500">
                        Belum ada data peserta yang terdaftar.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>