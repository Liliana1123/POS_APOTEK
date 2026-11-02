-- =============================================================================
-- DATABASE SIAP PAKAI: POS APOTEK (UNTUK CLIENT)
-- =============================================================================
-- - Struktur tabel lengkap 100% (37 tabel)
-- - Master data: KOSONG (Obat, Kategori, Satuan, Supplier, Pabrik, Pelanggan = 0)
-- - Transaksi: KOSONG (Penjualan, Pembelian, Log = 0)
-- - Hak Akses: AKTIF (Tabel permissions & role_permissions terisi)
-- - Akun Default:
--   * superadmin@apotek.test / password
--   * admin@apotek.test / password
--   * kasir@apotek.test / password
-- =============================================================================
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: pos_apotek
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `kategori` varchar(255) DEFAULT 'sistem',
  `target` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_kategori_index` (`kategori`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barang_kode_sequences`
--

DROP TABLE IF EXISTS `barang_kode_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barang_kode_sequences` (
  `id` bigint(20) unsigned NOT NULL,
  `last_number` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barangs`
--

DROP TABLE IF EXISTS `barangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_apotek` varchar(50) DEFAULT NULL,
  `kode_kfa` varchar(100) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `kategori_id` bigint(20) unsigned NOT NULL,
  `satuan_id` bigint(20) unsigned NOT NULL,
  `pabrik_id` bigint(20) unsigned NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `butuh_resep` tinyint(1) NOT NULL DEFAULT 0,
  `stok_minimum` int(11) NOT NULL DEFAULT 0,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barangs_barcode_unique` (`barcode`),
  UNIQUE KEY `barangs_kode_apotek_unique` (`kode_apotek`),
  KEY `barangs_kategori_id_foreign` (`kategori_id`),
  KEY `barangs_satuan_id_foreign` (`satuan_id`),
  KEY `barangs_pabrik_id_foreign` (`pabrik_id`),
  CONSTRAINT `barangs_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`),
  CONSTRAINT `barangs_pabrik_id_foreign` FOREIGN KEY (`pabrik_id`) REFERENCES `pabriks` (`id`),
  CONSTRAINT `barangs_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bebans`
--

DROP TABLE IF EXISTS `bebans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bebans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bebans_user_id_foreign` (`user_id`),
  KEY `bebans_tanggal_index` (`tanggal`),
  KEY `bebans_kategori_index` (`kategori`),
  CONSTRAINT `bebans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_discount_barangs`
--

DROP TABLE IF EXISTS `custom_discount_barangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_discount_barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_discount_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_discount_barangs_custom_discount_id_barang_id_unique` (`custom_discount_id`,`barang_id`),
  KEY `custom_discount_barangs_barang_id_foreign` (`barang_id`),
  CONSTRAINT `custom_discount_barangs_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `custom_discount_barangs_custom_discount_id_foreign` FOREIGN KEY (`custom_discount_id`) REFERENCES `custom_discounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_discount_categories`
--

DROP TABLE IF EXISTS `custom_discount_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_discount_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_discount_id` bigint(20) unsigned NOT NULL,
  `kategori_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_discount_categories_custom_discount_id_kategori_id_unique` (`custom_discount_id`,`kategori_id`),
  KEY `custom_discount_categories_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `custom_discount_categories_custom_discount_id_foreign` FOREIGN KEY (`custom_discount_id`) REFERENCES `custom_discounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `custom_discount_categories_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_discounts`
--

DROP TABLE IF EXISTS `custom_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `persentase` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `cakupan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detail_penerimaans`
--

DROP TABLE IF EXISTS `detail_penerimaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_penerimaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penerimaan_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  `no_batch` varchar(255) NOT NULL,
  `harga_beli` decimal(12,2) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `expired_date` date NOT NULL,
  `no_rak` varchar(255) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_penerimaans_penerimaan_id_foreign` (`penerimaan_id`),
  KEY `detail_penerimaans_barang_id_expired_date_index` (`barang_id`,`expired_date`),
  CONSTRAINT `detail_penerimaans_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`),
  CONSTRAINT `detail_penerimaans_penerimaan_id_foreign` FOREIGN KEY (`penerimaan_id`) REFERENCES `penerimaans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detail_penjualans`
--

