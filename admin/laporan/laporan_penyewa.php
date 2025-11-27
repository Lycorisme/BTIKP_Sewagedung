<?php
// admin/laporan/laporan_penyewa.php
$title = 'Laporan Penyewa';
require_once '../../config/config.php';
require_once '../../functions/helpers.php';
requireLogin();

// Filter
$kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';
$min_booking = isset($_GET['min_booking']) ? (int)$_GET['min_booking'] : 0;

// Query penyewa
$query = "SELECT 
    p.*,
    k.nama_kategori,
    k.diskon_persen,
    COUNT(b.id_booking) as total_booking,
    SUM(CASE WHEN b.status_booking = 'selesai' THEN 1 ELSE 0 END) as booking_selesai,
    SUM(CASE WHEN b.status_booking = 'dibatalkan' THEN 1 ELSE 0 END) as booking_batal,
    SUM(b.total_bayar) as total_transaksi,
    MAX(b.tanggal_booking) as booking_terakhir,
    MIN(b.tanggal_booking) as booking_pertama
    FROM penyewa p
    LEFT JOIN kategori_penyewa k ON p.id_kategori = k.id_kategori
    LEFT JOIN booking b ON p.id_penyewa = b.id_penyewa
    WHERE 1=1";

if ($kategori) {
    $query .= " AND p.id_kategori = '$kategori'";
}

$query .= " GROUP BY p.id_penyewa";

if ($min_booking > 0) {
    $query .= " HAVING total_booking >= $min_booking";
}

$query .= " ORDER BY total_booking DESC, total_transaksi DESC";
$result = mysqli_query($conn, $query);

// Summary
$querySummary = "SELECT 
    COUNT(DISTINCT p.id_penyewa) as total_penyewa,
    COUNT(b.id_booking) as total_booking,
    SUM(b.total_bayar) as total_transaksi,
    AVG(b.total_bayar) as rata_rata_transaksi
    FROM penyewa p
    LEFT JOIN booking b ON p.id_penyewa = b.id_penyewa";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $querySummary));

// Kategori penyewa distribution
$queryKatDist = "SELECT 
    k.nama_kategori,
    COUNT(DISTINCT p.id_penyewa) as total_penyewa,
    COUNT(b.id_booking) as total_booking
    FROM kategori_penyewa k
    LEFT JOIN penyewa p ON k.id_kategori = p.id_kategori
    LEFT JOIN booking b ON p.id_penyewa = b.id_penyewa
    WHERE k.status = 'aktif'
    GROUP BY k.id_kategori
    ORDER BY total_penyewa DESC";
$resultKatDist = mysqli_query($conn, $queryKatDist);

// Get kategori for filter
$queryKategori = "SELECT * FROM kategori_penyewa WHERE status = 'aktif' ORDER BY nama_kategori";
$resultKategori = mysqli_query($conn, $queryKategori);

include '../../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Laporan Penyewa</h1>
    <button onclick="printReport()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-print mr-2"></i>Cetak Laporan
    </button>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block font-semibold mb-2">Kategori Penyewa</label>
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
        <div>
            <label class="block font-semibold mb-2">Min. Booking</label>
            <input type="number" name="min_booking" value="<?php echo $min_booking > 0 ? $min_booking : ''; ?>"
                   placeholder="Contoh: 2"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="laporan_penyewa.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Total Penyewa</p>
        <p class="text-3xl font-bold text-blue-600"><?php echo $summary['total_penyewa']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Total Booking</p>
        <p class="text-3xl font-bold text-green-600"><?php echo $summary['total_booking']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Total Transaksi</p>
        <p class="text-2xl font-bold text-purple-600"><?php echo formatRupiah($summary['total_transaksi']); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Rata-rata per Booking</p>
        <p class="text-2xl font-bold text-orange-600"><?php echo formatRupiah($summary['rata_rata_transaksi']); ?></p>
    </div>
</div>

<!-- Distribusi Kategori -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">Distribusi per Kategori</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php while($kat = mysqli_fetch_assoc($resultKatDist)): ?>
        <div class="border rounded-lg p-4">
            <h4 class="font-semibold mb-2"><?php echo $kat['nama_kategori']; ?></h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Penyewa:</span>
                    <span class="font-semibold text-blue-600"><?php echo $kat['total_penyewa']; ?> orang</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking:</span>
                    <span class="font-semibold text-green-600"><?php echo $kat['total_booking']; ?> kali</span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Table -->
<div id="printArea" class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b print-header hidden">
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold">LAPORAN DATA PENYEWA</h2>
            <p class="text-gray-600">History Booking & Transaksi</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Penyewa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Booking</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Transaksi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking Terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $no = 1;
                $total_booking_all = 0;
                $total_transaksi_all = 0;
                while($row = mysqli_fetch_assoc($result)): 
                    $total_booking_all += $row['total_booking'];
                    $total_transaksi_all += $row['total_transaksi'];
                    
                    // Badge for loyal customer (3+ bookings)
                    $badge = '';
                    if ($row['total_booking'] >= 5) {
                        $badge = '<span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">VIP</span>';
                    } elseif ($row['total_booking'] >= 3) {
                        $badge = '<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Loyal</span>';
                    }
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 text-sm"><?php echo $no++; ?></td>
                    <td class="px-4 py-4">
                        <div class="font-semibold"><?php echo $row['nama_lengkap']; ?> <?php echo $badge; ?></div>
                        <?php if ($row['instansi']): ?>
                        <div class="text-sm text-gray-500"><?php echo $row['instansi']; ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <div><?php echo $row['no_hp']; ?></div>
                        <div class="text-gray-500 text-xs"><?php echo $row['email']; ?></div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <?php echo $row['nama_kategori']; ?>
                        </span>
                        <?php if ($row['diskon_persen'] > 0): ?>
                        <div class="text-xs text-green-600 mt-1">Diskon <?php echo $row['diskon_persen']; ?>%</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold text-sm">
                            <?php echo $row['total_booking']; ?> kali
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-green-600 font-semibold"><?php echo $row['booking_selesai']; ?></td>
                    <td class="px-4 py-4 text-sm text-red-600 font-semibold"><?php echo $row['booking_batal']; ?></td>
                    <td class="px-4 py-4 text-sm font-semibold text-purple-600"><?php echo formatRupiah($row['total_transaksi']); ?></td>
                    <td class="px-4 py-4 text-sm">
                        <?php if ($row['booking_terakhir']): ?>
                        <?php echo date('d/m/Y', strtotime($row['booking_terakhir'])); ?>
                        <?php else: ?>
                        <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <tr class="bg-gray-50 font-bold">
                    <td colspan="4" class="px-4 py-4 text-right">TOTAL:</td>
                    <td class="px-4 py-4 text-green-600"><?php echo $total_booking_all; ?> kali</td>
                    <td colspan="2"></td>
                    <td colspan="2" class="px-4 py-4 text-purple-600"><?php echo formatRupiah($total_transaksi_all); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print-header {
        display: block !important;
    }
}
</style>

<script>
function printReport() {
    window.print();
}
</script>

<?php include '../../layouts/admin_footer.php'; ?>  