<?php
// admin/gedung.php
$title = 'Manajemen Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Delete (Soft Delete)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "UPDATE gedung SET deleted_at = NOW() WHERE id_gedung = $id");
    setAlert('success', 'Berhasil!', 'Gedung berhasil dihapus');
    header('Location: gedung.php');
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_gedung = isset($_POST['id_gedung']) ? (int)$_POST['id_gedung'] : 0;
    $nama_gedung = sanitize($_POST['nama_gedung']);
    $slug = generateSlug($nama_gedung);
    $deskripsi = sanitize($_POST['deskripsi']);
    $kapasitas = (int)$_POST['kapasitas'];
    $luas_gedung = sanitize($_POST['luas_gedung']);
    $alamat = sanitize($_POST['alamat']);
    $fasilitas = sanitize($_POST['fasilitas']);
    $harga_weekday = (float)$_POST['harga_weekday'];
    $harga_weekend = (float)$_POST['harga_weekend'];
    $status = sanitize($_POST['status']);
    
    // Upload foto utama
    $foto_utama = '';
    if (!empty($_FILES['foto_utama']['name'])) {
        $upload = uploadFile($_FILES['foto_utama'], UPLOAD_GEDUNG, ['jpg', 'jpeg', 'png']);
        if ($upload['success']) {
            $foto_utama = $upload['filename'];
        }
    }
    
    if ($id_gedung > 0) {
        // Update
        $query = "UPDATE gedung SET 
                  nama_gedung = '$nama_gedung',
                  slug = '$slug',
                  deskripsi = '$deskripsi',
                  kapasitas = $kapasitas,
                  luas_gedung = '$luas_gedung',
                  alamat = '$alamat',
                  fasilitas = '$fasilitas',
                  harga_weekday = $harga_weekday,
                  harga_weekend = $harga_weekend,
                  status = '$status',
                  updated_at = NOW()";
        
        if ($foto_utama) {
            $query .= ", foto_utama = '$foto_utama'";
        }
        
        $query .= " WHERE id_gedung = $id_gedung";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'Gedung berhasil diupdate');
    } else {
        // Insert
        $query = "INSERT INTO gedung (nama_gedung, slug, deskripsi, kapasitas, luas_gedung, alamat, fasilitas, harga_weekday, harga_weekend, foto_utama, status, created_at, updated_at)
                  VALUES ('$nama_gedung', '$slug', '$deskripsi', $kapasitas, '$luas_gedung', '$alamat', '$fasilitas', $harga_weekday, $harga_weekend, '$foto_utama', '$status', NOW(), NOW())";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'Gedung berhasil ditambahkan');
    }
    
    header('Location: gedung.php');
    exit;
}

// Get edit data
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM gedung WHERE id_gedung = $id");
    $editData = mysqli_fetch_assoc($result);
}

// Get all gedung
$query = "SELECT * FROM gedung WHERE deleted_at IS NULL ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Manajemen Gedung</h1>
    <button onclick="showModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Tambah Gedung
    </button>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kapasitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Weekday</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
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
                        <?php if($row['foto_utama']): ?>
                        <img src="../uploads/gedung/<?php echo $row['foto_utama']; ?>" class="w-16 h-16 object-cover rounded">
                        <?php else: ?>
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold"><?php echo $row['nama_gedung']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo substr($row['alamat'], 0, 50); ?>...</div>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['kapasitas']; ?> orang</td>
                    <td class="px-6 py-4 text-sm font-semibold"><?php echo formatRupiah($row['harga_weekday']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $row['status'] == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="editGedung(<?php echo $row['id_gedung']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteGedung(<?php echo $row['id_gedung']; ?>)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-lg w-full max-w-3xl my-8">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold" id="modalTitle">Tambah Gedung</h3>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="id_gedung" id="id_gedung" value="<?php echo $editData['id_gedung'] ?? ''; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-2">Nama Gedung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_gedung" id="nama_gedung" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Kapasitas (orang) <span class="text-red-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-2">Luas Gedung</label>
                    <input type="text" name="luas_gedung" id="luas_gedung" placeholder="Contoh: 500 m²"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" id="alamat" rows="2" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Fasilitas (pisahkan dengan koma)</label>
                <input type="text" name="fasilitas" id="fasilitas" placeholder="AC, Sound System, Toilet, Parkir"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-2">Harga Weekday <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_weekday" id="harga_weekday" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Harga Weekend <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_weekend" id="harga_weekend" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Foto Utama</label>
                <input type="file" name="foto_utama" accept=".jpg,.jpeg,.png"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG (Max 5MB)</p>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="closeModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal() {
    document.getElementById('modalForm').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Gedung';
    document.querySelector('form').reset();
    document.getElementById('id_gedung').value = '';
}

function closeModal() {
    document.getElementById('modalForm').classList.add('hidden');
}

function editGedung(id) {
    window.location.href = 'gedung.php?edit=' + id;
}

function deleteGedung(id) {
    Swal.fire({
        title: 'Hapus Gedung?',
        text: 'Data gedung akan dihapus dari sistem',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'gedung.php?delete=' + id;
        }
    });
}

<?php if($editData): ?>
// Auto populate edit data
document.getElementById('modalForm').classList.remove('hidden');
document.getElementById('modalTitle').textContent = 'Edit Gedung';
document.getElementById('id_gedung').value = '<?php echo $editData['id_gedung']; ?>';
document.getElementById('nama_gedung').value = '<?php echo $editData['nama_gedung']; ?>';
document.getElementById('kapasitas').value = '<?php echo $editData['kapasitas']; ?>';
document.getElementById('luas_gedung').value = '<?php echo $editData['luas_gedung']; ?>';
document.getElementById('alamat').value = '<?php echo $editData['alamat']; ?>';
document.getElementById('deskripsi').value = '<?php echo $editData['deskripsi']; ?>';
document.getElementById('fasilitas').value = '<?php echo $editData['fasilitas']; ?>';
document.getElementById('harga_weekday').value = '<?php echo $editData['harga_weekday']; ?>';
document.getElementById('harga_weekend').value = '<?php echo $editData['harga_weekend']; ?>';
document.getElementById('status').value = '<?php echo $editData['status']; ?>';
<?php endif; ?>
</script>

<?php include '../layouts/admin_footer.php'; ?>