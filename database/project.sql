-- ============================================================
--  JDTech — Main Database Schema
--  PURPOSE: Creates all tables needed for the website.
--
--  HOW PHP CONNECTS TO MYSQL:
--  1. PHP uses mysqli_connect(host, user, password) to open
--     a connection to the MySQL server
--  2. mysqli_select_db() switches to the 'jdtech' database
--  3. mysqli_query() sends SQL commands to MySQL
--  4. MySQL processes them and returns results
--  5. PHP reads results with mysqli_fetch_assoc()
--  6. mysqli_close() closes the connection when done
--
--  TO USE THIS FILE:
--  Option A: phpMyAdmin → Import → select this file → Go
--  Option B: mysql -u root -p < database/project.sql
--  Option C: includes/db.php creates tables automatically
-- ============================================================

-- Create and use the database
-- NOTE: Database creation and selection is handled by PHP in includes/db.php
-- The USE statement was removed to support Railway (database='railway') and local dev (database='jdtech')

-- ── admin table ───────────────────────────────────────────
-- Stores admin login credentials.
-- Separate from users for better security.
CREATE TABLE IF NOT EXISTS `admin` (
  `id`         INT          AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `email`      VARCHAR(150) UNIQUE DEFAULT NULL,
  `password`   VARCHAR(255) NOT NULL   -- Always stored as bcrypt hash
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── users table ───────────────────────────────────────────
-- Stores registered customer accounts.
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT          AUTO_INCREMENT PRIMARY KEY,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,  -- bcrypt hash, NEVER plain text
  `role`       ENUM('user') NOT NULL DEFAULT 'user',
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name`  VARCHAR(100) DEFAULT NULL,
  `phone`          VARCHAR(50)  DEFAULT NULL,
  `address`        TEXT         DEFAULT NULL,
  `payment_method` VARCHAR(100) DEFAULT NULL,
  `contact_number` VARCHAR(100) DEFAULT NULL,
  `avatar`         VARCHAR(20)  DEFAULT '👤',
  `joined_at`      DATETIME     DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── categories table ──────────────────────────────────────
-- Stores product categories dynamically managed by admin.
CREATE TABLE IF NOT EXISTS `categories` (
  `id`       INT          AUTO_INCREMENT PRIMARY KEY,
  `name`     VARCHAR(100) NOT NULL UNIQUE,
  `slug`     VARCHAR(100) NOT NULL UNIQUE,
  `icon`     VARCHAR(50)  DEFAULT NULL,
  `created_at` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── items table (products) ────────────────────────────────
-- Stores all products displayed in the shop.
CREATE TABLE IF NOT EXISTS `items` (
  `id`           INT            AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(150)   NOT NULL,
  `price`        DECIMAL(10,2)  NOT NULL,
  `stock_status` ENUM('On-hand','Pre-order') NOT NULL DEFAULT 'On-hand',
  `category`     VARCHAR(50)    DEFAULT 'phones',
  `icon`         VARCHAR(50)    DEFAULT 'phone',
  `stock`        INT            NOT NULL DEFAULT 10,
  `rating`       INT            NOT NULL DEFAULT 5,
  `badge`        VARCHAR(50)    DEFAULT NULL,
  `description`  TEXT           DEFAULT NULL,
  `image_url`    TEXT           DEFAULT NULL,
  `specs`        TEXT           DEFAULT NULL,
  `reviews`      INT            NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── orders table ─────────────────────────────────────────
-- Stores customer orders. Items are saved as JSON.
-- FOREIGN KEY links each order to a user.
CREATE TABLE IF NOT EXISTS `orders` (
  `id`      INT          AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT          NOT NULL,
  `status`  ENUM('Processing','On the Way','Delivered','Cancelled')
            NOT NULL DEFAULT 'Processing',
  `date`    DATETIME     NOT NULL,
  `items`           TEXT         NOT NULL, -- JSON array of {name, price, qty}
  `total`           DECIMAL(10,2) NOT NULL,
  `delivery_address` TEXT        DEFAULT NULL,
  `payment_method`   VARCHAR(100) DEFAULT NULL,
  `contact_number`   VARCHAR(100) DEFAULT NULL,
  `product_rating`   INT          DEFAULT NULL,
  `product_feedback` TEXT         DEFAULT NULL,
  `shop_rating`      INT          DEFAULT NULL,
  `shop_feedback`    TEXT         DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
  -- ON DELETE CASCADE: if a user is deleted, their orders are deleted too
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── homepage table ────────────────────────────────────────
-- Stores editable homepage content (managed via admin panel).
-- There is always exactly ONE row in this table.
CREATE TABLE IF NOT EXISTS `homepage` (
  `id`            INT          AUTO_INCREMENT PRIMARY KEY,
  `hero_tag`      VARCHAR(150) DEFAULT NULL,
  `hero_title`    VARCHAR(255) NOT NULL,
  `hero_text`     TEXT         NOT NULL,
  `hero_image`    VARCHAR(255) DEFAULT NULL,
  `about_headline` VARCHAR(255) DEFAULT NULL,
  `about_text`    TEXT         DEFAULT NULL,
  `customers`     VARCHAR(100) DEFAULT NULL,
  `footer_text`   TEXT         DEFAULT NULL,
  `facebook_page` TEXT         DEFAULT NULL,
  `contact_number` TEXT        DEFAULT NULL,
  `location`      TEXT         DEFAULT NULL,
  `opening_hours` TEXT         DEFAULT NULL,
  `owner`         VARCHAR(150) DEFAULT NULL,
  `staffs`        TEXT         DEFAULT NULL,  -- One per line: Name|Role|PhotoPath
  `staff_photos`  TEXT         DEFAULT NULL,
  `store_photos`  TEXT         DEFAULT NULL   -- One URL per line
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── staff table ────────────────────────────────────────────
-- Stores team members (owner and staff).
CREATE TABLE IF NOT EXISTS `staff` (
  `id`          INT          AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(150) NOT NULL,
  `position`    VARCHAR(150) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ── Default Categories (inserted automatically on first run) ─
INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `icon`) VALUES
  (1, 'Smartphones', 'phones', '📱'),
  (2, 'Laptops', 'laptops', '💻'),
  (3, 'Audio', 'audio', '🎧'),
  (4, 'Gaming', 'gaming', '🎮'),
  (5, 'Accessories', 'accessories', '🔌'),
  (6, 'Tablets', 'tablets', '📱');