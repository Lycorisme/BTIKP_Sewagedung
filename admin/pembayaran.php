<?php
// admin/pembayaran.php
$title = 'Verifikasi Pembayaran';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Verifikasi
if (isset($_POST['verifikasi'])) {
    $id_pembayaran = (int)$_POST['id_pembayaran'];
    $status = sanitize($_POST['status_bayar']);
    $catatan = sanitize($_POST['catatan']);
    $id_booking = (int)$_POST['id_booking'];
    
    mysqli_query($conn, "UPDATE pembayaran SET 
                        status_bayar = '$status',
                        catatan = '$catatan',
                        verified_by = {$_SESSION['admin_id']},
                        verified_at = NOW()
                        WHERE id_pembayaran = $id_pembayaran");
    
    // Jika verified, update status booking jadi confirmed
    if ($status == 'verified') {
        mysqli_query($conn, "UPDATE booking SET status_booking = 'confirmed', updated_at = NOW() WHERE id_booking = $id_booking");
        setAlert('success', 'Berhasil!', 'Pembayaran telah diverifikasi dan booking dikonfirmasi');
    } else {
        setAlert('warning', 'Pembayaran Ditolak', 'Pembayaran telah ditolak');
    }
    
    header('Location: pembayaran.php');
    exit;
}

// Filter
$filterStatus = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Query pembayaran
$query = "SELECT p.*, b.kode_booking, b.total_bayar as total_booking, 
          g.nama_gedung, py.nama_lengkap, py.no_hp,
          u.nama_lengkap as verified_by_name
          FROM pembayaran p
          JOIN booking b ON p.id_booking = b.id_booking
          JOIN gedung g ON b.id_gedung = g.id_gedung
          JOIN penyewa py ON b.id_penyewa = py.id_penyewa
          LEFT JOIN user_admin u ON p.verified_by = u.id_user
          WHERE 1=1";

if ($filterStatus) {
    $query .= " AND p.status_bayar = '$filterStatus'";
}

$query .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

// Get detail if view
$detailData = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $queryDetail = "SELECT p.*, b.*, g.nama_gedung, py.nama_lengkap, py.email, py.no_hp,
                    u.nama_lengkap as verified_by_name
                    FROM pembayaran p
                    JOIN booking b ON p.id_booking = b.id_booking
                    JOIN gedung g ON b.id_gedung = g.id_gedung
                    JOIN penyewa py ON b.id_penyewa = py.id_penyewa
                    LEFT JOIN user_admin u ON p.verified_by = u.id_user
                    WHERE p.id_pembayaran = $id";
    $resultDetail = mysqli_query($conn, $queryDetail);
    $detailData = mysqli_fetch_assoc($resultDetail);
}

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Verifikasi Pembayaran</h1>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <label class="block font-semibold mb-2">Status Pembayaran</label>
            <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" <?php echo $filterStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="verified" <?php echo $filterStatus == 'verified' ? 'selected' : ''; ?>>Verified</option>
                <option value="rejected" <?php echo $filterStatus == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="pembayaran.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm"><?php echo formatTanggal($row['tanggal_bayar']); ?></td>
                    <td class="px-6 py-4 text-sm font-medium"><?php echo $row['kode_booking']; ?></td>
                    <td class="px-6 py-4">
                        <div class="font-semibold"><?php echo $row['nama_lengkap']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $row['no_hp']; ?></div>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['nama_gedung']; ?></td>
                    <td class="px-6 py-4 text-sm font-semibold"><?php echo formatRupiah($row['jumlah_bayar']); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo ucfirst($row['metode_bayar']); ?></td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'verified' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$row['status_bayar']]; ?>">
                            <?php echo ucfirst($row['status_bayar']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewDetail(<?php echo $row['id_pembayaran']; ?>)" class="text-blue-600 hover:text-blue-800">
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
    <div class="bg-white rounded-lg w-full max-w-4xl my-8">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold">Detail Pembayaran - <?php echo $detailData['kode_booking']; ?></h3>
            <button onclick="closeDetail()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Info Booking -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Informasi Booking</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Kode Booking:</span> <strong><?php echo $detailData['kode_booking']; ?></strong></div>
                        <div><span class="text-gray-600">Gedung:</span> <?php echo $detailData['nama_gedung']; ?></div>
                        <div><span class="text-gray-600">Tanggal Acara:</span> <?php echo formatTanggal($detailData['tanggal_mulai']); ?> - <?php echo formatTanggal($detailData['tanggal_selesai']); ?></div>
                        <div><span class="text-gray-600">Total Booking:</span> <strong class="text-blue-600"><?php echo formatRupiah($detailData['total_bayar']); ?></strong></div>
                    </div>
                </div>
                
                <!-- Info Penyewa -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Informasi Penyewa</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Nama:</span> <strong><?php echo $detailData['nama_lengkap']; ?></strong></div>
                        <div><span class="text-gray-600">Email:</span> <?php echo $detailData['email']; ?></div>
                        <div><span class="text-gray-600">HP:</span> <?php echo $detailData['no_hp']; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Pembayaran -->
            <div class="border rounded-lg p-4 mb-6 bg-blue-50">
                <h4 class="font-bold mb-3 text-lg">Detail Pembayaran</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-gray-600">Tanggal Bayar:</span> <strong><?php echo formatTanggal($detailData['tanggal_bayar']); ?></strong></div>
                    <div><span class="text-gray-600">Jumlah Bayar:</span> <strong class="text-green-600"><?php echo formatRupiah($detailData['jumlah_bayar']); ?></strong></div>
                    <div><span class="text-gray-600">Metode:</span> <?php echo ucfirst($detailData['metode_bayar']); ?></div>
                    <div><span class="text-gray-600">Status:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$detailData['status_bayar']]; ?>">
                            <?php echo ucfirst($detailData['status_bayar']); ?>
                        </span>
                    </div>
                    <?php if ($detailData['bank_tujuan']): ?>
                    <div><span class="text-gray-600">Bank:</span> <?php echo $detailData['bank_tujuan']; ?></div>
                    <div><span class="text-gray-600">No Rekening:</span> <?php echo $detailData['nomor_rekening']; ?></div>
                    <?php endif; ?>
                    <?php if ($detailData['atas_nama']): ?>
                    <div class="col-span-2"><span class="text-gray-600">Atas Nama:</span> <?php echo $detailData['atas_nama']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bukti Bayar -->
            <?php if ($detailData['bukti_bayar']): ?>
            <div class="border rounded-lg p-4 mb-6">
                <h4 class="font-bold mb-3 text-lg">Bukti Pembayaran</h4>
                <div class="flex justify-center">
                    <img src="../uploads/bukti_bayar/<?php echo $detailData['bukti_bayar']; ?>" 
                         alt="Bukti Bayar"
                         class="max-w-full max-h-96 object-contain border rounded cursor-pointer"
                         onclick="window.open(this.src, '_blank')">
                </div>
                <p class="text-center text-sm text-gray-500 mt-2">Klik gambar untuk memperbesar</p>
            </div>
            <?php endif; ?>
            
            <!-- Verifikasi Form -->
            <?php if ($detailData['status_bayar'] == 'pending'): ?>
            <form method="POST" class="border rounded-lg p-4">
                <h4 class="font-bold mb-3 text-lg">Verifikasi Pembayaran</h4>
                <input type="hidden" name="id_pembayaran" value="<?php echo $detailData['id_pembayaran']; ?>">
                <input type="hidden" name="id_booking" value="<?php echo $detailData['id_booking']; ?>">
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Status Verifikasi</label>
                    <select name="status_bayar" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="verified">Terverifikasi (Approve)</option>
                        <option value="rejected">Ditolak (Reject)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Catatan</label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan jika perlu..."></textarea>
                </div>
                
                <button type="submit" name="verifikasi" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-2"></i>Verifikasi Sekarang
                </button>
            </form>
            <?php else: ?>
            <div class="border rounded-lg p-4 bg-gray-50">
                <h4 class="font-bold mb-3 text-lg">Status Verifikasi</h4>
                <div class="space-y-2">
                    <?php if ($detailData['verified_by_name']): ?>
                    <div><span class="text-gray-600">Diverifikasi oleh:</span> <strong><?php echo $detailData['verified_by_name']; ?></strong></div>
                    <div><span class="text-gray-600">Waktu:</span> <?php echo formatDateTime($detailData['verified_at']); ?></div>
                    <?php endif; ?>
                    <?php if ($detailData['catatan']): ?>
                    <div><span class="text-gray-600">Catatan:</span> <?php echo $detailData['catatan']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function viewDetail(id) {
    window.location.href = 'pembayaran.php?view=' + id;
}

function closeDetail() {
    window.location.href = 'pembayaran.php';
}
</script>

<?php include '../layouts/admin_footer.php'; ?>