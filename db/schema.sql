-- THE DRIVE CLINIC — Database Schema (MySQL 8.0+)
-- Character set: utf8mb4, Collation: utf8mb4_unicode_ci, Engine: InnoDB

CREATE DATABASE IF NOT EXISTS `drive_clinic` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `drive_clinic`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. SETTINGS TABLE
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(64) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. SERVICES TABLE
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(64) NOT NULL UNIQUE,
  `name` VARCHAR(128) NOT NULL,
  `category` ENUM('protect', 'detail', 'clean') NOT NULL,
  `vehicle_types` SET('car', 'bike') NOT NULL DEFAULT 'car',
  `is_major` TINYINT(1) NOT NULL DEFAULT 1,
  `module` VARCHAR(32) NULL DEFAULT NULL,
  `short_desc` VARCHAR(255) NOT NULL,
  `intro_lead` TEXT NULL,
  `intro_body` TEXT NULL,
  `included_json` JSON NULL,
  `benefits_json` JSON NULL,
  `who_json` JSON NULL,
  `process_json` JSON NULL,
  `hero_slides_json` JSON NULL,
  `related_slugs_json` JSON NULL,
  `duration_note` VARCHAR(64) NULL DEFAULT '[CONFIRM DURATION]',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `seo_title` VARCHAR(128) NULL,
  `seo_description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_sort` (`sort_order`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. HERO SLIDES TABLE
DROP TABLE IF EXISTS `hero_slides`;
CREATE TABLE `hero_slides` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(128) NOT NULL,
  `headline_lines_json` JSON NOT NULL,
  `supporting` VARCHAR(255) NULL,
  `cta_label` VARCHAR(64) NOT NULL,
  `cta_href` VARCHAR(128) NOT NULL,
  `secondary_cta_label` VARCHAR(64) NULL,
  `secondary_cta_href` VARCHAR(128) NULL,
  `image_desktop` VARCHAR(255) NOT NULL,
  `image_mobile` VARCHAR(255) NOT NULL,
  `video_src` VARCHAR(255) NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. FAQS TABLE
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT UNSIGNED NULL,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `is_answered` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_service_faq` (`service_id`, `sort_order`),
  CONSTRAINT `fk_faqs_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. GALLERY ITEMS TABLE
DROP TABLE IF EXISTS `gallery_items`;
CREATE TABLE `gallery_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT UNSIGNED NULL,
  `type` ENUM('image', 'before_after') NOT NULL DEFAULT 'image',
  `image` VARCHAR(255) NULL,
  `before_image` VARCHAR(255) NULL,
  `after_image` VARCHAR(255) NULL,
  `alt` VARCHAR(255) NOT NULL,
  `vehicle_label` VARCHAR(128) NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_gallery_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. REVIEWS TABLE
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(128) NOT NULL,
  `vehicle` VARCHAR(128) NULL,
  `service_id` INT UNSIGNED NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `body` TEXT NOT NULL,
  `source` VARCHAR(32) NOT NULL DEFAULT 'Google',
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. CUSTOMERS TABLE
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(15) NOT NULL UNIQUE,
  `whatsapp` VARCHAR(15) NULL,
  `email` VARCHAR(128) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. VEHICLES TABLE
DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `type` ENUM('car', 'bike') NOT NULL DEFAULT 'car',
  `brand` VARCHAR(64) NOT NULL,
  `model` VARCHAR(64) NOT NULL,
  `year` VARCHAR(4) NULL,
  `registration` VARCHAR(20) NULL,
  `color` VARCHAR(32) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_customer_vehicle` (`customer_id`),
  CONSTRAINT `fk_vehicles_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. APPOINTMENTS TABLE
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ref` VARCHAR(16) NOT NULL UNIQUE,
  `customer_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `service_id` INT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  `status` ENUM('new_lead', 'contacted', 'quote_sent', 'booked', 'vehicle_received', 'in_service', 'quality_check', 'completed', 'review_requested', 'return_customer', 'cancelled') NOT NULL DEFAULT 'new_lead',
  `is_recurring` TINYINT(1) NOT NULL DEFAULT 0,
  `recurring_weeks` TINYINT UNSIGNED NULL,
  `notes` TEXT NULL,
  `source` VARCHAR(32) NOT NULL DEFAULT 'booking_flow',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_date_time` (`date`, `time`),
  INDEX `idx_status` (`status`),
  INDEX `idx_ref` (`ref`),
  CONSTRAINT `fk_appointments_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_appointments_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `fk_appointments_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. LEADS TABLE
DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `source` ENUM('booking', 'diagnosis', 'contact', 'whatsapp') NOT NULL,
  `customer_json` JSON NOT NULL,
  `service_id` INT UNSIGNED NULL,
  `message` TEXT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'new',
  `utm_json` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_leads_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. REMINDERS TABLE
DROP TABLE IF EXISTS `reminders`;
CREATE TABLE `reminders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NULL,
  `service_id` INT UNSIGNED NULL,
  `type` ENUM('ppf_inspection', 'ceramic_maintenance', 'maintenance_wash', 'follow_up', 'quote_follow_up', 'review_request', 'repeat_service') NOT NULL,
  `due_date` DATE NOT NULL,
  `status` ENUM('pending', 'sent', 'dismissed', 'completed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reminders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reminders_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reminders_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. AVAILABILITY RULES & BLOCKED DATES
DROP TABLE IF EXISTS `availability_rules`;
CREATE TABLE `availability_rules` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `weekday` TINYINT UNSIGNED NOT NULL COMMENT '0=Sunday, 1=Monday ... 6=Saturday',
  `open_time` TIME NOT NULL,
  `close_time` TIME NOT NULL,
  `slot_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  `capacity_per_slot` TINYINT UNSIGNED NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. INVENTORY ITEMS
DROP TABLE IF EXISTS `inventory_items`;
CREATE TABLE `inventory_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(32) NOT NULL UNIQUE,
  `name` VARCHAR(128) NOT NULL,
  `category` VARCHAR(64) NOT NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'Piece',
  `current_stock` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_stock_alert` DECIMAL(10,2) NOT NULL DEFAULT 5.00,
  `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `sell_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `supplier` VARCHAR(128) NULL,
  `location` VARCHAR(64) NULL,
  `notes` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. INVENTORY TRANSACTIONS (Stock In, Job Out, Decline / Damage / Waste Logs)
DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT UNSIGNED NOT NULL,
  `type` ENUM('IN_PURCHASE', 'OUT_JOB_CARD', 'DECLINE_DAMAGED', 'DECLINE_EXPIRED', 'DECLINE_SCRAP', 'AUDIT_ADJUST') NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `reference_no` VARCHAR(64) NULL,
  `reason` VARCHAR(255) NULL,
  `logged_by` VARCHAR(64) NOT NULL DEFAULT 'Studio Owner',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_item_tx` (`item_id`),
  INDEX `idx_type` (`type`),
  CONSTRAINT `fk_tx_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. JOB CARDS (Workshop Bay Operations & Damage Maps)
DROP TABLE IF EXISTS `job_cards`;
CREATE TABLE `job_cards` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `card_no` VARCHAR(32) NOT NULL UNIQUE,
  `appointment_id` INT UNSIGNED NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `bay_no` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `assigned_tech` VARCHAR(64) NOT NULL DEFAULT 'Sameer',
  `status` ENUM('CHECKED_IN', 'INSPECTION', 'IN_PROGRESS', 'QUALITY_CHECK', 'COMPLETED', 'DELIVERED') NOT NULL DEFAULT 'CHECKED_IN',
  `fuel_level` VARCHAR(16) NOT NULL DEFAULT '1/2',
  `odometer` VARCHAR(32) NULL,
  `service_names` VARCHAR(255) NOT NULL,
  `damages_json` JSON NULL,
  `consumables_json` JSON NULL,
  `technician_notes` TEXT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_bay_status` (`bay_no`, `status`),
  CONSTRAINT `fk_jc_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_jc_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. INVOICES & BILLING
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_no` VARCHAR(32) NOT NULL UNIQUE,
  `job_card_id` INT UNSIGNED NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'UNPAID',
  `payment_method` ENUM('CASH', 'UPI', 'CARD', 'BANK_TRANSFER') NULL DEFAULT 'UPI',
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_inv_status` (`payment_status`),
  CONSTRAINT `fk_inv_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. WARRANTY CERTIFICATES
DROP TABLE IF EXISTS `warranty_certificates`;
CREATE TABLE `warranty_certificates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cert_no` VARCHAR(32) NOT NULL UNIQUE,
  `customer_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `service_slug` VARCHAR(64) NOT NULL,
  `product_brand` VARCHAR(64) NOT NULL,
  `roll_batch_no` VARCHAR(64) NOT NULL,
  `warranty_years` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `install_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `next_inspection_date` DATE NOT NULL,
  `status` ENUM('ACTIVE', 'INSPECTION_DUE', 'EXPIRED', 'VOID') NOT NULL DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_cert_no` (`cert_no`),
  CONSTRAINT `fk_war_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_war_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

