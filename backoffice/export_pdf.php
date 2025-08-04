<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

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
$pdf->SetHeaderData('', 0, 'Laporan Data Peserta - Rumah Merdeka', 'Dibuat pada: ' . date('d-m-Y H:i'));

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

$html = '<h1>Data Peserta Program Rumah Merdeka</h1>';
$html .= '<table border="1" cellpadding="4">';
// Header Tabel
$html .= '<thead style="background-color:#eeeeee;">
            <tr>
                <th><b>Info Karyawan</b></th>
                <th><b>Alamat & Kontak</b></th>
                <th><b>Status</b></th>
                <th><b>Info Pasangan</b></th>
                <th><b>Alamat & Kontak Pasangan</b></th>
                <th><b>Dokumen</b></th>
            </tr>
          </thead>';

$html .= '<tbody>';
// Body Tabel
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    // Info Karyawan
    $html .= '<td>' . htmlspecialchars($row['nama_karyawan']) . '<br><small>NIK: ' . htmlspecialchars($row['nik_karyawan']) . '</small><br><small>NIP: ' . htmlspecialchars($row['nomor_induk_karyawan']) . '</small></td>';
    // Kontak & Alamat Karyawan
    $html .= '<td>' . htmlspecialchars($row['email_karyawan']) . '<br>' . htmlspecialchars($row['no_hp_karyawan']) . '<br><small>' . htmlspecialchars($row['alamat_karyawan']) . '</small></td>';
    // Status
    $html .= '<td>' . ucfirst($row['status_perkawinan']) . '<br><small>Penghasilan Sesuai: ' . ucfirst($row['penghasilan_sesuai']) . '</small></td>';
    
    // Info Pasangan
    if ($row['status_perkawinan'] == 'menikah') {
        $html .= '<td>' . htmlspecialchars($row['nama_pasangan']) . '<br><small>NIK: ' . htmlspecialchars($row['nik_pasangan']) . '</small></td>';
        $html .= '<td>' . htmlspecialchars($row['email_pasangan']) . '<br>' . htmlspecialchars($row['no_hp_pasangan']) . '<br><small>' . htmlspecialchars($row['alamat_pasangan']) . '</small></td>';
    } else {
        $html .= '<td colspan="2" align="center">-</td>';
    }

    // Dokumen
    $ktp_karyawan_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../' . $row['path_ktp_karyawan'];
    $dokumen_html = '<a href="' . $ktp_karyawan_url . '">KTP Karyawan</a>';
    if ($row['status_perkawinan'] == 'menikah' && !empty($row['path_ktp_pasangan'])) {
        $ktp_pasangan_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../' . $row['path_ktp_pasangan'];
        $dokumen_html .= '<br><a href="' . $ktp_pasangan_url . '">KTP Pasangan</a>';
    }
    $html .= '<td>' . $dokumen_html . '</td>';
    
    $html .= '</tr>';
}
$html .= '</tbody></table>';

// Tulis HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');


// 5. Menutup dan Mengirim PDF ke Browser
$conn->close();
$pdf->Output('data-peserta-rumah-merdeka.pdf', 'I'); // 'I' untuk inline (tampil di browser), 'D' untuk download

?>