DROP TABLE IF EXISTS `detail_penjualans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_penjualans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint(20) unsigned NOT NULL,
  `detail_penerimaan_id` bigint(20) unsigned NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `diskon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_penjualans_penjualan_id_foreign` (`penjualan_id`),
  KEY `detail_penjualans_detail_penerimaan_id_foreign` (`detail_penerimaan_id`),
  CONSTRAINT `detail_penjualans_detail_penerimaan_id_foreign` FOREIGN KEY (`detail_penerimaan_id`) REFERENCES `detail_penerimaans` (`id`),
  CONSTRAINT `detail_penjualans_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detail_pesanan_penerimaans`
--

DROP TABLE IF EXISTS `detail_pesanan_penerimaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_pesanan_penerimaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penerimaan_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned NOT NULL,
  `jumlah_dipesan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `detail_pesanan_penerimaans_penerimaan_id_barang_id_unique` (`penerimaan_id`,`barang_id`),
  KEY `detail_pesanan_penerimaans_barang_id_foreign` (`barang_id`),
  CONSTRAINT `detail_pesanan_penerimaans_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`),
  CONSTRAINT `detail_pesanan_penerimaans_penerimaan_id_foreign` FOREIGN KEY (`penerimaan_id`) REFERENCES `penerimaans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discount_usages`
--

DROP TABLE IF EXISTS `discount_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discount_usages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint(20) unsigned NOT NULL,
  `detail_penjualan_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) unsigned DEFAULT NULL,
  `barang_nama` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `custom_discount_id` bigint(20) unsigned DEFAULT NULL,
  `custom_discount_nama` varchar(255) DEFAULT NULL,
  `persentase` int(11) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discount_usages_penjualan_id_foreign` (`penjualan_id`),
  KEY `discount_usages_detail_penjualan_id_foreign` (`detail_penjualan_id`),
  KEY `discount_usages_barang_id_foreign` (`barang_id`),
  KEY `discount_usages_custom_discount_id_foreign` (`custom_discount_id`),
  CONSTRAINT `discount_usages_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `discount_usages_custom_discount_id_foreign` FOREIGN KEY (`custom_discount_id`) REFERENCES `custom_discounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `discount_usages_detail_penjualan_id_foreign` FOREIGN KEY (`detail_penjualan_id`) REFERENCES `detail_penjualans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discount_usages_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `info_apoteks`
--

DROP TABLE IF EXISTS `info_apoteks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info_apoteks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_apotek` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `alamat_jalan` varchar(255) DEFAULT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_izin_sia` varchar(255) DEFAULT NULL,
  `nama_apoteker_pj` varchar(255) DEFAULT NULL,
  `no_sipa` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `background_type` varchar(255) NOT NULL DEFAULT 'default',
  `background_image` varchar(255) DEFAULT NULL,
  `background_opacity` tinyint(3) unsigned NOT NULL DEFAULT 40,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kategoris`
--

DROP TABLE IF EXISTS `kategoris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategoris` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pabriks`
--

DROP TABLE IF EXISTS `pabriks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pabriks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pelanggans`
--

DROP TABLE IF EXISTS `pelanggans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pelanggans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status_member` varchar(255) DEFAULT NULL,
  `custom_discount_percentage` decimal(5,2) DEFAULT NULL,
  `saldo_piutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_member` tinyint(1) NOT NULL DEFAULT 0,
  `member_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `member_since` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pelanggans_member_id_unique` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pembayaran_penerimaans`
--

DROP TABLE IF EXISTS `pembayaran_penerimaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran_penerimaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penerimaan_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembayaran_penerimaans_penerimaan_id_foreign` (`penerimaan_id`),
  KEY `pembayaran_penerimaans_user_id_foreign` (`user_id`),
  CONSTRAINT `pembayaran_penerimaans_penerimaan_id_foreign` FOREIGN KEY (`penerimaan_id`) REFERENCES `penerimaans` (`id`),
  CONSTRAINT `pembayaran_penerimaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pembayaran_piutangs`
--

DROP TABLE IF EXISTS `pembayaran_piutangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran_piutangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint(20) unsigned NOT NULL,
  `pelanggan_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `metode_pembayaran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembayaran_piutangs_penjualan_id_foreign` (`penjualan_id`),
  KEY `pembayaran_piutangs_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `pembayaran_piutangs_user_id_foreign` (`user_id`),
  CONSTRAINT `pembayaran_piutangs_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_piutangs_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_piutangs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `penerimaans`
--

DROP TABLE IF EXISTS `penerimaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penerimaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `telepon_supplier` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `no_faktur` varchar(255) NOT NULL,
  `lunas` tinyint(1) NOT NULL DEFAULT 0,
  `jatuh_tempo` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tanggal_faktur` date DEFAULT NULL,
  `ppn` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penerimaans_no_faktur_unique` (`no_faktur`),
  KEY `penerimaans_user_id_foreign` (`user_id`),
  KEY `penerimaans_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `penerimaans_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `penerimaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `penjualans`
--

DROP TABLE IF EXISTS `penjualans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penjualans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `pelanggan_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `no_faktur` varchar(255) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` varchar(255) NOT NULL DEFAULT 'cash',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jenis_transaksi` varchar(255) NOT NULL DEFAULT 'non_resep',
  `nama_dokter` varchar(255) DEFAULT NULL,
  `id_dokter` varchar(255) DEFAULT NULL,
  `alamat_lembaga` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penjualans_no_faktur_unique` (`no_faktur`),
  KEY `penjualans_user_id_foreign` (`user_id`),
  KEY `penjualans_pelanggan_id_foreign` (`pelanggan_id`),
  CONSTRAINT `penjualans_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggans` (`id`),
  CONSTRAINT `penjualans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(60) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'page',
  `label` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group` varchar(30) NOT NULL,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `riwayat_penerimaans`
