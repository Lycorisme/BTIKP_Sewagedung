<?php
// public/daftar-gedung.php
$title = 'Daftar Gedung - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

// Filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$min_kapasitas = isset($_GET['min_kapasitas']) ? (int)$_GET['min_kapasitas'] : 0;
$max_harga = isset($_GET['max_harga']) ? (int)$_GET['max_harga'] : 0;

// Query gedung
$query = "SELECT * FROM gedung WHERE status = 'aktif' AND deleted_at IS NULL";

if ($search) {
    $query .= " AND (nama_gedung LIKE '%$search%' OR alamat LIKE '%$search%')";
}

if ($min_kapasitas > 0) {
    $query .= " AND kapasitas >= $min_kapasitas";
}

if ($max_harga > 0) {
    $query .= " AND (harga_weekday <= $max_harga OR harga_weekend <= $max_harga)";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

include '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Daftar Gedung</h1>
        <p class="text-gray-600">Temukan gedung yang sesuai dengan kebutuhan acara Anda</p>
    </div>
    
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Cari Gedung</label>
                <input type="text" name="search" value="<?php echo $search; ?>" 
                       placeholder="Nama gedung atau lokasi..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-2">Min. Kapasitas</label>
                <input type="number" name="min_kapasitas" value="<?php echo $min_kapasitas > 0 ? $min_kapasitas : ''; ?>" 
                       placeholder="Contoh: 100"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-2">Max. Harga</label>
                <input type="number" name="max_harga" value="<?php echo $max_harga > 0 ? $max_harga : ''; ?>" 
                       placeholder="Contoh: 5000000"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="daftar-gedung.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
    
    <!-- Gedung Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($gedung = mysqli_fetch_assoc($result)): ?>
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
                
                <div class="p-5">
                    <h3 class="font-bold text-xl mb-2"><?php echo $gedung['nama_gedung']; ?></h3>
                    
                    <div class="flex items-center text-gray-600 text-sm mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                        <?php echo substr($gedung['alamat'], 0, 50); ?>...
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        <?php echo substr($gedung['deskripsi'], 0, 100); ?>...
                    </p>
                    
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-blue-50 px-3 py-2 rounded text-center">
                            <i class="fas fa-users text-blue-600"></i>
                            <span class="text-sm font-semibold ml-1"><?php echo $gedung['kapasitas']; ?> orang</span>
                        </div>
                        <div class="bg-green-50 px-3 py-2 rounded text-center">
                            <i class="fas fa-ruler-combined text-green-600"></i>
                            <span class="text-sm font-semibold ml-1"><?php echo $gedung['luas_gedung']; ?></span>
                        </div>
                    </div>
                    
                    <?php if($gedung['fasilitas']): ?>
                    <div class="mb-4">
                        <div class="flex flex-wrap gap-1">
                            <?php 
                            $fasilitas = explode(',', $gedung['fasilitas']);
                            $show = array_slice($fasilitas, 0, 3);
                            foreach($show as $f): 
                            ?>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo trim($f); ?></span>
                            <?php endforeach; ?>
                            <?php if(count($fasilitas) > 3): ?>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">+<?php echo count($fasilitas) - 3; ?> lagi</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 text-sm">Weekday:</span>
                            <span class="font-bold text-blue-600 text-sm"><?php echo formatRupiah($gedung['harga_weekday']); ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600 text-sm">Weekend:</span>
                            <span class="font-bold text-blue-600 text-sm"><?php echo formatRupiah($gedung['harga_weekend']); ?></span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <a href="detail-gedung.php?id=<?php echo $gedung['id_gedung']; ?>" 
                               class="text-center bg-white border-2 border-blue-600 text-blue-600 py-2 rounded-lg hover:bg-blue-50 transition text-sm font-semibold">
                                Detail
                            </a>
                            <a href="booking.php?gedung=<?php echo $gedung['id_gedung']; ?>" 
                               class="text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                Booking
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-12">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Gedung Tidak Ditemukan</h3>
                <p class="text-gray-500">Coba ubah filter pencarian Anda</p>
                <a href="daftar-gedung.php" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </a>
            </div>
        <?php endif; ?>
    </div>
    
</div>

<?php include '../layouts/footer.php'; ?>