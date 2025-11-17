<!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Sewa Gedung</h3>
                    <p class="text-gray-400">Platform terpercaya untuk penyewaan gedung dan ruang acara Anda.</p>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4">Menu</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white">Beranda</a></li>
                        <li><a href="daftar-gedung.php" class="text-gray-400 hover:text-white">Daftar Gedung</a></li>
                        <li><a href="tentang.php" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                        <li><a href="kontak.php" class="text-gray-400 hover:text-white">Kontak</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +62 812-3456-7890</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@sewagedung.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Palangkaraya, Kalimantan Tengah</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Sewa Gedung. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <?php
    // Show alert jika ada
    if (function_exists('showAlert')) {
        showAlert();
    }
    ?>
</body>
</html>