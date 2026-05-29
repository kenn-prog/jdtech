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

function normalizeSql(string $sql): string {
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    $sql = preg_replace('/^\s*USE\s+`[^`]+`\s*;?/mi', '', $sql);
    $sql = preg_replace('/^\s*CREATE\s+DATABASE.*$/mi', '', $sql);
    return trim($sql);
}

function splitSqlStatements(string $sql): array {
    $sql = normalizeSql($sql);
    $parts = array_map('trim', explode(';', $sql));
    return array_filter($parts, fn($part) => $part !== '');
}

function executeSqlFile(string $path, int &$count, array &$errors): void {
    global $conn;

    if (!file_exists($path)) {
        throw new RuntimeException("SQL file not found: $path");
    }

    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Unable to read file: $path");
    }

    $statements = splitSqlStatements($content);
    $count = 0;
    $errors = [];
    foreach ($statements as $i => $statement) {
        if (runQuery($statement)) {
            $count++;
        } else {
            $error = mysqli_error($conn);
            if (stripos($error, 'already exists') === false) {
                $errors[] = "Statement " . ($i + 1) . ": " . $error;
            }
        }
    }
}

echo '<h1>JDTech Database Setup</h1>';
echo '<pre style="background: #f5f5f5; padding: 20px; white-space: pre-wrap; word-wrap: break-word;">';

try {
    $projectFile = __DIR__ . '/database/project.sql';
    echo "📂 Loading schema: $projectFile\n";
    $sql = file_get_contents($projectFile);
    if ($sql === false) {
        throw new RuntimeException("Could not read schema file: $projectFile");
    }
    echo "✅ Read schema file (" . strlen($sql) . " bytes)\n";

    executeSqlFile($projectFile, $schemaCount, $schemaErrors);
    echo "📊 Executed $schemaCount schema statements.\n";
    if (!empty($schemaErrors)) {
        echo "\n⚠️ Schema errors:\n";
        foreach ($schemaErrors as $error) {
            echo "  - $error\n";
        }
    }

    $seedFile = __DIR__ . '/database/seed.sql';
    if (file_exists($seedFile)) {
        echo "\n📂 Loading seed data: $seedFile\n";
        $seedSql = file_get_contents($seedFile);
        if ($seedSql === false) {
            throw new RuntimeException("Could not read seed file: $seedFile");
        }
        echo "✅ Read seed file (" . strlen($seedSql) . " bytes)\n";

        executeSqlFile($seedFile, $seedCount, $seedErrors);
        echo "📊 Executed $seedCount seed statements.\n";
        if (!empty($seedErrors)) {
            echo "\n⚠️ Seed errors:\n";
            foreach ($seedErrors as $error) {
                echo "  - $error\n";
            }
        }
    } else {
        echo "\n⚠️ No seed file found: $seedFile\n";
    }

    echo "\n✅ Database setup complete!\n";

    echo "\n📋 Verifying tables:\n";
    $tables = ['admin', 'users', 'categories', 'items', 'orders', 'homepage'];
    $allExist = true;
    foreach ($tables as $table) {
        $result = fetchOne("SELECT 1 FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '$table'");
        $status = $result ? '✅' : '❌';
        echo "  - $table: $status\n";
        if (!$result) {
            $allExist = false;
        }
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
