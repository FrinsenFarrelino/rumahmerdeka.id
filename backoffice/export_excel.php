<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
// 0. Mulai Output Buffering untuk mencegah output yang tidak diinginkan

ob_start();
date_default_timezone_set('Asia/Jakarta');

// 1. Memuat Pustaka XLSXWriter dan Koneksi DB
require_once 'api/get_participants.php';
require_once('../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\DataType; // [FIX] Import DataType
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// 3. Membuat Dokumen Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Peserta');

// 4. Menambahkan Judul dan Tanggal
$sheet->mergeCells('A1:T1')->setCellValue('A1', 'Data Peserta Program Rumah Merdeka');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:T2')->setCellValue('A2', 'Dokumen dibuat pada: ' . date('d F Y, H:i:s'));
$sheet->getStyle('A2')->getFont()->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// 5. Menulis Header Tabel
$headers = [
    'Nama Karyawan', 'NIK Karyawan', 'NIP', 'Email Karyawan', 'No. HP Karyawan', 'Alamat Karyawan',
    'Status Perkawinan', 'Penghasilan Sesuai',
    'Nama Pasangan', 'NIK Pasangan', 'Email Pasangan', 'No. HP Pasangan', 'Alamat Pasangan',
    'KTP Karyawan', 'KTP Pasangan', 'SIKASEP FILE 1', 'SIKASEP FILE 2', 'SLIK / BI Checking', 'Status Proses', 'Status Data'
];
$sheet->fromArray($headers, NULL, 'A4');

// Memberi style pada header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E8449']],
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
];
$sheet->getStyle('A4:T4')->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

// 6. Menulis Data dan Menyisipkan Gambar
$rowNum = 5;
$imageHeight = 90; // Tinggi gambar dalam piksel

