<?php
// admin/user.php
$title = 'User Admin';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Only superadmin can manage users
if ($_SESSION['admin_level'] != 'superadmin') {
    setAlert('error', 'Akses Ditolak!', 'Hanya superadmin yang dapat mengakses halaman ini');
    header('Location: index.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Tidak bisa hapus diri sendiri
    if ($id == $_SESSION['admin_id']) {
        setAlert('error', 'Gagal!', 'Tidak dapat menghapus akun sendiri');
    } else {
        mysqli_query($conn, "DELETE FROM user_admin WHERE id_user = $id");
        setAlert('success', 'Berhasil!', 'User berhasil dihapus');
    }
    
    header('Location: user.php');
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = isset($_POST['id_user']) ? (int)$_POST['id_user'] : 0;
    $username = sanitize($_POST['username']);
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $level = sanitize($_POST['level']);
    $status = sanitize($_POST['status']);
    
    // Cek username duplicate
    $checkUsername = mysqli_query($conn, "SELECT id_user FROM user_admin WHERE username = '$username' AND id_user != $id_user");
    if (mysqli_num_rows($checkUsername) > 0) {
        setAlert('error', 'Gagal!', 'Username sudah digunakan');
        header('Location: user.php');
        exit;
    }
    
    if ($id_user > 0) {
        // Update
        $query = "UPDATE user_admin SET 
                  username = '$username',
                  nama_lengkap = '$nama_lengkap',
                  email = '$email',
                  level = '$level',
                  status = '$status',
                  updated_at = NOW()";
        
        // Update password jika diisi
        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
            $query .= ", password = '$password'";
        }
        
        $query .= " WHERE id_user = $id_user";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'User berhasil diupdate');
    } else {
        // Insert
        $password = $_POST['password'];
        $query = "INSERT INTO user_admin (username, password, nama_lengkap, email, level, status, created_at, updated_at)
                  VALUES ('$username', '$password', '$nama_lengkap', '$email', '$level', '$status', NOW(), NOW())";
        mysqli_query($conn, $query);
        setAlert('success', 'Berhasil!', 'User berhasil ditambahkan');
    }
    
    header('Location: user.php');
    exit;
}

// Get edit data
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM user_admin WHERE id_user = $id");
    $editData = mysqli_fetch_assoc($result);
}

// Get all users
$query = "SELECT * FROM user_admin ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

include '../layouts/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">User Admin</h1>
    <button onclick="showModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Tambah User
    </button>
</div>

<!-- Info Box -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
        <div>
            <h4 class="font-semibold text-yellow-900 mb-1">Penting!</h4>
            <p class="text-yellow-800 text-sm">
                Level akses: <strong>Superadmin</strong> (full akses), <strong>Admin</strong> (kelola booking & data), <strong>Operator</strong> (view only)
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr class="hover:bg-gray-50 <?php echo $row['id_user'] == $_SESSION['admin_id'] ? 'bg-blue-50' : ''; ?>">
                    <td class="px-6 py-4 text-sm"><?php echo $no++; ?></td>
                    <td class="px-6 py-4">
                        <span class="font-semibold"><?php echo $row['username']; ?></span>
                        <?php if ($row['id_user'] == $_SESSION['admin_id']): ?>
                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Anda</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['nama_lengkap']; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo $row['email']; ?></td>
                    <td class="px-6 py-4">
                        <?php
                        $levelColors = [
                            'superadmin' => 'bg-purple-100 text-purple-800',
                            'admin' => 'bg-blue-100 text-blue-800',
                            'operator' => 'bg-gray-100 text-gray-800'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $levelColors[$row['level']]; ?>">
                            <?php echo ucfirst($row['level']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $row['status'] == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php echo $row['last_login'] ? formatDateTime($row['last_login']) : 'Belum login'; ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="editUser(<?php echo $row['id_user']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if ($row['id_user'] != $_SESSION['admin_id']): ?>
                        <button onclick="deleteUser(<?php echo $row['id_user']; ?>)" class="text-red-600 hover:text-red-800">
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
            <h3 class="text-xl font-bold" id="modalTitle">Tambah User</h3>
        </div>
        <form method="POST" class="p-6">
            <input type="hidden" name="id_user" id="id_user" value="<?php echo $editData['id_user'] ?? ''; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Password <span class="text-red-500" id="pwdRequired">*</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1" id="pwdHint">Min. 6 karakter</p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-2">Email</label>
                <input type="email" name="email" id="email"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Level <span class="text-red-500">*</span></label>
                    <select name="level" id="level" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
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
    document.getElementById('modalTitle').textContent = 'Tambah User';
    document.querySelector('form').reset();
    document.getElementById('id_user').value = '';
    document.getElementById('password').required = true;
    document.getElementById('pwdRequired').style.display = 'inline';
    document.getElementById('pwdHint').textContent = 'Min. 6 karakter';
}

function closeModal() {
    document.getElementById('modalForm').classList.add('hidden');
}

function editUser(id) {
    window.location.href = 'user.php?edit=' + id;
}

function deleteUser(id) {
    Swal.fire({
        title: 'Hapus User?',
        text: 'User akan dihapus dari sistem',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'user.php?delete=' + id;
        }
    });
}

<?php if($editData): ?>
// Auto populate edit data
document.getElementById('modalForm').classList.remove('hidden');
document.getElementById('modalTitle').textContent = 'Edit User';
document.getElementById('id_user').value = '<?php echo $editData['id_user']; ?>';
document.getElementById('username').value = '<?php echo $editData['username']; ?>';
document.getElementById('nama_lengkap').value = '<?php echo $editData['nama_lengkap']; ?>';
document.getElementById('email').value = '<?php echo $editData['email']; ?>';
document.getElementById('level').value = '<?php echo $editData['level']; ?>';
document.getElementById('status').value = '<?php echo $editData['status']; ?>';
document.getElementById('password').required = false;
document.getElementById('pwdRequired').style.display = 'none';
document.getElementById('pwdHint').textContent = 'Kosongkan jika tidak ingin mengubah password';
<?php endif; ?>
</script>

<?php include '../layouts/admin_footer.php'; ?>