# Diwebs Tech Agency — Deployment Guide

> **Platform**: Laravel 11 | **Domain**: diwebstechagency.website  
> **Contact**: info.diwebs@gmail.com | **Compliance**: compliance@diwebstechagency.website  
> **Phone**: +234 9064130817

---

## 📋 Table of Contents

1. [System Requirements](#system-requirements)
2. [Shared Hosting Deployment — All in public_html](#shared-hosting-deployment--all-in-public_html)
3. [Database Setup](#database-setup)
4. [Installation Wizard](#installation-wizard)
5. [Mail Configuration](#mail-configuration)
6. [Post-Deployment Checklist](#post-deployment-checklist)
7. [Admin Panel Access](#admin-panel-access)
8. [Referral System Configuration](#referral-system-configuration)
9. [Troubleshooting](#troubleshooting)

---

## ✅ System Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.2+ | 8.3+ |
| MySQL | 5.7+ | 8.0+ |
| Disk Space | 200 MB | 500 MB |
| Memory | 128 MB | 256 MB |
| PHP Extensions | BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO_MySQL, Tokenizer, XML | All listed |
| Writable Paths | `storage/`, `bootstrap/cache/` | All listed |

---

## 🚀 Shared Hosting Deployment — All in `public_html`

This is the **simplest method** for cPanel shared hosting. Everything goes directly inside `public_html/`.

### Final Directory Structure

```
public_html/
    ├── index.php          ← Entry point (already configured)
    ├── .htaccess          ← Security + routing rules
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env               ← Your environment config
    ├── artisan
    └── public/            ← Original public/ folder (CSS, JS, images)
```

> **Security Note**: The included `.htaccess` blocks all direct browser access to sensitive folders (`app/`, `config/`, `.env`, `storage/`, `vendor/`, etc.). Only `public/` assets and `index.php` are accessible to visitors.

---

### Step 1 — Upload Files

1. **Login to cPanel** → File Manager, or use FTP (FileZilla).
2. Navigate to `public_html/`.
3. **Upload** the `diwebstechagency_deploy.zip` file to `public_html/`.
4. **Extract** the zip — all files will land inside `public_html/`.
5. **Done** — no file restructuring needed.

> If your host extracted into a subfolder like `public_html/diwebstechagency/`, move all files **up one level** into `public_html/` directly using File Manager's "Move" option.

---

### Step 2 — Verify `.htaccess` Security Rules

Your `public_html/.htaccess` must contain the security rules to protect Laravel internals.  
The file is already included in the deployment package and should look like this:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Serve existing files and directories directly
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # Route all requests through index.php
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Block access to sensitive files and folders
<FilesMatch "^\.env|^composer\.(json|lock)$|^artisan$|^phpunit\.xml$">
    Order allow,deny
    Deny from all
</FilesMatch>

<IfModule mod_rewrite.c>
    RewriteRule ^(app|bootstrap|config|database|resources|routes|storage|vendor|tests)(/.*)?$ - [F,L]
</IfModule>
```

If `mod_rewrite` is not enabled on your host, contact your hosting provider or enable it via cPanel → Apache Handlers.

---

### Step 3 — Set File Permissions

In **cPanel File Manager**, right-click each folder and set permissions:

| Path | Permission |
|---|---|
| `storage/` (and all sub-folders) | `755` or `775` |
| `bootstrap/cache/` | `755` or `775` |
| `.env` | `644` |

Via SSH:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env
```

---

### Step 4 — Verify `index.php`

Open `public_html/index.php`. It should already be correctly configured to load from the same directory:

```php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

No changes needed — it works as-is when all files are in `public_html/`.

---

## 🗄️ Database Setup

### MySQL (Recommended for Shared Hosting)

1. In **cPanel → MySQL Databases**:
   - Create a new database: e.g. `youraccount_diwebs`
   - Create a new user with a strong password
   - Add the user to the database with **ALL PRIVILEGES**

2. Note your credentials:
   - **Host**: `localhost`
   - **Database**: `youraccount_diwebs`
   - **Username**: `youraccount_dbuser`
   - **Password**: your chosen password

---

## 🧙 Installation Wizard

After uploading files and creating the database, visit the **Installation Wizard** — it configures everything automatically.

### Access the Wizard

Navigate to: **`https://yourdomain.website/install`**

> **Security Note**: The wizard is **automatically disabled** after installation completes and cannot be re-accessed.

### Step 1 — Database Configuration

- Select **MySQL**
- Enter host (`localhost`), port (`3306`), database name, username, and password
- Click **"Test & Configure Database"**
- The wizard tests the connection and writes settings to `.env`

### Step 2 — Admin Account Setup

- Enter your **Full Name**, **Email**, and a **strong password** (minimum 12 characters)
- Click **"Complete Installation"**
- The system will:
  - Run all database migrations
  - Seed default settings
  - Create your admin account
  - Lock the installer permanently

### Step 3 — Login

After installation completes, go to the hidden admin login gate:

- **Admin Login**: `https://yourdomain.website/secure-gate-admin`

---

## 📧 Mail Configuration

Update `.env` with your SMTP details for OTP codes and notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.website
MAIL_PORT=465
MAIL_USERNAME=noreply@diwebstechagency.website
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS="noreply@diwebstechagency.website"
MAIL_FROM_NAME="Diwebs Tech Agency"
```

For **cPanel Email**:
- Host: `mail.yourdomain.website`
- Port: `465` (SSL) or `587` (TLS)
- Username: full email address (e.g. `noreply@diwebstechagency.website`)

---

## ✔️ Post-Deployment Checklist

### Via cPanel Terminal or SSH

```bash
# Navigate to your public_html (where all files are)
cd /home/youraccount/public_html

# Generate application key if not set
php artisan key:generate

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage for file uploads
php artisan storage:link
```

### If No SSH Access — Use a PHP Runner

Create a **temporary** file `run_setup.php` in `public_html/`:

```php
<?php
// ⚠️ TEMPORARY — DELETE THIS FILE IMMEDIATELY AFTER USE ⚠️
$cmds = [
    'php artisan key:generate --force',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'php artisan storage:link',
];
foreach ($cmds as $cmd) {
    echo "<pre><b>$ $cmd</b>\n";
    echo shell_exec($cmd);
    echo "</pre>";
}
?>
```

Visit `https://yourdomain.website/run_setup.php` **once**, then **immediately delete** the file.

### Production Environment Settings

Edit `.env` and confirm:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
APP_URL=https://yourdomain.website
```

---

## 🔐 Admin Panel Access

| URL | Description |
|---|---|
| `https://yourdomain.website/secure-gate-admin` | **Hidden admin login gateway** |
| `https://yourdomain.website/admin` | Admin dashboard |
| `https://yourdomain.website/admin/settings` | General settings |
| `https://yourdomain.website/admin/payment-settings` | Currency & payment settings |
| `https://yourdomain.website/admin/referrals` | Referral system management |
| `https://yourdomain.website/admin/users` | User management |

> **Important**: Never share `/secure-gate-admin` URL publicly.

---

## 🎁 Referral System Configuration

1. Go to **Admin → Settings** → Referral Settings
2. Set the **Referral Bonus Amount** (default: ₦50.00)
3. Configure whether bonuses are auto-approved or manually approved

### How It Works

- Every registered user automatically gets a **unique referral code**
- Users share their code via **Client Portal → Referral Program** tab
- When a new user registers with a referral code, a **pending referral record** is created
- Admin can **approve and mark bonuses as paid** from `/admin/referrals`
- Clients track referral status and earnings in their dashboard

---

## 🔧 Troubleshooting

### "500 Server Error" on first load

1. Check `.env` exists and `APP_KEY` is set (`php artisan key:generate`)
2. Check `storage/` and `bootstrap/cache/` are writable (chmod 775)
3. Check PHP version is 8.2+ (`php -v`)
4. Review `storage/logs/laravel.log`

### "The page isn't working" / Blank White Screen

1. Temporarily set `APP_DEBUG=true` in `.env`
2. Reload the page — you'll see the actual error
3. Fix the issue, then **immediately set** `APP_DEBUG=false`

### Database Connection Errors

1. Verify credentials in `.env`
2. Ensure the database user has ALL PRIVILEGES
3. Use `localhost` as the host (not an IP) on cPanel
4. Test: `php artisan db:show`

### Sensitive Files Exposed (403/forbidden not working)

Ensure `mod_rewrite` is enabled. In cPanel go to:
**Software → Apache Handlers** → check that `.htaccess` is being processed.

### "Class not found" Errors

```bash
composer dump-autoload
php artisan config:clear
```

### Installation Wizard Shows 404

The wizard has already been completed — `storage/installed` lock file exists.  
To reset **(development only)**: delete `storage/installed` then clear caches.

### Mail / OTP Not Sending

1. Configure SMTP in `.env`
2. During development, OTP codes are logged to `storage/logs/laravel.log`
3. Test via tinker: `Mail::raw('test', fn($m) => $m->to('you@test.com')->subject('test'));`

---

## 📁 Important File Locations

| File / Folder | Purpose |
|---|---|
| `.env` | Environment configuration |
| `storage/installed` | Installation lock file — delete to re-run installer |
| `storage/logs/laravel.log` | Application error logs |
| `storage/app/` | Uploaded files |
| `public/` | Public assets (CSS, JS, images) — accessed via browser |
| `database/database.sqlite` | SQLite database (if using SQLite mode) |

---

## 🔄 Updating the Application

1. Backup `.env` and `storage/app/`
2. Upload new files (overwrite everything except `.env`)
3. Run: `php artisan migrate --force`
4. Run: `php artisan optimize`

---

## 📞 Support

- **Email**: info.diwebs@gmail.com
- **Compliance**: compliance@diwebstechagency.website
- **Phone**: +234 9064130817
- **Website**: https://diwebstechagency.website

---

*Diwebs Tech Agency © 2025. All Rights Reserved.*
