<?php
// ============================================================
//  Database Setup Script
//  Visit: https://web-production-f2742.up.railway.app/setup-db.php
//  This will import the schema if tables don't exist
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';

echo '<h1>JDTech Database Setup</h1>';
echo '<pre>';

// Read and execute project.sql
$sql = file_get_contents('database/project.sql');
if (!$sql) {
    die('Error: Could not read database/project.sql');
}

// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)), function($s) {
    return !empty($s) && !preg_match('/^\s*--/', $s);
});

$count = 0;
foreach ($statements as $statement) {
    if (runQuery($statement)) {
        $count++;
    } else {
        echo "⚠️ Statement skipped (likely already exists)\n";
    }
}

echo "\n✅ Database setup complete! Executed $count statements.\n";
echo "\nNow visiting this page again will confirm all tables exist.\n";

// Verify tables exist
echo "\n📋 Verifying tables:\n";
$tables = ['admin', 'users', 'categories', 'items', 'orders', 'homepage'];
foreach ($tables as $table) {
    $result = fetchOne("SHOW TABLES LIKE '$table'");
    echo "  - $table: " . ($result ? '✅' : '❌') . "\n";
}

echo '</pre>';
echo '<p><a href="index.php">← Go back to homepage</a></p>';
?>
