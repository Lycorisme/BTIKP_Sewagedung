<?php
// admin/foto_gedung.php
$title = 'Galeri Foto Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = mysqli_query($conn, "SELECT nama_file FROM foto_gedung WHERE id_foto = $id");
    $foto = mysqli_fetch_assoc($query);
    
    if ($foto) {
        unlink(UPLOAD_GALERI . $foto['nama_file']);
        mysqli_query($conn, "DELETE FROM foto_gedung WHERE id_foto = $id");
        setAlert('success', 'Berhasil!', 'Foto berhasil dihapus');
    }
    header('Location: foto_gedung.php');
    exit;
}

// Handle Upload Multiple
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $id_gedung = (int)$_POST['id_gedung'];
    
    if (!empty($_FILES['foto']['name'][0])) {
        $uploaded = 0;
        $total = count($_FILES['foto']['name']);
        
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['foto']['error'][$i] == 0) {
                $file = [
                    'name' => $_FILES['foto']['name'][$i],
                    'type' => $_FILES['foto']['type'][$i],
                    'tmp_name' => $_FILES['foto']['tmp_name'][$i],
                    'error' => $_FILES['foto']['error'][$i],
                    'size' => $_FILES['foto']['size'][$i]
                ];
                
                $upload = uploadFile($file, UPLOAD_GALERI, ['jpg', 'jpeg', 'png']);
                
                if ($upload['success']) {
                    $keterangan = sanitize($_POST['keterangan'][$i] ?? '');
                    $urutan = $i + 1;
                    
                    mysqli_query($conn, "INSERT INTO foto_gedung (id_gedung, nama_file, keterangan, urutan, created_at) 
                                        VALUES ('$id_gedung', '{$upload['filename']}', '$keterangan', $urutan, NOW())");
                    $uploaded++;
                }
            }
        }
        
        setAlert('success', 'Berhasil!', "$uploaded dari $total foto berhasil diupload");
    } else {
        setAlert('error', 'Gagal!', 'Pilih foto terlebih dahulu');
    }
    
    header('Location: foto_gedung.php?gedung=' . $id_gedung);
    exit;
}

// Get all gedung
$queryGedung = "SELECT id_gedung, nama_gedung FROM gedung WHERE deleted_at IS NULL ORDER BY nama_gedung";
$resultGedung = mysqli_query($conn, $queryGedung);

// Get selected gedung
$selected_gedung = isset($_GET['gedung']) ? (int)$_GET['gedung'] : 0;

// Get foto if gedung selected
$resultFoto = null;
if ($selected_gedung > 0) {
    $queryFoto = "SELECT f.*, g.nama_gedung 
                  FROM foto_gedung f
                  JOIN gedung g ON f.id_gedung = g.id_gedung
                  WHERE f.id_gedung = $selected_gedung
                  ORDER BY f.urutan ASC";
    $resultFoto = mysqli_query($conn, $queryFoto);
}

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Galeri Foto Gedung</h1>
</div>

<!-- Pilih Gedung -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <label class="block font-semibold mb-2">Pilih Gedung</label>
            <select name="gedung" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Gedung --</option>
                <?php 
                mysqli_data_seek($resultGedung, 0);
                while($gedung = mysqli_fetch_assoc($resultGedung)): 
                ?>
                    <option value="<?php echo $gedung['id_gedung']; ?>" 
                            <?php echo ($selected_gedung == $gedung['id_gedung']) ? 'selected' : ''; ?>>
                        <?php echo $gedung['nama_gedung']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <?php if ($selected_gedung > 0): ?>
        <div class="flex items-end">
            <button type="button" onclick="showUploadModal()" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Upload Foto
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Galeri Foto -->
<?php if ($resultFoto && mysqli_num_rows($resultFoto) > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <?php while($foto = mysqli_fetch_assoc($resultFoto)): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden group relative">
        <div class="aspect-square bg-gray-300">
            <img src="../uploads/galeri/<?php echo $foto['nama_file']; ?>" 
                 alt="<?php echo $foto['keterangan']; ?>"
                 class="w-full h-full object-cover">
        </div>
        
        <div class="p-4">
            <?php if ($foto['keterangan']): ?>
            <p class="text-sm text-gray-600 mb-2"><?php echo $foto['keterangan']; ?></p>
            <?php endif; ?>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Urutan: <?php echo $foto['urutan']; ?></span>
                <button onclick="deleteFoto(<?php echo $foto['id_foto']; ?>)" 
                        class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <!-- Hover overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
            <button onclick="viewImage('../uploads/galeri/<?php echo $foto['nama_file']; ?>')" 
                    class="bg-white text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-search-plus mr-2"></i>Lihat
            </button>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php elseif ($selected_gedung > 0): ?>
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Foto</h3>
    <p class="text-gray-500 mb-4">Upload foto untuk gedung ini</p>
    <button onclick="showUploadModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Upload Foto
    </button>
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600 mb-2">Pilih Gedung</h3>
    <p class="text-gray-500">Pilih gedung terlebih dahulu untuk melihat galeri foto</p>
</div>
<?php endif; ?>

<!-- Modal Upload -->
<div id="modalUpload" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold">Upload Foto Gedung</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="id_gedung" value="<?php echo $selected_gedung; ?>">
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Pilih Foto (Multiple) <span class="text-red-500">*</span></label>
                <input type="file" name="foto[]" multiple accept=".jpg,.jpeg,.png" required
                       onchange="previewImages(this)"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG (Max 5MB per file)</p>
            </div>
            
            <div id="previewContainer" class="mb-4 hidden">
                <label class="block font-semibold mb-2">Preview</label>
                <div id="previewImages" class="grid grid-cols-3 gap-4"></div>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Keterangan (Optional)</label>
                <div id="keteranganContainer">
                    <!-- Keterangan inputs akan ditambahkan via JS -->
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" name="upload" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
                <button type="button" onclick="closeModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal View Image -->
<div id="modalView" class="hidden fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4" onclick="closeViewModal()">
    <div class="max-w-4xl max-h-screen">
        <img id="viewImage" src="" class="max-w-full max-h-screen object-contain">
    </div>
</div>

<script>
function showUploadModal() {
    document.getElementById('modalUpload').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modalUpload').classList.add('hidden');
    document.querySelector('form').reset();
    document.getElementById('previewContainer').classList.add('hidden');
}

function previewImages(input) {
    const container = document.getElementById('previewImages');
    const keteranganContainer = document.getElementById('keteranganContainer');
    container.innerHTML = '';
    keteranganContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        document.getElementById('previewContainer').classList.remove('hidden');
        
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'aspect-square bg-gray-200 rounded overflow-hidden';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                container.appendChild(div);
            }
            
            reader.readAsDataURL(file);
            
            // Add keterangan input
            const inputDiv = document.createElement('div');
            inputDiv.className = 'mb-2';
            inputDiv.innerHTML = `
                <input type="text" name="keterangan[]" placeholder="Keterangan foto ${i + 1}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            `;
            keteranganContainer.appendChild(inputDiv);
        }
    }
}

function deleteFoto(id) {
    Swal.fire({
        title: 'Hapus Foto?',
        text: 'Foto akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'foto_gedung.php?delete=' + id + '&gedung=<?php echo $selected_gedung; ?>';
        }
    });
}

function viewImage(src) {
    document.getElementById('viewImage').src = src;
    document.getElementById('modalView').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('modalView').classList.add('hidden');
}
</script>

<?php include '../layouts/admin_footer.php'; ?>