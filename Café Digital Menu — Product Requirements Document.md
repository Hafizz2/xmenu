# Café Digital Menu — Product Requirements Document

Nov 24, 2026 · @Hafiz

## Overview

We are building a QR-code digital menu that opens in under 2 seconds on a cheap Android phone over mobile data, and gives customers something fun to do while they wait. Version 1 does no ordering and takes no payments. It shows the menu, the café's details and how to pay, plus a set of 2-player games. Loyalty comes in a later release.

**Problem.** Printed menus in Addis cafés go out of date, wear out and cannot show photos. Prices change often, so owners cross out prices by hand or reprint. Customers wait 10–20 minutes for food with nothing to do, and often ask staff for the Wi-Fi password, bank account or Telebirr number.

**Vision.** Become the default menu layer for cafés and restaurants in Ethiopia. Start with the menu and in-café entertainment. Grow into loyalty, promotions and customer insight for owners.

**Goals for v1**

- A café can go live in under 30 minutes, from sign-up to printed QR codes on tables.
- Menu page loads in under 2 seconds on a 3G connection, with a first-load size under 150 KB before images.
- Owners can change a price or mark an item sold out from their phone in under 10 seconds.
- At least 1 in 5 menu visitors open a game.

**Non-goals for v1**

- Ordering, carts, call-waiter or kitchen tickets. Our earlier design had these; they are left out so v1 ships faster and does not compete with the café's POS. They can return as an add-on.
- Taking payments inside the app. We only display the café's accounts.
- Building our own games. We integrate games from other sources.
- A native app download. Customers use the browser only.

## Users and personas

The product serves three users. The customer is who we design for first; the owner is who pays.

| Persona | Who they are | What they need | What success looks like |
| --- | --- | --- | --- |
| Customer ("Selam") | Café visitor, 18–40, mid-range Android phone, mobile data or café Wi-Fi, reads Amharic and some English | See food with photos and real prices, filter by fasting or category, find how to pay, kill time with a friend | Scans, finds what to eat in under a minute, plays a game while waiting |
| Café owner or manager ("Dawit") | Runs 1–3 branches, busy, phone-first, not technical | Update prices and sold-out items fast, look modern, fewer staff questions, know what customers look at | Menu is always correct without reprinting; sees weekly scan numbers |
| Platform admin (us) | Our team | Onboard cafés, manage subscriptions, manage the game library, support | Cafés onboard without our help; low support load |

A fourth user, staff (waiter or cashier), gets a limited role in v1.1 so they can mark items sold out without access to settings.

## Scope by release

v1 ships the menu, café info and games. Each later release adds one reason for owners to keep paying. Move to the next release only once its gate is met.

| Release | What ships | Gate to move on |
| --- | --- | --- |
| v1 MVP | Menu with categories, items, photos, prices, variants. Fasting and dietary tags. Café info: phone, address, map, hours, Wi-Fi, socials. Payment info: bank accounts, Telebirr, CBE Birr with copy buttons. Amharic and English. Game hub with 6–10 two-player games. Owner panel. QR code generator. Basic scan analytics. | 5 paying cafés live; menu loads under 2 s on 3G |
| v1.1 | Staff role for sold-out toggles. Today's specials and happy hour. Announcements and events (live music, new items). Private customer feedback. Brand colour and logo themes. Multi-branch. Afaan Oromo. | 20 cafés live; under 5% monthly churn |
| v1.2 | Trivia and quiz rounds about the café and Ethiopia. Table-vs-table leaderboards. Owner analytics: top viewed items, peak hours, game use. | 2 in 5 visitors open a game |
| v2 Loyalty | Phone-number sign-in with SMS code. Digital stamp card (buy 9, get the 10th free). Points, rewards, birthday offers. Staff confirms a visit with a rotating code. | Pilot with 5 cafés shows repeat visits rise |
| v3 and later | Optional ordering or call-waiter add-on. Promotions network across cafés. In-app payment via Telebirr or Chapa. POS integrations. | Decided from v2 data |

## Customer-facing features

The customer sees one page per café, opened by QR at `/{cafe-slug}?t={table}`. It has four tabs at the bottom: Menu, Games, Info, Pay.

