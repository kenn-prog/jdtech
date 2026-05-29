<?php
// ============================================================
//  JDTech — Authentication Guards
//  PURPOSE: Protects pages that require login or admin access.
//           Include this at the TOP of any protected page.
//
//  AUTHENTICATION FLOW:
//  1. User submits login form (email + password)
//  2. backend/login-process.php receives the data via POST
//  3. We look up the user in the database by email
//  4. password_verify() checks the submitted password against
//     the hashed password stored in the database
//  5. If correct, we save user data to $_SESSION['user']
//  6. On every protected page, auth.php checks if $_SESSION
//     has a valid user — if not, redirect to login
//  7. Logout destroys the session entirely
// ============================================================

require_once __DIR__ . '/session.php';

/**
 * AUTHENTICATION GUARD — Any Logged-In User
 * Add this to pages like dashboard.php, cart.php, profile.php
 *
 * Usage at top of page:
 *   require_once 'includes/auth.php';
 *   requireLogin();
 */
function requireLogin(string $redirectTo = 'login.php'): void {
    if (!isLoggedIn()) {
        // Save where they were trying to go so we can redirect after login
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . APP_URL . '/' . $redirectTo);
        exit;
    }
}

/**
 * ADMIN GUARD — Admins Only
 * Add this to ALL files inside /admin/
 *
 * Usage at top of admin page:
 *   require_once '../includes/auth.php';
 *   requireAdmin();
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        if (!isLoggedIn()) {
            header('Location: ' . APP_URL . '/login.php');
        } else {
            // User is logged in but not admin — show forbidden page
            header('HTTP/1.1 403 Forbidden');
            include __DIR__ . '/../404.php';
        }
        exit;
    }
}

/**
 * GUEST GUARD — Redirect logged-in users away from login/register
 * Add this to login.php and register.php so logged-in users
 * don't see those pages again.
 */
function requireGuest(): void {
    if (isLoggedIn()) {
        $redirect = isAdmin() ? 'admin/index.php' : 'dashboard.php';
        header('Location: ' . APP_URL . '/' . $redirect);
        exit;
    }
}

/**
 * JSON-safe authentication check for API endpoints.
 * Returns a 401 JSON error instead of redirecting.
 */
function requireLoginAPI(): void {
    if (!isLoggedIn()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Unauthorized. Please log in.']);
        exit;
    }
}

/**
 * JSON-safe admin check for API endpoints.
 */
function requireAdminAPI(): void {
    if (!isAdmin()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Forbidden. Admins only.']);
        exit;
    }
}