--

DROP TABLE IF EXISTS `riwayat_penerimaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riwayat_penerimaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penerimaan_id` bigint(20) unsigned NOT NULL,
  `detail_pesanan_penerimaan_id` bigint(20) unsigned NOT NULL,
  `detail_penerimaan_id` bigint(20) unsigned DEFAULT NULL,
  `jenis` varchar(20) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_penerimaans_penerimaan_id_foreign` (`penerimaan_id`),
  KEY `riwayat_penerimaans_detail_pesanan_penerimaan_id_foreign` (`detail_pesanan_penerimaan_id`),
  KEY `riwayat_penerimaans_detail_penerimaan_id_foreign` (`detail_penerimaan_id`),
  KEY `riwayat_penerimaans_user_id_foreign` (`user_id`),
  CONSTRAINT `riwayat_penerimaans_detail_penerimaan_id_foreign` FOREIGN KEY (`detail_penerimaan_id`) REFERENCES `detail_penerimaans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_penerimaans_detail_pesanan_penerimaan_id_foreign` FOREIGN KEY (`detail_pesanan_penerimaan_id`) REFERENCES `detail_pesanan_penerimaans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_penerimaans_penerimaan_id_foreign` FOREIGN KEY (`penerimaan_id`) REFERENCES `penerimaans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_penerimaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(30) NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT 0,
  `manage` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_permission_id_unique` (`role`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2495 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rusaks`
--

DROP TABLE IF EXISTS `rusaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rusaks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `detail_penerimaan_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rusaks_detail_penerimaan_id_foreign` (`detail_penerimaan_id`),
  CONSTRAINT `rusaks_detail_penerimaan_id_foreign` FOREIGN KEY (`detail_penerimaan_id`) REFERENCES `detail_penerimaans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `satuans`
--

DROP TABLE IF EXISTS `satuans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `satuans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'kasir',
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_follow_up_histories`
--

DROP TABLE IF EXISTS `whatsapp_follow_up_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_follow_up_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `whatsapp_log_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `status_sebelum` varchar(50) DEFAULT NULL,
  `status_sesudah` varchar(50) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_follow_up_histories_user_id_foreign` (`user_id`),
  KEY `whatsapp_follow_up_histories_whatsapp_log_id_index` (`whatsapp_log_id`),
  KEY `whatsapp_follow_up_histories_created_at_index` (`created_at`),
  CONSTRAINT `whatsapp_follow_up_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_follow_up_histories_whatsapp_log_id_foreign` FOREIGN KEY (`whatsapp_log_id`) REFERENCES `whatsapp_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_logs`
--

