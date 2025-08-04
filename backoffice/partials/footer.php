<!-- File: backoffice/partials/footer.php -->
<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

    // Logika untuk toggle sidebar di mobile
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
</script>
</body>
</html>
