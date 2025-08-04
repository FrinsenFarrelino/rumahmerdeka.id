<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

date_default_timezone_set('Asia/Jakarta');

// Mengambil data dari file API
require_once 'api/get_participants.php';
require_once '../vendor/tcpdf/tcpdf.php';

// 3. Membuat Dokumen PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Backoffice Rumah Merdeka');
$pdf->SetTitle('Data Peserta Rumah Merdeka');
$pdf->SetSubject('Laporan Data Peserta');

// Set header dan footer
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetHeaderData('', 0, 'Laporan Data Peserta - Rumah Merdeka', 'Dibuat pada: ' . date('d-m-Y H:i') . ' oleh ' . $_SESSION['username']);

// Set margin
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set font
$pdf->SetFont('helvetica', '', 10);

// 4. Menambahkan Halaman dan Konten
$pdf->AddPage('L', 'A4'); // 'L' untuk Landscape

$html = '<h2>Data Peserta Program Rumah Merdeka</h2>';
$html .= '<table border="1" cellpadding="4" cellspacing="0">';
$html .= '<thead style="background-color:#f2f2f2; font-weight:bold;">
            <tr>
                <th>Info Karyawan</th>
                <th>Kontak & Alamat Karyawan</th>
                <th>Status</th>
                <th>Info Pasangan</th>
                <th>Kontak & Alamat Pasangan</th>
                <th>Dokumen KTP</th>
            </tr>
          </thead>';

$html .= '<tbody>';
// Body Tabel
while ($row = $result->fetch_assoc()) {
    $html .= '<tr nobr="true">';
    // Info Karyawan, Kontak, Status
    $html .= '<td style="vertical-align: top;"><b>' . htmlspecialchars($row['nama_karyawan']) . '</b><br><small>NIK: ' . htmlspecialchars($row['nik_karyawan']) . '</small><br><small>NIP: ' . htmlspecialchars($row['nomor_induk_karyawan']) . '</small></td>';
    $html .= '<td style="vertical-align: top;">' . htmlspecialchars($row['email_karyawan']) . '<br>' . htmlspecialchars($row['no_hp_karyawan']) . '<br><small>' . htmlspecialchars($row['alamat_karyawan']) . '</small></td>';
    $html .= '<td style="vertical-align: top;">' . ucfirst($row['status_perkawinan']) . '<br><small>Gaji: ' . ucfirst($row['penghasilan_sesuai']) . '</small></td>';
    
    // Info Pasangan
    if ($row['status_perkawinan'] == 'menikah') {
        $html .= '<td style="vertical-align: top;"><b>' . htmlspecialchars($row['nama_pasangan']) . '</b><br><small>NIK: ' . htmlspecialchars($row['nik_pasangan']) . '</small></td>';
        $html .= '<td style="vertical-align: top;">' . htmlspecialchars($row['email_pasangan']) . '<br>' . htmlspecialchars($row['no_hp_pasangan']) . '<br><small>' . htmlspecialchars($row['alamat_pasangan']) . '</small></td>';
    } else {
        $html .= '<td align="center" style="vertical-align: middle;">-</td>';
        $html .= '<td align="center" style="vertical-align: middle;">-</td>';
    }

    // Dokumen (Gambar KTP) - Menggunakan path absolut server untuk keandalan
    $dokumen_html = '';
    // Mendapatkan direktori root proyek (satu level di atas 'backoffice')
    $project_server_root = dirname(__DIR__); 
    
    // Proses KTP Karyawan
    $ktp_karyawan_path_db = $row['path_ktp_karyawan'];
    $full_path_karyawan = $project_server_root . '/' . ltrim($ktp_karyawan_path_db, '/');
    if (!empty($ktp_karyawan_path_db) && file_exists($full_path_karyawan) && is_readable($full_path_karyawan)) {
        $imageData = base64_encode(file_get_contents($full_path_karyawan));
        $dokumen_html .= '<img src="@' . $imageData . '" width="120"><br/><small>KTP Karyawan</small>';
    } else {
        $dokumen_html .= '<small>KTP Karyawan tidak ada</small>';
    }
    
    // Proses KTP Pasangan
    if ($row['status_perkawinan'] == 'menikah') {
        $ktp_pasangan_path_db = $row['path_ktp_pasangan'];
        $full_path_pasangan = $project_server_root . '/' . ltrim($ktp_pasangan_path_db, '/');
        if (!empty($ktp_pasangan_path_db) && file_exists($full_path_pasangan) && is_readable($full_path_pasangan)) {
            $imageData = base64_encode(file_get_contents($full_path_pasangan));
            $dokumen_html .= '<br/><br/><img src="@' . $imageData . '" width="120"><br/><small>KTP Pasangan</small>';
        } else {
            $dokumen_html .= '<br/><br/><small>KTP Pasangan tidak ada</small>';
        }
    }
    $html .= '<td style="vertical-align: top; text-align: center;">' . $dokumen_html . '</td>';
    
    $html .= '</tr>';
}
$html .= '</tbody></table>';

// Tulis HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');


// 5. Menutup dan Mengirim PDF ke Browser
$conn->close();
$pdf->Output('data-peserta-rumah-merdeka.pdf', 'I'); // 'I' untuk inline (tampil di browser), 'D' untuk download

?>