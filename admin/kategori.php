<?php
// admin/kategori.php
$title = 'Kategori Penyewa';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah kategori masih digunakan
    $check = mysqli_query($conn, "SELECT COUNT(*) as total FROM penyewa WHERE id_kategori = $id");
    $result = mysqli_fetch_assoc($check);
    
    if ($result['total'] > 0) {
        setAlert('error', 'Gagal Hapus!', 'Kategori masih digunakan oleh ' . $result['total'] . ' penyewa');
    } else {
        mysqli_query($conn, "DELETE FROM kategori_penyewa WHERE id_kategori = $id");
        setAlert('success', 'Berhasil!', 'Kategori berhasil dihapus');
    }
    
    header('Location: kategori.php');
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 0;
    $nama_kategori = sanitize($_POST['nama_kategori']);
    $diskon_persen = (float)$_POST['diskon_persen'];
    $keterangan = sanitize($_POST['keterangan']);
    $status = sanitize($_POST['status']);
    
    if ($id_kategori > 0) {
        // Update
        $query = "UPDATE kategori_penyewa SET 
                  nama_kategori = '$nama_kategori',
                  diskon_persen = $diskon_persen,
                  keterangan = '$keterangan',
                  status = '$status'
                  WHERE id_kategori = $id_kategori";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'Kategori berhasil diupdate');
    } else {
        // Insert
        $query = "INSERT INTO kategori_penyewa (nama_kategori, diskon_persen, keterangan, status)
                  VALUES ('$nama_kategori', $diskon_persen, '$keterangan', '$status')";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'Kategori berhasil ditambahkan');
    }
    
    header('Location: kategori.php');
    exit;
}

// Get edit data
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM kategori_penyewa WHERE id_kategori = $id");
    $editData = mysqli_fetch_assoc($result);
}

// Get all kategori
$query = "SELECT k.*, 
          (SELECT COUNT(*) FROM penyewa WHERE id_kategori = k.id_kategori) as total_penyewa
          FROM kategori_penyewa k
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($conn, $query);

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Kategori Penyewa</h1>
    <button onclick="showModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Tambah Kategori
    </button>
</div>

<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
        <div>
            <h4 class="font-semibold text-blue-900 mb-1">Tentang Kategori Penyewa</h4>
            <p class="text-blue-800 text-sm">
                Kategori penyewa digunakan untuk memberikan diskon otomatis saat booking. 
                Misalnya kategori "Mahasiswa" dapat diberikan diskon 10%, "Instansi Pemerintah" diskon 15%, dll.
            </p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Penyewa</th>
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
                        <span class="font-semibold"><?php echo $row['nama_kategori']; ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold">
                            <?php echo $row['diskon_persen']; ?>%
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?php echo $row['keterangan'] ? substr($row['keterangan'], 0, 50) . '...' : '-'; ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-blue-600"><?php echo $row['total_penyewa']; ?></span> orang
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $row['status'] == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="editKategori(<?php echo $row['id_kategori']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if ($row['total_penyewa'] == 0): ?>
                        <button onclick="deleteKategori(<?php echo $row['id_kategori']; ?>)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php else: ?>
                        <button disabled class="text-gray-400 cursor-not-allowed" title="Tidak bisa dihapus, masih digunakan">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold" id="modalTitle">Tambah Kategori</h3>
        </div>
        <form method="POST" class="p-6">
            <input type="hidden" name="id_kategori" id="id_kategori" value="<?php echo $editData['id_kategori'] ?? ''; ?>">
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" required
                       placeholder="Contoh: Umum, Mahasiswa, Instansi Pemerintah"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Diskon (%) <span class="text-red-500">*</span></label>
                <input type="number" name="diskon_persen" id="diskon_persen" required
                       step="0.01" min="0" max="100" placeholder="Contoh: 10"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Masukkan 0 jika tidak ada diskon</p>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          placeholder="Deskripsi singkat tentang kategori ini..."
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
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
    document.getElementById('modalTitle').textContent = 'Tambah Kategori';
    document.querySelector('form').reset();
    document.getElementById('id_kategori').value = '';
}

function closeModal() {
    document.getElementById('modalForm').classList.add('hidden');
}

function editKategori(id) {
    window.location.href = 'kategori.php?edit=' + id;
}

function deleteKategori(id) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: 'Data kategori akan dihapus dari sistem',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'kategori.php?delete=' + id;
        }
    });
}

<?php if($editData): ?>
// Auto populate edit data
document.getElementById('modalForm').classList.remove('hidden');
document.getElementById('modalTitle').textContent = 'Edit Kategori';
document.getElementById('id_kategori').value = '<?php echo $editData['id_kategori']; ?>';
document.getElementById('nama_kategori').value = '<?php echo $editData['nama_kategori']; ?>';
document.getElementById('diskon_persen').value = '<?php echo $editData['diskon_persen']; ?>';
document.getElementById('keterangan').value = '<?php echo $editData['keterangan']; ?>';
document.getElementById('status').value = '<?php echo $editData['status']; ?>';
<?php endif; ?>
</script>

<?php include '../layouts/admin_footer.php'; ?>