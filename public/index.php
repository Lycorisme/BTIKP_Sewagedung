<?php
// public/index.php
$title = 'Beranda - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

// Ambil gedung unggulan (3 teratas)
$queryGedung = "SELECT * FROM gedung WHERE status = 'aktif' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 3";
$resultGedung = mysqli_query($conn, $queryGedung);

include '../layouts/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">Selamat Datang di Sewa Gedung</h1>
        <p class="text-xl md:text-2xl mb-8">Temukan gedung terbaik untuk acara Anda dengan mudah dan cepat</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="daftar-gedung.php" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition">
                <i class="fas fa-search mr-2"></i>Lihat Daftar Gedung
            </a>
            <a href="booking.php" class="bg-blue-500 text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-400 transition border-2 border-white">
                <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
            </a>
        </div>
    </div>
</section>

<!-- Cara Booking -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Cara Booking Mudah</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-blue-600 text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">1. Pilih Gedung</h3>
                <p class="text-gray-600">Cari gedung sesuai kebutuhan acara Anda</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-blue-600 text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">2. Cek Ketersediaan</h3>
                <p class="text-gray-600">Lihat tanggal yang tersedia</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-edit text-blue-600 text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">3. Isi Form Booking</h3>
                <p class="text-gray-600">Lengkapi data pemesanan Anda</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-blue-600 text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">4. Konfirmasi</h3>
                <p class="text-gray-600">Tunggu approval dari admin</p>
            </div>
        </div>
    </div>
</section>

<!-- Gedung Unggulan -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Gedung Unggulan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while($gedung = mysqli_fetch_assoc($resultGedung)): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="h-48 bg-gray-300 overflow-hidden">
                    <?php if($gedung['foto_utama']): ?>
                        <img src="../uploads/gedung/<?php echo $gedung['foto_utama']; ?>" 
                             alt="<?php echo $gedung['nama_gedung']; ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                            <i class="fas fa-building text-gray-400 text-5xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2"><?php echo $gedung['nama_gedung']; ?></h3>
                    <p class="text-gray-600 mb-4 line-clamp-2"><?php echo substr($gedung['deskripsi'], 0, 100); ?>...</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-users mr-1"></i><?php echo $gedung['kapasitas']; ?> orang
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-ruler-combined mr-1"></i><?php echo $gedung['luas_gedung']; ?>
                        </span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600 text-sm">Weekday:</span>
                            <span class="font-bold text-blue-600"><?php echo formatRupiah($gedung['harga_weekday']); ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600 text-sm">Weekend:</span>
                            <span class="font-bold text-blue-600"><?php echo formatRupiah($gedung['harga_weekend']); ?></span>
                        </div>
                        <a href="detail-gedung.php?id=<?php echo $gedung['id_gedung']; ?>" 
                           class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-10">
            <a href="daftar-gedung.php" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Lihat Semua Gedung <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Keunggulan -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Mengapa Memilih Kami?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-3">Terpercaya</h3>
                <p class="text-gray-600">Sistem booking yang aman dan terpercaya dengan approval admin</p>
            </div>
            
            <div class="text-center p-6">
                <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-3">Cepat & Mudah</h3>
                <p class="text-gray-600">Proses booking yang simpel dan cepat hanya dalam beberapa menit</p>
            </div>
            
            <div class="text-center p-6">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-3">Support 24/7</h3>
                <p class="text-gray-600">Tim kami siap membantu Anda kapan saja</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Siap Memesan Gedung?</h2>
        <p class="text-xl mb-8">Dapatkan penawaran terbaik untuk acara Anda</p>
        <a href="booking.php" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition">
            Mulai Booking Sekarang <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</section>

<?php include '../layouts/footer.php'; ?>