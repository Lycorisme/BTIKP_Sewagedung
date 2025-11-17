<?php
// public/kontak.php
$title = 'Kontak Kami - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

// Proses form kontak
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $subjek = sanitize($_POST['subjek']);
    $pesan = sanitize($_POST['pesan']);
    
    // Dalam implementasi real, bisa kirim email atau simpan ke database
    // Untuk saat ini hanya tampilkan notifikasi
    setAlert('success', 'Pesan Terkirim!', 'Terima kasih, pesan Anda telah kami terima. Tim kami akan segera menghubungi Anda.');
    header('Location: kontak.php');
    exit;
}

include '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Hubungi Kami</h1>
            <p class="text-xl text-gray-600">Ada pertanyaan? Kami siap membantu Anda</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Info Kontak -->
            <div>
                <h2 class="text-2xl font-bold mb-6">Informasi Kontak</h2>
                
                <div class="space-y-6 mb-8">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-map-marker-alt text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Alamat</h3>
                            <p class="text-gray-600">
                                Jl. G. Obos No. 123<br>
                                Palangkaraya, Kalimantan Tengah<br>
                                73111
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-green-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-phone text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Telepon</h3>
                            <p class="text-gray-600">
                                +62 812-3456-7890<br>
                                +62 536-123456
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-purple-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-envelope text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Email</h3>
                            <p class="text-gray-600">
                                info@sewagedung.com<br>
                                booking@sewagedung.com
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-yellow-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Jam Operasional</h3>
                            <p class="text-gray-600">
                                Senin - Jumat: 08:00 - 17:00<br>
                                Sabtu: 08:00 - 14:00<br>
                                Minggu: Tutup
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4">
                        <a href="#" class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-pink-600 text-white w-12 h-12 rounded-full flex items-center justify-center hover:bg-pink-700 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-green-500 text-white w-12 h-12 rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="bg-blue-400 text-white w-12 h-12 rounded-full flex items-center justify-center hover:bg-blue-500 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Form Kontak -->
            <div>
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Kirim Pesan</h2>
                    
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="block font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan nama Anda">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="email@example.com">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block font-semibold mb-2">Subjek <span class="text-red-500">*</span></label>
                            <input type="text" name="subjek" required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Subjek pesan">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block font-semibold mb-2">Pesan <span class="text-red-500">*</span></label>
                            <textarea name="pesan" rows="5" required
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Tuliskan pesan Anda..."></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
        
        <!-- Map (Optional - bisa gunakan Google Maps embed) -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6 text-center">Lokasi Kami</h2>
            <div class="bg-gray-200 rounded-lg overflow-hidden" style="height: 400px;">
                <!-- Placeholder untuk map - dalam produksi bisa embed Google Maps -->
                <div class="w-full h-full flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-gray-400 text-6xl mb-4"></i>
                        <p class="text-gray-600">Google Maps akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-6 text-center">Pertanyaan yang Sering Diajukan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-2">Bagaimana cara booking gedung?</h3>
                    <p class="text-gray-600">Anda dapat melakukan booking melalui form online di website kami. Pilih gedung, isi data, dan tunggu konfirmasi dari admin.</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-2">Berapa lama proses approval?</h3>
                    <p class="text-gray-600">Proses approval biasanya memakan waktu 1-2 hari kerja. Anda akan dihubungi melalui email/telepon setelah booking disetujui.</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-2">Apakah ada DP atau pembayaran penuh?</h3>
                    <p class="text-gray-600">Sistem pembayaran dapat disesuaikan. Bisa DP terlebih dahulu atau pembayaran penuh tergantung kesepakatan.</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-2">Bisakah membatalkan booking?</h3>
                    <p class="text-gray-600">Pembatalan dapat dilakukan dengan menghubungi admin kami. Kebijakan pembatalan tergantung pada waktu pembatalan.</p>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<?php include '../layouts/footer.php'; ?>