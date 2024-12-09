-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 07:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_buah`
--

-- --------------------------------------------------------

--
-- Table structure for table `keluhan`
--

CREATE TABLE `keluhan` (
  `id` int(6) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `masalah` text NOT NULL,
  `masukan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keluhan`
--

INSERT INTO `keluhan` (`id`, `nama`, `masalah`, `masukan`, `tanggal`) VALUES
(1, 'Eka', 'a', 'a', '2024-12-04 14:05:37'),
(2, 'Eka', 'a', 'a', '2024-12-04 14:06:24'),
(3, 'Eka', 'a', 'a', '2024-12-04 14:09:13'),
(4, 'Eka', 'a', 'a', '2024-12-04 14:11:47'),
(5, 'SELARA WARUWU', 'Buah terlalu matang', 'Lebih diperhatikan lagi untuk kesegaran buah', '2024-12-05 06:56:50'),
(6, 'Nia', 'Buah ada yang kurang segar', 'lsin kali lebih di perhatikan lagi', '2024-12-05 07:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL,
  `total_price` float NOT NULL,
  `payment_status` varchar(50) DEFAULT 'Belum Dibayar',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  `user_phone` varchar(15) NOT NULL,
  `customer_username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_address`, `product_name`, `quantity`, `price`, `total_price`, `payment_status`, `order_date`, `payment_proof`, `user_phone`, `customer_username`) VALUES
(1, 'Denis', 'Tanjung Karang', 'Apel', 6, 15000, 90000, 'Belum Dibayar', '2024-12-01 17:06:12', NULL, '', ''),
(5, 'Arya', 'Danau', 'Pisang', 3, 10000, 30000, 'Sudah Bayar', '2024-12-01 17:17:06', 'uploads/red-dragon-fruit-1973815_1920.jpg', '', ''),
(7, 'Denis', 'Danau', 'Apel', 5, 15000, 75000, 'Belum Dibayar', '2024-12-03 05:00:42', 'uploads/Jingga Pamflet Paket Hemat Restoran Makanan (1).png', '0854132', 'Arya'),
(8, 'Denis', 'Danau', 'Apel', 5, 15000, 75000, 'Belum Dibayar', '2024-12-03 05:01:08', 'uploads/Jingga Pamflet Paket Hemat Restoran Makanan (1).png', '0854132', 'Arya'),
(9, 'Denis', 'Danau', 'Apel', 5, 15000, 75000, 'Belum Dibayar', '2024-12-03 05:02:19', 'uploads/Jingga Pamflet Paket Hemat Restoran Makanan (1).png', '0854132', 'Arya'),
(10, 'selaaa', 'balam', 'Pisang', 1, 10000, 10000, 'Belum Dibayar', '2024-12-05 06:54:11', 'uploads/beranda 2.png', '08824737', 'Selara'),
(11, 'selaaa', 'balam', 'Pisang', 1, 10000, 10000, 'Belum Dibayar', '2024-12-05 06:55:32', 'uploads/beranda 2.png', '08824737', 'Selara'),
(12, 'Nia', 'balam', 'Apel', 3, 15000, 45000, 'Belum Dibayar', '2024-12-05 07:13:17', 'uploads/manggaa.jpg', '087467', 'Selara'),
(13, 'Nia', 'balam', 'Apel', 3, 15000, 45000, 'Belum Dibayar', '2024-12-05 07:13:33', 'uploads/manggaa.jpg', '087467', 'Selara');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `stock`, `price`, `image`) VALUES
(2, 'Pisang', 25, 10000.00, 'https://cdn.pixabay.com/photo/2016/09/03/20/48/bananas-1642706_1280.jpg'),
(3, 'Jeruk', 37, 20000.00, 'https://cdn.pixabay.com/photo/2016/10/07/14/11/tangerines-1721633_1280.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_phone` varchar(15) DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `shipped_date` datetime DEFAULT current_timestamp(),
  `status` enum('In Transit','Delivered') DEFAULT 'In Transit',
  `shipping_status` varchar(255) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `order_id`, `customer_name`, `customer_address`, `product_name`, `quantity`, `total_price`, `user_phone`, `order_date`, `shipped_date`, `status`, `shipping_status`) VALUES
(1, 6, 'Arya', 'Danau', 'Jeruk', 3, 60000.00, '0854132', '2024-12-02 00:37:38', '2024-12-02 21:58:56', 'In Transit', 'Dikirim');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'kelompok2', 'a47720763c353c387ad738bd8c0cacee', 'admin'),
(2, 'Arya', '202cb962ac59075b964b07152d234b70', 'user'),
(3, 'Selara', '250cf8b51c773f3f8dc8b4be867a9a02', 'user'),
(4, 'Melva', '68053af2923e00204c3ca7c6a3150cf7', 'user'),
(5, 'Orang', 'df6d2338b2b8fce1ec2f6dda0a630eb0', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
