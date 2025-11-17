<?php
// admin/login.php
session_start();
require_once '../config/config.php';
require_once '../functions/helpers.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM user_admin WHERE username = '$username' AND status = 'aktif'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Cek password (plain text sesuai permintaan)
        if ($password == $user['password']) {
            // Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id_user'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_nama'] = $user['nama_lengkap'];
            $_SESSION['admin_level'] = $user['level'];
            
            // Update last login
            mysqli_query($conn, "UPDATE user_admin SET last_login = NOW() WHERE id_user = {$user['id_user']}");
            
            setAlert('success', 'Login Berhasil!', 'Selamat datang, ' . $user['nama_lengkap']);
            header('Location: index.php');
            exit;
        }
    }
    
    setAlert('error', 'Login Gagal!', 'Username atau password salah');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sewa Gedung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-900 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="bg-blue-600 text-white p-8 text-center">
                <i class="fas fa-user-shield text-5xl mb-4"></i>
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="text-blue-100 mt-2">Sewa Gedung Management</p>
            </div>
            
            <!-- Form -->
            <div class="p-8">
                <form method="POST" action="">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user mr-2"></i>Username
                        </label>
                        <input type="text" name="username" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Masukkan username">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Masukkan password">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="../public/index.php" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Website
                    </a>
                </div>
            </div>
            
        </div>
        
        <!-- Default Login Info (untuk testing) -->
        <div class="bg-white bg-opacity-90 rounded-lg p-4 mt-4 text-sm text-gray-700">
            <p class="font-semibold mb-1">Default Login (untuk testing):</p>
            <p>Username: <code class="bg-gray-200 px-2 py-1 rounded">admin</code></p>
            <p>Password: <code class="bg-gray-200 px-2 py-1 rounded">admin123</code></p>
        </div>
    </div>
    
    <?php showAlert(); ?>
    
</body>
</html>