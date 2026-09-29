<?php
/**
 * Diwebs Tech Agency — Database Permissions & Diagnostics Tool
 * ==========================================================
 * Place this in public/fix_database_permissions.php and visit:
 * https://diwebstechagency.website/fix_database_permissions.php
 * DELETE THIS FILE after use!
 */

header('Content-Type: text/html; charset=utf-8');

$root = dirname(__DIR__);
$dbDir = $root . '/database';
$dbFile = $dbDir . '/database.sqlite';
$storageDir = $root . '/storage';

$errors = [];
$successes = [];

// Parse .env connection configuration
$envPath = $root . '/.env';
$dbConnection = 'sqlite';
$dbHost = '127.0.0.1';
$dbPort = '3306';
$dbDatabase = '';
$dbUsername = '';
$dbPassword = '';

if (file_exists($envPath)) {
    $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1], " \t\n\r\0\x0B\"'");
            if ($key === 'DB_CONNECTION') $dbConnection = $val;
            if ($key === 'DB_HOST') $dbHost = $val;
            if ($key === 'DB_PORT') $dbPort = $val;
            if ($key === 'DB_DATABASE') $dbDatabase = $val;
            if ($key === 'DB_USERNAME') $dbUsername = $val;
            if ($key === 'DB_PASSWORD') $dbPassword = $val;
        }
    }
}

// Helper function to set write permissions
function makeWritable($path) {
    if (!file_exists($path)) {
        return false;
    }
    // Attempt standard PHP permissions change
    @chmod($path, is_dir($path) ? 0777 : 0666);
    return is_writable($path);
}

// 1. Check & Fix Database Folder
if (!file_exists($dbDir)) {
    if (@mkdir($dbDir, 0777, true)) {
        $successes[] = "Created database directory: " . str_replace($root, '', $dbDir);
    } else {
        if ($dbConnection === 'sqlite') {
            $errors[] = "Database directory does not exist and could not be created: " . $dbDir;
        } else {
            $successes[] = "Database directory does not exist (not required for MySQL, but recommended): " . str_replace($root, '', $dbDir);
        }
    }
} else {
    if (makeWritable($dbDir)) {
        $successes[] = "Database directory is writable ✔";
    } else {
        if ($dbConnection === 'sqlite') {
            $errors[] = "Database directory is NOT writable by the web server: " . str_replace($root, '', $dbDir) . ". SQLite requires the containing directory to be writable to manage transaction journals.";
        } else {
            $successes[] = "Database directory is not writable (not required for MySQL) ✔";
        }
    }
}

// 2. Check & Fix Database File
if ($dbConnection === 'sqlite') {
    if (!file_exists($dbFile)) {
        if (@touch($dbFile)) {
            makeWritable($dbFile);
            $successes[] = "Created database file: " . str_replace($root, '', $dbFile);
        } else {
            $errors[] = "Database file does not exist and could not be created: " . $dbFile;
        }
    } else {
        if (makeWritable($dbFile)) {
            $successes[] = "Database file is writable ✔";
        } else {
            $errors[] = "Database file is NOT writable by the web server: " . str_replace($root, '', $dbFile);
        }
    }
} else {
    $successes[] = "SQLite database file check skipped (MySQL connection active: '{$dbDatabase}') ✔";
}

// 3. Check & Fix Storage Folder
if (file_exists($storageDir)) {
    if (makeWritable($storageDir)) {
        $successes[] = "Storage directory is writable ✔";
    } else {
        $errors[] = "Storage directory is NOT writable: " . str_replace($root, '', $storageDir);
    }
    // Recursively check subdirectories
    $subDirs = ['app', 'framework', 'framework/cache', 'framework/sessions', 'framework/views', 'logs'];
    foreach ($subDirs as $sub) {
        $fullSub = $storageDir . '/' . $sub;
        if (file_exists($fullSub)) {
            if (makeWritable($fullSub)) {
                $successes[] = "Storage subfolder '$sub' is writable ✔";
            } else {
                $errors[] = "Storage subfolder '$sub' is NOT writable: " . str_replace($root, '', $fullSub);
            }
        }
    }
}

