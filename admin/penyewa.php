<?php
// admin/penyewa.php
$title = 'Data Penyewa';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';

// Query penyewa
$query = "SELECT p.*, k.nama_kategori,
          (SELECT COUNT(*) FROM booking WHERE id_penyewa = p.id_penyewa) as total_booking
          FROM penyewa p
          JOIN kategori_penyewa k ON p.id_kategori = k.id_kategori
          WHERE 1=1";

if ($search) {
    $query .= " AND (p.nama_lengkap LIKE '%$search%' OR p.email LIKE '%$search%' OR p.no_hp LIKE '%$search%')";
}

if ($kategori) {
    $query .= " AND p.id_kategori = '$kategori'";
}

$query .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

// Get kategori for filter
$queryKategori = "SELECT * FROM kategori_penyewa WHERE status = 'aktif' ORDER BY nama_kategori";
$resultKategori = mysqli_query($conn, $queryKategori);

// Get detail if view
$detailData = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $queryDetail = "SELECT p.*, k.nama_kategori, k.diskon_persen
                    FROM penyewa p
                    JOIN kategori_penyewa k ON p.id_kategori = k.id_kategori
                    WHERE p.id_penyewa = $id";
    $resultDetail = mysqli_query($conn, $queryDetail);
    $detailData = mysqli_fetch_assoc($resultDetail);
    
    // Get booking history
    if ($detailData) {
        $queryBooking = "SELECT b.*, g.nama_gedung
                        FROM booking b
                        JOIN gedung g ON b.id_gedung = g.id_gedung
                        WHERE b.id_penyewa = $id
                        ORDER BY b.created_at DESC";
        $resultBooking = mysqli_query($conn, $queryBooking);
    }
}

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Data Penyewa</h1>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block font-semibold mb-2">Cari Penyewa</label>
            <input type="text" name="search" value="<?php echo $search; ?>" 
                   placeholder="Nama, email, atau HP..."
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold mb-2">Kategori</label>
            <select name="kategori" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <?php 
                mysqli_data_seek($resultKategori, 0);
                while($kat = mysqli_fetch_assoc($resultKategori)): 
                ?>
                    <option value="<?php echo $kat['id_kategori']; ?>" <?php echo $kategori == $kat['id_kategori'] ? 'selected' : ''; ?>>
                        <?php echo $kat['nama_kategori']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="penyewa.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instansi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdaftar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm"><?php echo $no++; ?></td>
                    <td class="px-6 py-4">
                        <div class="font-semibold"><?php echo $row['nama_lengkap']; ?></div>
                        <?php if ($row['no_ktp']): ?>
                        <div class="text-sm text-gray-500">KTP: <?php echo $row['no_ktp']; ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div><?php echo $row['email']; ?></div>
                        <div class="text-gray-500"><?php echo $row['no_hp']; ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <?php echo $row['nama_kategori']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['instansi'] ?: '-'; ?></td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-green-600"><?php echo $row['total_booking']; ?></span> kali
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewDetail(<?php echo $row['id_penyewa']; ?>)" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<?php if ($detailData): ?>
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-lg w-full max-w-5xl my-8">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold">Detail Penyewa - <?php echo $detailData['nama_lengkap']; ?></h3>
            <button onclick="closeDetail()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <!-- Informasi Penyewa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Data Pribadi</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Nama Lengkap:</span> <strong><?php echo $detailData['nama_lengkap']; ?></strong></div>
                        <div><span class="text-gray-600">Email:</span> <?php echo $detailData['email']; ?></div>
                        <div><span class="text-gray-600">No HP:</span> <?php echo $detailData['no_hp']; ?></div>
                        <div><span class="text-gray-600">No KTP:</span> <?php echo $detailData['no_ktp'] ?: '-'; ?></div>
                        <div><span class="text-gray-600">Alamat:</span> <?php echo $detailData['alamat'] ?: '-'; ?></div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Informasi Lainnya</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Kategori:</span> 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?php echo $detailData['nama_kategori']; ?>
                            </span>
                        </div>
                        <div><span class="text-gray-600">Diskon:</span> <strong class="text-green-600"><?php echo $detailData['diskon_persen']; ?>%</strong></div>
                        <div><span class="text-gray-600">Instansi:</span> <?php echo $detailData['instansi'] ?: '-'; ?></div>
                        <div><span class="text-gray-600">Terdaftar:</span> <?php echo formatDateTime($detailData['created_at']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Dokumen -->
            <?php if ($detailData['file_ktp'] || $detailData['file_surat']): ?>
            <div class="border rounded-lg p-4 mb-6">
                <h4 class="font-bold mb-3 text-lg">Dokumen</h4>
                <div class="grid grid-cols-2 gap-4">
                    <?php if ($detailData['file_ktp']): ?>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">File KTP</p>
                        <a href="../uploads/dokumen/<?php echo $detailData['file_ktp']; ?>" target="_blank"
                           class="inline-block bg-blue-100 text-blue-600 px-4 py-2 rounded hover:bg-blue-200">
                            <i class="fas fa-file-image mr-2"></i>Lihat KTP
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($detailData['file_surat']): ?>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">File Surat</p>
                        <a href="../uploads/dokumen/<?php echo $detailData['file_surat']; ?>" target="_blank"
                           class="inline-block bg-green-100 text-green-600 px-4 py-2 rounded hover:bg-green-200">
                            <i class="fas fa-file-pdf mr-2"></i>Lihat Surat
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- History Booking -->
            <div class="border rounded-lg p-4">
                <h4 class="font-bold mb-3 text-lg">History Booking</h4>
                <?php if (mysqli_num_rows($resultBooking) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kode</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Gedung</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($booking = mysqli_fetch_assoc($resultBooking)): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm"><?php echo $booking['kode_booking']; ?></td>
                                <td class="px-4 py-2 text-sm"><?php echo $booking['nama_gedung']; ?></td>
                                <td class="px-4 py-2 text-sm"><?php echo formatTanggal($booking['tanggal_mulai']); ?></td>
                                <td class="px-4 py-2 text-sm font-semibold"><?php echo formatRupiah($booking['total_bayar']); ?></td>
                                <td class="px-4 py-2">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'selesai' => 'bg-gray-100 text-gray-800',
                                        'dibatalkan' => 'bg-red-100 text-red-800'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$booking['status_booking']]; ?>">
                                        <?php echo ucfirst($booking['status_booking']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-gray-500 py-4">Belum ada history booking</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function viewDetail(id) {
    window.location.href = 'penyewa.php?view=' + id;
}

function closeDetail() {
    window.location.href = 'penyewa.php';
}
</script>

<?php include '../layouts/admin_footer.php'; ?>