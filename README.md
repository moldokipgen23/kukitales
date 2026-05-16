# KukiTales — Voices of the Hills

A Laravel 11 cultural-media platform for the Kuki people of Northeast India.
Stories, episode serials, history, folktales, news, blog, music, gallery,
advertisements, donation campaigns, member accounts, social sign-in, and a
production-grade SEO stack (sitemap, Google News, JSON-LD) — all editable
through a Filament v3 admin panel, with text-to-speech on every article
(Web Speech API).

Built from the brief in `_reference/KUKITALES_PROJECT_BRIEF.md`. The
`_reference/kukitales-demo.html` file in this repo is the visual reference
the production layout was ported from.

## Feature map (what's built)

| Area | Where to manage | What it does |
|---|---|---|
| Content (8 types) | Admin → Stories/News/History/Culture/Blog sections | Per-type Post resources with auto-injected `type`, scoped queries, dedicated forms |
| Categories | Same sections (filtered) + Settings → All Categories | Tree-aware (folktale sub-cats, news cats) |
| Episode series | Admin → Stories → Episode Series | Group multiple episodes into a series |
| Comments | Admin → Moderation → Comments | Pending queue, approve/reject quick actions, bulk approve |
| **SEO** | Admin → Settings → SEO & Search | Sitemaps, robots.txt, RSS, OG/Twitter/JSON-LD, GA/GTM, Search Console verification, per-post meta |
| **Advertisements** | Admin → Monetization → Advertisements | Banner / video / custom HTML in 8 placement slots, view+click tracking, schedule windows |
| **Donations** | Admin → Monetization → Donation Campaigns + Donations | Campaign pages with progress bars, donor form, multiple payment methods (UPI/bank/Razorpay/Stripe/PayPal), admin approval flow |
| **Members** | Admin → People → Members | Public registered users (readers), with one-click promote-to-author |
| **Staff** | Admin → People → Staff & Authors | Admins, editors, authors only |
| **Social sign-in** | `.env` keys for Google + Facebook | Buttons auto-show on login/register when configured |
| Site settings | Admin → Settings → Site Settings | Site name, tagline, social URLs, breaking news ticker, donation payment methods |

## Stack

- **Backend** — Laravel 11 (PHP 8.2+)
- **Admin panel** — Filament v3
- **Frontend** — Blade templates with vanilla CSS ported from the demo,
  small vanilla JS for the mobile menu, reading progress bar, and TTS
- **Database** — SQLite for local dev (zero setup), MySQL for production
- **TTS** — `window.speechSynthesis` (no API key required)
- **Auth** — lightweight controllers (`App\Http\Controllers\Auth\AuthController`)
  with four roles: `admin`, `editor`, `author`, `reader`

## Local setup

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite — file is created automatically)
touch database/database.sqlite
php artisan migrate --seed

# 4. Storage link (so uploaded covers/avatars are served)
php artisan storage:link

# 5. Serve
php artisan serve   # http://127.0.0.1:8000
```

Open the homepage at <http://127.0.0.1:8000> and the admin panel at
<http://127.0.0.1:8000/admin>.

### Seeded accounts

| Role   | Email                    | Password   |
| ------ | ------------------------ | ---------- |
| Admin  | admin@kukitales.com      | `password` |
| Editor | editor@kukitales.com     | `password` |
| Author | author@kukitales.com     | `password` |

The seeder also creates: 12 top-level categories + 8 folktale sub-categories,
2 episode series with chapters, ~25 sample posts spanning every content type
(including a breaking-news item, a featured story, and a featured history piece),
~10 tags, and site settings (name, tagline, breaking-news ticker, social URLs).

## Project map

```
app/
├── Filament/
│   ├── Resources/       Post, Category, Series, Tag, User, Comment, SiteSetting
│   └── Widgets/         StatsOverview (dashboard)
├── Http/Controllers/    Home, Post, Category, Series, Search, Author,
│                        Comment, Bookmark, Submission, UserDashboard, Auth
├── Models/              User, Category, Series, Post, Tag, Comment,
│                        Bookmark, Follow, Media, PostView, SiteSetting
├── Providers/Filament/  AdminPanelProvider (brand colors → red palette)
database/
├── migrations/          12 tables (users + 11 app tables)
└── seeders/             Admin, Categories, SiteSettings, SampleContent
resources/views/
├── layouts/app.blade.php   single layout with the demo's full CSS inlined,
│                           topbar, sticky header, mobile hamburger menu,
│                           search bar, footer, reading progress bar
├── home.blade.php          all 8 homepage sections in brief order
│                           (Hero → Ticker → Stories → Episodes → History →
│                            Folktales → News → Blog → Submit CTA)
├── posts/show.blade.php    article page + TTS bar + share + comments
├── categories/show.blade.php
├── series/show.blade.php
├── auth/{login,register}.blade.php
├── user/{dashboard,bookmarks}.blade.php
├── submissions/create.blade.php
├── authors/show.blade.php
└── search.blade.php
routes/web.php           all public + auth routes (catch-all `/{type}/{slug}`
                         restricted to known content types)
_reference/              Original brief + standalone HTML demo (visual source)
```

## Content types

Posts share a single `posts` table; `type` discriminates between
`story`, `episode`, `history`, `folktale`, `news`, `blog`, `music`, `gallery`.
Episodes additionally use `series_id` + `episode_number`. History uses
`year_era`. News can be flagged `is_breaking` (shows in the homepage ticker).

URLs:
- `/{type}/{slug}` — every content type except episodes
- `/series/{slug}` — series landing page
- `/series/{slug}/{episode-slug}` — single episode
- `/category/{slug}` — paginated category listings
- `/author/{id}` — author profile

## Text-to-Speech

Every article whose `allow_tts` is true gets a red bar above the body with
play / pause / stop and a speed selector (0.75× – 2×). Implementation is in
`resources/views/posts/show.blade.php` — uses `SpeechSynthesisUtterance`
against `document.getElementById('article-body').innerText`.

## Deployment — Railway

1. **Push to GitHub** — `git init && git add . && git commit -m "Initial build" && git remote add origin … && git push -u origin main`
2. **Railway → New Project → Deploy from GitHub** → select the repo.
3. **Add a MySQL plugin** to the project.
4. **Set environment variables** (Railway → Variables):
   ```
   APP_NAME=KukiTales
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<output of: php artisan key:generate --show>
   APP_URL=https://<your-domain>.railway.app
   APP_TIMEZONE=Asia/Kolkata
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQL_HOST}}
   DB_PORT=${{MySQL.MYSQL_PORT}}
   DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
   DB_USERNAME=${{MySQL.MYSQL_USER}}
   DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
   FILESYSTEM_DISK=local
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```
5. **Build command** —
   `composer install --no-dev --optimize-autoloader`
6. **Start command** —
   `php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT`
   (Use the seed step only on the first deploy. Remove `--seed` after.)
7. **Deploy** → visit your Railway URL → log in to `/admin` with
   `admin@kukitales.com` / `password` → **change that password immediately**.

## Notes

- The layout is a single `layouts/app.blade.php` file that carries the entire
  CSS payload from the demo, so the production site visually matches the
  reference HTML without a build step.
- No Tailwind / Vite asset pipeline is required for the public site. Filament
  ships its own assets.
- For very large content libraries, swap the LIKE-based `SearchController`
  for Laravel Scout against a Meilisearch / Algolia index — the controller
  is the only file to change.
