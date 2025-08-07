<!-- File: backoffice/partials/sidebar.php -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-white transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between">
    
    <!-- Navigasi Utama -->
    <div>
        <div class="px-8 py-4">
            <h2 class="text-2xl font-semibold">Rumah Merdeka</h2>
            <span class="text-sm text-gray-400">Backoffice Panel</span>
        </div>
        <nav class="mt-4">
            <a href="dashboard.php?page=participants" class="flex items-center px-8 py-3 hover:bg-gray-700">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i> Peserta
            </a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
                <!-- [UPDATE v1.8] Link ke Dashboard Status -->
                <a href="dashboard.php?page=status_dashboard" class="flex items-center px-8 py-3 hover:bg-gray-700">
                    <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i> Dashboard Status
                </a>
                <a href="dashboard.php?page=analytics" class="flex items-center px-8 py-3 hover:bg-gray-700">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i> Analytics
                </a>
                <a href="dashboard.php?page=counters" class="flex items-center px-8 py-3 hover:bg-gray-700">
                    <i data-lucide="mouse-pointer-click" class="w-5 h-5 mr-3"></i> Counter
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Info Pengguna & Logout -->
    <div class="px-6 py-4 border-t border-gray-700">
        <div class="flex items-center mb-4">
            <i data-lucide="user-circle" class="w-10 h-10 mr-3 text-gray-400"></i>
            <div>
                <p class="font-semibold text-sm"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></p>
                <p class="text-xs text-gray-400 capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'No Role'); ?></p>
            </div>
        </div>
        <a href="logout.php" class="flex items-center justify-center w-full py-2 bg-red-600/20 text-red-300 hover:bg-red-600/40 hover:text-red-200 rounded-lg transition-colors">
            <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
            <span>Logout</span>
        </a>
    </div>
</div>