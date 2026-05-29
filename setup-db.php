<?php
// ============================================================
//  Database Setup Script
//  Visit: https://web-production-f2742.up.railway.app/setup-db.php
//  This will import the schema if tables don't exist
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/db.php';

echo '<h1>JDTech Database Setup</h1>';
echo '<pre style="background: #f5f5f5; padding: 20px; white-space: pre-wrap; word-wrap: break-word;">';

try {
    // Read and execute project.sql
    $sqlFile = __DIR__ . '/database/project.sql';
    echo "📂 Looking for: $sqlFile\n";
    
    if (!file_exists($sqlFile)) {
        echo "❌ File not found: $sqlFile\n";
        echo "Current directory: " . getcwd() . "\n";
        echo "Files in database/: " . implode(', ', glob(__DIR__ . '/database/*')) . "\n";
        die();
    }
    
    $sql = file_get_contents($sqlFile);
    echo "✅ Read schema file (" . strlen($sql) . " bytes)\n";
    
    if (!$sql) {
        die('❌ Error: Schema file is empty\n');
    }

    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)), function($s) {
        return !empty($s) && !preg_match('/^\s*--/', $s) && !preg_match('/^\s*\/\*/', $s);
    });

    echo "📊 Found " . count($statements) . " SQL statements to execute\n\n";

    $count = 0;
    $errors = [];
    foreach ($statements as $i => $statement) {
        if (runQuery($statement)) {
            $count++;
        } else {
            global $conn;
            $error = mysqli_error($conn);
            if (strpos($error, 'already exists') === false) {
                $errors[] = "Statement " . ($i + 1) . ": " . $error;
            } else {
                echo "ℹ️ Table already exists (skipped)\n";
            }
        }
    }

    echo "\n✅ Database setup complete! Executed $count statements.\n";
    
    if (!empty($errors)) {
        echo "\n⚠️ Errors encountered:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }

    // Verify tables exist
    echo "\n📋 Verifying tables:\n";
    $tables = ['admin', 'users', 'categories', 'items', 'orders', 'homepage'];
    $allExist = true;
    foreach ($tables as $table) {
        $result = fetchOne("SELECT 1 FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '$table'");
        $status = $result ? '✅' : '❌';
        echo "  - $table: $status\n";
        if (!$result) $allExist = false;
    }
    
    if ($allExist) {
        echo "\n🎉 All tables created successfully!\n";
    } else {
        echo "\n⚠️ Some tables are missing. Check errors above.\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="index.php">← Go back to homepage</a></p>';
?>
