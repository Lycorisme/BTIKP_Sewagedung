<?php
// admin/laporan/laporan_status_booking.php
$title = 'Laporan Status Booking';
require_once '../../config/config.php';
require_once '../../functions/helpers.php';
requireLogin();

// Filter
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? sanitize($_GET['tanggal_mulai']) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? sanitize($_GET['tanggal_akhir']) : date('Y-m-d');

// Query status booking
$queryStatus = "SELECT 
    status_booking,
    COUNT(*) as total,
    SUM(total_bayar) as total_nilai
    FROM booking
    WHERE tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
    GROUP BY status_booking
    ORDER BY 
        CASE status_booking
            WHEN 'pending' THEN 1
            WHEN 'approved' THEN 2
            WHEN 'confirmed' THEN 3
            WHEN 'selesai' THEN 4
            WHEN 'dibatalkan' THEN 5
        END";
$resultStatus = mysqli_query($conn, $queryStatus);

// Summary
$querySummary = "SELECT 
    COUNT(*) as total_booking,
    SUM(total_bayar) as total_nilai,
    SUM(CASE WHEN status_booking = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status_booking = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status_booking = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status_booking = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status_booking = 'dibatalkan' THEN 1 ELSE 0 END) as dibatalkan
    FROM booking
    WHERE tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $querySummary));

// Detail per status
$details = [];
foreach(['pending', 'approved', 'confirmed', 'selesai', 'dibatalkan'] as $status) {
    $queryDetail = "SELECT b.*, g.nama_gedung, p.nama_lengkap
                   FROM booking b
                   JOIN gedung g ON b.id_gedung = g.id_gedung
                   JOIN penyewa p ON b.id_penyewa = p.id_penyewa
                   WHERE b.status_booking = '$status'
                   AND b.tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
                   ORDER BY b.created_at DESC
                   LIMIT 5";
    $details[$status] = mysqli_query($conn, $queryDetail);
}

// Data untuk chart
$chartLabels = ['Pending', 'Approved', 'Confirmed', 'Selesai', 'Dibatalkan'];
$chartData = [
    $summary['pending'],
    $summary['approved'],
    $summary['confirmed'],
    $summary['selesai'],
    $summary['dibatalkan']
];

include '../../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Laporan Status Booking</h1>
    <button onclick="printReport()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-print mr-2"></i>Cetak Laporan
    </button>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm mb-1">Pending</p>
        <p class="text-3xl font-bold text-yellow-600"><?php echo $summary['pending']; ?></p>
        <p class="text-xs text-gray-500 mt-1">Menunggu approval</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm mb-1">Approved</p>
        <p class="text-3xl font-bold text-blue-600"><?php echo $summary['approved']; ?></p>
        <p class="text-xs text-gray-500 mt-1">Sudah disetujui</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm mb-1">Confirmed</p>
        <p class="text-3xl font-bold text-green-600"><?php echo $summary['confirmed']; ?></p>
        <p class="text-xs text-gray-500 mt-1">Lunas & dikonfirmasi</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-gray-500">
        <p class="text-gray-500 text-sm mb-1">Selesai</p>
        <p class="text-3xl font-bold text-gray-600"><?php echo $summary['selesai']; ?></p>
        <p class="text-xs text-gray-500 mt-1">Acara selesai</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-red-500">
        <p class="text-gray-500 text-sm mb-1">Dibatalkan</p>
        <p class="text-3xl font-bold text-red-600"><?php echo $summary['dibatalkan']; ?></p>
        <p class="text-xs text-gray-500 mt-1">Dibatalkan</p>
    </div>
</div>

<!-- Chart -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Distribusi Status</h3>
        <canvas id="chartStatus"></canvas>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Statistik</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-semibold">Total Booking</span>
                    <span class="text-sm font-bold"><?php echo $summary['total_booking']; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <?php 
            $sukses = $summary['confirmed'] + $summary['selesai'];
            $persenSukses = $summary['total_booking'] > 0 ? ($sukses / $summary['total_booking'] * 100) : 0;
            ?>
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-semibold">Sukses (Confirmed + Selesai)</span>
                    <span class="text-sm font-bold text-green-600"><?php echo $sukses; ?> (<?php echo number_format($persenSukses, 1); ?>%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo $persenSukses; ?>%"></div>
                </div>
            </div>
            
            <?php 
            $persenBatal = $summary['total_booking'] > 0 ? ($summary['dibatalkan'] / $summary['total_booking'] * 100) : 0;
            ?>
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-semibold">Dibatalkan</span>
                    <span class="text-sm font-bold text-red-600"><?php echo $summary['dibatalkan']; ?> (<?php echo number_format($persenBatal, 1); ?>%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: <?php echo $persenBatal; ?>%"></div>
                </div>
            </div>
            
            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between">
                    <span class="font-semibold">Total Nilai Booking</span>
                    <span class="font-bold text-purple-600"><?php echo formatRupiah($summary['total_nilai']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Summary -->
<div id="printArea" class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="p-6 border-b print-header hidden">
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold">LAPORAN STATUS BOOKING</h2>
            <p class="text-gray-600">Periode: <?php echo formatTanggal($tanggal_mulai); ?> - <?php echo formatTanggal($tanggal_akhir); ?></p>
        </div>
    </div>
    
    <div class="p-6 border-b">
        <h3 class="text-lg font-bold">Ringkasan per Status</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persentase</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                mysqli_data_seek($resultStatus, 0);
                while($row = mysqli_fetch_assoc($resultStatus)): 
                    $persen = $summary['total_booking'] > 0 ? ($row['total'] / $summary['total_booking'] * 100) : 0;
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-blue-100 text-blue-800',
                        'confirmed' => 'bg-green-100 text-green-800',
                        'selesai' => 'bg-gray-100 text-gray-800',
                        'dibatalkan' => 'bg-red-100 text-red-800'
                    ];
                ?>
                <tr>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $statusColors[$row['status_booking']]; ?>">
                            <?php echo ucfirst($row['status_booking']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 font-semibold"><?php echo $row['total']; ?> booking</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="mr-2 font-semibold"><?php echo number_format($persen, 1); ?>%</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $persen; ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-green-600"><?php echo formatRupiah($row['total_nilai']); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="bg-gray-50 font-bold">
                    <td class="px-6 py-4">TOTAL</td>
                    <td class="px-6 py-4 text-blue-600"><?php echo $summary['total_booking']; ?> booking</td>
                    <td class="px-6 py-4">100%</td>
                    <td class="px-6 py-4 text-green-600"><?php echo formatRupiah($summary['total_nilai']); ?></td>
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
// Chart Status
const ctx = document.getElementById('chartStatus').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($chartData); ?>,
            backgroundColor: [
                'rgba(234, 179, 8, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(156, 163, 175, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function printReport() {
    window.print();
}
</script>

<?php include '../../layouts/admin_footer.php'; ?>