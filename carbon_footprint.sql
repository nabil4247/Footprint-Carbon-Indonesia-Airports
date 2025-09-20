-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Sep 2025 pada 10.59
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carbon_footprint`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `airports`
--

CREATE TABLE `airports` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `iata_code` varchar(3) NOT NULL,
  `city` varchar(50) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `airports`
--

INSERT INTO `airports` (`id`, `name`, `iata_code`, `city`, `latitude`, `longitude`) VALUES
(198, 'Bandar Udara Internasional Syamsudin Noor', 'BDJ', 'Banjarmasin', '-3.44255000', '114.76239000'),
(199, 'Bandar Udara Tjilik Riwut', 'PKY', 'Palangkaraya', '-2.20156100', '113.91405500'),
(200, 'Bandar Udara Internasional El Tari', 'KOE', 'Kupang', '-10.18300000', '123.66250000'),
(201, 'Bandar Udara Internasional Halim Perdanakusuma', 'HLP', 'Jakarta', '-6.26666700', '106.89083300'),
(202, 'Bandar Udara Sultan Aji Muhammad Sulaiman Sepinggan', 'BPN', 'Balikpapan', '-1.26089600', '116.90074200'),
(203, 'Bandar Udara Sultan Thaha', 'DJB', 'Jambi', '-1.63707700', '103.64378400'),
(204, 'Bandar Udara Abdul Rachman Saleh', 'MLG', 'Malang', '-7.92750000', '112.71472200'),
(205, 'Bandar Udara Achmad Yani', 'SRG', 'Semarang', '-6.97222200', '110.37444400'),
(206, 'Bandar Udara Adi Sumarmo Wiryokusumo', 'SOC', 'Surakarta', '-7.51416700', '110.76861100'),
(207, 'Bandar Udara Internasional Lombok', 'LOP', 'Lombok', '-8.74805600', '116.03111100'),
(208, 'Bandar Udara Radin Inten II', 'TKG', 'Bandar Lampung', '-5.24233900', '105.17893900'),
(209, 'Bandar Udara Husein Sastranegara', 'BDO', 'Bandung', '-6.90055600', '107.57500000'),
(210, 'Bandar Udara Sentani', 'DJJ', 'Jayapura', '-2.57695300', '140.51637200'),
(211, 'Bandar Udara Domine Eduard Osok', 'SOQ', 'Sorong', '-0.92611100', '131.12111100'),
(212, 'Bandar Udara Frans Kaisiepo', 'BIK', 'Biak', '-1.19000000', '136.10861100'),
(213, 'Bandar Udara Mopah', 'MKW', 'Merauke', '-8.52027800', '140.41833300'),
(214, 'Bandar Udara Tembagapura Timika (Mozes Kilangin)', 'TIM', 'Timika', '-4.52827500', '136.88737500'),
(215, 'Bandar Udara Pattimura', 'AMQ', 'Ambon', '-3.71026400', '128.08913600'),
(216, 'Bandar Udara Haluoleo', 'KDI', 'Kendari', '-4.08160800', '122.41739700'),
(217, 'Bandar Udara Matahora', 'WNI', 'Wakatobi', '-5.29319400', '123.63488900'),
(218, 'Bandar Udara Betoambari', 'BUW', 'Baubau', '-5.48687500', '122.56944400'),
(219, 'Bandar Udara Umbu Mehang Kunda', 'WGP', 'Waingapu', '-9.66921900', '120.30200600'),
(220, 'Bandar Udara Tambolaka', 'TMC', 'Sumba Barat Daya', '-9.40971700', '119.24473600'),
(221, 'Bandar Udara Mali', 'ARD', 'Alor', '-8.13233900', '124.59700000'),
(222, 'Bandar Udara Tardamu', 'SAU', 'Sawu', '-10.49305600', '121.85722200'),
(223, 'Bandar Udara Komodo', 'LBJ', 'Labuan Bajo', '-8.48666700', '119.88900000'),
(224, 'Bandar Udara Sultan Babullah', 'TTE', 'Ternate', '0.83138900', '127.38000000'),
(225, 'Bandar Udara Naha', 'NAH', 'Tahuna', '3.68321400', '125.52700000'),
(226, 'Bandar Udara Dewadaru', 'KWB', 'Karimunjawa', '-5.80083300', '110.47888900'),
(227, 'Bandar Udara Long Apung', 'LPU', 'Long Apung', '1.70444400', '114.97083300'),
(228, 'Bandar Udara Long Bawan', 'LBW', 'Nunukan', '3.90277800', '115.69222200'),
(229, 'Bandar Udara Nunukan', 'NNX', 'Nunukan', '4.13333300', '117.66666700'),
(230, 'Bandar Udara Datah Dawai', 'DTD', 'Kutai Barat', '0.71722200', '114.97916700'),
(231, 'Bandar Udara Temindung', 'SRI', 'Samarinda', '-0.48416700', '117.15750000'),
(232, 'Bandar Udara APT Pranoto', 'AAP', 'Samarinda', '-0.48416700', '117.15750000'),
(233, 'Bandar Udara Malinau (Robert Atty Bessing)', 'LNU', 'Malinau', '3.68300000', '116.61600000'),
(234, 'Bandar Udara Tanjung Harapan', 'TJS', 'Tanjung Selor', '2.83500000', '117.37300000'),
(235, 'Bandar Udara Pangsuma', 'PSU', 'Putussibau', '0.83500000', '112.93700000'),
(236, 'Bandar Udara Susilo', 'SQG', 'Sintang', '0.06361100', '111.47333300'),
(237, 'Bandar Udara Rahadi Oesman', 'KTG', 'Ketapang', '-1.81666700', '109.96666700'),
(238, 'Bandar Udara Internasional Soekarno-Hatta', 'CGK', 'Jakarta', '-6.12556700', '106.65589700'),
(239, 'Bandar Udara Internasional Ngurah Rai', 'DPS', 'Denpasar', '-8.74816900', '115.16717200'),
(240, 'Bandar Udara Internasional Juanda', 'SUB', 'Surabaya', '-7.37983100', '112.78685800'),
(241, 'Bandar Udara Internasional Kuala Namu', 'KNO', 'Medan', '3.64222200', '98.88527800'),
(242, 'Bandar Udara Sultan Hasanuddin', 'UPG', 'Makassar', '-5.06163100', '119.55404200'),
(243, 'Bandar Udara Internasional Minangkabau', 'PDG', 'Padang', '-0.87498900', '100.35188100'),
(244, 'Bandar Udara Adisutjipto', 'JOG', 'Yogyakarta', '-7.78818100', '110.43175800'),
(245, 'Bandar Udara Hang Nadim', 'BTH', 'Batam', '1.12102800', '104.11875300'),
(246, 'Bandar Udara Supadio', 'PNK', 'Pontianak', '-0.15071100', '109.40389200'),
(247, 'Bandar Udara Sam Ratulangi', 'MDC', 'Manado', '1.54944700', '124.92587800'),
(323, 'Kalimarau Airport', 'BEJ', 'Tanjung Redeb', '-1.94250000', '117.79250000'),
(324, 'Iskandar Airport', 'PKN', 'Pangkalan Bun', '-2.70305600', '111.66944400'),
(325, 'Singkawang Airport', 'SKJ', 'Singkawang', '-0.79000000', '108.94500000'),
(326, 'Stevanus Rumbewas Airport', 'ZRI', 'Serui', '-1.70500000', '136.66100000'),
(327, 'Douw Aturure Airport', 'NBX', 'Nabire', '-3.35000000', '135.52500000'),
(328, 'Bandar Udara Bersujud (Batulicin)', 'BTW', 'Batulicin', '-3.41222000', '115.99527800'),
(329, 'Bandar Udara Utarom (Kaimana)', 'KNG', 'Kaimana', '-3.64451700', '133.69556300'),
(334, 'Bandar Udara H. Asan', 'SMQ', 'Sampit', '-2.50194500', '112.97500000'),
(335, 'Bandar Udara Syukuran Aminuddin Amir', 'LUW', 'Luwuk', '-1.03888900', '122.77250000'),
(336, 'Bandar Udara Mutiara SIS Al-Jufrie', 'PLW', 'Palu', '-0.91833300', '119.91000000');

-- --------------------------------------------------------

--
-- Struktur dari tabel `flight_logs`
--

CREATE TABLE `flight_logs` (
  `id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `departure_code` varchar(3) NOT NULL,
  `arrival_code` varchar(3) NOT NULL,
  `flight_date` date NOT NULL,
  `flight_class` enum('economy','premium','business','first') NOT NULL DEFAULT 'economy',
  `passengers` int(11) NOT NULL DEFAULT 1,
  `distance` decimal(10,2) NOT NULL,
  `carbon` decimal(10,2) NOT NULL,
  `total_carbon` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `flight_logs`
