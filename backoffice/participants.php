<?php
// File: backoffice/participants.php

// Memuat koneksi database dan mengambil semua data peserta dari tabel 'pendaftar'
require_once 'api/get_participants.php';
?>

<div class="flex flex-wrap justify-between items-center mb-6 gap-4">
    <h2 class="text-3xl font-semibold text-gray-700">Data Lengkap Peserta</h2>
    <a href="export_pdf.php" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
        <i data-lucide="file-down" class="w-4 h-4 mr-2"></i>
        <span>Ekspor ke PDF</span>
    </a>
</div>

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
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 font-bold whitespace-no-wrap"><?php echo htmlspecialchars($row['nama_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap">NIK: <?php echo htmlspecialchars($row['nik_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap">NIP: <?php echo htmlspecialchars($row['nomor_induk_karyawan']); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($row['email_karyawan']); ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo htmlspecialchars($row['no_hp_karyawan']); ?></p>
                            <p class="text-gray-500 whitespace-no-wrap mt-1 text-xs"><?php echo htmlspecialchars($row['alamat_karyawan']); ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                             <span class="relative inline-block px-3 py-1 font-semibold <?php echo ($row['status_perkawinan'] == 'menikah') ? 'text-green-900' : 'text-yellow-900'; ?> leading-tight">
                                <span aria-hidden class="absolute inset-0 <?php echo ($row['status_perkawinan'] == 'menikah') ? 'bg-green-200' : 'bg-yellow-200'; ?> opacity-50 rounded-full"></span>
                                <span class="relative capitalize"><?php echo htmlspecialchars($row['status_perkawinan']); ?></span>
                            </span>
                            <p class="text-gray-600 whitespace-no-wrap mt-2">Penghasilan Sesuai: <span class="font-semibold"><?php echo ucfirst($row['penghasilan_sesuai']); ?></span></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah') ? htmlspecialchars($row['nama_pasangan']) : '-'; ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah') ? 'NIK: ' . htmlspecialchars($row['nik_pasangan']) : ''; ?></p>
                        </td>
                         <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['email_pasangan']) ? htmlspecialchars($row['email_pasangan']) : '-'; ?></p>
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['no_hp_pasangan']) ? htmlspecialchars($row['no_hp_pasangan']) : ''; ?></p>
                            <p class="text-gray-500 whitespace-no-wrap mt-1 text-xs"><?php echo ($row['status_perkawinan'] == 'menikah' && $row['alamat_pasangan']) ? htmlspecialchars($row['alamat_pasangan']) : ''; ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                            <a href="../<?php echo htmlspecialchars($row['path_ktp_karyawan']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 underline">KTP Karyawan</a>
                            <?php if ($row['status_perkawinan'] == 'menikah' && !empty($row['path_ktp_pasangan'])): ?>
                                <br>
                                <a href="../<?php echo htmlspecialchars($row['path_ktp_pasangan']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 underline">KTP Pasangan</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">
                        Belum ada data peserta yang terdaftar.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