| Feature | Requirement | Release |
| --- | --- | --- |
| Café header | Logo, name, open or closed now, short tagline. Collapses on scroll. | v1 |
| Category bar | Sticky, horizontally scrolling chips (Breakfast, Coffee, Juice, Burgers…). Tapping scrolls to the section; the active chip follows scroll. | v1 |
| Item card | Photo (optional), name, short description, price in ETB, tags. Sold-out items greyed and moved to the end of their category. | v1 |
| Item detail sheet | Bottom sheet with large photo, full description, sizes or variants with prices (e.g. small, large), add-ons, tags. | v1 |
| Tags and filters | Fasting (ጤም), vegetarian, spicy, new, popular, chef's pick. One tap filters the whole menu. | v1 |
| Search | Instant client-side search over item names in both languages. | v1 |
| Language switch | Amharic and English; remembers the choice. Item falls back to the other language if a translation is missing. | v1 |
| Price display | Owner chooses whether prices include VAT and service charge; a footnote shows it. | v1 |
| Info tab | Phone with tap-to-call, address with Google Maps link, opening hours, Wi-Fi name and password with copy button, Telegram, Instagram, TikTok, Facebook links. | v1 |
| Pay tab | Bank accounts (bank, account name, number), Telebirr and CBE Birr numbers. Each has a copy button and bank logo. Text reminds customers to show the receipt to the cashier. | v1 |
| Games tab | Game hub; see Games integration. | v1 |
| Today's specials | Highlighted strip at the top of the menu, with optional time window (e.g. happy hour 4–6 pm). | v1.1 |
| Announcements | Banner or card for events: live music, new item, holiday hours. | v1.1 |
| Feedback | 1–5 stars plus a comment, sent privately to the owner. Not public. | v1.1 |
| Share | Share the café page or a single item to Telegram. | v1.1 |
| Offline repeat visits | Service worker caches the last menu so a second visit opens instantly, even on weak signal. | v1.1 |
| Loyalty card | Sign in by phone, see stamps and rewards. | v2 |

## Café admin panel

The owner panel is mobile-first too, because owners will edit from their phone behind the counter. The two most common jobs, changing a price and marking sold out, must take one screen and no page reload.

| Area | Requirement | Release |
| --- | --- | --- |
| Sign-up and onboarding | Sign up with phone or email. A 4-step wizard: café details, first category, first items, payment accounts. Shows a live preview and the QR at the end. | v1 |
| Menu builder | Create, edit, reorder (drag) categories and items. Item fields: names in each language, description, price, variants, add-ons, tags, photo, visible or hidden. Duplicate item. | v1 |
| Quick edit list | One list of all items with inline price field and sold-out switch. Saves on change. | v1 |
| Bulk import | Upload a CSV or Excel sheet of items and prices; we provide the template. | v1 |
| Photos | Upload from phone; server crops, compresses and makes WebP in 3 sizes. Max upload 5 MB. | v1 |
| Café info | Phone, address, map pin, hours per day, Wi-Fi, social links, VAT and service-charge setting. | v1 |
| Payment accounts | Add any number of bank, Telebirr and CBE Birr accounts; reorder; hide. | v1 |
| Games settings | Turn games on or off; pick which games show. | v1 |
| QR codes | Generate a QR per table (with table number) or one per café. Download as PNG or a print-ready PDF of table tents. | v1 |
| Basic stats | Scans per day, top 10 viewed items, game opens. | v1 |
| Staff accounts | Roles: owner (all), manager (menu + info), staff (sold-out only). | v1.1 |
| Specials and announcements | Schedule specials and banners with start and end times. | v1.1 |
| Theme | Brand colour, logo, cover photo; light or dark. Choice of 2–3 layouts (list, grid). | v1.1 |
| Multi-branch | One account, many branches; shared menu with per-branch prices and sold-out states. | v1.1 |
| Feedback inbox | Read and archive customer feedback. | v1.1 |
| Subscription | See plan, renewal date, pay via Telebirr or bank transfer with receipt upload. | v1 |

## Platform admin and business model

We charge cafés a monthly subscription per branch, with a free trial and a paid setup service. Prices below are placeholders to test in the first 3–4 café conversations, not decided.

**Super-admin panel (v1)**

- List of cafés with status (trial, active, overdue, suspended), plan, scans in the last 30 days.
- Approve manual payments: see the uploaded receipt, mark paid, extend the plan.
- Log in as a café to help with setup (logged for audit).
- Manage the game library: add a game, set its source URL, thumbnail, players, category, on or off for all.
- Global settings: bank logos, tag list, default translations.

**Plans (to validate)**

| Plan | For | Includes | Price idea |
| --- | --- | --- | --- |
| Trial | Any new café | Everything in Basic for 30 days | Free |
| Basic | Small café | Menu, info, pay, games, up to 10 table QRs, basic stats | ETB per month, to test |
| Pro | Busy café or restaurant | Basic plus staff roles, specials, themes, feedback, full stats | Higher monthly fee |
| Chain | 2+ branches | Pro per branch with a discount, central menu | Per branch |
| Setup service | Owners who won't do it themselves | We enter the menu, take photos, print table tents | One-time fee |

