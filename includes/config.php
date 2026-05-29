<?php
// ============================================================
//  JDTech — Application Configuration
//  PURPOSE: Central place for ALL settings. Edit this file
//           to configure your database, app name, etc.
// ============================================================

// ── App Settings ──────────────────────────────────────────
define('APP_NAME',    'JDTech');
define('APP_VERSION', '1.0.0');
// Read APP_URL and APP_ENV from environment when available (Railway, Render, etc.)
// Normalize APP_URL: ensure it has a scheme and no trailing slash.
$app_url = getenv('APP_URL') ?: 'http://localhost/jdtech';
if (!preg_match('#^https?://#i', $app_url)) {
    $app_url = 'https://' . $app_url;
}
$app_url = rtrim($app_url, '/');
define('APP_URL', $app_url);
define('APP_ENV', getenv('APP_ENV') ?: 'development');               // 'development' or 'production'

// ── Database Settings ─────────────────────────────────────
// These are loaded from .env in production.
// For local development, set them directly here.
define('DB_HOST',     getenv('DB_HOST')     ?: '127.0.0.1');
define('DB_USER',     getenv('DB_USER')     ?: 'root');
define('DB_PASS',     getenv('DB_PASS')     ?: '');
define('DB_NAME',     getenv('DB_NAME')     ?: 'jdtech');
define('DB_CHARSET',  'utf8mb4');

// ── Upload Settings ───────────────────────────────────────
define('UPLOAD_DIR',          __DIR__ . '/../uploads/');
define('UPLOAD_MAX_SIZE',     5 * 1024 * 1024);   // 5 MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ── Session Settings ──────────────────────────────────────
define('SESSION_LIFETIME', 3600);   // 1 hour in seconds
define('SESSION_NAME',     'jdtech_sess');

// ── Security ──────────────────────────────────────────────
define('BCRYPT_COST', 12);   // Password hash strength (10–14 is good)

// ── Error Reporting ───────────────────────────────────────
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}
