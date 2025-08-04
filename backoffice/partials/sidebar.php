<!-- File: backoffice/partials/sidebar.php -->
<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-white transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="px-8 py-4">
        <h2 class="text-2xl font-semibold">Rumah Merdeka</h2>
        <span class="text-sm text-gray-400">Backoffice Panel</span>
    </div>
    <nav class="mt-4">
        <a href="dashboard.php?page=analytics" class="flex items-center px-8 py-3 hover:bg-gray-700">
            <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i> Analytics
        </a>
        <a href="dashboard.php?page=participants" class="flex items-center px-8 py-3 hover:bg-gray-700">
            <i data-lucide="users" class="w-5 h-5 mr-3"></i> Peserta
        </a>
        <a href="dashboard.php?page=counters" class="flex items-center px-8 py-3 hover:bg-gray-700">
            <i data-lucide="mouse-pointer-click" class="w-5 h-5 mr-3"></i> Penghitung
        </a>
        <a href="logout.php" class="flex items-center px-8 py-3 mt-4 text-red-400 hover:bg-gray-700">
            <i data-lucide="log-out" class="w-5 h-5 mr-3"></i> Logout
        </a>
    </nav>
</div>
