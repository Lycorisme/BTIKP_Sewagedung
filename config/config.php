<?php
// config/config.php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sewa_gedung_db');

// Koneksi Database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL
define('BASE_URL', 'http://localhost/sewa-gedung/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('PUBLIC_URL', BASE_URL . 'public/');

// Path Upload
define('UPLOAD_GEDUNG', '../uploads/gedung/');
define('UPLOAD_GALERI', '../uploads/galeri/');
define('UPLOAD_BUKTI', '../uploads/bukti_bayar/');
define('UPLOAD_DOKUMEN', '../uploads/dokumen/');

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function untuk cek login admin
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Helper function untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
?>