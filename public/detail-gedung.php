<?php
// public/detail-gedung.php
$title = 'Detail Gedung - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

$id_gedung = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_gedung) {
    header('Location: daftar-gedung.php');
    exit;
}

// Ambil data gedung
$queryGedung = "SELECT * FROM gedung WHERE id_gedung = $id_gedung AND status = 'aktif' AND deleted_at IS NULL";
$resultGedung = mysqli_query($conn, $queryGedung);

if (mysqli_num_rows($resultGedung) == 0) {
    header('Location: daftar-gedung.php');
    exit;
}

$gedung = mysqli_fetch_assoc($resultGedung);

// Ambil galeri foto
$queryFoto = "SELECT * FROM foto_gedung WHERE id_gedung = $id_gedung ORDER BY urutan ASC";
$resultFoto = mysqli_query($conn, $queryFoto);

// Ambil booking yang sudah confirmed untuk kalender
$queryBooking = "SELECT tanggal_mulai, tanggal_selesai FROM booking 
                 WHERE id_gedung = $id_gedung 
                 AND status_booking IN ('approved', 'confirmed', 'selesai')";
$resultBooking = mysqli_query($conn, $queryBooking);

$bookedDates = [];
while($booking = mysqli_fetch_assoc($resultBooking)) {
    $start = new DateTime($booking['tanggal_mulai']);
    $end = new DateTime($booking['tanggal_selesai']);
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($start, $interval, $end->modify('+1 day'));
    
    foreach($daterange as $date){
        $bookedDates[] = $date->format('Y-m-d');
    }
}

include '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Breadcrumb -->
    <div class="mb-6 text-sm">
        <a href="index.php" class="text-blue-600 hover:text-blue-800">Beranda</a>
        <span class="mx-2">/</span>
        <a href="daftar-gedung.php" class="text-blue-600 hover:text-blue-800">Daftar Gedung</a>
        <span class="mx-2">/</span>
        <span class="text-gray-600"><?php echo $gedung['nama_gedung']; ?></span>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2">
            
            <!-- Foto Utama -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="h-96 bg-gray-300">
                    <?php if($gedung['foto_utama']): ?>
                        <img src="../uploads/gedung/<?php echo $gedung['foto_utama']; ?>" 
                             alt="<?php echo $gedung['nama_gedung']; ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-building text-gray-400 text-8xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Galeri Foto -->
            <?php if(mysqli_num_rows($resultFoto) > 0): ?>
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-4">Galeri Foto</h3>
                <div class="grid grid-cols-4 gap-4">
                    <?php while($foto = mysqli_fetch_assoc($resultFoto)): ?>
                    <div class="aspect-square bg-gray-300 rounded-lg overflow-hidden cursor-pointer hover:opacity-75 transition">
                        <img src="../uploads/galeri/<?php echo $foto['nama_file']; ?>" 
                             alt="<?php echo $foto['keterangan']; ?>"
                             class="w-full h-full object-cover">
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Deskripsi -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4"><?php echo $gedung['nama_gedung']; ?></h2>
                <div class="flex items-center text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                    <?php echo $gedung['alamat']; ?>
                </div>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line"><?php echo $gedung['deskripsi']; ?></p>
            </div>
            
            <!-- Spesifikasi -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Spesifikasi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-600 mr-3 text-xl"></i>
                        <div>
                            <span class="text-sm text-gray-600">Kapasitas</span>
                            <p class="font-semibold"><?php echo $gedung['kapasitas']; ?> orang</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-ruler-combined text-green-600 mr-3 text-xl"></i>
                        <div>
                            <span class="text-sm text-gray-600">Luas Gedung</span>
                            <p class="font-semibold"><?php echo $gedung['luas_gedung']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fasilitas -->
            <?php if($gedung['fasilitas']): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">Fasilitas</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <?php 
                    $fasilitas = explode(',', $gedung['fasilitas']);
                    foreach($fasilitas as $f): 
                    ?>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span><?php echo trim($f); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            
            <!-- Harga -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6 sticky top-4">
                <h3 class="text-lg font-bold mb-4">Harga Sewa</h3>
                
                <div class="mb-4 pb-4 border-b">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Weekday (Senin-Jumat)</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600"><?php echo formatRupiah($gedung['harga_weekday']); ?></p>
                    <span class="text-sm text-gray-500">per hari</span>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Weekend (Sabtu-Minggu)</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600"><?php echo formatRupiah($gedung['harga_weekend']); ?></p>
                    <span class="text-sm text-gray-500">per hari</span>
                </div>
                
                <a href="booking.php?gedung=<?php echo $gedung['id_gedung']; ?>" 
                   class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                </a>
            </div>
            
            <!-- Kalender Ketersediaan -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold mb-4">Ketersediaan</h3>
                <div class="text-sm mb-4">
                    <div class="flex items-center mb-2">
                        <div class="w-4 h-4 bg-green-200 rounded mr-2"></div>
                        <span>Tersedia</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-200 rounded mr-2"></div>
                        <span>Sudah Dibooking</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    Untuk melihat ketersediaan detail dan melakukan booking, silakan klik tombol "Booking Sekarang" di atas.
                </p>
            </div>
            
        </div>
        
    </div>
    
</div>

<script>
const bookedDates = <?php echo json_encode($bookedDates); ?>;
console.log('Tanggal yang sudah dibooking:', bookedDates);
</script>

<?php include '../layouts/footer.php'; ?>