DROP TABLE IF EXISTS `whatsapp_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pelanggan_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `penjualan_id` bigint(20) unsigned DEFAULT NULL,
  `nama_member` varchar(255) NOT NULL,
  `nomor_whatsapp` varchar(30) NOT NULL,
  `kategori` varchar(50) NOT NULL DEFAULT 'pesan_custom',
  `nama_template` varchar(255) DEFAULT NULL,
  `isi_pesan` text NOT NULL,
  `status` enum('terkirim','menunggu','gagal','dibatalkan') NOT NULL DEFAULT 'terkirim',
  `status_follow_up` varchar(50) NOT NULL DEFAULT 'belum_ditindaklanjuti',
  `catatan_follow_up` text DEFAULT NULL,
  `waktu_follow_up` datetime DEFAULT NULL,
  `follow_up_user_id` bigint(20) unsigned DEFAULT NULL,
  `waktu_kirim` datetime NOT NULL,
  `waktu_respon` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `faktur_info` varchar(255) DEFAULT NULL,
  `total_tagihan` decimal(15,2) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_logs_penjualan_id_foreign` (`penjualan_id`),
  KEY `whatsapp_logs_pelanggan_id_index` (`pelanggan_id`),
  KEY `whatsapp_logs_user_id_index` (`user_id`),
  KEY `whatsapp_logs_kategori_index` (`kategori`),
  KEY `whatsapp_logs_status_index` (`status`),
  KEY `whatsapp_logs_waktu_kirim_index` (`waktu_kirim`),
  KEY `whatsapp_logs_follow_up_user_id_foreign` (`follow_up_user_id`),
  KEY `whatsapp_logs_status_follow_up_index` (`status_follow_up`),
  KEY `whatsapp_logs_waktu_follow_up_index` (`waktu_follow_up`),
  CONSTRAINT `whatsapp_logs_follow_up_user_id_foreign` FOREIGN KEY (`follow_up_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_logs_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_logs_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_templates`
--

DROP TABLE IF EXISTS `whatsapp_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `konten` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'pos_apotek'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-01 21:57:23


-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: pos_apotek
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'master.barang','page','Barang / Produk',NULL,'Data Master',1,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(2,'master.kategori','page','Kategori',NULL,'Data Master',2,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(3,'master.satuan','page','Satuan',NULL,'Data Master',3,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(4,'master.pabrik','page','Pabrik',NULL,'Data Master',4,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(5,'master.supplier','page','Supplier',NULL,'Data Master',5,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(6,'master.pelanggan','page','Pelanggan / Member',NULL,'Data Master',6,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(7,'master.custom-discount','page','Custom Discount',NULL,'Promo & Log',18,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(8,'transaksi.penjualan','page','Penjualan (Kasir)',NULL,'Transaksi',9,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(9,'transaksi.penerimaan','page','Penerimaan Barang',NULL,'Transaksi',10,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(10,'transaksi.rusak','page','Barang Rusak',NULL,'Transaksi',11,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(11,'laporan.stok','page','Laporan Stok',NULL,'Laporan',12,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(12,'laporan.penerimaan','page','Laporan Penerimaan',NULL,'Laporan',13,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(13,'laporan.penjualan','page','Laporan Penjualan',NULL,'Laporan',14,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(14,'laporan.rusak','page','Laporan Barang Rusak',NULL,'Laporan',15,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(15,'laporan.laba-rugi','page','Laporan Laba Rugi',NULL,'Laporan',16,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(16,'laporan.diskon','page','Laporan Diskon',NULL,'Laporan',17,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(17,'sistem.kelola-user','page','Kelola User',NULL,'Data Master',7,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(18,'sistem.pengaturan','page','Pengaturan Apotek',NULL,'Data Master',8,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(19,'sistem.activity-log','page','Log Aktivitas',NULL,'Promo & Log',19,'2026-09-30 01:12:26','2026-10-25 12:41:39'),(22,'dashboard.nilai-persediaan','dashboard','Valuasi Nilai Modal Persediaan','Menampilkan estimasi total nilai modal/valuasi rupiah dari persediaan barang di apotek.','Dashboard: Stok & Barang',36,'2026-10-21 12:49:31','2026-10-25 12:41:39'),(24,'dashboard.kinerja-karyawan','dashboard','Tabel Kinerja & Ranking Karyawan','Menampilkan tabel peringkat capaian transaksi dan total penjualan seluruh pegawai apotek.','Dashboard: Obat & Karyawan',41,'2026-10-21 12:49:31','2026-10-25 12:41:39'),(25,'dashboard.omzet-obat','dashboard','Nominal Omzet (Rp) pada Tabel Obat','Menampilkan kolom nominal rupiah omzet pada tabel obat terlaris & paling sedikit terjual.','Dashboard: Obat & Karyawan',40,'2026-10-21 12:49:31','2026-10-25 12:41:39'),(26,'dashboard.kpi-omzet-kotor','page','Kartu KPI: Omzet Kotor',NULL,'Dashboard: KPI',20,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(27,'dashboard.kpi-total-diskon','page','Kartu KPI: Total Diskon',NULL,'Dashboard: KPI',21,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(28,'dashboard.kpi-omzet-bersih','page','Kartu KPI: Omzet Bersih',NULL,'Dashboard: KPI',22,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(29,'dashboard.kpi-total-member','page','Kartu KPI: Total Member',NULL,'Dashboard: KPI',23,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(30,'dashboard.kpi-hutang-supplier','page','Kartu KPI: Hutang Supplier',NULL,'Dashboard: KPI',24,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(31,'dashboard.kpi-piutang-member','page','Kartu KPI: Piutang Member',NULL,'Dashboard: KPI',25,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(32,'dashboard.grafik-omzet','page','Grafik Tren Penjualan & Omzet',NULL,'Dashboard: Grafik & Analisis',26,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(33,'dashboard.grafik-profit','page','Gross Profit & Margin pada Grafik',NULL,'Dashboard: Grafik & Analisis',27,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(34,'dashboard.kondisi-bisnis','page','Widget Kondisi Bisnis (Business Health Score)',NULL,'Dashboard: Grafik & Analisis',28,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(36,'dashboard.pemantauan-stok','page','Pemantauan Fisik Stok & Expiry Health',NULL,'Dashboard: Stok & Barang',35,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(37,'dashboard.barang-harus-dibeli','page','Tabel Barang yang Harus Dibeli (Reorder)',NULL,'Dashboard: Stok & Barang',37,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(38,'dashboard.obat-terlaris','page','Tabel 10 Obat Terlaris',NULL,'Dashboard: Obat & Karyawan',38,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(39,'dashboard.obat-terendah','page','Tabel 10 Obat Paling Sedikit Terjual',NULL,'Dashboard: Obat & Karyawan',39,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(40,'dashboard.dokter-resep','page','Tabel Dokter Penyumbang Resep',NULL,'Dashboard: Obat & Karyawan',42,'2026-10-25 12:23:15','2026-10-25 12:41:39'),(41,'dashboard.alert-stok-habis','page','Alert Tindak Lanjut: Stok Habis (Kritis)',NULL,'Dashboard: Tindak Lanjut',29,'2026-10-25 12:41:39','2026-10-25 12:41:39'),(42,'dashboard.alert-stok-menipis','page','Alert Tindak Lanjut: Stok Menipis (Perhatian)',NULL,'Dashboard: Tindak Lanjut',30,'2026-10-25 12:41:39','2026-10-25 12:41:39'),(43,'dashboard.alert-expired','page','Alert Tindak Lanjut: Mendekati Expired < 30 Hari',NULL,'Dashboard: Tindak Lanjut',31,'2026-10-25 12:41:39','2026-10-25 12:41:39'),(44,'dashboard.alert-hutang-tempo','page','Alert Tindak Lanjut: Hutang Supplier Jatuh Tempo',NULL,'Dashboard: Tindak Lanjut',32,'2026-10-25 12:41:39','2026-10-25 12:41:39'),(45,'dashboard.alert-piutang-member','page','Alert Tindak Lanjut: Piutang Member Terbuka',NULL,'Dashboard: Tindak Lanjut',33,'2026-10-25 12:41:39','2026-10-25 12:41:39'),(46,'dashboard.tombol-buka-kasir','page','Tombol Pintasan: Buka Kasir',NULL,'Dashboard: Tindak Lanjut',34,'2026-10-25 12:41:39','2026-10-25 12:41:39');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,'superadmin',1,1,1),(2,'superadmin',2,1,1),(3,'superadmin',3,1,1),(4,'superadmin',4,1,1),(5,'superadmin',5,1,1),(6,'superadmin',6,1,1),(7,'superadmin',7,1,1),(8,'superadmin',8,1,1),(9,'superadmin',9,1,1),(10,'superadmin',10,1,1),(11,'superadmin',11,1,1),(12,'superadmin',12,1,1),(13,'superadmin',13,1,1),(14,'superadmin',14,1,1),(15,'superadmin',15,1,1),(16,'superadmin',16,1,1),(17,'superadmin',17,1,1),(18,'superadmin',18,1,1),(19,'superadmin',19,1,1),(20,'admin',1,1,1),(21,'admin',2,1,1),(22,'admin',3,1,1),(23,'admin',4,1,1),(24,'admin',5,1,1),(25,'admin',6,1,1),(26,'admin',7,1,1),(27,'admin',8,1,1),(28,'admin',9,1,1),(29,'admin',10,1,1),(30,'admin',11,1,1),(31,'admin',12,1,1),(32,'admin',13,1,1),(33,'admin',14,1,1),(34,'admin',15,1,1),(35,'admin',16,1,1),(36,'admin',17,1,1),(37,'admin',18,1,1),(38,'admin',19,1,1),(39,'apoteker',1,1,0),(40,'apoteker',2,0,0),(41,'apoteker',3,0,0),(42,'apoteker',4,0,0),(43,'apoteker',5,0,0),(44,'apoteker',6,0,0),(45,'apoteker',7,0,0),(46,'apoteker',8,1,1),(47,'apoteker',9,0,0),(48,'apoteker',10,0,0),(49,'apoteker',11,0,0),(50,'apoteker',12,0,0),(51,'apoteker',13,0,0),(52,'apoteker',14,0,0),(53,'apoteker',15,0,0),(54,'apoteker',16,0,0),(55,'apoteker',17,0,0),(56,'apoteker',18,0,0),(57,'apoteker',19,0,0),(58,'kasir',1,0,0),(59,'kasir',2,0,0),(60,'kasir',3,0,0),(61,'kasir',4,0,0),(62,'kasir',5,0,0),(63,'kasir',6,0,0),(64,'kasir',7,0,0),(65,'kasir',8,1,1),(66,'kasir',9,0,0),(67,'kasir',10,0,0),(68,'kasir',11,0,0),(69,'kasir',12,0,0),(70,'kasir',13,0,0),(71,'kasir',14,0,0),(72,'kasir',15,0,0),(73,'kasir',16,0,0),(74,'kasir',17,0,0),(75,'kasir',18,0,0),(76,'kasir',19,0,0),(77,'gudang',16,0,0),(78,'gudang',15,0,0),(79,'gudang',12,1,0),(80,'gudang',13,0,0),(81,'gudang',14,1,0),(82,'gudang',11,1,0),(83,'gudang',1,1,1),(84,'gudang',7,0,0),(85,'gudang',2,1,0),(86,'gudang',4,1,0),(87,'gudang',6,0,0),(88,'gudang',3,1,0),(89,'gudang',5,1,1),(90,'gudang',19,0,0),(91,'gudang',17,0,0),(92,'gudang',18,0,0),(93,'gudang',9,1,1),(94,'gudang',8,0,0),(95,'gudang',10,1,1),(918,'superadmin',22,1,1),(920,'superadmin',24,1,1),(921,'superadmin',25,1,1),(924,'admin',22,1,0),(926,'admin',24,1,0),(927,'admin',25,1,0),(930,'apoteker',22,0,0),(932,'apoteker',24,0,0),(933,'apoteker',25,0,0),(936,'gudang',22,0,0),(938,'gudang',24,0,0),(939,'gudang',25,0,0),(942,'kasir',22,0,0),(944,'kasir',24,0,0),(945,'kasir',25,0,0),(1246,'superadmin',26,1,1),(1247,'superadmin',27,1,1),(1248,'superadmin',28,1,1),(1249,'superadmin',29,1,1),(1250,'superadmin',30,1,1),(1251,'superadmin',31,1,1),(1252,'superadmin',32,1,1),(1253,'superadmin',33,1,1),(1254,'superadmin',34,1,1),(1256,'superadmin',36,1,1),(1257,'superadmin',37,1,1),(1258,'superadmin',38,1,1),(1259,'superadmin',39,1,1),(1260,'superadmin',40,1,1),(1261,'admin',26,1,0),(1262,'admin',27,1,0),(1263,'admin',28,1,0),(1264,'admin',29,1,0),(1265,'admin',30,1,0),(1266,'admin',31,1,0),(1267,'admin',32,1,0),(1268,'admin',33,1,0),(1269,'admin',34,1,0),(1271,'admin',36,1,0),(1272,'admin',37,1,0),(1273,'admin',38,1,0),(1274,'admin',39,1,0),(1275,'admin',40,1,0),(1276,'apoteker',26,0,0),(1277,'apoteker',27,0,0),(1278,'apoteker',28,0,0),(1279,'apoteker',29,1,0),(1280,'apoteker',30,0,0),(1281,'apoteker',31,0,0),(1282,'apoteker',32,0,0),(1283,'apoteker',33,0,0),(1284,'apoteker',34,0,0),(1286,'apoteker',36,1,0),(1287,'apoteker',37,1,0),(1288,'apoteker',38,1,0),(1289,'apoteker',39,1,0),(1290,'apoteker',40,1,0),(1291,'kasir',26,0,0),(1292,'kasir',27,0,0),(1293,'kasir',28,0,0),(1294,'kasir',29,1,0),(1295,'kasir',30,0,0),(1296,'kasir',31,0,0),(1297,'kasir',32,0,0),(1298,'kasir',33,0,0),(1299,'kasir',34,0,0),(1301,'kasir',36,0,0),(1302,'kasir',37,0,0),(1303,'kasir',38,1,0),(1304,'kasir',39,1,0),(1305,'kasir',40,1,0),(1306,'gudang',26,0,0),(1307,'gudang',27,0,0),(1308,'gudang',28,0,0),(1309,'gudang',29,0,0),(1310,'gudang',30,0,0),(1311,'gudang',31,0,0),(1312,'gudang',32,0,0),(1313,'gudang',33,0,0),(1314,'gudang',34,0,0),(1316,'gudang',36,1,0),(1317,'gudang',37,1,0),(1318,'gudang',38,0,0),(1319,'gudang',39,0,0),(1320,'gudang',40,0,0),(1617,'superadmin',41,1,1),(1618,'superadmin',42,1,1),(1619,'superadmin',43,1,1),(1620,'superadmin',44,1,1),(1621,'superadmin',45,1,1),(1622,'superadmin',46,1,1),(1623,'admin',41,1,0),(1624,'admin',42,1,0),(1625,'admin',43,1,0),(1626,'admin',44,1,0),(1627,'admin',45,1,0),(1628,'admin',46,1,0),(1629,'apoteker',41,1,0),(1630,'apoteker',42,1,0),(1631,'apoteker',43,1,0),(1632,'apoteker',44,0,0),(1633,'apoteker',45,0,0),(1634,'apoteker',46,1,0),(1635,'kasir',41,1,0),(1636,'kasir',42,1,0),(1637,'kasir',43,0,0),(1638,'kasir',44,0,0),(1639,'kasir',45,0,0),(1640,'kasir',46,1,0),(1641,'gudang',41,1,0),(1642,'gudang',42,1,0),(1643,'gudang',43,1,0),(1644,'gudang',44,0,0),(1645,'gudang',45,0,0),(1646,'gudang',46,0,0);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000001_add_role_to_users_table',1),(5,'2024_01_01_000002_create_kategoris_table',1),(6,'2024_01_01_000003_create_satuans_table',1),(7,'2024_01_01_000004_create_pabriks_table',1),(8,'2024_01_01_000005_create_suppliers_table',1),(9,'2024_01_01_000006_create_pelanggans_table',1),(10,'2024_01_01_000007_create_barangs_table',1),(11,'2024_01_01_000008_create_penerimaans_table',1),(12,'2024_01_01_000009_create_detail_penerimaans_table',1),(13,'2024_01_01_000010_create_penjualans_table',1),(14,'2024_01_01_000011_create_detail_penjualans_table',1),(15,'2024_01_01_000012_create_rusaks_table',1),(16,'2026_08_26_140139_add_membership_fields_to_pelanggans_table',2),(17,'2026_08_26_142356_create_custom_discounts_table',3),(18,'2026_08_26_142400_create_custom_discount_categories_table',3),(19,'2026_08_26_142405_create_custom_discount_barangs_table',3),(20,'2026_08_26_143410_create_discount_usages_table',4),(21,'2026_08_26_144720_create_activity_logs_table',5),(22,'2026_09_03_000001_add_kode_kfa_kode_apotek_merk_to_barangs_table',6),(23,'2026_09_03_000002_add_telepon_alamat_pic_to_pabriks_table',7),(24,'2026_09_04_000001_add_metadata_to_penerimaans_table',8),(25,'2026_09_04_000002_create_pembayaran_penerimaans_table',8),(26,'2026_09_03_071455_add_saldo_piutang_to_pelanggans_table',9),(27,'2026_09_04_000001_add_member_aktif_to_pelanggans_table',9),(28,'2026_09_07_130729_add_tanggal_faktur_to_penerimaans_table',9),(29,'2026_09_07_141657_add_ppn_to_penerimaans_table',9),(30,'2026_09_08_144421_create_detail_pesanan_penerimaans_table',9),(31,'2026_09_08_150114_create_riwayat_penerimaans_table',9),(32,'2026_09_06_000001_add_member_profile_fields_to_pelanggans_table',10),(33,'2026_09_07_171915_add_status_member_to_pelanggans_table',10),(34,'2026_09_07_174922_add_status_member_to_pelanggans_table',10),(35,'2026_09_08_145907_add_metode_pembayaran_to_penjualans_table',10),(36,'2026_09_08_151236_create_pembayaran_piutangs_table',10),(37,'2026_09_09_000001_move_pic_from_pabriks_to_suppliers_table',11),(38,'2026_09_11_000001_create_info_apoteks_table',12),(39,'2026_09_16_000001_add_nama_pemilik_to_info_apoteks_table',13),(40,'2026_09_16_000002_add_kategori_to_activity_logs_table',14),(41,'2026_09_21_201858_add_detailed_address_to_info_apoteks_table',15),(42,'2026_09_22_072826_add_resep_fields_to_penjualans_table',16),(43,'2026_09_24_000001_add_custom_discount_percentage_to_pelanggans_table',16),(44,'2026_09_25_000001_add_due_date_to_penjualans_table',16),(45,'2026_09_30_000001_create_permission_tables',17),(46,'2026_09_30_133825_add_metode_pembayaran_to_pembayaran_piutangs_table',18),(47,'2026_10_03_000001_sync_permission_group_sort',19),(48,'2026_10_06_162750_add_latitude_longitude_to_info_apoteks_table',20),(49,'2026_10_10_000001_create_bebans_table',21),(50,'2026_10_22_000001_add_dashboard_permissions',22),(51,'2026_10_19_000001_create_whatsapp_templates_table',23),(52,'2026_10_19_000002_create_whatsapp_logs_table',23),(53,'2026_10_20_000001_create_whatsapp_follow_ups_table',23),(55,'2026_10_26_000001_sync_granular_dashboard_permissions',24),(56,'2026_10_29_000001_remove_nama_lengkap_from_users_table',25),(57,'2026_10_31_000001_add_background_settings_to_info_apoteks_table',26);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `info_apoteks`
--

LOCK TABLES `info_apoteks` WRITE;
/*!40000 ALTER TABLE `info_apoteks` DISABLE KEYS */;
INSERT INTO `info_apoteks` VALUES (1,'Apotek Kita','H. Alim bin ibnu','Jl. Jend. Sudirman No. 21, Kel. Menteng, Kec. Menteng, Jakarta Pusat, DKI Jakarta, 10310','Jl. Jend. Sudirman No. 21','Menteng','Menteng','Jakarta Pusat','DKI Jakarta','10310',-7.78040432,110.36694600,'09876543211','apotekkita@gmail.com','598/021/912/8401284/2026','apt. Budione Abdul','1209471/2122.43.21/2026/8188','info-apotek/WUnKbRjU4VWZa9dsP31kkkrKPftH0pkPTmaWLqyw.jpg','custom',NULL,60,'2026-09-10 15:17:32','2026-10-30 21:19:11');
/*!40000 ALTER TABLE `info_apoteks` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-01 21:57:33



--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `role`, `aktif`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Superadmin', 'superadmin@apotek.test', 'superadmin', 1, '$2y$10$UMDviJNwuVGf9ppCY4H9fuu4DVzroiYP0G35RMc.1dQkoExmZP60O', NOW(), NOW()),
(2, 'Admin', 'admin@apotek.test', 'admin', 1, '$2y$10$UMDviJNwuVGf9ppCY4H9fuu4DVzroiYP0G35RMc.1dQkoExmZP60O', NOW(), NOW()),
(3, 'Kasir', 'kasir@apotek.test', 'kasir', 1, '$2y$10$UMDviJNwuVGf9ppCY4H9fuu4DVzroiYP0G35RMc.1dQkoExmZP60O', NOW(), NOW());
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

