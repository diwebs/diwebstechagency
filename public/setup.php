<?php
/**
 * Diwebs Tech Agency — Server Setup & Repair Tool
 * =================================================
 * Upload to: public_html/public/setup.php
 * Visit:     https://diwebstechagency.website/setup.php
 * DELETE IMMEDIATELY after use!
 */

$root = dirname(__DIR__); // public_html/ (Laravel root)
$errors = [];
$ok = [];
$warnings = [];

// ─── 1. Check PHP Version ───────────────────────────────────────
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $errors[] = "PHP version is " . PHP_VERSION . " — Laravel 11 requires PHP 8.2+. Change your PHP version in cPanel → MultiPHP Manager.";
} else {
    $ok[] = "PHP Version: " . PHP_VERSION . " ✔";
}

// ─── 2. Create Required storage/ Directories ────────────────────
$dirs = [
    $root . '/storage',
    $root . '/storage/app',
    $root . '/storage/app/public',
    $root . '/storage/framework',
    $root . '/storage/framework/cache',
    $root . '/storage/framework/cache/data',
    $root . '/storage/framework/sessions',
    $root . '/storage/framework/testing',
    $root . '/storage/framework/views',
    $root . '/storage/logs',
    $root . '/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            $ok[] = "Created: " . str_replace($root, '', $dir);
        } else {
            $errors[] = "Could not create: " . str_replace($root, '', $dir) . " — check parent folder permissions.";
        }
    } else {
        // Try to make writable if not already
        if (!is_writable($dir)) {
            chmod($dir, 0755);
        }
        if (is_writable($dir)) {
            $ok[] = "Writable: " . str_replace($root, '', $dir) . " ✔";
        } else {
            $errors[] = "NOT writable: " . str_replace($root, '', $dir) . " — set permission to 755 in File Manager.";
        }
    }
}

// ─── 3. Check .env Exists & Is Readable ─────────────────────────
$envPath = $root . '/.env';
if (!file_exists($envPath)) {
    $errors[] = ".env file MISSING at $envPath — upload it from the deploy package.";
} elseif (!is_readable($envPath)) {
    $errors[] = ".env file exists but is NOT READABLE — set permission to 644.";
    chmod($envPath, 0644);
} else {
    $ok[] = ".env file found and readable ✔";
    // Check APP_KEY
    $env = file_get_contents($envPath);
    if (!preg_match('/^APP_KEY=base64:.+/m', $env)) {
        $errors[] = "APP_KEY is missing or empty in .env — run 'php artisan key:generate' or the install wizard will handle it.";
    } else {
        $ok[] = "APP_KEY is set ✔";
    }
    // Check APP_DEBUG
    if (preg_match('/^APP_DEBUG=true/m', $env)) {
        $warnings[] = "APP_DEBUG=true — change to false after troubleshooting.";
    }
}

// ─── 4. Check vendor/autoload.php ───────────────────────────────
$autoload = $root . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    $errors[] = "vendor/autoload.php NOT FOUND — the vendor/ folder may not have been uploaded. Re-upload the full ZIP.";
} else {
    $ok[] = "vendor/autoload.php found ✔";
}

// ─── 5. Check bootstrap/app.php ─────────────────────────────────
$bootstrap = $root . '/bootstrap/app.php';
if (!file_exists($bootstrap)) {
    $errors[] = "bootstrap/app.php NOT FOUND — upload the full ZIP again.";
} else {
    $ok[] = "bootstrap/app.php found ✔";
}

// ─── 6. Check Required PHP Extensions ───────────────────────────
$required = ['bcmath','ctype','fileinfo','json','mbstring','openssl','pdo','pdo_mysql','tokenizer','xml'];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "PHP extension '$ext' is NOT loaded — switch to PHP 8.2+ in cPanel MultiPHP Manager (usually fixes all missing extensions).";
    }
}

// ─── 7. Try to Write a Test File (verify storage/logs writability) ──
$testLog = $root . '/storage/logs/laravel.log';
if (!file_exists($testLog)) {
    file_put_contents($testLog, '');
}
if (is_writable(dirname($testLog))) {
    $ok[] = "storage/logs/ is writable ✔";
} else {
    $errors[] = "storage/logs/ is NOT writable — set to 755 in File Manager.";
}

