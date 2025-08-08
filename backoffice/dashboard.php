<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$page = $_GET['page'] ?? 'participants';

// [UPDATE v2.0] Menambahkan 'pilih_unit' ke dalam array.
$allowed_pages = [
    'analytics',
    'participants',
    'counters',
    'edit_participant',
    'status_dashboard',
    'pilih_unit' // <-- INI PERUBAHANNYA
];

if (!in_array($page, $allowed_pages)) {
    $page = 'participants';
}

include 'partials/header.php';
?>

<div class="flex h-screen bg-gray-100">
    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <?php
                if (file_exists($page . '.php')) {
                    include $page . '.php';
                } else {
                    echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>Error: Halaman '{$page}.php' tidak ditemukan.</div>";
                }
                ?>
            </div>
        </main>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
