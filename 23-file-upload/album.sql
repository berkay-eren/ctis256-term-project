-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 25 Nis 2025, 14:23:03
-- Sunucu sürümü: 9.1.0
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `test`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `album`
--

DROP TABLE IF EXISTS `album`;
CREATE TABLE IF NOT EXISTS `album` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
  `filename` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
  `tags` varchar(200) COLLATE utf8mb4_turkish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `album`
--

INSERT INTO `album` (`id`, `original`, `filename`, `tags`, `created_at`) VALUES
(16, 'yellow-flowers-in-spring.png', '8c41f110884e2bc3.png', 'yellow flower tulip', '2025-04-25 14:19:26'),
(17, 'waterlilies.jpg', '36d80d2621760b1b.jpg', 'waterlily lotus yellow water', '2025-04-25 14:20:44'),
(15, 'sun-flowers.jpg', '91e921157c403781.jpg', 'sunflower flower yellow', '2025-04-25 14:18:58'),
(14, 'nature.jpg', '603e14dad3610ea5.jpg', 'nature spring green', '2025-04-25 14:17:50');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
