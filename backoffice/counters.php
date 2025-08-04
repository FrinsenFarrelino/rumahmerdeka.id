<?php
// Mengambil data dari file API
require_once 'api/get_counters.php';

$button_map = [
    'daftar_sekarang_btn' => ['label' => 'Tombol "Daftar Sekarang" (Hero)', 'icon' => 'arrow-right-circle'],
    'saya_mau_daftar_btn' => ['label' => 'Tombol "Saya Mau Daftar"', 'icon' => 'home'],
    // 'submitBtn' => ['label' => 'Tombol "Kirim Pendaftaran"', 'icon' => 'send']
    'tes' => ['label' => 'Tombol "Kirim Pendaftaran"', 'icon' => 'send']
];
?>

<h2 class="text-3xl font-semibold text-gray-700 mb-6">Button Click Counters</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php foreach ($button_map as $id => $details): ?>
    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
        <div class="bg-red-100 text-red-600 rounded-full p-3 mr-4">
            <i data-lucide="<?php echo $details['icon']; ?>" class="w-8 h-8"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm"><?php echo $details['label']; ?></p>
            <p class="text-3xl font-bold text-gray-800"><?php echo $counters[$id] ?? '-'; ?></p>
        </div>
    </div>
    <?php endforeach; ?>

</div>
