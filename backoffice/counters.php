<?php
// File: backoffice/counters.php

// Menggunakan file API yang sudah ada
require_once 'api/get_counters.php';
?>

<h2 class="text-3xl font-semibold text-gray-700 mb-6">Penghitung Interaksi Tombol</h2>

<div class="bg-white shadow-md rounded-lg p-6">
    <p class="mb-4 text-gray-600">Total Click</p>
    
    <?php if (!empty($counters)): ?>
        <ul class="list-disc list-inside space-y-2">
            <?php foreach ($counters as $button_id => $count): ?>
                <li class="text-gray-700">
                    <span class="font-semibold"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $button_id))); ?>:</span>
                    <span class="text-xl font-bold text-blue-600"><?php echo $count; ?></span> klik
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-center py-5 text-gray-500">Data penghitung tidak ditemukan atau masih kosong.</p>
    <?php endif; ?>
</div>