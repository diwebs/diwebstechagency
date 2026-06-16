<?php
/**
 * Diwebs Tech Agency — Server Diagnostic Tool
 * =============================================
 * Upload this file to public_html/public/diag.php
 * Visit: https://diwebstechagency.website/diag.php
 * DELETE THIS FILE immediately after reading the results!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<title>Server Diagnostic — Diwebs Tech Agency</title>
<style>
body { font-family: monospace; background: #0f0f0f; color: #e0e0e0; padding: 30px; max-width: 900px; margin: 0 auto; }
h2 { color: #6ee7b7; border-bottom: 1px solid #333; padding-bottom: 10px; }
.ok   { color: #6ee7b7; } /* green */
.fail { color: #f87171; } /* red */
.warn { color: #fbbf24; } /* yellow */
.row  { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1f1f1f; }
.label { color: #9ca3af; }
pre { background: #1a1a1a; padding: 15px; border-radius: 6px; overflow-x: auto; font-size: 13px; }
</style>
</head>
<body>

<h2>🔍 PHP & Server Info</h2>
<?php
$info = [
    'PHP Version'       => PHP_VERSION,
    'PHP SAPI'          => PHP_SAPI,
    'Operating System'  => PHP_OS,
    'Server Software'   => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'Document Root'     => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'Script Path'       => __FILE__,
    'Memory Limit'      => ini_get('memory_limit'),
    'Max Execution Time'=> ini_get('max_execution_time') . 's',
    'Upload Max Size'   => ini_get('upload_max_filesize'),
    'Post Max Size'     => ini_get('post_max_size'),
];
foreach ($info as $k => $v) {
    echo "<div class='row'><span class='label'>$k</span><span>$v</span></div>";
}
?>

<h2>✅ Required PHP Extensions</h2>
<?php
$required = ['bcmath','ctype','fileinfo','json','mbstring','openssl','pdo','pdo_mysql','pdo_sqlite','tokenizer','xml','curl','zip'];
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "<span class='ok'>✔ Loaded</span>" : "<span class='fail'>✘ MISSING</span>";
    echo "<div class='row'><span class='label'>$ext</span>$status</div>";
}
?>

<h2>📁 Laravel File Structure Check</h2>
<?php
// Our public/diag.php is at public_html/public/diag.php
// Laravel root is public_html/ (one level up from public/)
$root = dirname(__DIR__); // public_html/
$paths = [
    'vendor/autoload.php'         => $root . '/vendor/autoload.php',
    'bootstrap/app.php'           => $root . '/bootstrap/app.php',
    '.env file'                   => $root . '/.env',
    'storage/ (writable)'         => $root . '/storage',
    'bootstrap/cache/ (writable)' => $root . '/bootstrap/cache',
    'database/ folder'            => $root . '/database',
    'app/ folder'                 => $root . '/app',
];
foreach ($paths as $label => $path) {
    if (strpos($label, 'writable') !== false) {
        $ok = is_dir($path) && is_writable($path);
        $status = $ok ? "<span class='ok'>✔ Exists & Writable</span>" : "<span class='fail'>✘ NOT WRITABLE or missing</span>";
    } elseif (is_file($path)) {
        $status = "<span class='ok'>✔ Found</span>";
    } elseif (is_dir($path)) {
        $count = count(scandir($path)) - 2;
        $status = "<span class='ok'>✔ Found ($count items)</span>";
    } else {
        $status = "<span class='fail'>✘ NOT FOUND — path: $path</span>";
    }
    echo "<div class='row'><span class='label'>$label</span>$status</div>";
}
?>

<h2>🔑 .env File Contents (safe fields only)</h2>
<?php
$envPath = $root . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $safe = [];
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        // Mask sensitive values
        if (preg_match('/^(DB_PASSWORD|MAIL_PASSWORD|APP_KEY|.*SECRET.*|.*API_KEY.*)=/i', $line)) {
            $key = explode('=', $line)[0];
            $safe[] = "$key=***HIDDEN***";
        } else {
            $safe[] = $line;
        }
    }
    echo "<pre>" . implode("\n", $safe) . "</pre>";
} else {
    echo "<p class='fail'>✘ .env file NOT FOUND at: $envPath</p>";
}
?>

<h2>📂 Vendor Autoloader Test</h2>
<?php
$autoload = $root . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "<p class='fail'>✘ vendor/autoload.php NOT FOUND. The vendor/ directory may not have been uploaded.</p>";
} else {
    try {
        require $autoload;
        echo "<p class='ok'>✔ vendor/autoload.php loaded successfully.</p>";
    } catch (Throwable $e) {
        echo "<p class='fail'>✘ Error loading autoloader: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h2>🌐 HTTP Routing Test</h2>
<?php
$testUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
echo "<p>Domain: <strong>$testUrl</strong></p>";
echo "<p>This file is being served correctly via HTTP. ✔</p>";
echo "<p>If <code>/install</code> times out but this page loads, the issue is in Laravel's bootstrap process (PHP version, missing extension, or .env error).</p>";
?>

<hr>
<p style="color:#6b7280;font-size:12px;">
    ⚠️ <strong>DELETE this file from your server immediately after reading these results!</strong><br>
    Path to delete: <code><?= htmlspecialchars(__FILE__) ?></code>
</p>
</body>
</html>
