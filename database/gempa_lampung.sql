-- Tabel untuk menyimpan history gempa di Lampung dan sekitarnya
CREATE TABLE IF NOT EXISTS `gempa_lampung_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` varchar(50) NOT NULL,
  `jam` varchar(20) NOT NULL,
  `datetime` datetime NOT NULL,
  `coordinates` varchar(50) NOT NULL,
  `lintang` decimal(10, 6) DEFAULT NULL,
  `bujur` decimal(10, 6) DEFAULT NULL,
  `magnitude` decimal(3, 1) NOT NULL,
  `kedalaman` varchar(20) NOT NULL,
  `wilayah` text NOT NULL,
  `potensi` text DEFAULT NULL,
  `shakemap` varchar(255) DEFAULT NULL,
  `is_lampung` tinyint(1) DEFAULT 0,
  `is_nearby` tinyint(1) DEFAULT 0,
  `distance_from_lampung` decimal(10, 2) DEFAULT NULL COMMENT 'Jarak dari Lampung dalam KM',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_datetime` (`datetime`),
  KEY `idx_magnitude` (`magnitude`),
  KEY `idx_is_lampung` (`is_lampung`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index untuk performa query
CREATE INDEX idx_wilayah ON gempa_lampung_history(wilayah(100));
CREATE INDEX idx_tanggal_jam ON gempa_lampung_history(tanggal, jam);
