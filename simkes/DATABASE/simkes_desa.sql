-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2026 at 03:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simkes_desa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin123', '2026-05-16 02:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `poli` varchar(50) DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `nomor_antrian` int(11) DEFAULT NULL,
  `status` enum('menunggu','selesai') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `nama`, `nik`, `no_hp`, `tanggal`, `poli`, `keluhan`, `nomor_antrian`, `status`) VALUES
(7, 'nara', '1112223334445555', '087654321234', '2026-05-16', 'Poli Umum', 'demam', 1, 'selesai'),
(8, 'Nayla Rahma', '1234567891234567', '082125385275', '2026-05-16', 'Poli Umum', 'influenza', 2, 'menunggu'),
(10, 'yara', '2222223334445555', '08888225999', '2026-05-26', 'Poli Gigi', '0', 1, 'selesai'),
(12, 'Salsa', '4442223334445555', '08888225949', '2026-05-26', 'Poli Umum', '0', 3, 'menunggu'),
(13, 'Salsa bila', '5552223334445555', '08888225949', '2026-05-26', 'Poli Umum', 'batuk', 4, 'selesai'),
(14, 'Tiaraaa', '6662223334445555', '087654321234', '2026-05-26', 'Poli Umum', 'migren', 5, 'menunggu'),
(15, 'Tiara1', '1112223334445555', '087654321234', '2026-05-26', 'Poli Umum', 'coba1', 6, 'menunggu'),
(16, 'Tiara2', '3332223334445555', '08888225949', '2026-05-26', 'Poli Umum', 'coba2', 7, 'menunggu'),
(17, 'Tiara3', '3332223334445555', '08888225444', '2026-05-26', 'KIA', 'coba3', 1, 'menunggu'),
(18, 'Tiara4', '2222223334445555', '08888225999', '2026-05-26', 'KIA', 'coba4', 2, 'menunggu'),
(19, 'Tiara5', '1234567891234567', '08888225444', '2026-05-26', 'Poli Gigi', 'coba5', 2, 'menunggu'),
(20, 'Tiara6', '1234567891234567', '087654321234', '2026-05-26', 'Poli Gigi', 'coba6', 3, 'menunggu'),
(21, 'coba7', '1112223334445555', '08888225999', '2026-05-27', 'Poli Gigi', 'coba7', 1, 'menunggu'),
(22, 'cek', '1234567891234567', '088888225949', '2026-05-25', 'Poli Gigi', 'cekkk', 1, 'menunggu'),
(23, 'cekcek', '1112223334445555', '08888225949', '2026-05-25', 'Poli Umum', 'hhh', 1, 'menunggu'),
(24, 'Tiara Salsabila', '3332223334445555', '0888225949', '2026-05-25', 'Poli Umum', '', 2, 'menunggu'),
(25, 'yara', '0992222223334449', '088888200000', '2026-05-25', 'Poli Umum', '', 3, 'menunggu'),
(26, 'yaras', '0992222223339999', '0888822999', '2026-05-25', 'Poli Umum', 'hehe', 4, 'menunggu'),
(27, 'y', '9992222223334449', '088888225949', '2026-05-25', 'Poli Umum', 'jj', 5, 'menunggu'),
(28, 'kk', '9992222223334449', '088888225949', '2026-06-29', 'Poli Umum', 'l', 1, 'selesai'),
(29, 'yar', '9992222223334440', '088888225949', '2026-05-31', 'Poli Umum', 'h', 1, 'menunggu'),
(30, 'cekkkk', '1234567891234567', '08888225949', '2026-05-27', 'Poli Gigi', 'kkk', 2, 'menunggu'),
(31, 'cekll', '1112223334445555', '08888225444', '2026-05-26', 'Poli Gigi', 'kkkpppp', 4, 'menunggu'),
(32, 'narayara', '7234567891234567', '088888225949', '2026-05-26', 'KIA', 'mumet', 3, 'selesai'),
(33, 'nayla rahma', '1234567891234567', '082125385275', '2026-05-26', 'Poli Umum', 'batuk', 8, 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `spesialis` varchar(100) DEFAULT NULL,
  `jadwal` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_dokter`
--

CREATE TABLE `jadwal_dokter` (
  `id` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `poli` varchar(50) NOT NULL,
  `hari` varchar(20) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktu_praktik` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_dokter`
--

INSERT INTO `jadwal_dokter` (`id`, `nama_dokter`, `poli`, `hari`, `jam_mulai`, `jam_selesai`, `status`, `created_at`, `waktu_praktik`) VALUES
(2, 'dr. Tiara Salsabila', 'Poli Umum', 'Rabu', '00:00:00', '12:00:00', 'aktif', '2026-05-16 02:30:51', 'Senin-Sabtu (10.00-15.00)'),
(4, 'drg. Nayla Rahma', 'Poli Gigi', 'Kamis', '09:00:00', '13:00:00', 'aktif', '2026-05-16 02:30:51', 'Senin-Jumat (09.00-16.00)'),
(5, 'dr. Farhanah Salsabila', 'KIA', 'Senin', '08:00:00', '11:00:00', 'aktif', '2026-05-16 02:30:51', 'Senin-Minggu (08.00-17.00)'),
(12, 'dr. Salsabila Tiara', 'Poli Umum', '', '00:00:00', '00:00:00', 'aktif', '2026-05-25 22:01:04', 'Senin–Jumat (10.00–15.00)'),
(15, 'dr. Yaraa', 'Poli Umum', '', '00:00:00', '00:00:00', 'aktif', '2026-05-26 08:42:12', 'Senin–Jumat (10.00–15.00)');

-- --------------------------------------------------------

--
-- Table structure for table `konten`
--

CREATE TABLE `konten` (
  `id` int(11) NOT NULL,
  `kunci` varchar(100) NOT NULL,
  `nilai` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `judul_konten` varchar(255) NOT NULL,
  `isi_konten` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konten`
--

INSERT INTO `konten` (`id`, `kunci`, `nilai`, `updated_at`, `judul_konten`, `isi_konten`) VALUES
(1, 'hero_judul', 'Selamat Datang di SIMKES', '2026-05-25 19:19:30', '', 'test'),
(2, 'hero_subjudul', 'Melayani kesehatan masyarakat Desa Krapyak dengan sepenuh hati.', '2026-05-25 19:19:44', '', 'hehe'),
(3, 'tentang_judul', 'Tentang SIMKES Krapyak', '2026-05-16 02:30:51', '', ''),
(4, 'tentang_isi', 'SIMKES Krapyak adalah sistem informasi kesehatan yang melayani masyarakat Desa Krapyak. Kami berkomitmen memberikan pelayanan kesehatan terbaik dan terjangkau.', '2026-05-16 02:30:51', '', ''),
(5, 'alamat', 'Desa Krapyak, Kecamatan Tahunan, Jepara', '2026-05-16 02:30:51', '', ''),
(6, 'no_telp', '(0291) 123456', '2026-05-16 02:30:51', '', ''),
(7, 'jam_operasional', 'Senin - Jumat: 08.00 - 14.00 WIB', '2026-05-16 02:30:51', '', ''),
(8, '', '', '2026-05-25 19:17:14', 'Hero', 'Melayani kesehatan masyarakat Desa Krapyak dengan sepenuh hati.'),
(11, '-entang-ami', 'Tentang Kami', '2026-05-25 19:18:59', 'Tentang Kami', 'SIMKES Desa Krapyak hadir sebagai wujud cinta dan kepedulian kami terhadap kesehatan seluruh lapisan masyarakat. Kami percaya bahwa pelayanan kesehatan yang berkualitas harus bisa diakses dengan mudah, nyaman, dan cepat oleh siapa pun.\\n\\nMelalui sistem ini, kami berupaya menghapus jarak dan waktu antrean yang panjang. Kini, warga Krapyak dapat merencanakan kunjungan kesehatan keluarga hanya dalam beberapa klik dari rumah, sehingga waktu berharga Anda bisa lebih banyak dihabiskan bersama orang tercinta.'),
(12, '-ayanan', 'Layanan', '2026-05-25 19:18:59', 'Layanan', 'Pelayanan kesehatan Desa Krapyak tersedia untuk semua layanan utama yang Anda butuhkan.'),
(13, '-ooking', 'Booking', '2026-05-25 19:18:59', 'Booking', 'Tak perlu antre lama, cukup pesan dari rumah untuk kenyamanan bersama.'),
(14, '-ontak', 'Kontak', '2026-05-25 19:18:59', 'Kontak', 'Kami siap membantu kebutuhan kesehatan Anda.');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id_periksa` int(11) NOT NULL,
  `id_booking` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `obat` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesan`
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL DEFAULT '',
  `pesan` text NOT NULL,
  `waktu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesan`
--

INSERT INTO `pesan` (`id`, `nama`, `no_hp`, `pesan`, `waktu`) VALUES
(7, 'Tiara', '088888225949', 'coba1', '2026-05-26 04:31:18'),
(8, 'Tiaraaa', '088888225949', 'coba2', '2026-05-26 04:31:54'),
(9, 'tir', '08888225949', 'nyoba', '2026-05-26 04:36:40');

-- --------------------------------------------------------

--
-- Table structure for table `poli_info`
--

CREATE TABLE `poli_info` (
  `id` int(11) NOT NULL,
  `nama_poli` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `layanan_1` varchar(255) DEFAULT NULL,
  `layanan_2` varchar(255) DEFAULT NULL,
  `layanan_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poli_info`
--

INSERT INTO `poli_info` (`id`, `nama_poli`, `deskripsi`, `layanan_1`, `layanan_2`, `layanan_3`) VALUES
(1, 'Poli Umum', 'Layanan kesehatan primer yang tulus untuk seluruh warga Krapyak, mulai dari pemeriksaan fisik hingga konsultasi keluhan harian.', 'Pemeriksaan Fisik: Tensi, suhu tubuh, dan konsultasi kesehatan harian.', 'Pengobatan Umum: Penanganan flu, batuk, demam, dan infeksi ringan.', 'Surat Keterangan: Layanan pembuatan surat keterangan sehat untuk berbagai keperluan.'),
(2, 'Poli Gigi', 'Wujudkan senyum cerah bersama Poli Gigi SIMKES. Kami hadir dengan peralatan steril dan penanganan lembut untuk meminimalisir rasa takut.', 'Pembersihan Karang: Scaling rutin untuk menjaga kesegaran mulut dan gusi.', 'Penambalan Gigi: Solusi estetis dan fungsional untuk gigi berlubang.', 'Edukasi Gigi Anak: Membiasakan sikat gigi dengan cara yang seru dan menyenangkan.'),
(3, 'KIA', 'Pelayanan khusus yang penuh kasih sayang bagi ibu hamil dan balita untuk memastikan tumbuh kembang buah hati terpantau sempurna.', 'Pemeriksaan Kehamilan: Pemantauan rutin (ANC) untuk kesehatan ibu dan janin.', 'Imunisasi Dasar: Layanan vaksinasi lengkap untuk memperkuat imun si kecil.', 'Konsultasi KB: Perencanaan keluarga yang aman, nyaman, dan teredukasi.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','dokter','pasien') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `nik`, `username`, `password`, `role`) VALUES
(1, 'Petugas SIMKES', '-', 'admin', '123', 'admin'),
(2, 'Dr Andi', '-', 'dokter1', 'cab2d8232139ee4f469a920732578f71', 'dokter'),
(3, 'Nayla Rahma', '320xxxxxxxxx', '320xxxxxxxxx', '827ccb0eea8a706c4c34a16891f84e7b', 'pasien');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indexes for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `konten`
--
ALTER TABLE `konten`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunci` (`kunci`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id_periksa`);

--
-- Indexes for table `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poli_info`
--
ALTER TABLE `poli_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_poli` (`nama_poli`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `konten`
--
ALTER TABLE `konten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id_periksa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `poli_info`
--
ALTER TABLE `poli_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
