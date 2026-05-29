<?php
// ============================================================
//  JDTech — Session Handling
//  PURPOSE: Starts PHP sessions securely and provides helpers
//           to read session data from anywhere.
//
//  SESSION HANDLING EXPLAINED:
//  - A session is like a "memory" for each visitor
//  - When a user logs in, we save their info in $_SESSION
//  - PHP gives the browser a secret cookie (session ID)
//  - On every page load, PHP reads that cookie and restores
//    the $_SESSION data automatically
//  - Sessions expire after SESSION_LIFETIME seconds of inactivity
// ============================================================

require_once __DIR__ . '/config.php';

// Configure session security BEFORE starting the session
ini_set('session.cookie_httponly', 1);    // JS cannot read the cookie
ini_set('session.cookie_secure',   0);    // Set to 1 on HTTPS (production)
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime',  SESSION_LIFETIME);
session_name(SESSION_NAME);

// Start session only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Session Helper Functions ──────────────────────────────

/**
 * Get the currently logged-in user's data.
 * Returns null if no one is logged in.
 */
function getUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if someone is logged in (user OR admin).
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user']);
}

/**
 * Check if the logged-in user is an admin.
 */
function isAdmin(): bool {
    $user = getUser();
    return $user && isset($user['role']) && $user['role'] === 'admin';
}

/**
 * Check if the logged-in user is a regular user.
 */
function isUser(): bool {
    $user = getUser();
    return $user && isset($user['role']) && $user['role'] === 'user';
}

/**
 * Save a flash message (shown once on the next page load).
 * Usage: setFlash('success', 'Profile updated!');
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash messages.
 * Usage: $flash = getFlash(); // returns ['success' => '...'] or null
 */
function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Log out the current user (destroys all session data).
 */
function destroySession(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
