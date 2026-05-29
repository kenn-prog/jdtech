<?php
// ============================================================
//  JDTech — Database Connection
//  PURPOSE: Creates a single MySQL connection shared across
//           all pages. Uses MySQLi with utf8mb4 charset.
//
//  HOW PHP CONNECTS TO MYSQL:
//  1. PHP calls mysqli_connect() with host, user, password
//  2. MySQL server verifies credentials
//  3. On success, a "connection handle" ($conn) is returned
//  4. Every query uses $conn to talk to the database
//  5. utf8mb4 supports emoji and all Unicode characters
// ============================================================

require_once __DIR__ . '/config.php';

// Connect to MySQL server
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

// Stop everything if connection fails
if (!$conn) {
    // In production, never show the raw error to users
    if (APP_ENV === 'development') {
        die('Database connection failed: ' . mysqli_connect_error());
    } else {
        die('Service temporarily unavailable. Please try again later.');
    }
}

// Set character encoding to utf8mb4 (supports emoji + all languages)
mysqli_set_charset($conn, DB_CHARSET);

// Create the database if it doesn't exist yet (useful for first-time setup)
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` 
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Select our database
mysqli_select_db($conn, DB_NAME);

// ── Helper Functions ──────────────────────────────────────

/**
 * Safely escape a string for use in SQL queries.
 * ALWAYS use this before putting user input into a query!
 * Better alternative: use prepared statements (see functions.php)
 */
function escape(string $value): string {
    global $conn;
    return mysqli_real_escape_string($conn, $value);
}

/**
 * Run a query and return all rows as an array.
 * Example: $users = fetchAll("SELECT * FROM users");
 */
function fetchAll(string $query): array {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Run a query and return just ONE row.
 * Example: $user = fetchOne("SELECT * FROM users WHERE id = 1");
 */
function fetchOne(string $query): ?array {
    global $conn;
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : null;
}

/**
 * Run an INSERT/UPDATE/DELETE query.
 * Returns true on success, false on failure.
 */
function runQuery(string $query): bool {
    global $conn;
    return (bool) mysqli_query($conn, $query);
}

/**
 * Get the ID of the last inserted row.
 * Use this right after an INSERT query.
 */
function lastInsertId(): int {
    global $conn;
    return (int) mysqli_insert_id($conn);
}
