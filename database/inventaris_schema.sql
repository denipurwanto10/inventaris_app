-- =====================================================================
-- Skema Database: Gudang.in — Inventory & Asset Management
-- Gunakan file ini hanya sebagai referensi struktur tabel.
-- Untuk membuat tabel di aplikasi, jalankan: php artisan migrate --seed
-- =====================================================================

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'staff',
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `contact_person` VARCHAR(255) NULL,
  `phone` VARCHAR(30) NULL,
  `email` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

CREATE TABLE `items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `barcode` VARCHAR(50) NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `supplier_id` BIGINT UNSIGNED NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'pcs',
  `purchase_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `min_stock` INT NOT NULL DEFAULT 5,
  `stock` INT NOT NULL DEFAULT 0,
  `location` VARCHAR(255) NULL,
  `photo` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `status` ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL
);

CREATE TABLE `stock_movements` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('masuk','keluar') NOT NULL,
  `reference_no` VARCHAR(30) NOT NULL UNIQUE,
  `item_id` BIGINT UNSIGNED NOT NULL,
  `qty` INT NOT NULL,
  `unit_price` DECIMAL(15,2) NULL,
  `total` DECIMAL(15,2) NULL,
  `party` VARCHAR(255) NULL COMMENT 'Supplier utk masuk, tujuan/penerima utk keluar',
  `pic` VARCHAR(255) NULL COMMENT 'Penanggung jawab',
  `date` DATE NOT NULL,
  `notes` TEXT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

CREATE TABLE `loans` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `borrower_name` VARCHAR(255) NOT NULL,
  `borrower_division` VARCHAR(255) NULL,
  `phone` VARCHAR(30) NULL,
  `loan_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `return_date` DATE NULL,
  `status` ENUM('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam',
  `notes` TEXT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

CREATE TABLE `loan_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `loan_id` BIGINT UNSIGNED NOT NULL,
  `item_id` BIGINT UNSIGNED NOT NULL,
  `qty` INT NOT NULL,
  `condition_out` VARCHAR(255) NOT NULL DEFAULT 'Baik',
  `condition_in` VARCHAR(255) NULL,
  `returned_qty` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`loan_id`) REFERENCES `loans`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE
);

CREATE TABLE `maintenances` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `item_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('perbaikan','servis_rutin','kalibrasi') NOT NULL DEFAULT 'perbaikan',
  `description` TEXT NOT NULL,
  `technician` VARCHAR(255) NULL,
  `cost` DECIMAL(15,2) NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `status` ENUM('diproses','selesai','dibatalkan') NOT NULL DEFAULT 'diproses',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE
);