while($row = $result->fetch_assoc()) {
    // [FIX] Menyesuaikan tinggi baris dan perataan vertikal
    $sheet->getRowDimension($rowNum)->setRowHeight($imageHeight + 5);
    $sheet->getStyle('A'.$rowNum.':T'.$rowNum)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A'.$rowNum.':M'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Rata kiri untuk teks
    $sheet->getStyle('R'.$rowNum.':T'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Rata kiri untuk teks

    // [FIX] Menulis data sel per sel untuk kontrol format
    $sheet->setCellValue('A' . $rowNum, $row['nama_karyawan']);
    // Menggunakan setCellValueExplicit untuk memaksa NIK/NIP menjadi Teks
    $sheet->setCellValueExplicit('B' . $rowNum, $row['nik_karyawan'], DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('C' . $rowNum, $row['nomor_induk_karyawan'], DataType::TYPE_STRING);
    $sheet->setCellValue('D' . $rowNum, $row['email_karyawan']);
    $sheet->setCellValue('E' . $rowNum, $row['no_hp_karyawan']);
    $sheet->setCellValue('F' . $rowNum, $row['alamat_karyawan']);
    $sheet->setCellValue('G' . $rowNum, ucfirst($row['status_perkawinan']));
    $sheet->setCellValue('H' . $rowNum, ucfirst($row['penghasilan_sesuai']));
    
    // Data Pasangan
    $sheet->setCellValue('I' . $rowNum, $row['nama_pasangan'] ?? '-');
    $sheet->setCellValueExplicit('J' . $rowNum, $row['nik_pasangan'] ?? '-', DataType::TYPE_STRING);
    $sheet->setCellValue('K' . $rowNum, $row['email_pasangan'] ?? '-');
    $sheet->setCellValue('L' . $rowNum, $row['no_hp_pasangan'] ?? '-');
    $sheet->setCellValue('M' . $rowNum, $row['alamat_pasangan'] ?? '-');

    // [FIX] Menyisipkan gambar KTP Karyawan
    $path_ktp_karyawan = '../' . $row['path_ktp_karyawan'];
    if (file_exists($path_ktp_karyawan)) {
        $drawing = new Drawing();
        $drawing->setName('KTP Karyawan');
        $drawing->setPath($path_ktp_karyawan);
        $drawing->setCoordinates('N' . $rowNum);
        $drawing->setHeight($imageHeight); // Atur tinggi
        // Lebar akan disesuaikan otomatis untuk menjaga rasio aspek
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    } else {
        $sheet->setCellValue('N' . $rowNum, 'File tidak ditemukan');
    }

    // [FIX] Menyisipkan gambar KTP Pasangan
    if ($row['status_perkawinan'] == 'menikah' && !empty($row['path_ktp_pasangan'])) {
        $path_ktp_pasangan = '../' . $row['path_ktp_pasangan'];
        if (file_exists($path_ktp_pasangan)) {
            $drawing_pasangan = new Drawing();
            $drawing_pasangan->setName('KTP Pasangan');
            $drawing_pasangan->setPath($path_ktp_pasangan);
            $drawing_pasangan->setCoordinates('O' . $rowNum);
            $drawing_pasangan->setHeight($imageHeight); // Atur tinggi
            $drawing_pasangan->setOffsetX(5);
            $drawing_pasangan->setOffsetY(5);
            $drawing_pasangan->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('O' . $rowNum, 'File tidak ditemukan');
        }
    } else {
        $sheet->setCellValue('O' . $rowNum, '-');
    }

    // [FIX] Menyisipkan gambar SIKASEP 1
    if (!empty($row['path_sikasep_1'])) {
        $path_sikasep_1 = '../' . $row['path_sikasep_1'];
        if (file_exists($path_sikasep_1)) {
            $drawing = new Drawing();
            $drawing->setName('SIKASEP FILE 1');
            $drawing->setPath($path_sikasep_1);
            $drawing->setCoordinates('P' . $rowNum);
            $drawing->setHeight($imageHeight); // Atur tinggi
            // Lebar akan disesuaikan otomatis untuk menjaga rasio aspek
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('P' . $rowNum, 'File tidak ditemukan');
        }
    } else {
        $sheet->setCellValue('P' . $rowNum, '-');
    }

    // [FIX] Menyisipkan gambar SIKASEP 2
    if (!empty($row['path_sikasep_2'])) {
        $path_sikasep_2 = '../' . $row['path_sikasep_2'];
        if (file_exists($path_sikasep_2)) {
            $drawing = new Drawing();
            $drawing->setName('SIKASEP FILE 2');
            $drawing->setPath($path_sikasep_2);
            $drawing->setCoordinates('Q' . $rowNum);
            $drawing->setHeight($imageHeight); // Atur tinggi
            // Lebar akan disesuaikan otomatis untuk menjaga rasio aspek
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('Q' . $rowNum, 'File tidak ditemukan');
        }
    } else {
        $sheet->setCellValue('Q' . $rowNum, '-');
    }

    $sheet->setCellValue('R' . $rowNum, $row['slik_bi_checking'] ?? '-');
    $sheet->setCellValue('S' . $rowNum, $row['status_proses'] ?? '-');
    $sheet->setCellValue('T' . $rowNum, ucfirst($row['status_data']) ?? '-');
    
    $rowNum++;
}

// 7. Mengatur Lebar Kolom
foreach (range('A', 'T') as $columnID) {
    if ($columnID != 'F' && $columnID != 'M') { // Jangan auto-size kolom alamat
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
}
$sheet->getColumnDimension('F')->setWidth(40); // Lebar tetap untuk alamat karyawan
$sheet->getColumnDimension('M')->setWidth(40); // Lebar tetap untuk alamat pasangan
$sheet->getColumnDimension('N')->setWidth(25); // [FIX] Lebar kolom gambar Karyawan
$sheet->getColumnDimension('O')->setWidth(25); // [FIX] Lebar kolom gambar Pasangan
$sheet->getColumnDimension('P')->setWidth(25); // [FIX] Lebar kolom gambar SIKASEP 1
$sheet->getColumnDimension('Q')->setWidth(25); // [FIX] Lebar kolom gambar SIKASEP 2


// 8. Mengirim File ke Browser
$filename = "data-peserta-rumah-merdeka-" . date('Ymd-His') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Tutup koneksi dan hentikan skrip
$conn->close();
exit;
?>