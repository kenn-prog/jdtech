-- ============================================================
--  JDTech — Seed Data
--  PURPOSE: Insert sample data for development and testing.
--           Run AFTER project.sql to populate the database.
--
--  TO USE: mysql -u root -p jdtech < database/seed.sql
-- ============================================================

USE `jdtech`;

-- ── Default Admin Account ─────────────────────────────────
-- Password: admin123
INSERT IGNORE INTO `admin` (username, email, password)
VALUES (
  'admin',
  'admin@jdtech.com',
  '$2y$10$uRu5yW77f0iQQtHzvpcije/Q86Z6TZmxje2rM9qSi7FTWUdzI8fme'
);

-- ── Demo User Account ───────────────────────────────────────
-- Password: user123
INSERT IGNORE INTO `users` (email, password, role, first_name, last_name, phone, joined_at, avatar)
VALUES (
  'user@jdtech.com',
  '$2y$10$ZpDw0l23kisTwFdZPqj37e9VxZVluW2RQng76I7Lv6srjmjr1Myqu',
  'user',
  'Juan',
  'Dela Cruz',
  '+63 917 000 0001',
  NOW(),
  '👤'
);

-- ── Homepage Default Content ──────────────────────────────
INSERT IGNORE INTO `homepage`
  (hero_tag, hero_title, hero_text, about_headline, about_text,
   customers, footer_text, facebook_page, contact_number,
   location, opening_hours, owner, staffs)
VALUES (
  'NEW ARRIVALS 2026',
  'Welcome to JDTech',
  'Find the latest devices, accessories, and trusted customer support in one premium destination.',
  'Why Choose JDTech?',
  'Everything you need, all in one place.',
  '500+',
  'Your premium destination for the latest electronics, gadgets, and accessories. Quality products, trusted support.',
  'https://www.facebook.com/jdtechstore',
  '+63 912 345 6789',
  '123 Main St, Makati City, Metro Manila',
  'Monday – Saturday: 10:00 AM – 8:00 PM',
  'Juan D. Tech',
  'Maria Santos|Customer Service
Karlo Reyes|Sales
Nina Cruz|Cashier'
);

-- ── Sample Products ────────────────────────────────────────
INSERT IGNORE INTO `items` (name, price, stock_status, category, icon, stock, rating, badge, description) VALUES
('iPhone 16 Pro',        89995.00, 'On-hand',  'phones',      'phone',      15, 5, 'NEW',  'Apple iPhone 16 Pro with A18 Pro chip, 6.1-inch ProMotion display, and titanium design.'),
('Samsung Galaxy S25',   79995.00, 'On-hand',  'phones',      'phone',      20, 5, 'HOT',  'Samsung flagship with Snapdragon 8 Elite, 200MP camera, and 7-year OS support.'),
('MacBook Air M3',      109995.00, 'On-hand',  'laptops',     'laptop',      8, 5, NULL,   'Apple MacBook Air with M3 chip, 18-hour battery, and stunning Liquid Retina display.'),
('ASUS ROG Zephyrus G14', 94995.00,'On-hand',  'laptops',     'laptop',      5, 5, 'SALE', 'AMD Ryzen 9 + RTX 4060, ultra-slim gaming laptop built for pros.'),
('Sony WH-1000XM5',     19995.00, 'On-hand',  'audio',       'headphones', 30, 5, NULL,   'Industry-leading noise cancellation, 30-hour battery, and exceptional sound quality.'),
('AirPods Pro 2',       16995.00, 'On-hand',  'audio',       'earbuds',    25, 5, 'NEW',  'Active Noise Cancellation, Adaptive Transparency, and spatial audio.'),
('PS5 Controller',       4295.00, 'Pre-order', 'gaming',      'controller', 12, 4, NULL,   'DualSense wireless controller with haptic feedback and adaptive triggers.'),
('Logitech MX Master 3', 4995.00, 'On-hand',  'accessories', 'mouse',      40, 5, NULL,   'Advanced wireless mouse with MagSpeed electromagnetic scrolling.'),
('iPad Air M2',         49995.00, 'On-hand',  'tablets',     'tablet',     10, 5, 'NEW',  'Apple iPad Air with M2 chip, 11-inch Liquid Retina display, USB-C connectivity.'),
('Samsung 4K Monitor',  29995.00, 'Pre-order', 'accessories', 'monitor',     3, 4, NULL,   '27-inch 4K UHD IPS panel with 144Hz refresh rate and HDR600 support.');