--

INSERT INTO `flight_logs` (`id`, `passenger_id`, `departure_code`, `arrival_code`, `flight_date`, `flight_class`, `passengers`, `distance`, `carbon`, `total_carbon`, `created_at`) VALUES
(1, 1, 'BDJ', 'CGK', '2025-09-19', 'economy', 1, '946.00', '80.45', '80.45', '2025-09-19 09:31:42'),
(2, 1, 'BTW', 'BDJ', '2025-09-19', 'economy', 1, '137.00', '11.64', '11.64', '2025-09-19 09:32:55'),
(3, 1, 'KNG', 'SUB', '2025-09-13', 'economy', 1, '2351.00', '199.80', '199.80', '2025-09-19 09:33:42'),
(4, 1, 'SUB', 'BDJ', '2025-09-14', 'economy', 1, '489.00', '41.60', '41.60', '2025-09-19 09:34:12'),
(5, 4, 'PKN', 'CGK', '2025-09-17', 'economy', 1, '674.00', '57.25', '57.25', '2025-09-19 09:45:25'),
(7, 3, 'PLW', 'CGK', '2025-09-16', 'economy', 1, '1580.00', '134.33', '134.33', '2025-09-19 09:49:28'),
(8, 3, 'PLW', 'CGK', '2025-09-16', 'economy', 1, '1580.00', '134.33', '134.33', '2025-09-19 09:49:28'),
(9, 2, 'CGK', 'KNO', '2025-08-05', 'economy', 1, '1387.00', '117.91', '117.91', '2025-09-20 08:45:16'),
(10, 2, 'CGK', 'KNO', '2025-08-05', 'economy', 1, '1387.00', '117.91', '117.91', '2025-09-20 08:45:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `passengers`
--

INSERT INTO `passengers` (`id`, `name`) VALUES
(1, 'Nabil'),
(2, 'Revan'),
(3, 'Rahman'),
(4, 'Wildan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iata_code` (`iata_code`);

--
-- Indeks untuk tabel `flight_logs`
--
ALTER TABLE `flight_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passenger_id` (`passenger_id`);

--
-- Indeks untuk tabel `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `airports`
--
ALTER TABLE `airports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT untuk tabel `flight_logs`
--
ALTER TABLE `flight_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `flight_logs`
--
ALTER TABLE `flight_logs`
  ADD CONSTRAINT `flight_logs_ibfk_1` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
