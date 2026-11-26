# Putting Xmenu live on xmenu.xvalue.et

## Addresses

| Who | Address |
|---|---|
| Everyone (landing page) | `https://xmenu.xvalue.et` |
| A café's customer menu (what the QR opens) | `https://xmenu.xvalue.et/{cafe-link}`, e.g. `/buna-house` |
| Café owners | `https://xmenu.xvalue.et/dashboard` (one address for all owners; each sees only their own café) |
| Super admin | `https://xmenu.xvalue.et/admin` |

## Server requirements

- PHP 8.2+ with: `gd` (WebP support), `exif`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, **OPcache on**
- MySQL 8 / MariaDB 10.6+
- Apache with `mod_rewrite`, `mod_headers`, `mod_deflate`, `mod_expires` (or Nginx with equivalent rules)
- HTTPS certificate for `xmenu.xvalue.et` (Let's Encrypt or Cloudflare)
- The web root must point to the **`public/`** folder, never the project folder.

## Production `.env`

```env
APP_NAME=Xmenu
APP_ENV=production
APP_DEBUG=false
APP_URL=https://xmenu.xvalue.et
APP_TIMEZONE=Africa/Addis_Ababa

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=xmenu
DB_USERNAME=xmenu          # a dedicated user, not root
DB_PASSWORD=...            # long random password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

CACHE_STORE=file           # use redis if installed: CACHE_STORE=redis
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
```

Generate a fresh key on the server: `php artisan key:generate`.

## First deploy

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force          # plans, games and the demo café (no test logins in production)
php artisan storage:link
php artisan xmenu:admin you@xvalue.et   # prints the super admin password once
php artisan optimize                 # caches config, routes and views
```

Folder permissions: the web server user must be able to write to `storage/` and `bootstrap/cache/`, and nothing else.

## Cron (once per minute)

```
* * * * * cd /path/to/xmenu && php artisan schedule:run >> /dev/null 2>&1
```

This marks ended trials as overdue every night and removes analytics older than 90 days.

## Every update

```bash
php artisan down
git pull   # or upload the new files
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

If you changed a game file, bump `VERSION` in `public/games/sw.js` so phones fetch the new copy.

## What is already done for security

- Owners can only read and change their own café (every edit is checked against the signed-in owner).
- Super admin "manage mode" edits a café without its password; every change is written to the audit log.
- Login is limited per address and per email (6 wrong tries locks that email for 15 minutes).
- Passwords are stored hashed (bcrypt); sessions are regenerated on sign-in.
- CSRF protection on every form; security headers (CSP, frame, sniffing, referrer, permissions, HSTS on HTTPS).
- Uploads are re-encoded to WebP by the server, and the upload folder can't run code.
- Hidden files (`.env`, `.git`) are blocked; HTTP is redirected to HTTPS on the live domain.
- `APP_DEBUG=false` shows friendly error pages instead of technical details.

## What is already done for speed

- Each café's menu page is built once and cached, then served as a ready file until the owner changes something.
- Customer menus and the analytics ping run without sessions or cookies, so a scan costs no session write.
- Photos are resized to WebP in 3 sizes; phones download only the size they need.
- CSS, JS and photos are cached by browsers for a year; text is gzip-compressed.
- Games are cached on the phone and work offline.

For thousands of scans per minute, put the site behind Cloudflare (free plan is fine) and switch `CACHE_STORE` to `redis`.
