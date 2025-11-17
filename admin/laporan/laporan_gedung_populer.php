<?php
// admin/laporan/laporan_gedung_populer.php
$title = 'Laporan Gedung Populer';
require_once '../../config/config.php';
require_once '../../functions/helpers.php';
requireLogin();

// Filter
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? sanitize($_GET['tanggal_mulai']) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? sanitize($_GET['tanggal_akhir']) : date('Y-m-d');

// Query gedung populer
$query = "SELECT 
    g.*,
    COUNT(b.id_booking) as total_booking,
    SUM(b.durasi_hari) as total_hari_sewa,
    SUM(b.total_bayar) as total_pendapatan,
    AVG(b.total_bayar) as rata_rata_pendapatan,
    SUM(CASE WHEN b.status_booking = 'selesai' THEN 1 ELSE 0 END) as booking_selesai,
    SUM(CASE WHEN b.status_booking = 'dibatalkan' THEN 1 ELSE 0 END) as booking_batal
    FROM gedung g
    LEFT JOIN booking b ON g.id_gedung = b.id_gedung 
        AND b.tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
    WHERE g.deleted_at IS NULL
    GROUP BY g.id_gedung
    ORDER BY total_booking DESC, total_pendapatan DESC";
$result = mysqli_query($conn, $query);

// Summary
$querySummary = "SELECT 
    COUNT(DISTINCT b.id_gedung) as total_gedung_terpakai,
    COUNT(*) as total_booking,
    SUM(b.total_bayar) as total_pendapatan
    FROM booking b
    WHERE b.tanggal_booking BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $querySummary));

// Data untuk chart (top 5)
$chartLabels = [];
$chartData = [];
$count = 0;
mysqli_data_seek($result, 0);
while($row = mysqli_fetch_assoc($result)) {
    if ($count < 5) {
        $chartLabels[] = $row['nama_gedung'];
        $chartData[] = $row['total_booking'];
        $count++;
    }
}

include '../../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Laporan Gedung Populer</h1>
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

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Gedung Terpakai</p>
        <p class="text-3xl font-bold text-blue-600"><?php echo $summary['total_gedung_terpakai']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Total Booking</p>
        <p class="text-3xl font-bold text-green-600"><?php echo $summary['total_booking']; ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm mb-2">Total Pendapatan</p>
        <p class="text-2xl font-bold text-purple-600"><?php echo formatRupiah($summary['total_pendapatan']); ?></p>
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">Top 5 Gedung Terpopuler</h3>
    <canvas id="chartGedung"></canvas>
</div>

<!-- Table -->
<div id="printArea" class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b print-header hidden">
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold">LAPORAN GEDUNG POPULER</h2>
            <p class="text-gray-600">Periode: <?php echo formatTanggal($tanggal_mulai); ?> - <?php echo formatTanggal($tanggal_akhir); ?></p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Gedung</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kapasitas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Booking</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Hari</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $rank = 1;
                $total_booking_all = 0;
                $total_pendapatan_all = 0;
                mysqli_data_seek($result, 0);
                while($row = mysqli_fetch_assoc($result)): 
                    $total_booking_all += $row['total_booking'];
                    $total_pendapatan_all += $row['total_pendapatan'];
                    
                    // Medal icons for top 3
                    $medal = '';
                    if ($rank == 1) $medal = '<i class="fas fa-medal text-yellow-500 mr-2"></i>';
                    elseif ($rank == 2) $medal = '<i class="fas fa-medal text-gray-400 mr-2"></i>';
                    elseif ($rank == 3) $medal = '<i class="fas fa-medal text-orange-600 mr-2"></i>';
                ?>
                <tr class="<?php echo $rank <= 3 ? 'bg-blue-50' : ''; ?>">
                    <td class="px-4 py-4 text-sm font-bold">
                        <?php echo $medal . $rank; ?>
                    </td>
                    <td class="px-4 py-4">
                        <div class="font-semibold"><?php echo $row['nama_gedung']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo substr($row['alamat'], 0, 40); ?>...</div>
                    </td>
                    <td class="px-4 py-4 text-sm"><?php echo $row['kapasitas']; ?> orang</td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">
                            <?php echo $row['total_booking']; ?> kali
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm"><?php echo $row['total_hari_sewa']; ?> hari</td>
                    <td class="px-4 py-4 text-sm text-green-600 font-semibold"><?php echo $row['booking_selesai']; ?></td>
                    <td class="px-4 py-4 text-sm text-red-600 font-semibold"><?php echo $row['booking_batal']; ?></td>
                    <td class="px-4 py-4 text-sm font-semibold text-green-600"><?php echo formatRupiah($row['total_pendapatan']); ?></td>
                    <td class="px-4 py-4 text-sm"><?php echo formatRupiah($row['rata_rata_pendapatan']); ?></td>
                </tr>
                <?php 
                    $rank++;
                endwhile; 
                ?>
                <tr class="bg-gray-50 font-bold">
                    <td colspan="3" class="px-4 py-4 text-right">TOTAL:</td>
                    <td class="px-4 py-4 text-blue-600"><?php echo $total_booking_all; ?> kali</td>
                    <td colspan="3"></td>
                    <td colspan="2" class="px-4 py-4 text-green-600"><?php echo formatRupiah($total_pendapatan_all); ?></td>
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
    .bg-blue-50 {
        background-color: #f0f9ff !important;
    }
}
</style>

<script>
// Chart Gedung Populer
const ctx = document.getElementById('chartGedung').getContext('2d');
new Chart(ctx, {
    type: 'horizontalBar',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Total Booking',
            data: <?php echo json_encode($chartData); ?>,
            backgroundColor: [
                'rgba(234, 179, 8, 0.5)',
                'rgba(156, 163, 175, 0.5)',
                'rgba(234, 88, 12, 0.5)',
                'rgba(59, 130, 246, 0.5)',
                'rgba(34, 197, 94, 0.5)'
            ],
            borderColor: [
                'rgb(234, 179, 8)',
                'rgb(156, 163, 175)',
                'rgb(234, 88, 12)',
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function printReport() {
    window.print();
}
</script>

<?php include '../../layouts/admin_footer.php'; ?>