// ─── 8. Try Loading Laravel ─────────────────────────────────────
$laravelOk = false;
$laravelError = '';
if (file_exists($autoload) && file_exists($envPath)) {
    try {
        require_once $autoload;
        $laravelOk = true;
        $ok[] = "Composer autoloader loaded successfully ✔";
    } catch (Throwable $e) {
        $laravelError = $e->getMessage();
        $errors[] = "Autoloader error: " . $e->getMessage();
    }
}

// ─── OUTPUT ─────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Setup & Repair — Diwebs Tech Agency</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0d1117;color:#c9d1d9;padding:30px;max-width:800px;margin:0 auto}
h1{color:#58a6ff;margin-bottom:5px;font-size:22px}
.subtitle{color:#8b949e;margin-bottom:30px;font-size:14px}
h2{color:#f0f6fc;font-size:16px;margin:25px 0 10px;padding-bottom:8px;border-bottom:1px solid #30363d}
.item{padding:8px 12px;border-radius:6px;margin:4px 0;font-size:14px;display:flex;align-items:flex-start;gap:8px}
.ok{background:#0d2818;border-left:3px solid #3fb950}
.error{background:#2d0c0c;border-left:3px solid #f85149}
.warn{background:#2d1f00;border-left:3px solid #d29922}
.icon{flex-shrink:0;font-size:16px}
.summary{padding:20px;border-radius:8px;margin:25px 0;text-align:center;font-size:18px;font-weight:bold}
.summary.pass{background:#0d2818;border:1px solid #3fb950;color:#3fb950}
.summary.fail{background:#2d0c0c;border:1px solid #f85149;color:#f85149}
.next{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:20px;margin-top:25px}
.next h2{border:none;margin-top:0}
.next ol{padding-left:20px;line-height:2}
.next a{color:#58a6ff}
code{background:#21262d;padding:2px 6px;border-radius:4px;font-size:13px}
.warn-box{background:#161205;border:1px solid #d29922;border-radius:6px;padding:12px 16px;margin-top:20px;font-size:13px;color:#d29922}
</style>
</head>
<body>

<h1>🔧 Diwebs Tech Agency — Setup & Repair</h1>
<p class="subtitle">PHP <?= PHP_VERSION ?> | Server: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?> | <?= date('Y-m-d H:i:s') ?></p>

<?php if (empty($errors)): ?>
<div class="summary pass">✅ All checks passed! Your server is ready.</div>
<?php else: ?>
<div class="summary fail">❌ <?= count($errors) ?> issue(s) found — see details below</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<h2>❌ Issues Found (fix these)</h2>
<?php foreach ($errors as $e): ?>
<div class="item error"><span class="icon">✘</span><span><?= htmlspecialchars($e) ?></span></div>
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($warnings)): ?>
<h2>⚠️ Warnings</h2>
<?php foreach ($warnings as $w): ?>
<div class="item warn"><span class="icon">⚠</span><span><?= htmlspecialchars($w) ?></span></div>
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($ok)): ?>
<h2>✅ Passed Checks</h2>
<?php foreach ($ok as $o): ?>
<div class="item ok"><span class="icon">✔</span><span><?= htmlspecialchars($o) ?></span></div>
<?php endforeach; ?>
<?php endif; ?>

<?php if (empty($errors)): ?>
<div class="next">
    <h2>🚀 Next Steps</h2>
    <ol>
        <li>Visit the installer: <a href="/install" target="_blank">https://diwebstechagency.website/install</a></li>
        <li>Configure your MySQL database credentials</li>
        <li>Create your admin account</li>
        <li><strong>Delete this file immediately:</strong> <code>public_html/public/setup.php</code></li>
    </ol>
</div>
<?php else: ?>
<div class="next">
    <h2>📋 How to Fix</h2>
    <ol>
        <li>Fix all <strong>red issues</strong> listed above</li>
        <li>Refresh this page to re-run all checks</li>
        <li>Once all green, visit <a href="/install">/install</a></li>
        <li><strong>Delete this file:</strong> <code>public_html/public/setup.php</code></li>
    </ol>
</div>
<?php endif; ?>

<div class="warn-box">
    ⚠️ <strong>Security Warning:</strong> Delete <code>public_html/public/setup.php</code> from your server immediately after fixing all issues. This file exposes server information.
</div>

</body>
</html>
