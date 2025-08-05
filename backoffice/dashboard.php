<?php
session_start();

// Jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Tentukan halaman mana yang akan ditampilkan. Defaultnya adalah analytics.
$page = $_GET['page'] ?? 'analytics';

// Daftar halaman yang valid untuk mencegah include file sembarangan
// [FIX v1.2.1] Menambahkan 'edit_participant' ke dalam array.
$allowed_pages = [
    'analytics',
    'participants',
    'counters',
    'edit_participant' // <-- INI PERBAIKANNYA
];

// Jika halaman yang diminta tidak ada dalam daftar, tampilkan halaman default
if (!in_array($page, $allowed_pages)) {
    $page = 'analytics';
}

// Memuat header
include 'partials/header.php';
?>

<div class="flex h-screen bg-gray-100">
    <?php
    // Memuat sidebar
    include 'partials/sidebar.php';
    ?>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <?php
                // Ini akan memuat file seperti 'analytics.php', 'participants.php', 'edit_participant.php', dll.
                // Pastikan file yang di-include ada.
                if (file_exists($page . '.php')) {
                    include $page . '.php';
                } else {
                    // Tampilkan pesan error jika file tidak ditemukan
                    echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>Error: Halaman '{$page}.php' tidak ditemukan.</div>";
                }
                ?>
            </div>
        </main>
    </div>
</div>

<?php
// Memuat footer
include 'partials/footer.php';
?>