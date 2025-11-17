<?php
// layouts/admin_header.php
if (!isset($title)) $title = 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Admin</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js untuk grafik dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-900 text-white w-64 fixed h-full overflow-y-auto transition-transform duration-300 z-30">
            <div class="p-4 border-b border-blue-800">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-building mr-2"></i>
                    Admin Panel
                </h2>
            </div>
            
            <nav class="p-4">
                <a href="index.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                
                <div class="mb-2">
                    <button onclick="toggleSubmenu('gedungMenu')" class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-blue-800 transition">
                        <span><i class="fas fa-building mr-3"></i> Gedung</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="gedungMenu" class="hidden ml-4 mt-2 space-y-1">
                        <a href="gedung.php" class="block px-4 py-2 rounded hover:bg-blue-800">Data Gedung</a>
                        <a href="foto_gedung.php" class="block px-4 py-2 rounded hover:bg-blue-800">Galeri Foto</a>
                    </div>
                </div>
                
                <a href="booking.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-calendar-check mr-3"></i> Booking
                </a>
                
                <a href="pembayaran.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-money-bill mr-3"></i> Pembayaran
                </a>
                
                <a href="penyewa.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-users mr-3"></i> Penyewa
                </a>
                
                <a href="kategori.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-tags mr-3"></i> Kategori
                </a>
                
                <div class="mb-2">
                    <button onclick="toggleSubmenu('laporanMenu')" class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-blue-800 transition">
                        <span><i class="fas fa-file-alt mr-3"></i> Laporan</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="laporanMenu" class="hidden ml-4 mt-2 space-y-1">
                        <a href="laporan/laporan_booking.php" class="block px-4 py-2 rounded hover:bg-blue-800">Lap. Booking</a>
                        <a href="laporan/laporan_pendapatan.php" class="block px-4 py-2 rounded hover:bg-blue-800">Lap. Pendapatan</a>
                        <a href="laporan/laporan_gedung_populer.php" class="block px-4 py-2 rounded hover:bg-blue-800">Lap. Gedung Populer</a>
                        <a href="laporan/laporan_status_booking.php" class="block px-4 py-2 rounded hover:bg-blue-800">Lap. Status</a>
                        <a href="laporan/laporan_penyewa.php" class="block px-4 py-2 rounded hover:bg-blue-800">Lap. Penyewa</a>
                    </div>
                </div>
                
                <a href="user.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-user-cog mr-3"></i> User Admin
                </a>
                
                <a href="pengaturan.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-800 transition">
                    <i class="fas fa-cog mr-3"></i> Pengaturan
                </a>
                
                <a href="logout.php" onclick="return confirm('Yakin ingin logout?')" class="flex items-center px-4 py-3 mb-2 rounded-lg bg-red-600 hover:bg-red-700 transition mt-4">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 ml-64 flex flex-col overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">
                            <i class="fas fa-user-circle mr-2"></i>
                            <?php echo $_SESSION['admin_nama'] ?? 'Admin'; ?>
                        </span>
                        <a href="../public/index.php" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-external-link-alt"></i> Lihat Website
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            submenu.classList.toggle('hidden');
        }
    </script>