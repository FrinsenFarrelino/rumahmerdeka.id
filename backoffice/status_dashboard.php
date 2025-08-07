<?php
// File: backoffice/status_dashboard.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>Akses ditolak. Halaman ini hanya untuk Superadmin.</div>";
    return;
}

require_once '../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// [UPDATE v1.9] Ambil total keseluruhan peserta
$total_result = $conn->query("SELECT COUNT(id) as total FROM pendaftar");
$total_peserta = $total_result->fetch_assoc()['total'];

// [UPDATE v1.9] Query untuk mendapatkan jumlah peserta per status, mengelompokkan status kosong
$sql = "SELECT 
            CASE 
                WHEN status_proses IS NULL OR status_proses = '' THEN 'Belum Diproses' 
                ELSE status_proses 
            END as status_group, 
            COUNT(id) as jumlah 
        FROM pendaftar 
        GROUP BY status_group";
$result = $conn->query($sql);

// Opsi status yang mungkin ada
$status_proses_options = [
    'Proses BI Checking', 'Reject BI Checking', 'Pemberkasan', 'Pengajuan Kredit Bank',
    'Terbit SP3K', 'Reject Bank', 'Siap AKAD', 'Sudah AKAD'
];

// Inisialisasi semua status dengan 0
$status_counts = [];
foreach ($status_proses_options as $status) {
    $status_counts[$status] = 0;
}
$status_counts['Belum Diproses'] = 0; // Tambahkan status baru

// Isi jumlah dari hasil query
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status_counts[$row['status_group']] = $row['jumlah'];
    }
}
$conn->close();

// Data untuk Chart.js
$chart_labels = json_encode(array_keys($status_counts));
$chart_data = json_encode(array_values($status_counts));

// [UPDATE v1.9] Palet Warna ditambah satu untuk status baru
$colors = [
    'rgba(54, 162, 235, 0.8)',  // blue
    'rgba(255, 99, 132, 0.8)',  // red
    'rgba(255, 206, 86, 0.8)',  // yellow
    'rgba(75, 192, 192, 0.8)',  // green
    'rgba(153, 102, 255, 0.8)', // purple
    'rgba(255, 159, 64, 0.8)',  // orange
    'rgba(46, 204, 113, 0.8)',  // emerald
    'rgba(52, 73, 94, 0.8)',    // asphalt
    'rgba(149, 165, 166, 0.8)'  // grey (Belum Diproses)
];
$chart_colors = json_encode($colors);
?>

<h2 class="text-3xl font-semibold text-gray-700 mb-6">Dashboard Status Peserta</h2>
<!-- [UPDATE v1.9] Total peserta dihitung dari keseluruhan data -->
<p class="text-gray-600 mb-6">Total Keseluruhan Peserta Terdaftar: <span class="font-bold text-xl"><?php echo $total_peserta; ?></span></p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Card View -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Rekapitulasi Status</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($status_counts as $status => $jumlah): ?>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($status); ?></p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $jumlah; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chart View -->
    <div class="bg-white p-6 rounded-lg shadow-md">
         <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Visualisasi Data</h3>
        <canvas id="statusChart"></canvas>
    </div>
</div>

<!-- Script untuk Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chart_labels; ?>,
            datasets: [{
                label: 'Jumlah Peserta',
                data: <?php echo $chart_data; ?>,
                backgroundColor: <?php echo $chart_colors; ?>,
                borderColor: <?php echo str_replace('0.8', '1', $chart_colors); ?>,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Membuat bar menjadi horizontal agar label lebih mudah dibaca
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Jumlah Peserta per Status Proses'
                }
            }
        }
    });
});
</script>