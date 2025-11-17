<?php
// public/tentang.php
$title = 'Tentang Kami - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

include '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">Tentang Kami</h1>
        <p class="text-xl text-gray-600">Platform penyewaan gedung terpercaya untuk berbagai kebutuhan acara Anda</p>
    </div>
    
    <!-- Profil Perusahaan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <div>
            <div class="bg-blue-600 h-96 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-white text-9xl opacity-50"></i>
            </div>
        </div>
        <div class="flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-6">Siapa Kami?</h2>
            <p class="text-gray-700 mb-4 leading-relaxed">
                Sewa Gedung adalah platform penyewaan gedung dan ruang acara yang berkomitmen untuk memberikan 
                layanan terbaik dalam membantu Anda menemukan venue yang sempurna untuk berbagai kebutuhan acara.
            </p>
            <p class="text-gray-700 mb-4 leading-relaxed">
                Dengan pengalaman bertahun-tahun di industri ini, kami memahami betapa pentingnya memilih lokasi 
                yang tepat untuk kesuksesan acara Anda. Oleh karena itu, kami menyediakan berbagai pilihan gedung 
                dengan fasilitas lengkap dan harga yang kompetitif.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Tim kami siap membantu Anda dari proses pemilihan gedung hingga hari acara berlangsung, memastikan 
                setiap detail berjalan sesuai rencana.
            </p>
        </div>
    </div>
    
    <!-- Visi Misi -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-12 mb-16 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg mr-4">
                        <i class="fas fa-eye text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Visi</h3>
                </div>
                <p class="leading-relaxed">
                    Menjadi platform penyewaan gedung terkemuka yang memberikan solusi terbaik untuk setiap kebutuhan 
                    acara dengan layanan profesional dan berkualitas.
                </p>
            </div>
            <div>
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg mr-4">
                        <i class="fas fa-bullseye text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Misi</h3>
                </div>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Menyediakan pilihan gedung berkualitas dengan fasilitas lengkap</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Memberikan pelayanan yang cepat, mudah, dan transparan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Membangun kepercayaan pelanggan melalui sistem booking yang aman</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Keunggulan -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-center mb-12">Keunggulan Kami</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-medal text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Gedung Berkualitas</h3>
                <p class="text-gray-600">
                    Semua gedung yang kami tawarkan telah melalui seleksi ketat untuk memastikan kualitas dan 
                    kelayakan fasilitas.
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Harga Kompetitif</h3>
                <p class="text-gray-600">
                    Kami menawarkan harga sewa yang kompetitif dengan berbagai pilihan paket sesuai kebutuhan dan 
                    budget Anda.
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-purple-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Support Terbaik</h3>
                <p class="text-gray-600">
                    Tim customer service kami siap membantu Anda 24/7 untuk memastikan acara Anda berjalan lancar.
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Keamanan Terjamin</h3>
                <p class="text-gray-600">
                    Sistem booking kami menggunakan teknologi keamanan terkini untuk melindungi data dan transaksi Anda.
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-yellow-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Proses Cepat</h3>
                <p class="text-gray-600">
                    Proses booking yang mudah dan cepat, dari pemilihan gedung hingga konfirmasi hanya dalam hitungan menit.
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-indigo-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Terpercaya</h3>
                <p class="text-gray-600">
                    Telah dipercaya oleh ribuan pelanggan untuk berbagai jenis acara dari pernikahan hingga seminar.
                </p>
            </div>
        </div>
    </div>
    
    <!-- CTA -->
    <div class="bg-blue-600 rounded-lg p-12 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Siap Booking Gedung Anda?</h2>
        <p class="text-xl mb-8">Hubungi kami sekarang atau langsung booking online</p>
        <div class="flex gap-4 justify-center">
            <a href="booking.php" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-bold hover:bg-blue-50 transition">
                <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
            </a>
            <a href="kontak.php" class="bg-blue-500 text-white px-8 py-4 rounded-lg font-bold hover:bg-blue-400 transition border-2 border-white">
                <i class="fas fa-phone mr-2"></i>Hubungi Kami
            </a>
        </div>
    </div>
    
</div>

<?php include '../layouts/footer.php'; ?>