Loyalty in v2 becomes the main reason to upgrade to Pro.

## Games integration

We use existing HTML5 games, loaded only when a customer taps a game, so they never slow down the menu. Start with pass-and-play games on one phone, because two people at one table usually share a phone and it needs no server.

**Game types**

| Type | How it works | Examples | Release |
| --- | --- | --- | --- |
| Pass-and-play | Two players take turns on one phone | Tic-tac-toe, Connect Four, checkers, dots and boxes, memory match | v1 |
| Split-screen | Each player holds one end of the phone | Air hockey, reaction duels, tap races | v1 |
| Two phones | Players join the same room by code or table | Online chess, quiz battles | v1.2 |
| Solo | One player | 2048, puzzles, quick trivia | v1 |

**Where games come from**

1. Open-source HTML5 games with a licence that allows commercial use (MIT, Apache, BSD). We host the files on our server or CDN. This is the preferred source: fast, no third-party ads, works on café Wi-Fi.
2. Game distribution platforms that offer embeddable games (iframe). Fast to add, but many show their own ads and need their terms checked before we use them.
3. Licensed games bought from independent developers for a one-time fee, for flagship games.

**Requirements**

- Each game runs in a sandboxed iframe opened full screen, with our close button on top.
- The game hub page is under 30 KB; each game loads on tap with a loading bar.
- Every game in the library records: name, source, licence, URL, players (1 or 2), mode, size in KB, thumbnail, language.
- No game may show ads for other cafés, gambling or adult content. We review each game before it goes live.
- We log game opens and play time per café for owner stats.
- Games are on by default; owners can turn the whole hub or single games off.

## Design and UX

The design rule is: a customer who has never seen the product finds a dish and its price in under 10 seconds, with one thumb.

- **Mobile first.** Design for a 360 px wide screen first. Desktop shows the same layout centred, max 480 px wide.
- **Thumb zone.** Main navigation is a bottom tab bar (Menu, Games, Info, Pay). Tap targets at least 44 px.
- **Few screens.** The menu is one long page with sticky categories. Item details open as a bottom sheet, not a new page, so back is never confusing.
- **Readable.** Body text 16 px, prices bold and right-aligned. A font that renders Amharic well (e.g. Noto Sans Ethiopic) is loaded only when Amharic is on, and is subset to cut its size.
- **Photos optional.** The layout looks good with no photos, because many cafés will start without them. Cards without photos switch to a compact list.
- **Café's brand, our structure.** Owners set colour, logo and cover; layout, spacing and type stay ours so every menu stays fast and clean.
- **Light and dark.** Follows the phone setting; dark mode helps in dim cafés at night.
- **Accessible.** Colour contrast at WCAG AA, every image has alt text from the item name, works with large system font sizes.
- **Owner panel.** Same component style. Big buttons, plain words ("Sold out", "Change price"), Amharic interface option, no jargon.

## Technical architecture

We build a server-rendered Laravel + MySQL app with almost no JavaScript, and serve every menu page from cache. A customer's scan should never wait for a database query unless the owner has just edited the menu.

&#91;embedded content: system architecture · request path and edit path\]

When an owner saves a change, Laravel rebuilds that café's cached page and purges it at the CDN, so the next scan gets fresh prices.

**Stack**

| Layer | Choice | Why |
| --- | --- | --- |
| Backend | Laravel 11+ (PHP 8.3) | Your main stack; fast to build; queues, auth, storage built in |
| Database | MySQL 8 | Simple relational data; JSON columns for translations |
| Customer pages | Blade templates, server-rendered | No SPA framework; HTML arrives ready to show |
| Styling | Tailwind CSS, purged at build | Final CSS around 10–15 KB |
| JavaScript | Vanilla JS or Alpine.js (about 15 KB) | Only for category scroll, bottom sheet, search, copy buttons |
| Owner panel | Blade + Livewire or Filament | Fast CRUD and inline edits without a separate frontend |
| Images | Intervention Image on a queue → WebP at 3 widths (320, 640, 1024 px) | Phones download only the size they need |
| Cache | Full-page cache per café and language (Redis, or file cache on cheap hosting) | Menu served without database hits |
| CDN | Cloudflare (free plan to start) | Caches pages, images and game files close to users |
| Offline | Small service worker (v1.1) | Repeat visits open instantly |
| Hosting | One VPS to start (4 GB RAM) with daily database backups | Cheap; scale later |

**Performance budget (customer menu page)**

