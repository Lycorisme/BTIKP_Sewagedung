<?php
// public/booking.php
$title = 'Booking Gedung - Sewa Gedung';
require_once '../config/config.php';
require_once '../functions/helpers.php';

// Ambil daftar gedung aktif
$queryGedung = "SELECT id_gedung, nama_gedung FROM gedung WHERE status = 'aktif' AND deleted_at IS NULL ORDER BY nama_gedung";
$resultGedung = mysqli_query($conn, $queryGedung);

// Ambil kategori penyewa
$queryKategori = "SELECT * FROM kategori_penyewa WHERE status = 'aktif' ORDER BY nama_kategori";
$resultKategori = mysqli_query($conn, $queryKategori);

$selected_gedung = isset($_GET['gedung']) ? (int)$_GET['gedung'] : 0;

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_gedung = (int)$_POST['id_gedung'];
    $tanggal_mulai = sanitize($_POST['tanggal_mulai']);
    $tanggal_selesai = sanitize($_POST['tanggal_selesai']);
    $id_kategori = (int)$_POST['id_kategori'];
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $no_hp = sanitize($_POST['no_hp']);
    $alamat = sanitize($_POST['alamat']);
    $instansi = sanitize($_POST['instansi']);
    $no_ktp = sanitize($_POST['no_ktp']);
    $keperluan = sanitize($_POST['keperluan']);
    $jumlah_tamu = (int)$_POST['jumlah_tamu'];
    
    // Validasi
    $errors = [];
    
    if (empty($id_gedung)) $errors[] = 'Pilih gedung';
    if (empty($tanggal_mulai)) $errors[] = 'Tanggal mulai wajib diisi';
    if (empty($tanggal_selesai)) $errors[] = 'Tanggal selesai wajib diisi';
    if ($tanggal_selesai < $tanggal_mulai) $errors[] = 'Tanggal selesai tidak valid';
    if (empty($nama_lengkap)) $errors[] = 'Nama lengkap wajib diisi';
    if (empty($email)) $errors[] = 'Email wajib diisi';
    if (empty($no_hp)) $errors[] = 'No HP wajib diisi';
    
    // Cek ketersediaan
    if (cekKetersediaan($conn, $id_gedung, $tanggal_mulai, $tanggal_selesai)) {
        $errors[] = 'Gedung sudah dibooking pada tanggal tersebut';
    }
    
    if (empty($errors)) {
        mysqli_begin_transaction($conn);
        
        try {
            // Upload file KTP
            $file_ktp = '';
            if (!empty($_FILES['file_ktp']['name'])) {
                $uploadKTP = uploadFile($_FILES['file_ktp'], UPLOAD_DOKUMEN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($uploadKTP['success']) {
                    $file_ktp = $uploadKTP['filename'];
                }
            }
            
            // Upload file surat
            $file_surat = '';
            if (!empty($_FILES['file_surat']['name'])) {
                $uploadSurat = uploadFile($_FILES['file_surat'], UPLOAD_DOKUMEN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($uploadSurat['success']) {
                    $file_surat = $uploadSurat['filename'];
                }
            }
            
            // Insert penyewa
            $queryPenyewa = "INSERT INTO penyewa (id_kategori, nama_lengkap, email, no_hp, alamat, instansi, no_ktp, file_ktp, file_surat, created_at, updated_at) 
                            VALUES ('$id_kategori', '$nama_lengkap', '$email', '$no_hp', '$alamat', '$instansi', '$no_ktp', '$file_ktp', '$file_surat', NOW(), NOW())";
            mysqli_query($conn, $queryPenyewa);
            $id_penyewa = mysqli_insert_id($conn);
            
            // Hitung harga
            $harga = hitungHargaBooking($conn, $id_gedung, $tanggal_mulai, $tanggal_selesai, $id_kategori);
            
            // Generate kode booking
            $kode_booking = generateKodeBooking($conn);
            
            // Insert booking
            $queryBooking = "INSERT INTO booking (
                kode_booking, id_gedung, id_penyewa, tanggal_booking, tanggal_mulai, tanggal_selesai, 
                durasi_hari, keperluan, jumlah_tamu, harga_per_hari, total_harga, diskon, total_bayar, 
                status_booking, created_at, updated_at
            ) VALUES (
                '$kode_booking', '$id_gedung', '$id_penyewa', CURDATE(), '$tanggal_mulai', '$tanggal_selesai',
                '{$harga['durasi']}', '$keperluan', '$jumlah_tamu', '{$harga['harga_per_hari']}', 
                '{$harga['total_harga']}', '{$harga['diskon']}', '{$harga['total_bayar']}',
                'pending', NOW(), NOW()
            )";
            mysqli_query($conn, $queryBooking);
            
            mysqli_commit($conn);
            
            setAlert('success', 'Booking Berhasil!', "Kode booking Anda: $kode_booking. Silakan tunggu konfirmasi dari admin.");
            header('Location: booking.php');
            exit;
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            setAlert('error', 'Booking Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    } else {
        setAlert('error', 'Validasi Gagal!', implode(', ', $errors));
    }
}

include '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Form Booking Gedung</h1>
        
        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-8">
            
            <!-- Pilih Gedung -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-4 pb-2 border-b">1. Pilih Gedung & Tanggal</h3>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Gedung <span class="text-red-500">*</span></label>
                    <select name="id_gedung" id="id_gedung" required onchange="loadHarga()"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block font-semibold mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required 
                               min="<?php echo date('Y-m-d'); ?>" onchange="hitungEstimasi()"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required 
                               min="<?php echo date('Y-m-d'); ?>" onchange="hitungEstimasi()"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div id="estimasiHarga" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="font-semibold mb-2">Estimasi Biaya:</p>
                    <p id="detailHarga" class="text-sm text-gray-700"></p>
                </div>
            </div>
            
            <!-- Data Penyewa -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-4 pb-2 border-b">2. Data Penyewa</h3>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Kategori Penyewa <span class="text-red-500">*</span></label>
                    <select name="id_kategori" id="id_kategori" required onchange="hitungEstimasi()"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Kategori --</option>
                        <?php 
                        mysqli_data_seek($resultKategori, 0);
                        while($kategori = mysqli_fetch_assoc($resultKategori)): 
                        ?>
                            <option value="<?php echo $kategori['id_kategori']; ?>" 
                                    data-diskon="<?php echo $kategori['diskon_persen']; ?>">
                                <?php echo $kategori['nama_kategori']; ?>
                                <?php if($kategori['diskon_persen'] > 0): ?>
                                    (Diskon <?php echo $kategori['diskon_persen']; ?>%)
                                <?php endif; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block font-semibold mb-2">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">No KTP</label>
                        <input type="text" name="no_ktp"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Instansi/Organisasi</label>
                    <input type="text" name="instansi"
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Detail Acara -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-4 pb-2 border-b">3. Detail Acara</h3>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Keperluan/Tujuan Acara</label>
                    <textarea name="keperluan" rows="3"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Estimasi Jumlah Tamu</label>
                    <input type="number" name="jumlah_tamu"
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Upload Dokumen -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-4 pb-2 border-b">4. Upload Dokumen</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-2">Upload KTP (Optional)</label>
                        <input type="file" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Upload Surat Permohonan (Optional)</label>
                        <input type="file" name="file_surat" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Booking
                </button>
                <a href="daftar-gedung.php" class="px-8 py-4 border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
    
</div>

<script>
// Load harga gedung via AJAX (simplified - dalam produksi gunakan AJAX ke API)
function loadHarga() {
    hitungEstimasi();
}

function hitungEstimasi() {
    // Implementasi sederhana - dalam produksi bisa gunakan AJAX ke backend
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    
    if (tanggalMulai && tanggalSelesai && tanggalSelesai >= tanggalMulai) {
        document.getElementById('estimasiHarga').classList.remove('hidden');
        document.getElementById('detailHarga').innerHTML = 'Silakan submit form untuk melihat estimasi biaya lengkap';
    }
}
</script>

<?php include '../layouts/footer.php'; ?>