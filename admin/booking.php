<?php
// admin/booking.php
$title = 'Manajemen Booking';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Update Status
if (isset($_POST['update_status'])) {
    $id_booking = (int)$_POST['id_booking'];
    $status = sanitize($_POST['status']);
    $catatan = sanitize($_POST['catatan_admin']);
    
    mysqli_query($conn, "UPDATE booking SET status_booking = '$status', catatan_admin = '$catatan', updated_at = NOW() WHERE id_booking = $id_booking");
    setAlert('success', 'Berhasil!', 'Status booking berhasil diupdate');
    header('Location: booking.php');
    exit;
}

// Filter
$filterStatus = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$filterTanggal = isset($_GET['tanggal']) ? sanitize($_GET['tanggal']) : '';

// Query booking
$query = "SELECT b.*, g.nama_gedung, p.nama_lengkap, p.email, p.no_hp 
          FROM booking b
          JOIN gedung g ON b.id_gedung = g.id_gedung
          JOIN penyewa p ON b.id_penyewa = p.id_penyewa
          WHERE 1=1";

if ($filterStatus) {
    $query .= " AND b.status_booking = '$filterStatus'";
}

if ($filterTanggal) {
    $query .= " AND b.tanggal_booking = '$filterTanggal'";
}

$query .= " ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);

// Get detail if view
$detailData = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $queryDetail = "SELECT b.*, g.*, p.*, k.nama_kategori, k.diskon_persen
                    FROM booking b
                    JOIN gedung g ON b.id_gedung = g.id_gedung
                    JOIN penyewa p ON b.id_penyewa = p.id_penyewa
                    JOIN kategori_penyewa k ON p.id_kategori = k.id_kategori
                    WHERE b.id_booking = $id";
    $resultDetail = mysqli_query($conn, $queryDetail);
    $detailData = mysqli_fetch_assoc($resultDetail);
}

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Manajemen Booking</h1>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block font-semibold mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" <?php echo $filterStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $filterStatus == 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="confirmed" <?php echo $filterStatus == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="selesai" <?php echo $filterStatus == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                <option value="dibatalkan" <?php echo $filterStatus == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-2">Tanggal Booking</label>
            <input type="date" name="tanggal" value="<?php echo $filterTanggal; ?>"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="booking.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Acara</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium"><?php echo $row['kode_booking']; ?></td>
                    <td class="px-6 py-4">
                        <div class="font-semibold"><?php echo $row['nama_lengkap']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $row['no_hp']; ?></div>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['nama_gedung']; ?></td>
                    <td class="px-6 py-4 text-sm">
                        <?php echo formatTanggal($row['tanggal_mulai']); ?><br>
                        <span class="text-gray-500 text-xs">s/d <?php echo formatTanggal($row['tanggal_selesai']); ?></span>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['durasi_hari']; ?> hari</td>
                    <td class="px-6 py-4 text-sm font-semibold"><?php echo formatRupiah($row['total_bayar']); ?></td>
                    <td class="px-6 py-4">
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
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewDetail(<?php echo $row['id_booking']; ?>)" class="text-blue-600 hover:text-blue-800 mr-2">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if ($row['status_booking'] == 'pending'): ?>
                        <button onclick="updateStatus(<?php echo $row['id_booking']; ?>, 'approved')" class="text-green-600 hover:text-green-800 mr-2" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                        <button onclick="updateStatus(<?php echo $row['id_booking']; ?>, 'dibatalkan')" class="text-red-600 hover:text-red-800" title="Reject">
                            <i class="fas fa-times"></i>
                        </button>
                        <?php endif; ?>
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
            <h3 class="text-xl font-bold">Detail Booking - <?php echo $detailData['kode_booking']; ?></h3>
            <button onclick="closeDetail()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Info Gedung -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Informasi Gedung</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Nama:</span> <strong><?php echo $detailData['nama_gedung']; ?></strong></div>
                        <div><span class="text-gray-600">Kapasitas:</span> <?php echo $detailData['kapasitas']; ?> orang</div>
                        <div><span class="text-gray-600">Alamat:</span> <?php echo $detailData['alamat']; ?></div>
                    </div>
                </div>
                
                <!-- Info Penyewa -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-bold mb-3 text-lg">Informasi Penyewa</h4>
                    <div class="space-y-2">
                        <div><span class="text-gray-600">Nama:</span> <strong><?php echo $detailData['nama_lengkap']; ?></strong></div>
                        <div><span class="text-gray-600">Email:</span> <?php echo $detailData['email']; ?></div>
                        <div><span class="text-gray-600">HP:</span> <?php echo $detailData['no_hp']; ?></div>
                        <div><span class="text-gray-600">Kategori:</span> <?php echo $detailData['nama_kategori']; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Booking -->
            <div class="border rounded-lg p-4 mb-6">
                <h4 class="font-bold mb-3 text-lg">Detail Pemesanan</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-gray-600">Tanggal Mulai:</span> <strong><?php echo formatTanggal($detailData['tanggal_mulai']); ?></strong></div>
                    <div><span class="text-gray-600">Tanggal Selesai:</span> <strong><?php echo formatTanggal($detailData['tanggal_selesai']); ?></strong></div>
                    <div><span class="text-gray-600">Durasi:</span> <?php echo $detailData['durasi_hari']; ?> hari</div>
                    <div><span class="text-gray-600">Jumlah Tamu:</span> <?php echo $detailData['jumlah_tamu']; ?> orang</div>
                    <div class="col-span-2"><span class="text-gray-600">Keperluan:</span> <?php echo $detailData['keperluan']; ?></div>
                </div>
            </div>
            
            <!-- Rincian Biaya -->
            <div class="border rounded-lg p-4 mb-6 bg-blue-50">
                <h4 class="font-bold mb-3 text-lg">Rincian Biaya</h4>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Harga per Hari:</span> <span><?php echo formatRupiah($detailData['harga_per_hari']); ?></span></div>
                    <div class="flex justify-between"><span>Total Harga (<?php echo $detailData['durasi_hari']; ?> hari):</span> <span><?php echo formatRupiah($detailData['total_harga']); ?></span></div>
                    <div class="flex justify-between text-red-600"><span>Diskon (<?php echo $detailData['diskon_persen']; ?>%):</span> <span>- <?php echo formatRupiah($detailData['diskon']); ?></span></div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total Bayar:</span> <span class="text-blue-600"><?php echo formatRupiah($detailData['total_bayar']); ?></span></div>
                </div>
            </div>
            
            <!-- Update Status -->
            <form method="POST" class="border rounded-lg p-4">
                <h4 class="font-bold mb-3 text-lg">Update Status</h4>
                <input type="hidden" name="id_booking" value="<?php echo $detailData['id_booking']; ?>">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending" <?php echo $detailData['status_booking'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $detailData['status_booking'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="confirmed" <?php echo $detailData['status_booking'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="selesai" <?php echo $detailData['status_booking'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="dibatalkan" <?php echo $detailData['status_booking'] == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo $detailData['catatan_admin']; ?></textarea>
                </div>
                <button type="submit" name="update_status" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Status
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function viewDetail(id) {
    window.location.href = 'booking.php?view=' + id;
}

function closeDetail() {
    window.location.href = 'booking.php';
}

function updateStatus(id, status) {
    const text = status == 'approved' ? 'Approve booking ini?' : 'Reject booking ini?';
    Swal.fire({
        title: 'Konfirmasi',
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="id_booking" value="${id}">
                <input type="hidden" name="status" value="${status}">
                <input type="hidden" name="catatan_admin" value="">
                <input type="hidden" name="update_status" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?php include '../layouts/admin_footer.php'; ?>