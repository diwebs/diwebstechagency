<?php
/**
 * Diwebs Tech Agency — Portfolio Image Link Repair Tool
 * ====================================================
 * Place this in public/fix_portfolio_images.php and visit:
 * https://diwebstechagency.website/fix_portfolio_images.php
 * DELETE THIS FILE after use!
 */

header('Content-Type: text/html; charset=utf-8');

// 1. Boot Laravel
$laravelBooted = false;
$laravelRoot = dirname(__DIR__);
$autoload = $laravelRoot . '/vendor/autoload.php';
$appFile = $laravelRoot . '/bootstrap/app.php';
$laravelError = '';

if (file_exists($autoload) && file_exists($appFile)) {
    try {
        require_once $autoload;
        $app = require_once $appFile;
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $laravelBooted = true;
    } catch (\Throwable $e) {
        $laravelError = $e->getMessage();
    }
}

// 2. Fix the symlink
$symlinkFixed = false;
$symlinkMsg = '';
$publicStoragePath = __DIR__ . '/storage';

// Try to clean up existing path
if (file_exists($publicStoragePath) || is_link($publicStoragePath) || is_dir($publicStoragePath)) {
    // If it is Windows and not a directory (or if permission is denied), try using shell to delete it safely.
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        @exec("rd /s /q \"" . $publicStoragePath . "\"");
        if (file_exists($publicStoragePath)) {
            @unlink($publicStoragePath);
        }
        if (file_exists($publicStoragePath)) {
            @rmdir($publicStoragePath);
        }
    } else {
        @unlink($publicStoragePath);
        if (is_dir($publicStoragePath)) {
            @rmdir($publicStoragePath);
        }
    }
}

// Recreate symlink
$target = $laravelRoot . '/storage/app/public';
if (file_exists($publicStoragePath)) {
    $symlinkMsg = "Could not delete existing public/storage link/folder. Please delete it manually in File Manager.";
} else {
    // Try PHP symlink
    if (@symlink($target, $publicStoragePath)) {
        $symlinkFixed = true;
        $symlinkMsg = "Created symbolic link successfully via PHP symlink() ✔";
    } else {
        // Try shell command
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows Junction
            $output = [];
            $resultCode = 0;
            exec("mklink /J \"" . $publicStoragePath . "\" \"" . $target . "\"", $output, $resultCode);
            if ($resultCode === 0 || file_exists($publicStoragePath)) {
                $symlinkFixed = true;
                $symlinkMsg = "Created Windows Junction link successfully via mklink /J ✔";
            } else {
                // Try Artisan
                if ($laravelBooted) {
                    try {
                        $exitCode = Illuminate\Support\Facades\Artisan::call('storage:link');
                        if (file_exists($publicStoragePath)) {
                            $symlinkFixed = true;
                            $symlinkMsg = "Created symbolic link via php artisan storage:link ✔";
                        } else {
                            $symlinkMsg = "Artisan storage:link exited with code $exitCode but public/storage link is still missing.";
                        }
                    } catch (\Throwable $ex) {
                        $symlinkMsg = "Failed creating junction and Artisan failed: " . $ex->getMessage();
                    }
                } else {
                    $symlinkMsg = "Failed creating junction. Laravel not booted. Command output: " . implode("\n", $output);
                }
            }
        } else {
            // Linux symlink
            exec("ln -s \"" . $target . "\" \"" . $publicStoragePath . "\"");
            if (file_exists($publicStoragePath)) {
                $symlinkFixed = true;
                $symlinkMsg = "Created Linux symbolic link successfully via ln -s ✔";
            } else {
                $symlinkMsg = "Failed creating Linux symbolic link.";
            }
        }
    }
}

// 3. Fix database portfolio images
$dbFixed = false;
$dbMsg = '';
$seededCount = 0;
$fixedCount = 0;
$records = [];

