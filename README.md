# Xmenu

Self-serve QR menus for cafés and restaurants. Owners sign up, build their menu, style it, and download print-ready QR codes. A super admin runs the SaaS: cafés, users, sales, analytics.

Built from `Café Digital Menu — Product Requirements Document.md`, changed to a **self-serve** model.

## Run it locally (XAMPP)

```bash
composer install
cp .env.example .env        # MySQL database "xmenu", root, no password
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve           # http://127.0.0.1:8000
```

Seeded accounts (change the passwords before going live):

| Who | Email | Password |
| --- | --- | --- |
| Super admin | admin@xmenu.test | password |
| Demo café owner | owner@xmenu.test | password |

Demo menu: http://127.0.0.1:8000/demo-cafe

In production, set `APP_URL` to the real domain *before* owners print QR codes (the codes encode it), and add the scheduler to cron: `* * * * * php artisan schedule:run`.

## What's where

| Area | URL | Code |
| --- | --- | --- |
| Landing page | `/` | `HomeController`, `views/home.blade.php` |
| Sign up / log in | `/register`, `/login` | `AuthController` |
| Customer menu | `/{slug}?t={table}` | `PublicMenuController`, `views/menu/show.blade.php` |
| Owner panel | `/dashboard/*` | `MenuBuilderController`, `CafeSettingsController`, `DesignController`, `QrController`, `BillingController` |
| Super admin | `/admin/*` | `Http/Controllers/Admin/*`, `views/admin/*` |
| Games | `/games/*/index.html` | `public/games` (self-hosted, no ads) |

### Owner panel
- **Menu**: categories and items (EN/Amharic names, prices, sizes, add-ons, tags, photos). Inline price edit and sold-out switch save instantly. Reorder, duplicate, CSV import with template.
- **Photos** are cropped and converted to WebP at 320/640/1024 px with GD (`ImageService`).
- **Design studio**: brand colour, layout (list/grid/cards), header (cover/solid/minimal), font, corners, light/dark, background, with a live phone preview.
- **QR studio**: 5 designs (Classic, Bold, Split, Minimal, Menu + Wi-Fi). A6/A5/A4, one per page or 4×A6 / 2×A5 on A4 with cut lines, one QR per café or per table (`1-12, VIP`). Print-ready PDF (dompdf) or PNG.
- **Café info, payments, games, plan & billing** (upload a payment receipt for admin approval), **settings**.

### Super admin
- **Overview**: MRR, revenue, sign-ups, scans, status counts, expiring trials.
- **Cafés**: search and filter, detail page with stats, plan/status/dates, private notes, extend trial, suspend menu, delete.
- **Manage content**: edit any café's menu from its own dashboard without its password. Every change is written to the audit log.
- **Users**: edit, make admin, reset password (shown once), suspend login, delete.
- **Sales**: approve or reject uploaded receipts, record cash/transfer payments, revenue by plan and method.
- **Plans & pricing**, **Analytics** (funnel, busy hours, top cafés, games, searches), **Game library**, **Audit log**, **Settings** (where cafés pay you, support contacts, trial length).

## Performance notes
- Customer menu HTML is cached per café and language (`Cache`, file store), and flushed automatically when the owner edits anything.
- CSS and JS are inline; no framework. About 60 KB HTML uncompressed for a 16-item menu (about 12 KB gzipped). Images lazy-load with `srcset`.

## Scheduled jobs
- `xmenu:subscriptions` (daily): ended trials and plans become **overdue**. Suspension stays a manual admin decision.
- `xmenu:prune-events` (daily): deletes analytics rows older than 90 days.

## Not built yet (from the PRD)
Staff roles, specials and announcements, multi-branch, feedback inbox, offline service worker, loyalty, online payment gateway, password reset by email/SMS (admins reset passwords for now).
