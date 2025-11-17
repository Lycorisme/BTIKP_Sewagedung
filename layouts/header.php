<?php
// layouts/header.php
if (!isset($title)) $title = 'Sewa Gedung';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Navbar Public -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-building text-white text-2xl"></i>
                    <a href="index.php" class="text-white text-xl font-bold">Sewa Gedung</a>
                </div>
                
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-white hover:text-blue-200 transition">Beranda</a>
                    <a href="daftar-gedung.php" class="text-white hover:text-blue-200 transition">Daftar Gedung</a>
                    <a href="tentang.php" class="text-white hover:text-blue-200 transition">Tentang Kami</a>
                    <a href="kontak.php" class="text-white hover:text-blue-200 transition">Kontak</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="booking.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                    </a>
                    <a href="../admin/login.php" class="text-white hover:text-blue-200">
                        <i class="fas fa-user-lock"></i>
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block text-white py-2 hover:bg-blue-700 px-4 rounded">Beranda</a>
                <a href="daftar-gedung.php" class="block text-white py-2 hover:bg-blue-700 px-4 rounded">Daftar Gedung</a>
                <a href="tentang.php" class="block text-white py-2 hover:bg-blue-700 px-4 rounded">Tentang Kami</a>
                <a href="kontak.php" class="block text-white py-2 hover:bg-blue-700 px-4 rounded">Kontak</a>
            </div>
        </div>
    </nav>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>