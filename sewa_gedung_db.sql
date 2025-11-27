-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 03:48 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewa_gedung_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `kode_booking` varchar(20) NOT NULL,
  `id_gedung` int(11) DEFAULT NULL,
  `id_penyewa` int(11) DEFAULT NULL,
  `tanggal_booking` date DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `durasi_hari` int(11) DEFAULT NULL,
  `keperluan` text,
  `jumlah_tamu` int(11) DEFAULT NULL,
  `harga_per_hari` decimal(10,2) DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `diskon` decimal(10,2) DEFAULT NULL,
  `total_bayar` decimal(10,2) DEFAULT NULL,
  `status_booking` enum('pending','approved','confirmed','selesai','dibatalkan') DEFAULT 'pending',
  `catatan_admin` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `kode_booking`, `id_gedung`, `id_penyewa`, `tanggal_booking`, `tanggal_mulai`, `tanggal_selesai`, `durasi_hari`, `keperluan`, `jumlah_tamu`, `harga_per_hari`, `total_harga`, `diskon`, `total_bayar`, `status_booking`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 'BKG20251115001', 1, 1, '2025-11-15', '2025-11-25', '2025-11-25', 1, 'Acara pernikahan', 400, 4500000.00, 4500000.00, 0.00, 4500000.00, 'selesai', 'Acara berjalan lancar', '2025-11-18 05:21:01', '2025-11-18 05:27:50'),
