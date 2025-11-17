<?php
// admin/index.php
$title = 'Dashboard';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Statistik
$statGedung = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gedung WHERE deleted_at IS NULL AND status='aktif'"));
$statBooking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE MONTH(tanggal_booking) = MONTH(CURDATE())"));
$statPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status_booking = 'pending'"));
$statPendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_bayar), 0) as total FROM booking WHERE status_booking IN ('confirmed', 'selesai') AND MONTH(tanggal_booking) = MONTH(CURDATE())"));

// Booking terbaru
$queryBookingTerbaru = "SELECT b.*, g.nama_gedung, p.nama_lengkap 
                        FROM booking b
                        JOIN gedung g ON b.id_gedung = g.id_gedung
                        JOIN penyewa p ON b.id_penyewa = p.id_penyewa
                        ORDER BY b.created_at DESC LIMIT 5";
$resultBookingTerbaru = mysqli_query($conn, $queryBookingTerbaru);

// Gedung populer
$queryGedungPopuler = "SELECT g.nama_gedung, COUNT(b.id_booking) as total_booking
                       FROM gedung g
                       LEFT JOIN booking b ON g.id_gedung = b.id_gedung
                       WHERE g.deleted_at IS NULL
                       GROUP BY g.id_gedung
                       ORDER BY total_booking DESC LIMIT 5";
$resultGedungPopuler = mysqli_query($conn, $queryGedungPopuler);

// Data untuk chart
$queryChartBooking = "SELECT 
    DATE_FORMAT(tanggal_booking, '%Y-%m') as bulan,
    COUNT(*) as total
    FROM booking
    WHERE tanggal_booking >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY bulan
    ORDER BY bulan";
$resultChartBooking = mysqli_query($conn, $queryChartBooking);
$chartLabels = [];
$chartData = [];
while($row = mysqli_fetch_assoc($resultChartBooking)) {
    $chartLabels[] = $row['bulan'];
    $chartData[] = $row['total'];
}

include '../layouts/admin_header.php';
?>

<h1 class="text-3xl font-bold mb-8">Dashboard</h1>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Gedung</p>
                <p class="text-3xl font-bold text-blue-600"><?php echo $statGedung['total']; ?></p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-building text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Booking Bulan Ini</p>
                <p class="text-3xl font-bold text-green-600"><?php echo $statBooking['total']; ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Menunggu Approval</p>
                <p class="text-3xl font-bold text-yellow-600"><?php echo $statPending['total']; ?></p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
        <?php if($statPending['total'] > 0): ?>
        <a href="booking.php?status=pending" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
        <?php endif; ?>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pendapatan Bulan Ini</p>
                <p class="text-2xl font-bold text-purple-600"><?php echo formatRupiah($statPendapatan['total']); ?></p>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-money-bill-wave text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <!-- Chart Booking -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Statistik Booking (6 Bulan Terakhir)</h3>
        <canvas id="chartBooking"></canvas>
    </div>
    
    <!-- Gedung Populer -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Gedung Terpopuler</h3>
        <div class="space-y-3">
            <?php while($gedung = mysqli_fetch_assoc($resultGedungPopuler)): ?>
            <div class="flex items-center justify-between border-b pb-2">
                <div class="flex items-center">
                    <i class="fas fa-building text-blue-600 mr-3"></i>
                    <span><?php echo $gedung['nama_gedung']; ?></span>
                </div>
                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-semibold">
                    <?php echo $gedung['total_booking']; ?> booking
                </span>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
</div>

<!-- Booking Terbaru -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold">Booking Terbaru</h3>
            <a href="booking.php" class="text-blue-600 hover:text-blue-800 text-sm">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($booking = mysqli_fetch_assoc($resultBookingTerbaru)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium"><?php echo $booking['kode_booking']; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo $booking['nama_lengkap']; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo $booking['nama_gedung']; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo formatTanggal($booking['tanggal_mulai']); ?></td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            'selesai' => 'bg-gray-100 text-gray-800',
                            'dibatalkan' => 'bg-red-100 text-red-800'
                        ];
                        $statusClass = $statusColors[$booking['status_booking']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                            <?php echo ucfirst($booking['status_booking']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold"><?php echo formatRupiah($booking['total_bayar']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart.js - Booking Statistics
const ctx = document.getElementById('chartBooking').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Total Booking',
            data: <?php echo json_encode($chartData); ?>,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include '../layouts/admin_footer.php'; ?>