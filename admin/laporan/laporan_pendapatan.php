<?php
// admin/pengaturan.php
$title = 'Pengaturan';
require_once '../config/config.php';
require_once '../functions/helpers.php';
requireLogin();

// Handle Update Settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key != 'submit') {
            $nama_setting = sanitize($key);
            $nilai = sanitize($value);
            
            // Check if exists
            $check = mysqli_query($conn, "SELECT id_setting FROM pengaturan WHERE nama_setting = '$nama_setting'");
            
            if (mysqli_num_rows($check) > 0) {
                // Update
                mysqli_query($conn, "UPDATE pengaturan SET nilai = '$nilai', updated_at = NOW() WHERE nama_setting = '$nama_setting'");
            } else {
                // Insert
                mysqli_query($conn, "INSERT INTO pengaturan (nama_setting, nilai, tipe, updated_at) VALUES ('$nama_setting', '$nilai', 'text', NOW())");
            }
        }
    }
    
    setAlert('success', 'Berhasil!', 'Pengaturan berhasil disimpan');
    header('Location: pengaturan.php');
    exit;
}

// Get all settings
$query = "SELECT * FROM pengaturan ORDER BY nama_setting";
$result = mysqli_query($conn, $query);

// Convert to array
$settings = [];
while($row = mysqli_fetch_assoc($result)) {
    $settings[$row['nama_setting']] = $row['nilai'];
}

// Default settings if not exists
$defaultSettings = [
    'nama_website' => 'Sewa Gedung',
    'email' => 'info@sewagedung.com',
    'telepon' => '+62 812-3456-7890',
    'alamat' => 'Palangkaraya, Kalimantan Tengah',
    'dp_minimal' => '50',
    'hari_max_booking' => '365',
    'jam_operasional' => '08:00 - 17:00',
    'bank_1_nama' => 'BCA',
    'bank_1_rekening' => '1234567890',
    'bank_1_atas_nama' => 'CV Sewa Gedung',
    'bank_2_nama' => 'BRI',
    'bank_2_rekening' => '0987654321',
    'bank_2_atas_nama' => 'CV Sewa Gedung',
    'whatsapp' => '+62 812-3456-7890'
];

// Merge with defaults
$settings = array_merge($defaultSettings, $settings);

include '../layouts/admin_header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold">Pengaturan Sistem</h1>
    <p class="text-gray-600 mt-2">Kelola konfigurasi website dan sistem</p>
</div>

<form method="POST">
    <!-- Informasi Website -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold mb-4 pb-2 border-b">Informasi Website</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Nama Website</label>
                <input type="text" name="nama_website" value="<?php echo $settings['nama_website']; ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold mb-2">Email</label>
                <input type="email" name="email" value="<?php echo $settings['email']; ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Telepon</label>
                <input type="text" name="telepon" value="<?php echo $settings['telepon']; ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold mb-2">WhatsApp</label>
                <input type="text" name="whatsapp" value="<?php echo $settings['whatsapp']; ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block font-semibold mb-2">Alamat</label>
            <textarea name="alamat" rows="2"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo $settings['alamat']; ?></textarea>
        </div>
        
        <div>
            <label class="block font-semibold mb-2">Jam Operasional</label>
            <input type="text" name="jam_operasional" value="<?php echo $settings['jam_operasional']; ?>"
                   placeholder="Contoh: Senin-Jumat 08:00-17:00"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    
    <!-- Pengaturan Booking -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold mb-4 pb-2 border-b">Pengaturan Booking</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-2">DP Minimal (%)</label>
                <input type="number" name="dp_minimal" value="<?php echo $settings['dp_minimal']; ?>"
                       min="0" max="100"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Persentase DP minimal dari total booking</p>
            </div>
            <div>
                <label class="block font-semibold mb-2">Maksimal Booking (Hari ke Depan)</label>
                <input type="number" name="hari_max_booking" value="<?php echo $settings['hari_max_booking']; ?>"
                       min="1"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Berapa hari ke depan customer bisa booking</p>
            </div>
        </div>
    </div>
    
    <!-- Rekening Bank -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold mb-4 pb-2 border-b">Rekening Bank untuk Pembayaran</h3>
        
        <!-- Bank 1 -->
        <div class="border rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-3">Bank 1</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Nama Bank</label>
                    <input type="text" name="bank_1_nama" value="<?php echo $settings['bank_1_nama']; ?>"
                           placeholder="Contoh: BCA"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">No. Rekening</label>
                    <input type="text" name="bank_1_rekening" value="<?php echo $settings['bank_1_rekening']; ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Atas Nama</label>
                    <input type="text" name="bank_1_atas_nama" value="<?php echo $settings['bank_1_atas_nama']; ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Bank 2 -->
        <div class="border rounded-lg p-4">
            <h4 class="font-semibold mb-3">Bank 2</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Nama Bank</label>
                    <input type="text" name="bank_2_nama" value="<?php echo $settings['bank_2_nama']; ?>"
                           placeholder="Contoh: BRI"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">No. Rekening</label>
                    <input type="text" name="bank_2_rekening" value="<?php echo $settings['bank_2_rekening']; ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Atas Nama</label>
                    <input type="text" name="bank_2_atas_nama" value="<?php echo $settings['bank_2_atas_nama']; ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Social Media -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold mb-4 pb-2 border-b">Social Media</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-2">Facebook</label>
                <input type="text" name="facebook" value="<?php echo $settings['facebook'] ?? ''; ?>"
                       placeholder="https://facebook.com/..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold mb-2">Instagram</label>
                <input type="text" name="instagram" value="<?php echo $settings['instagram'] ?? ''; ?>"
                       placeholder="https://instagram.com/..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold mb-2">Twitter</label>
                <input type="text" name="twitter" value="<?php echo $settings['twitter'] ?? ''; ?>"
                       placeholder="https://twitter.com/..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold mb-2">YouTube</label>
                <input type="text" name="youtube" value="<?php echo $settings['youtube'] ?? ''; ?>"
                       placeholder="https://youtube.com/..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
    
    <!-- Button Save -->
    <div class="flex gap-3">
        <button type="submit" name="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
        </button>
        <a href="index.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
            Batal
        </a>
    </div>
</form>

<?php include '../layouts/admin_footer.php'; ?>