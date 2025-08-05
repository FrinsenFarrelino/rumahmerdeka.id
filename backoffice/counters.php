<?php
// Mengambil data dari file API
require_once 'api/get_counters.php';

// [UPDATE v1.1] Menambahkan page_views ke dalam map
$button_map = [
    'page_views' => ['label' => 'Total Pengunjung Website', 'icon' => 'eye'],
    'daftar_sekarang_btn' => ['label' => 'Tombol "Daftar Sekarang" (Hero)', 'icon' => 'arrow-right-circle'],
    'saya_mau_daftar_btn' => ['label' => 'Tombol "Saya Mau Daftar"', 'icon' => 'home'],
    'submitBtn' => ['label' => 'Tombol "Kirim Pendaftaran"', 'icon' => 'send']
];
?>

<h2 class="text-3xl font-semibold text-gray-700 mb-6">Website Counters</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <?php foreach ($button_map as $id => $details): ?>
    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
        <?php // [UPDATE v1.1] Memberi warna berbeda untuk page_views
        $icon_color_class = ($id === 'page_views') ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600';
        ?>
        <div class="<?php echo $icon_color_class; ?> rounded-full p-3 mr-4">
            <i data-lucide="<?php echo $details['icon']; ?>" class="w-8 h-8"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm"><?php echo $details['label']; ?></p>
            <p class="text-3xl font-bold text-gray-800"><?php echo $counters[$id] ?? '0'; ?></p>
        </div>
    </div>
    <?php endforeach; ?>

</div>