(2, 'BKG20251116001', 2, 3, '2025-11-16', '2025-11-28', '2025-11-29', 2, 'Seminar Pendidikan Nasional', 250, 2500000.00, 5000000.00, 750000.00, 4250000.00, 'selesai', 'Pembayaran lunas', '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(3, 'BKG20251117001', 3, 2, '2025-11-17', '2025-12-05', '2025-12-05', 1, 'Rapat organisasi mahasiswa', 100, 2300000.00, 2300000.00, 230000.00, 2070000.00, 'confirmed', 'DP sudah dibayar', '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(4, 'BKG20251117002', 4, 6, '2025-11-17', '2025-12-10', '2025-12-11', 2, 'Company Gathering PT Maju Sejahtera', 600, 6000000.00, 12000000.00, 600000.00, 11400000.00, 'approved', 'Menunggu pembayaran DP', '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(5, 'BKG20251118001', 1, 4, '2025-11-18', '2025-12-15', '2025-12-15', 1, 'Syukuran keluarga', 200, 4500000.00, 4500000.00, 0.00, 4500000.00, 'pending', 'Menunggu approval admin', '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(6, 'BKG20251118002', 5, 5, '2025-11-18', '2025-12-20', '2025-12-20', 1, 'Acara amal yayasan', 180, 2800000.00, 2800000.00, 336000.00, 2464000.00, 'pending', NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(7, 'BKG20251118003', 2, 7, '2025-11-18', '2025-11-18', '2025-11-20', 3, 'csdcsdcdscsdc', 13, 2500000.00, 7500000.00, 900000.00, 6600000.00, 'pending', NULL, '2025-11-18 05:35:55', '2025-11-18 05:35:55');

-- --------------------------------------------------------

--
-- Table structure for table `foto_gedung`
--

CREATE TABLE `foto_gedung` (
  `id_foto` int(11) NOT NULL,
  `id_gedung` int(11) DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `id_gedung` int(11) NOT NULL,
  `nama_gedung` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `deskripsi` text,
  `kapasitas` int(11) DEFAULT NULL,
  `luas_gedung` varchar(50) DEFAULT NULL,
  `alamat` text,
  `fasilitas` text,
  `harga_weekday` decimal(10,2) NOT NULL DEFAULT '0.00',
  `harga_weekend` decimal(10,2) NOT NULL DEFAULT '0.00',
  `foto_utama` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gedung`
--

INSERT INTO `gedung` (`id_gedung`, `nama_gedung`, `slug`, `deskripsi`, `kapasitas`, `luas_gedung`, `alamat`, `fasilitas`, `harga_weekday`, `harga_weekend`, `foto_utama`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Gedung Serbaguna Flamboyan', 'gedung-serbaguna-flamboyan', 'Gedung serbaguna dengan fasilitas lengkap cocok untuk berbagai acara seperti seminar, pernikahan, dan gathering. Dilengkapi dengan AC, sound system berkualitas, dan parkir luas.', 500, '600 m²', 'Jl. G. Obos No. 45, Palangkaraya', 'AC, Sound System, Proyektor, Toilet, Parkir Luas, Panggung, Catering Area', 3500000.00, 4500000.00, NULL, 'aktif', '2025-11-18 05:21:01', '2025-11-18 05:21:01', NULL),
(2, 'Aula Melati', 'aula-melati', 'Aula megah dengan desain modern yang cocok untuk acara formal seperti seminar, pelatihan, dan rapat besar. Dilengkapi dengan teknologi audio visual terkini.', 300, '400 m²', 'Jl. Tjilik Riwut Km. 5, Palangkaraya', 'AC, Sound System, Proyektor LCD, Wifi, Toilet, Pantry, Parkir', 2500000.00, 3200000.00, NULL, 'aktif', '2025-11-18 05:21:01', '2025-11-18 05:21:01', NULL),
(3, 'Gedung Pertemuan Anggrek', 'gedung-pertemuan-anggrek', 'Gedung pertemuan yang nyaman dengan kapasitas medium. Cocok untuk acara keluarga, arisan, atau rapat kecil hingga menengah.', 150, '250 m²', 'Jl. Yos Sudarso No. 88, Palangkaraya', 'AC, Sound System, Meja Kursi, Toilet, Parkir', 1800000.00, 2300000.00, NULL, 'aktif', '2025-11-18 05:21:01', '2025-11-18 05:21:01', NULL),
(4, 'Hall Mawar Convention', 'hall-mawar-convention', 'Convention hall besar dengan kapasitas hingga 800 orang. Ideal untuk pameran, konser, dan acara skala besar. Dilengkapi fasilitas VIP room.', 800, '1000 m²', 'Jl. Ahmad Yani No. 12, Palangkaraya', 'AC Central, Sound System Pro, LED Screen, VIP Room, Toilet, Food Court, Parkir 200 mobil', 6000000.00, 7500000.00, NULL, 'aktif', '2025-11-18 05:21:01', '2025-11-18 05:21:01', NULL),
(5, 'Pendopo Kenanga', 'pendopo-kenanga', 'Pendopo tradisional dengan nuansa khas Kalimantan. Cocok untuk acara pernikahan adat, syukuran, dan acara tradisional lainnya.', 200, '300 m²', 'Jl. RTA Milono Km. 2, Palangkaraya', 'Sound System, Dekorasi Tradisional, Panggung, Toilet, Parkir', 2000000.00, 2800000.00, NULL, 'aktif', '2025-11-18 05:21:01', '2025-11-18 05:21:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_penyewa`
--

CREATE TABLE `kategori_penyewa` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `diskon_persen` decimal(5,2) DEFAULT '0.00',
  `keterangan` text,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kategori_penyewa`
--

INSERT INTO `kategori_penyewa` (`id_kategori`, `nama_kategori`, `diskon_persen`, `keterangan`, `status`) VALUES
(1, 'Umum', 0.00, 'Kategori penyewa umum tanpa diskon', 'aktif'),
(2, 'Mahasiswa', 10.00, 'Diskon khusus untuk mahasiswa', 'aktif'),
(3, 'Instansi Pemerintah', 15.00, 'Diskon untuk instansi pemerintah', 'aktif'),
(4, 'Organisasi Sosial', 12.00, 'Diskon untuk organisasi sosial/yayasan', 'aktif'),
(5, 'Perusahaan', 5.00, 'Diskon untuk perusahaan swasta', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_booking` int(11) DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `jumlah_bayar` decimal(10,2) DEFAULT NULL,
  `metode_bayar` enum('transfer','cash','other') DEFAULT NULL,
  `bank_tujuan` varchar(50) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status_bayar` enum('pending','verified','rejected') DEFAULT 'pending',
  `catatan` text,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_booking`, `tanggal_bayar`, `jumlah_bayar`, `metode_bayar`, `bank_tujuan`, `nomor_rekening`, `atas_nama`, `bukti_bayar`, `status_bayar`, `catatan`, `verified_by`, `verified_at`, `created_at`) VALUES
(1, 1, '2025-11-20', 4500000.00, 'transfer', 'BCA', '1234567890', 'Budi Santoso', NULL, 'verified', 'Pembayaran lunas', 1, '2025-11-20 14:30:00', '2025-11-20 10:15:00'),
(2, 2, '2025-11-18', 4250000.00, 'transfer', 'BRI', '0987654321', 'Dinas Pendidikan', NULL, 'verified', 'Lunas', 1, '2025-11-18 16:00:00', '2025-11-18 13:20:00'),
(3, 3, '2025-11-19', 1035000.00, 'transfer', 'BCA', '1234567890', 'Siti Nurhaliza', NULL, 'verified', 'DP 50%', 1, '2025-11-19 11:00:00', '2025-11-19 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id_setting` int(11) NOT NULL,
  `nama_setting` varchar(50) NOT NULL,
  `nilai` text,
  `tipe` enum('text','number','image','textarea') DEFAULT 'text',
  `keterangan` text,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id_setting`, `nama_setting`, `nilai`, `tipe`, `keterangan`, `updated_at`) VALUES
(1, 'nama_website', 'Sewa Gedung Palangkaraya', 'text', 'Nama website', '2025-11-18 05:21:01'),
(2, 'email', 'info@sewagedung.com', 'text', 'Email kontak', '2025-11-18 05:21:01'),
(3, 'telepon', '+62 812-3456-7890', 'text', 'Nomor telepon', '2025-11-18 05:21:01'),
(4, 'whatsapp', '+62 812-3456-7890', 'text', 'Nomor WhatsApp', '2025-11-18 05:21:01'),
(5, 'alamat', 'Jl. G. Obos No. 123, Palangkaraya, Kalimantan Tengah 73111', 'textarea', 'Alamat kantor', '2025-11-18 05:21:01'),
(6, 'jam_operasional', 'Senin-Jumat: 08:00-17:00, Sabtu: 08:00-14:00', 'text', 'Jam operasional', '2025-11-18 05:21:01'),
(7, 'dp_minimal', '50', 'number', 'DP minimal dalam persen', '2025-11-18 05:21:01'),
(8, 'hari_max_booking', '365', 'number', 'Maksimal booking hari ke depan', '2025-11-18 05:21:01'),
(9, 'bank_1_nama', 'BCA', 'text', 'Nama bank 1', '2025-11-18 05:21:01'),
(10, 'bank_1_rekening', '1234567890', 'text', 'Nomor rekening bank 1', '2025-11-18 05:21:01'),
(11, 'bank_1_atas_nama', 'CV Sewa Gedung Palangkaraya', 'text', 'Atas nama rekening bank 1', '2025-11-18 05:21:01'),
(12, 'bank_2_nama', 'BRI', 'text', 'Nama bank 2', '2025-11-18 05:21:01'),
(13, 'bank_2_rekening', '0987654321', 'text', 'Nomor rekening bank 2', '2025-11-18 05:21:01'),
(14, 'bank_2_atas_nama', 'CV Sewa Gedung Palangkaraya', 'text', 'Atas nama rekening bank 2', '2025-11-18 05:21:01');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `instansi` varchar(100) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL,
  `file_ktp` varchar(255) DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id_penyewa`, `id_kategori`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `instansi`, `no_ktp`, `file_ktp`, `file_surat`, `created_at`, `updated_at`) VALUES
(1, 1, 'Budi Santoso', 'budi.santoso@email.com', '081234567890', 'Jl. Diponegoro No. 23, Palangkaraya', NULL, '6271012345670001', NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(2, 2, 'Siti Nurhaliza', 'siti.nur@student.ac.id', '082345678901', 'Jl. Mahir Mahar No. 45, Palangkaraya', 'Universitas Palangka Raya', '6271022345670002', NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(3, 3, 'Drs. Ahmad Fauzi', 'ahmad.fauzi@pemda.go.id', '081345678902', 'Komplek Perkantoran Provinsi', 'Dinas Pendidikan Provinsi Kalteng', '6271032345670003', NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(4, 1, 'Rina Wijaya', 'rina.wijaya@email.com', '082456789012', 'Jl. Imam Bonjol No. 67, Palangkaraya', NULL, '6271042345670004', NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(5, 4, 'Yayasan Peduli Anak', 'info@ypa.org', '081456789023', 'Jl. Halmahera No. 12, Palangkaraya', 'Yayasan Peduli Anak Kalteng', NULL, NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(6, 5, 'PT Maju Sejahtera', 'hrd@majusejahtera.com', '082567890134', 'Jl. DI Panjaitan No. 89, Palangkaraya', 'PT Maju Sejahtera Indonesia', NULL, NULL, NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(7, 4, 'cdscdscd', 'andi@mahasiswa.com', 'cscsdcddc', 'cdscdcdc', 'scdscdscsc', '2323232424424', '', '', '2025-11-18 05:35:54', '2025-11-18 05:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_admin`
--

CREATE TABLE `user_admin` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `level` enum('superadmin','admin','operator') DEFAULT 'admin',
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_admin`
--

INSERT INTO `user_admin` (`id_user`, `username`, `password`, `nama_lengkap`, `email`, `level`, `foto`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'admin@sewagedung.com', 'superadmin', NULL, 'aktif', '2025-11-24 10:05:32', '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(2, 'operator', 'operator123', 'Operator 1', 'operator@sewagedung.com', 'operator', NULL, 'aktif', NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01'),
(3, 'staff', 'staff123', 'Staff Admin', 'staff@sewagedung.com', 'admin', NULL, 'aktif', NULL, '2025-11-18 05:21:01', '2025-11-18 05:21:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `id_gedung` (`id_gedung`),
  ADD KEY `id_penyewa` (`id_penyewa`);

--
-- Indexes for table `foto_gedung`
--
ALTER TABLE `foto_gedung`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_gedung` (`id_gedung`);

--
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`id_gedung`);

--
-- Indexes for table `kategori_penyewa`
--
ALTER TABLE `kategori_penyewa`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id_setting`),
  ADD UNIQUE KEY `nama_setting` (`nama_setting`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id_penyewa`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `user_admin`
--
ALTER TABLE `user_admin`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `foto_gedung`
--
ALTER TABLE `foto_gedung`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `id_gedung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori_penyewa`
--
ALTER TABLE `kategori_penyewa`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id_setting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id_penyewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_admin`
--
ALTER TABLE `user_admin`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_gedung`) REFERENCES `gedung` (`id_gedung`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`);

--
-- Constraints for table `foto_gedung`
--
ALTER TABLE `foto_gedung`
  ADD CONSTRAINT `foto_gedung_ibfk_1` FOREIGN KEY (`id_gedung`) REFERENCES `gedung` (`id_gedung`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`);

--
-- Constraints for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD CONSTRAINT `penyewa_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_penyewa` (`id_kategori`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
