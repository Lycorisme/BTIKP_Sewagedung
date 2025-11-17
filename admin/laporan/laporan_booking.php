<?php
// admin/laporan/laporan_booking.php
$title = 'Laporan Booking';
require_once '../../config/config.php';
require_once '../../functions/helpers.php';
requireLogin();

// Filter
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? sanitize($_GET['tanggal_mulai']) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? sanitize($_GET['tanggal_akhir']) : date('Y-m-d');
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Query
$query = "SELECT b.*, g.nama_gedung, p.nama_lengkap, p.no_hp, k.nama_kategori
          FROM booking b
          JOIN gedung g ON b.id_gedung = g.id_gedung
          JOIN penyewa p ON b.id_penyewa = p.id_penyewa
          JOIN kategori_penyewa k ON p.id_kategori = k.id_kategori
          WHERE b.tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";

if ($status) {
    $query .= " AND b.status_booking = '$status'";
}

$query .= " ORDER BY b.tanggal_booking DESC";
$result = mysqli_query($conn, $query);

// Summary
$querySummary = "SELECT 
    COUNT(*) as total_booking,
    SUM(CASE WHEN status_booking = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status_booking = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status_booking = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status_booking = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status_booking = 'dibatalkan' THEN 1 ELSE 0 END) as dibatalkan,
    SUM(CASE WHEN status_booking IN ('confirmed', 'selesai') THEN total_bayar ELSE 0 END) as total_pendapatan
    FROM booking
    WHERE tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $querySummary));

include '../../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Laporan Booking</h1>
    <button onclick="printReport()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-print mr-2"></i>Cetak Laporan
    </button>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block font-semibold mb-2">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold mb-2">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $status == 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="selesai" <?php echo $status == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                <option value="dibatalkan" <?php echo $status == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-gray-500 text-sm">Total Booking</p>
        <p class="text-2xl font-bold text-blue-600"><?php echo $summary['total_booking']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600"><?php echo $summary['pending']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-gray-500 text-sm">Confirmed</p>
        <p class="text-2xl font-bold text-green-600"><?php echo $summary['confirmed']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-gray-500 text-sm">Total Pendapatan</p>
        <p class="text-xl font-bold text-purple-600"><?php echo formatRupiah($summary['total_pendapatan']); ?></p>
    </div>
</div>

<!-- Table -->
<div id="printArea" class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b print-header hidden">
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold">LAPORAN BOOKING</h2>
            <p class="text-gray-600">Periode: <?php echo formatTanggal($tanggal_mulai); ?> - <?php echo formatTanggal($tanggal_akhir); ?></p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gedung</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Acara</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $no = 1;
                $totalNominal = 0;
                while($row = mysqli_fetch_assoc($result)): 
                    if (in_array($row['status_booking'], ['confirmed', 'selesai'])) {
                        $totalNominal += $row['total_bayar'];
                    }
                ?>
                <tr>
                    <td class="px-4 py-3 text-sm"><?php echo $no++; ?></td>
                    <td class="px-4 py-3 text-sm font-medium"><?php echo $row['kode_booking']; ?></td>
                    <td class="px-4 py-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                    <td class="px-4 py-3 text-sm">
                        <div><?php echo $row['nama_lengkap']; ?></div>
                        <div class="text-gray-500 text-xs"><?php echo $row['no_hp']; ?></div>
                    </td>
                    <td class="px-4 py-3 text-sm"><?php echo $row['nama_gedung']; ?></td>
                    <td class="px-4 py-3 text-sm">
                        <?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?>
                    </td>
                    <td class="px-4 py-3 text-sm"><?php echo $row['durasi_hari']; ?> hari</td>
                    <td class="px-4 py-3 text-sm font-semibold"><?php echo formatRupiah($row['total_bayar']); ?></td>
                    <td class="px-4 py-3">
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            'selesai' => 'bg-gray-100 text-gray-800',
                            'dibatalkan' => 'bg-red-100 text-red-800'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$row['status_booking']]; ?>">
                            <?php echo ucfirst($row['status_booking']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                <tr class="bg-gray-50 font-bold">
                    <td colspan="7" class="px-4 py-3 text-right">TOTAL PENDAPATAN:</td>
                    <td colspan="2" class="px-4 py-3 text-blue-600"><?php echo formatRupiah($totalNominal); ?></td>
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
    .bg-yellow-100, .bg-blue-100, .bg-green-100, .bg-gray-100, .bg-red-100 {
        background-color: transparent !important;
        border: 1px solid #000;
    }
}
</style>

<script>
function printReport() {
    window.print();
}
</script>

<?php include '../../layouts/admin_footer.php'; ?>