| Item | Budget |
| --- | --- |
| HTML (gzipped, typical menu of 60 items) | Under 40 KB |
| CSS | Under 15 KB |
| JavaScript | Under 20 KB |
| Fonts | System font for English; Ethiopic font subset under 60 KB, loaded only for Amharic |
| Images above the fold | Under 60 KB total; the rest lazy-loaded |
| Server response from cache | Under 100 ms |
| Largest Contentful Paint on 3G | Under 2 s |
| Lighthouse mobile performance | 90+ |

**Data model (main tables)**

| Table | Key fields |
| --- | --- |
| `cafes` | id, slug, name, tagline, logo, cover, theme\_color, phone, address, lat, lng, wifi\_name, wifi\_password, vat\_included, service\_charge\_pct, default\_lang, plan, status, trial\_ends\_at |
| `branches` (v1.1) | id, cafe\_id, name, slug, address, phone, hours |
| `users` | id, name, phone, email, password |
| `cafe_user` | cafe\_id, user\_id, role (owner, manager, staff) |
| `opening_hours` | cafe\_id, day\_of\_week, opens\_at, closes\_at, closed |
| `categories` | id, cafe\_id, name (JSON per language), icon, sort\_order, visible |
| `items` | id, cafe\_id, category\_id, name (JSON), description (JSON), price, image, sort\_order, visible, sold\_out |
| `item_variants` | id, item\_id, name (JSON), price, sort\_order |
| `item_addons` | id, item\_id, name (JSON), price |
| `tags` / `item_tag` | id, key (fasting, vegetarian, spicy, new, popular), name (JSON), icon |
| `payment_accounts` | id, cafe\_id, type (bank, telebirr, cbe\_birr), provider, account\_name, account\_number, sort\_order, visible |
| `social_links` | cafe\_id, platform, url |
| `tables` | id, cafe\_id, number, qr\_token |
| `games` | id, name, source\_url, hosted, license, players, mode, size\_kb, thumbnail, active |
| `cafe_game` | cafe\_id, game\_id, enabled, sort\_order |
| `specials` (v1.1) | id, cafe\_id, item\_id, label, starts\_at, ends\_at |
| `announcements` (v1.1) | id, cafe\_id, title, body, image, starts\_at, ends\_at |
| `feedback` (v1.1) | id, cafe\_id, table\_id, rating, comment, created\_at |
| `events` | id, cafe\_id, table\_id, type, item\_id, game\_id, value, created\_at |
| `daily_stats` | cafe\_id, date, scans, item\_opens, game\_opens, top\_items (JSON) |
| `subscriptions` | id, cafe\_id, plan, amount, paid\_at, receipt\_image, period\_start, period\_end, status |

Loyalty (v2) adds `customers` (phone, name), `loyalty_programs`, `stamps` and `rewards`, keyed by café. Table QRs from v1 make this easier because each scan is already tied to a table.

## Metrics, risks and open questions

**Success metrics**

| Metric | Target after 3 months |
| --- | --- |
| Paying cafés | 20 |
| Monthly café churn | Under 5% |
| Menu scans per café per day | 30+ |
| Visitors who open a game | 20%+ |
| Menu load on 3G (75th percentile) | Under 2 s |
| Owner edits per café per week | 2+ (shows the menu is kept up to date) |

**Analytics events (v1).** `menu_view`, `category_view`, `item_open`, `search`, `filter_used`, `language_switch`, `pay_copy`, `wifi_copy`, `call_tap`, `map_tap`, `game_open`, `game_close` (with seconds played). Store as rows in an `events` table, roll up nightly into daily totals, and delete raw rows after 90 days. No personal data in v1.

**Risks**

| Risk | Mitigation |
| --- | --- |
| Owners don't keep menus updated | Quick-edit list, staff role, weekly Telegram reminder with stats |
| Many cheap competitors and free QR-menu tools | Compete on Amharic, fasting filter, games, local payment info, and done-for-you setup |
| Games carry unwanted ads or licence problems | Prefer self-hosted open-source games; review every game; keep a licence field |
| Slow café Wi-Fi or expensive data | 150 KB budget, images lazy-loaded, offline cache for repeat visits |
| Customers expect to order from the menu | Clear note "Order with your waiter"; ordering add-on stays on the roadmap |
| Collecting subscription payments manually | Receipt upload flow in v1; payment gateway such as Chapa or Telebirr later |

**Open questions**

- [ ] Product name and domain.
- [ ] Pricing per plan in ETB, tested with 3–4 cafés.
- [ ] Final list of v1 games and their licences.
- [ ] Do owners want table-level QR (needed later for loyalty and stats per table) or one QR per café?
- [ ] Do we offer a custom subdomain (cafename.ourdomain.com) in Pro?
- [ ] Which SMS provider for v2 loyalty codes?
