<?php
// functions/helpers.php

// Format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format tanggal Indonesia
function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Format datetime Indonesia
function formatDateTime($datetime) {
    if (!$datetime) return '-';
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

// Generate kode booking
function generateKodeBooking($conn) {
    $tanggal = date('Ymd');
    $prefix = 'BKG' . $tanggal;
    
    $query = "SELECT kode_booking FROM booking WHERE kode_booking LIKE '$prefix%' ORDER BY kode_booking DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastCode = $row['kode_booking'];
        $lastNumber = (int)substr($lastCode, -3);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

// Generate slug dari string
function generateSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    return $slug;
}

// Cek ketersediaan gedung
function cekKetersediaan($conn, $id_gedung, $tanggal_mulai, $tanggal_selesai, $exclude_booking = null) {
    $query = "SELECT * FROM booking 
              WHERE id_gedung = '$id_gedung' 
              AND status_booking NOT IN ('dibatalkan')
              AND (
                  (tanggal_mulai <= '$tanggal_mulai' AND tanggal_selesai >= '$tanggal_mulai')
                  OR (tanggal_mulai <= '$tanggal_selesai' AND tanggal_selesai >= '$tanggal_selesai')
                  OR (tanggal_mulai >= '$tanggal_mulai' AND tanggal_selesai <= '$tanggal_selesai')
              )";
    
    if ($exclude_booking) {
        $query .= " AND id_booking != '$exclude_booking'";
    }
    
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Hitung durasi hari
function hitungDurasi($tanggal_mulai, $tanggal_selesai) {
    $start = new DateTime($tanggal_mulai);
    $end = new DateTime($tanggal_selesai);
    $diff = $start->diff($end);
    return $diff->days + 1;
}

// Cek apakah tanggal adalah weekend
function isWeekend($tanggal) {
    $dayOfWeek = date('N', strtotime($tanggal));
    return ($dayOfWeek == 6 || $dayOfWeek == 7); // 6=Sabtu, 7=Minggu
}

// Hitung harga booking
function hitungHargaBooking($conn, $id_gedung, $tanggal_mulai, $tanggal_selesai, $id_kategori) {
    // Ambil data gedung
    $queryGedung = "SELECT harga_weekday, harga_weekend FROM gedung WHERE id_gedung = '$id_gedung'";
    $resultGedung = mysqli_query($conn, $queryGedung);
    $gedung = mysqli_fetch_assoc($resultGedung);
    
    // Ambil diskon kategori
    $queryKategori = "SELECT diskon_persen FROM kategori_penyewa WHERE id_kategori = '$id_kategori'";
    $resultKategori = mysqli_query($conn, $queryKategori);
    $kategori = mysqli_fetch_assoc($resultKategori);
    
    $durasi = hitungDurasi($tanggal_mulai, $tanggal_selesai);
    $totalHarga = 0;
    
    // Hitung per hari
    $currentDate = $tanggal_mulai;
    for ($i = 0; $i < $durasi; $i++) {
        if (isWeekend($currentDate)) {
            $totalHarga += $gedung['harga_weekend'];
        } else {
            $totalHarga += $gedung['harga_weekday'];
        }
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    }
    
    $diskon = ($totalHarga * $kategori['diskon_persen']) / 100;
    $totalBayar = $totalHarga - $diskon;
    
    return [
        'durasi' => $durasi,
        'harga_per_hari' => ($totalHarga / $durasi),
        'total_harga' => $totalHarga,
        'diskon' => $diskon,
        'total_bayar' => $totalBayar
    ];
}

// Upload file
function uploadFile($file, $folder, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    if ($fileError !== 0) {
        return ['success' => false, 'message' => 'Error upload file'];
    }
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    if ($fileSize > 5000000) { // 5MB
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
    $destination = $folder . $newFileName;
    
    if (move_uploaded_file($fileTmp, $destination)) {
        return ['success' => true, 'filename' => $newFileName];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Alert SweetAlert helper
function setAlert($type, $title, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}

// Show alert jika ada
function showAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<script>
            Swal.fire({
                icon: '{$alert['type']}',
                title: '{$alert['title']}',
                text: '{$alert['message']}'
            });
        </script>";
        unset($_SESSION['alert']);
    }
}
?>