if ($laravelBooted) {
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('portfolios')) {
            $portfolios = \App\Models\Portfolio::all();
            
            if ($portfolios->isEmpty()) {
                // Seed default portfolios since table is empty
                $availableImages = [];
                $dir = $laravelRoot . '/storage/app/public/portfolios';
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..' && preg_match('/\.(png|jpe?g|gif|webp|svg)$/i', $file)) {
                            $availableImages[] = 'portfolios/' . $file;
                        }
                    }
                }
                
                // Defaults
                $defaultPortfolios = [
                    [
                        'title' => 'Federal CBT Infrastructure Portal',
                        'description' => "A high-security, distributed computer-based testing infrastructure designed for national examination bodies. Features remote seat sync, live proctoring dashboard, custom lock-down browser integrations, and webcam log auditing.",
                        'project_url' => 'https://cbt.diwebstechagency.website',
                        'order' => 1,
                        'mock_image' => isset($availableImages[0]) ? $availableImages[0] : null,
                    ],
                    [
                        'title' => 'Academy LMS Portal',
                        'description' => "A comprehensive learning management system featuring live virtual classes, interactive audio-learning playlists, student progress tracking, automated certificates generation, and an AI-powered tutor.",
                        'project_url' => 'https://academy.diwebstechagency.website',
                        'order' => 2,
                        'mock_image' => isset($availableImages[1]) ? $availableImages[1] : (isset($availableImages[0]) ? $availableImages[0] : null),
                    ],
                    [
                        'title' => 'CRM Leads & Portal Sync',
                        'description' => "An enterprise customer relationship management dashboard that synchronizes client milestone requests with automated invoice dispatch, team invitations, and real-time support ticket ticketing.",
                        'project_url' => 'https://portal.diwebstechagency.website',
                        'order' => 3,
                        'mock_image' => isset($availableImages[2]) ? $availableImages[2] : (isset($availableImages[0]) ? $availableImages[0] : null),
                    ]
                ];
                
                foreach ($defaultPortfolios as $p) {
                    \App\Models\Portfolio::create($p);
                    $seededCount++;
                }
                $dbMsg = "Database portfolios table was empty. Seeded $seededCount default portfolio records ✔";
                $dbFixed = true;
            } else {
                // Fix existing paths
                foreach ($portfolios as $portfolio) {
                    $img = $portfolio->mock_image;
                    if ($img) {
                        // Check if it has incorrect prefixes
                        $original = $img;
                        // Strip leading slashes, storage/ prefixes, public/ prefixes, or absolute urls if applicable
                        $cleaned = preg_replace('/^(\/?storage\/|\/?public\/storage\/)/i', '', $img);
                        // Make sure it doesn't start with portfolios/portfolios/
                        if (strpos($cleaned, 'portfolios/') !== 0 && !empty($cleaned) && !preg_match('/^https?:\/\//i', $cleaned)) {
                            // If it's just the filename, prefix with portfolios/
                            $cleaned = 'portfolios/' . ltrim($cleaned, '/');
                        }
                        
                        if ($cleaned !== $original) {
                            $portfolio->update(['mock_image' => $cleaned]);
                            $fixedCount++;
                        }
                    }
                }
                $dbMsg = "Checked existing portfolios. Cleaned up image path prefixes for $fixedCount records ✔";
                $dbFixed = true;
            }
            // Fetch updated records for display
            $records = \App\Models\Portfolio::orderBy('order', 'asc')->get()->toArray();
        } else {
            $dbMsg = "Table 'portfolios' does not exist in the database. Run migrations first.";
        }
    } catch (\Throwable $e) {
        $dbMsg = "Error updating database: " . $e->getMessage();
    }
} else {
    $dbMsg = "Laravel could not be booted to scan/fix database records. Error: " . ($laravelError ?? 'Unknown boot failure');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Portfolio Image Repair — Diwebs Tech Agency</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0d1117;
            color: #c9d1d9;
            padding: 40px 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            color: #58a6ff;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid #30363d;
            padding-bottom: 10px;
        }
        .card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .success {
            border-left: 4px solid #3fb950;
        }
        .error {
            border-left: 4px solid #f85149;
        }
        .status-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-text {
            font-size: 14px;
            color: #8b949e;
            white-space: pre-wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #30363d;
        }
        th {
            background: #21262d;
            color: #f0f6fc;
        }
        img {
            max-height: 40px;
            border-radius: 4px;
            border: 1px solid #30363d;
        }
        .btn {
            display: inline-block;
            background: #21262d;
            border: 1px solid #30363d;
            color: #c9d1d9;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #30363d;
        }
        .btn-primary {
            background: #238636;
            border-color: #2ea44f;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #2ea44f;
        }
    </style>
</head>
<body>

    <h1>🔧 Diwebs Tech Agency — Portfolio Image Link Repair</h1>

    <div class="card <?= $symlinkFixed ? 'success' : 'error' ?>">
        <div class="status-title">
            <span><?= $symlinkFixed ? '✅' : '❌' ?></span>
            <span>Public Storage Symlink Status</span>
        </div>
        <div class="status-text"><?= htmlspecialchars($symlinkMsg) ?></div>
    </div>

    <div class="card <?= $dbFixed ? 'success' : 'error' ?>">
        <div class="status-title">
            <span><?= $dbFixed ? '✅' : '❌' ?></span>
            <span>Database Portfolios Scan Status</span>
        </div>
        <div class="status-text"><?= htmlspecialchars($dbMsg) ?></div>
    </div>

    <?php if ($laravelBooted && !empty($records)): ?>
    <div class="card">
        <div class="status-title">📂 Current Portfolios in Database</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image Path (in DB)</th>
                    <th>Preview</th>
                    <th>Project URL</th>
                    <th>Order</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                    <td><code><?= htmlspecialchars($r['mock_image']) ?></code></td>
                    <td>
                        <?php if ($r['mock_image']): ?>
                            <img src="/storage/<?= htmlspecialchars(preg_replace('/^(\/?storage\/|\/?public\/storage\/)/i', '', $r['mock_image'])) ?>" alt="Preview">
                        <?php else: ?>
                            <em>No Image</em>
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= htmlspecialchars($r['project_url']) ?>" target="_blank" style="color: #58a6ff;"><?= htmlspecialchars($r['project_url']) ?></a></td>
                    <td><?= $r['order'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div style="margin-top: 30px; display: flex; gap: 15px; align-items: center;">
        <a href="/portfolio" target="_blank" class="btn btn-primary">🔗 View Portfolio Page</a>
        <a href="/admin/portfolios" target="_blank" class="btn">⚙️ Admin Dashboard</a>
    </div>

    <div style="margin-top: 40px; color: #8b949e; font-size: 11px; border-top: 1px solid #21262d; padding-top: 15px;">
        ⚠️ <strong>Security Warning:</strong> Delete this file (<code>public/fix_portfolio_images.php</code>) immediately after use to prevent unauthorized database updates or information exposure.
    </div>

</body>
</html>