// 4. Try basic database check
$dbConnectionSuccess = false;
$dbConnectionError = '';
if ($dbConnection === 'mysql') {
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
        $db = new PDO($dsn, $dbUsername, $dbPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $dbConnectionSuccess = true;
        $successes[] = "MySQL Connection check: SUCCESS ✔ (" . count($tables) . " tables found on MySQL database '{$dbDatabase}')";
    } catch (\Exception $e) {
        $dbConnectionError = $e->getMessage();
        $errors[] = "MySQL Connection check: FAILED — " . $e->getMessage() . " (Verify MySQL credentials in your .env file)";
    }
} else {
    try {
        $db = new PDO("sqlite:" . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Try a test query
        $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $dbConnectionSuccess = true;
        $successes[] = "SQLite Connection check: SUCCESS ✔ (" . count($tables) . " tables found)";
    } catch (\Exception $e) {
        $dbConnectionError = $e->getMessage();
        $errors[] = "SQLite Connection check: FAILED — " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Database Permissions Diagnostic — Diwebs Tech</title>
    <style>
        body { font-family: monospace; background: #0f0f0f; color: #e0e0e0; padding: 30px; max-width: 900px; margin: 0 auto; }
        h1 { color: #58a6ff; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .ok { color: #6ee7b7; background: #0d2818; border-left: 3px solid #3fb950; padding: 10px; margin: 8px 0; border-radius: 4px; }
        .fail { color: #f87171; background: #2d0c0c; border-left: 3px solid #f85149; padding: 10px; margin: 8px 0; border-radius: 4px; }
        .info { color: #fbbf24; background: #2d1f00; border-left: 3px solid #d29922; padding: 10px; margin: 8px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🔧 Database Permissions & Diagnostics</h1>

    <?php if (empty($errors)): ?>
        <div class="ok" style="font-size: 16px; font-weight: bold; margin-bottom: 20px;">
            🎉 All checks passed! The web server has full read/write permissions for the SQLite database.
        </div>
    <?php else: ?>
        <div class="fail" style="font-size: 16px; font-weight: bold; margin-bottom: 20px;">
            ❌ Issues detected: <?= count($errors) ?> permission errors found.
        </div>
    <?php endif; ?>

    <h2>📋 Diagnostic Checklist</h2>
    
    <?php foreach ($errors as $err): ?>
        <div class="fail">✘ <?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <?php foreach ($successes as $suc): ?>
        <div class="ok">✔ <?= htmlspecialchars($suc) ?></div>
    <?php endforeach; ?>

    <h2>⚙️ Windows Permissions Troubleshooting Guide</h2>
    <div class="info" style="line-height: 1.6;">
        If you see permissions errors, follow these steps in Windows to grant access:<br><br>
        1. Open Windows File Explorer and navigate to: <code><?= htmlspecialchars($root) ?></code><br>
        2. Right-click the <strong>database</strong> folder and select <strong>Properties</strong>.<br>
        3. Go to the <strong>Security</strong> tab and click <strong>Edit</strong>.<br>
        4. Select the user matching your web server (e.g. <code>IUSR</code>, <code>IIS_IUSRS</code>, or <code>Users</code>).<br>
        5. Check the box for <strong>Full Control</strong> or <strong>Write</strong> and click <strong>Apply</strong>.<br>
        6. Do the exact same steps for the <strong>database.sqlite</strong> file inside the folder.<br>
        7. Refresh this page to run the check again.
    </div>

    <div style="margin-top: 30px; font-size: 11px; color: #888;">
        ⚠️ <strong>Security Warning:</strong> Delete this file (<code>public/fix_database_permissions.php</code>) immediately after use to prevent server configuration disclosure.
    </div>
</body>
</html>
