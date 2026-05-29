<?php
// ============================================================
//  JDTech — Shared Utility Functions
//  PURPOSE: Reusable helper functions used across all pages.
//           Import this in any PHP file that needs these tools.
// ============================================================

/**
 * Send a JSON response and stop execution.
 * Used by API endpoints and backend processors.
 *
 * Example: jsonResponse(['ok' => true, 'user' => $user]);
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Safely output text in HTML (prevents XSS attacks).
 * ALWAYS use h() when printing user-submitted text!
 *
 * Example: echo h($user['name']);
 */
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Redirect to a URL and stop execution.
 *
 * Example: redirect('login.php');
 */
function redirect(string $url): void {
    header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
    exit;
}

/**
 * FILE UPLOAD HANDLER
 * Validates and saves an uploaded file to the correct folder.
 *
 * FILE UPLOAD FLOW:
 * 1. User submits a form with enctype="multipart/form-data"
 * 2. PHP receives the file in $_FILES['field_name']
 * 3. We check: file size, file extension, no upload errors
 * 4. We generate a unique filename (prevents overwriting)
 * 5. move_uploaded_file() saves it to the uploads/ folder
 * 6. We store the path in the database for later display
 *
 * @param array  $file      The $_FILES['field'] array
 * @param string $subFolder 'profile', 'products', or 'documents'
 * @param array  &$errors   Pass an array — errors are added to it
 * @return string|null      The relative path to the saved file, or null
 */
function uploadFile(array $file, string $subFolder, array &$errors): ?string {
    // No file was submitted — that's okay, it's optional
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed. Please try again.';
        return null;
    }

    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $errors[] = 'File is too large. Maximum size is 5 MB.';
        return null;
    }

    // Check file extension (NEVER trust file['type'] — it can be faked)
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_TYPES, true)) {
        $errors[] = 'Invalid file type. Allowed: ' . implode(', ', UPLOAD_ALLOWED_TYPES);
        return null;
    }

    // Verify it's actually an image (not a renamed PHP file)
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'Uploaded file is not a valid image.';
        return null;
    }

    // Generate a unique filename to prevent overwriting
    $filename  = uniqid('img_', true) . '.' . $ext;
    $targetDir = UPLOAD_DIR . $subFolder . '/';
    $targetPath = $targetDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Move from PHP's temp folder to our uploads folder
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $errors[] = 'Could not save the file. Check folder permissions.';
        return null;
    }

    // Return a relative path (what we store in the database)
    return 'uploads/' . $subFolder . '/' . $filename;
}

/**
 * Format a price in Philippine Peso.
 * Example: formatPrice(1500) → "₱1,500.00"
 */
function formatPrice(float $amount): string {
    return '₱' . number_format($amount, 2);
}

/**
 * Format a MySQL datetime into a human-readable date.
 * Example: formatDate('2025-01-15 10:30:00') → "January 15, 2025"
 */
function formatDate(string $datetime): string {
    return date('F j, Y', strtotime($datetime));
}

/**
 * Truncate long text with an ellipsis.
 * Example: truncate('A very long description...', 50)
 */
function truncate(string $text, int $maxLength = 100): string {
    if (strlen($text) <= $maxLength) {
        return $text;
    }
    return substr($text, 0, $maxLength) . '…';
}

/**
 * Generate a CSRF token and store it in the session.
 * Use this in forms to prevent Cross-Site Request Forgery.
 *
 * SECURITY BEST PRACTICE:
 * Every form should include a hidden CSRF token.
 * The backend checks this token before processing the form.
 */
function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a submitted CSRF token matches the session token.
 */
function verifyCsrf(string $submittedToken): bool {
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return !empty($sessionToken) && hash_equals($sessionToken, $submittedToken);
}

/**
 * Validate an email address.
 */
function isValidEmail(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Check whether a table column exists in the current database.
 */
function columnExists(string $table, string $column): bool {
    if (!function_exists('fetchOne') || !function_exists('escape') || !defined('DB_NAME')) {
        return false;
    }

    $row = fetchOne("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . escape(DB_NAME) . "' AND TABLE_NAME = '" . escape($table) . "' AND COLUMN_NAME = '" . escape($column) . "' LIMIT 1");
    return !empty($row);
}

/**
 * Get the customizable logo icon used across the site.
 */
function getLogoImage(): ?string {
    if (!function_exists('fetchOne') || !defined('DB_NAME') || !columnExists('homepage', 'logo_image')) {
        return null;
    }

    $row = fetchOne('SELECT logo_image FROM homepage LIMIT 1');
    $image = trim($row['logo_image'] ?? '');
    if ($image === '') {
        return null;
    }

    $fullPath = __DIR__ . '/../' . $image;
    return file_exists($fullPath) ? $image : null;
}

function getLogoIcon(): string {
    if (function_exists('fetchOne') && function_exists('escape') && defined('DB_NAME') && columnExists('homepage', 'logo_icon')) {
        $row = fetchOne('SELECT logo_icon FROM homepage LIMIT 1');
        $icon = trim($row['logo_icon'] ?? '');
        if ($icon !== '') {
            return mb_substr($icon, 0, 4);
        }
    }
    return '⚡';
}

function orderStatusClass(string $status): string {
    return match($status) {
        'Processing'  => 'badge-warning',
        'On the Way'  => 'badge-info',
        'Delivered'   => 'badge-success',
        'Cancelled'   => 'badge-danger',
        default       => 'badge-secondary',
    };
}
