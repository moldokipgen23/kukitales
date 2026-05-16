# KukiTales — Complete Project Brief & Claude Code Master Prompt
# Save this file. Share with Claude Code to build everything automatically.

═══════════════════════════════════════════════════════════════
  SECTION 1: WHAT IS KUKITALES
═══════════════════════════════════════════════════════════════

KukiTales is a cultural media platform for the Kuki people of
Northeast India (Manipur, Mizoram, Nagaland, Assam, Tripura).

Think of it as a combination of:
  - BBC Culture (editorial, high-quality content)
  - Medium (community writing & submissions)
  - Wattpad (serialized episode stories)
  - Wikipedia (structured history archive)

The HEART of the platform is STORIES and HISTORY — not news.
News is secondary. The primary goal is to preserve and celebrate
Kuki oral traditions, folktales, history, and storytelling.

Target audience:
  - Kuki people living in Northeast India
  - Kuki diaspora worldwide
  - Researchers and academics studying Northeast Indian culture
  - Young Kukis who want to reconnect with their heritage

═══════════════════════════════════════════════════════════════
  SECTION 2: CONTENT TYPES (8 total)
═══════════════════════════════════════════════════════════════

1. STORIES (Short Stories)
   - Standalone fictional or real stories
   - Written by community members or editors
   - Has: title, content, author, cover image, read time, tags
   - Audio: text-to-speech enabled

2. EPISODE STORIES (Serialized)
   - Like a book series — multiple episodes per series
   - Example: "Kuki Legends" Series → Episode 1, 2, 3...
   - Users follow a series and get notified of new episodes
   - Has: series name, episode number, episode title, content
   - Audio: text-to-speech enabled

3. KUKI HISTORY (Articles)
   - Factual historical articles
   - Structured with timeline years
   - Example: "Anglo-Kuki War 1917–1919", "The Chiefs of the Hills"
   - Has: title, year/era, content, sources, author, cover image
   - Audio: text-to-speech enabled

4. FOLKTALES
   - Traditional Kuki folk stories passed down generations
   - Categorized by type (Animal Tales, Love, Battle, Nature, etc.)
   - Marked as "Traditional" with elder/source attribution
   - Audio: text-to-speech enabled

5. NEWS
   - Community news, events, announcements
   - Categories: Community, Politics, Culture, Education, Sports
   - Has: headline, content, location, date, source
   - Breaking news ticker on homepage

6. BLOG
   - Opinion pieces, personal essays, culture commentary
   - Open to registered authors
   - Has: title, content, tags, author bio

7. MUSIC & SONGS
   - Kuki traditional songs, lyrics, audio
   - Can upload audio files
   - Has: song title, lyrics, audio file, language/dialect, era

8. GALLERY
   - Photo collections of Kuki culture, events, history
   - Has: title, description, images (multiple), location, year

═══════════════════════════════════════════════════════════════
  SECTION 3: DESIGN SYSTEM
═══════════════════════════════════════════════════════════════

COLOR PALETTE:
  Primary Red:    #c0392b  (main brand color — crimson red)
  Red Dark:       #922b21  (hover states, gradients)
  Red Deep:       #7b241c  (dark sections background)
  Red Light:      #e74c3c  (lighter accent)
  Red Soft:       #fdf0ee  (light backgrounds, hover fills)
  Red Muted:      #f9e0dd  (section backgrounds)
  Gold Accent:    #d4a843  (secondary accent — cultural warmth)
  Dark BG:        #1a0a08  (hero sections, dark panels)
  Dark BG 2:      #2d1410  (gradient partner)
  Page BG:        #faf8f7  (main light background)
  Text Primary:   #1a0a08  (main text)
  Text Mid:       #5a2a22  (secondary text)
  Text Muted:     #9a6058  (captions, meta)
  Border:         #edddd9  (card borders)

TYPOGRAPHY:
  Headings:  "Cormorant Garamond" (Google Font) — elegant, literary
             Weights: 400, 600, 700 italic
  Body:      "Nunito" (Google Font) — friendly, readable
             Weights: 400, 500, 600, 700

FONT SIZES:
  Site Title:     22px Cormorant Garamond Bold
  Hero H1:        44px (mobile: 28px)
  Section Title:  28px (mobile: 24px)
  Card Title:     17-20px
  Body:           14px
  Meta/Caption:   11-12px

SPACING:
  Section padding:  48px top/bottom (mobile: 36px)
  Card padding:     14-18px
  Grid gap:         16-20px
  Page padding:     20px sides (mobile: 16px)

BORDER RADIUS:
  Cards:    12-14px
  Buttons:  8-9px
  Badges:   4-6px
  Pills:    30px (fully rounded)
  Avatars:  50% (circle)

SHADOWS:
  Card hover:  0 14px 36px rgba(192,57,43,0.12)
  Header:      0 2px 16px rgba(192,57,43,0.12)
  Logo mark:   0 3px 12px rgba(192,57,43,0.35)

═══════════════════════════════════════════════════════════════
  SECTION 4: COMPONENT SPECS
═══════════════════════════════════════════════════════════════

── HEADER ──
  Background:    white
  Bottom border: 3px solid #c0392b
  Height:        64px (mobile: 58px)
  Sticky:        yes (position: sticky, top: 0, z-index: 200)
  Logo:          Red square mark with "K" + "KukiTales" in red + tagline
  Nav links:     with dropdown menus (hover)
  Right side:    "Submit" outline button + "Login" dark button
  Mobile:        hamburger menu (slides in from right, full screen overlay)
  Hamburger:     3 lines → animates to X when open

── TOPBAR (above header) ──
  Background:    #1a0a08 (very dark)
  Text/links:    #d4a843 (gold)
  Content left:  "Northeast India's Premier Cultural Platform"
  Content right: Date | Login | Register | Dashboard link
  Font:          11px
  Mobile:        show only right side links

── SEARCH BAR (below header) ──
  Background:    #f9e0dd (red muted)
  Search input:  white, rounded pill shape, 38px height
  Quick tags:    horizontal chips (hidden on mobile)
  Placeholder:   "Search news, stories, history, folktales..."

── BREAKING NEWS TICKER ──
  Background:    #c0392b (red)
  Label badge:   dark bg, gold text "⚡ Breaking"
  Text:          white, scrolling animation (28s loop)
  Font:          12px, 600 weight

── SECTION TITLE STYLE ──
  Font:          Cormorant Garamond, 28px, bold
  Left accent:   4px wide, 28px tall red bar before title
  Paired with:   "View All →" link in red on right side

── CONTENT CARDS ──
  Background:    white
  Border:        1px solid #edddd9
  Hover:         translateY(-4px) + red border + shadow
  Thumb:         160px height, gradient background + emoji icon
  Category badge: absolute positioned, bottom-left of thumb
  Card body:     14px padding
  Title font:    Cormorant Garamond, 17px, bold
  Meta:          author avatar (circle) + name + time

── CATEGORY BADGES ──
  News:      #c0392b (red)
  Story:     #d4a843 (gold)
  History:   #6b3a2a (dark brown)
  Folktale:  #2a6b4a (forest green)
  Episode:   #3a2a6b (deep purple)
  Blog:      #2a4a6b (navy)

── CATEGORY FILTER PILLS ──
  Default:   white bg, red-brown border, dark text
  Active:    red bg, red border, white text
  Shape:     fully rounded (30px border-radius)
  Font:      11px, 700 weight

── HERO SECTION ──
  Background:  dark gradient (linear-gradient 150deg, #1a0a08 → #2d1410 → #3d0f0a)
               + radial red glow at 70% position
               + subtle dot pattern overlay
  Layout:      2-column grid (text left, featured card right)
  Mobile:      stacks to 1 column
  Badge:       "Northeast India's Cultural Hub" red-outlined pill
  H1:          44px, white, with italic gold em tag
  Paragraph:   14px, muted pinkish-grey
  Buttons:     Red primary + ghost outline

── HERO FEATURED CARD ──
  Background:  rgba white 6% (glass effect)
  Border:      1px solid rgba red 25%
  Hover:       translateY(-4px)
  Image area:  200px, dark red gradient + large emoji
  Body:        18px padding, gold category, white title, muted meta

── DARK SECTIONS (Stories, Episodes) ──
  Background:  #1a0a08 (very dark)
  Cards:       rgba white 4%, red-tinted borders
  Text:        white titles, gold accents, muted grey meta

── HISTORY TIMELINE ──
  Layout:      vertical line with dots
  Line:        2px, gradient red to transparent
  Dots:        14px circle, red fill, white ring border
  Year:        11px, red, bold, uppercase
  Title:       Cormorant Garamond 18px
  Text:        12px muted

── FOLKTALE CATEGORY CARDS ──
  Layout:      4-column grid (mobile: 2-column)
  Style:       white card, centered, large emoji icon
  Hover:       translateY(-4px) + red border + shadow

── EPISODE CARDS ──
  Thumb:       150px height with gradient backgrounds
  Play button: circle, glass effect → red on hover
  Episode num: top-right badge in red

── BLOG LIST ──
  Layout:      stacked list (not grid)
  Thumb:       80×72px left side (mobile: full width top)
  Tags:        small red pill badges
  Footer:      author + date left, "Read More →" right
  Mobile:      thumb goes full width above content

── SUBMIT SECTION ──
  Background:  dark gradient
  Layout:      centered text + 8 content type buttons in a row
  Type buttons: small cards with emoji + label, red border
  Main button: red, white text "Start Submitting"

── FOOTER ──
  Background:  #0f0704 (near black)
  Grid:        4 columns (mobile: 2 col → 1 col)
  Logo:        red Cormorant Garamond text
  Links:       muted brown → red on hover
  Social:      square buttons, red border, red icon → red bg on hover
  Bottom bar:  copyright left, regions right

═══════════════════════════════════════════════════════════════
  SECTION 5: PAGE STRUCTURE & LAYOUT ORDER
═══════════════════════════════════════════════════════════════

HOMEPAGE (in this exact order):
  1.  Topbar (dark, gold links)
  2.  Header (sticky, white, red border)
  3.  Search Bar (red-muted background)
  4.  Hero (dark, featured STORY or HISTORY — NOT news)
  5.  Breaking News Ticker (red scrolling bar)
  6.  ── FEATURED STORIES ── (dark section, 2fr+1fr+1fr grid)
  7.  ── EPISODE STORIES ── (dark section, 3-col cards)
  8.  ── KUKI HISTORY ── (light red-tinted, timeline + sidebar)
  9.  ── FOLKTALES ── (white, 4-col category cards)
  10. ── LATEST NEWS ── (light bg, 3-col cards with filter pills)
  11. ── BLOG ── (white, stacked list)
  12. ── SUBMIT YOUR STORY ── (dark CTA section)
  13. Footer

NOTE: Stories and History come BEFORE News. This is intentional.
The platform's identity is cultural preservation, not news media.

SINGLE ARTICLE PAGE:
  1. Header + topbar
  2. Breadcrumb (Home → Category → Title)
  3. Article hero (full-width cover image or gradient)
  4. Article header (category badge, title, author, date, read time)
  5. ── TTS AUDIO PLAYER BAR ──
     - Play/Pause button (red)
     - Progress indicator
     - Speed control (0.75x, 1x, 1.25x, 1.5x)
     - Uses Web Speech API (browser built-in, free)
  6. Reading progress bar (top of page, red, fills as you scroll)
  7. Article body (comfortable reading width, 680px max)
  8. Share buttons (WhatsApp, Facebook, Twitter/X, Copy Link)
  9. Tags
  10. Author bio card
  11. Related articles (3-col grid)
  12. Comments section
  13. Footer

CATEGORY PAGE:
  1. Header
  2. Category hero banner (red gradient, category name + icon)
  3. Filter/sort bar
  4. Content grid (3-col desktop, 2-col tablet, 1-col mobile)
  5. Pagination
  6. Footer

EPISODE SERIES PAGE:
  1. Series hero (dark, series title, description, episode count)
  2. Episode list (numbered, with read status for logged-in users)
  3. Continue Reading button (goes to last read episode)

USER PROFILE PAGE:
  1. Profile header (avatar, name, bio, join date)
  2. Tabs: My Articles | Bookmarks | Following
  3. Content grid

ADMIN DASHBOARD (Filament v3):
  - Overview stats (total posts, views, users, comments)
  - Recent submissions for approval
  - Post management (all content types)
  - User management with roles
  - Category & tag management
  - Series & episode management
  - Media library
  - Comments moderation
  - Site settings (logo, site name, social links, breaking news text)

═══════════════════════════════════════════════════════════════
  SECTION 6: DATABASE SCHEMA
═══════════════════════════════════════════════════════════════

TABLE: users
  id, name, email, password, role (admin|editor|author|reader),
  avatar, bio, location, is_verified, is_active,
  email_verified_at, remember_token, timestamps

TABLE: categories
  id, name, slug, type (story|history|news|folktale|episode|blog|music|gallery),
  description, icon (emoji), color (hex), parent_id, sort_order,
  is_active, timestamps

TABLE: series (for episode stories)
  id, title, slug, description, cover_image, user_id,
  category_id, status (ongoing|completed|hiatus),
  episode_count, is_featured, timestamps

TABLE: posts (ALL content types go here)
  id, title, slug, excerpt, content (longText),
  type (story|history|news|folktale|episode|blog|music|gallery),
  status (draft|pending|published|rejected),
  user_id, category_id, series_id (nullable, for episodes),
  episode_number (nullable), cover_image, audio_file (nullable),
  year_era (nullable, for history), source (nullable),
  is_featured, is_breaking (for news), allow_tts,
  view_count, read_time (minutes), published_at, timestamps

TABLE: tags
  id, name, slug, timestamps

TABLE: post_tags
  post_id, tag_id

TABLE: media
  id, post_id, file_path, file_type, file_size, alt_text,
  sort_order, timestamps

TABLE: comments
  id, post_id, user_id, parent_id (for replies),
  content, status (pending|approved|rejected), timestamps

TABLE: bookmarks
  id, user_id, post_id, timestamps

TABLE: post_views
  id, post_id, user_id (nullable), ip_address, timestamps

TABLE: follows (users following series)
  id, user_id, series_id, timestamps

TABLE: site_settings
  id, key, value, timestamps

═══════════════════════════════════════════════════════════════
  SECTION 7: USER ROLES & PERMISSIONS
═══════════════════════════════════════════════════════════════

ADMIN:
  - Full access to everything
  - Can publish/reject any content
  - Can manage users, settings, categories
  - Access to Filament admin panel

EDITOR:
  - Can publish/reject submissions
  - Can edit all posts
  - Cannot manage users or settings
  - Access to Filament admin panel

AUTHOR:
  - Can submit content (goes to pending)
  - Can edit own posts
  - Cannot publish directly (needs editor/admin approval)
  - No Filament access (uses frontend submission form)

READER (default):
  - Can read all public content
  - Can comment (after approval)
  - Can bookmark articles
  - Can follow episode series
  - Can submit content (goes to pending)

═══════════════════════════════════════════════════════════════
  SECTION 8: KEY FEATURES LIST
═══════════════════════════════════════════════════════════════

PUBLIC FEATURES:
  ✅ Homepage with all content sections
  ✅ Category browsing pages
  ✅ Single article reading page
  ✅ Text-to-Speech (TTS) on every article — Web Speech API
  ✅ Reading progress bar
  ✅ Episode series with numbered chapters
  ✅ Continue reading (remembers last episode)
  ✅ Search (across all content types)
  ✅ Filter by category, tag, year, author
  ✅ Share buttons (WhatsApp, Facebook, Twitter, Copy Link)
  ✅ Bookmarking (save articles, requires login)
  ✅ Comments with replies
  ✅ Author profiles
  ✅ User registration & login
  ✅ Story submission form
  ✅ Breaking news ticker
  ✅ Mobile responsive (all breakpoints)
  ✅ Hamburger menu on mobile

ADMIN FEATURES (Filament):
  ✅ Dashboard with stats widgets
  ✅ Post management (CRUD all content types)
  ✅ Content approval workflow
  ✅ Rich text editor (TipTap or Quill)
  ✅ Image upload & media library
  ✅ Audio file upload (for music/TTS)
  ✅ User management & role assignment
  ✅ Category & tag management
  ✅ Series & episode management
  ✅ Comment moderation
  ✅ Site settings (logo, tagline, social links, ticker text)
  ✅ SEO fields (meta title, description, og:image)

FUTURE (Phase 2 — after launch):
  ⬜ Flutter mobile app
  ⬜ Push notifications for new episodes
  ⬜ Newsletter subscription
  ⬜ Premium membership
  ⬜ Audio narration uploads (human voice)
  ⬜ Language toggle (Thadou-Kuki / English)

═══════════════════════════════════════════════════════════════
  SECTION 9: TECH STACK
═══════════════════════════════════════════════════════════════

  Backend:       Laravel 11 (PHP 8.2+)
  Database:      MySQL 8.0
  Admin Panel:   Filament v3
  Frontend:      Blade templates + Alpine.js + Tailwind CSS v3
  Auth:          Laravel Breeze (with role middleware)
  File Storage:  Laravel Storage (local dev → S3/Railway volume prod)
  TTS:           Web Speech API (browser built-in, no API key needed)
  Search:        Laravel Scout (basic) or simple LIKE queries
  Version Ctrl:  Git → GitHub
  Hosting:       Railway (PHP + MySQL services)
  CI/CD:         GitHub → Railway auto-deploy on push

═══════════════════════════════════════════════════════════════
  SECTION 10: RAILWAY HOSTING SETUP
═══════════════════════════════════════════════════════════════

  1. Push project to GitHub repository
  2. Go to railway.app → New Project → Deploy from GitHub
  3. Add MySQL service (Railway provides free MySQL)
  4. Set environment variables in Railway dashboard:
       APP_NAME=KukiTales
       APP_ENV=production
       APP_KEY= (run php artisan key:generate)
       APP_URL=https://yourapp.railway.app
       DB_CONNECTION=mysql
       DB_HOST= (Railway provides this)
       DB_PORT=3306
       DB_DATABASE=kukitales
       DB_USERNAME= (Railway provides this)
       DB_PASSWORD= (Railway provides this)
  5. Add build command:  composer install --no-dev
  6. Add start command:  php artisan serve --host=0.0.0.0 --port=$PORT
  7. Run migrations:     php artisan migrate --seed
  8. Set storage link:   php artisan storage:link

═══════════════════════════════════════════════════════════════
  SECTION 11: CLAUDE CODE MASTER PROMPT
  (Copy everything below this line and paste into Claude Code)
═══════════════════════════════════════════════════════════════

---CLAUDE CODE PROMPT START---

Build a complete Laravel 11 PHP web application called KukiTales.
This is a cultural media platform for the Kuki people of Northeast India.
Work through every step below in order. Do not skip any step.
Run commands, create files, and fix any errors automatically.

═══════════════════
TECH STACK
═══════════════════
- Laravel 11, PHP 8.2+
- MySQL database
- Filament v3 (admin panel)
- Blade + Alpine.js + Tailwind CSS v3
- Laravel Breeze for auth
- Web Speech API for text-to-speech (no external API)

═══════════════════
STEP 1: PROJECT SETUP
═══════════════════
1. Create fresh Laravel 11 project
2. Install and configure Tailwind CSS v3 with Laravel
3. Install Alpine.js via CDN in the main layout
4. Install Laravel Breeze (blade stack)
5. Install Filament v3: composer require filament/filament
6. Run: php artisan filament:install --panels
7. Install additional packages:
   - spatie/laravel-sluggable (for auto slugs)
   - spatie/laravel-medialibrary (for file uploads)

═══════════════════
STEP 2: DATABASE MIGRATIONS
═══════════════════
Create migrations in this order:

1. Modify users table migration:
   Add columns: role (enum: admin,editor,author,reader, default reader),
   avatar (nullable string), bio (nullable text),
   location (nullable string), is_active (boolean default true)

2. Create categories table:
   id, name, slug, type (enum: story,history,news,folktale,
   episode,blog,music,gallery), description (nullable text),
   icon (nullable string — emoji), color (nullable string — hex),
   parent_id (nullable foreignId), sort_order (integer default 0),
   is_active (boolean default true), timestamps

3. Create series table:
   id, title, slug, description (nullable text),
   cover_image (nullable string), user_id (foreignId),
   category_id (foreignId), status (enum: ongoing,completed,hiatus),
   is_featured (boolean default false), timestamps

4. Create posts table:
   id, title, slug, excerpt (nullable text),
   content (longText), type (enum: story,history,news,
   folktale,episode,blog,music,gallery),
   status (enum: draft,pending,published,rejected, default draft),
   user_id (foreignId), category_id (nullable foreignId),
   series_id (nullable foreignId), episode_number (nullable integer),
   cover_image (nullable string), audio_file (nullable string),
   year_era (nullable string), source (nullable string),
   is_featured (boolean default false),
   is_breaking (boolean default false),
   allow_tts (boolean default true),
   view_count (integer default 0),
   read_time (integer default 0 — minutes),
   meta_title (nullable string), meta_description (nullable text),
   published_at (nullable timestamp), timestamps

5. Create tags table: id, name, slug, timestamps

6. Create post_tag pivot table: post_id, tag_id

7. Create media table:
   id, post_id (foreignId), file_path (string),
   file_type (string), file_size (integer nullable),
   alt_text (nullable string), sort_order (integer default 0), timestamps

8. Create comments table:
   id, post_id (foreignId), user_id (foreignId),
   parent_id (nullable foreignId self-referential),
   content (text), status (enum: pending,approved,rejected, default pending),
   timestamps

9. Create bookmarks table:
   id, user_id (foreignId), post_id (foreignId), timestamps
   Add unique constraint on [user_id, post_id]

10. Create post_views table:
    id, post_id (foreignId), user_id (nullable foreignId),
    ip_address (nullable string), timestamps

11. Create follows table:
    id, user_id (foreignId), series_id (foreignId), timestamps
    Add unique constraint on [user_id, series_id]

12. Create site_settings table:
    id, key (string unique), value (text nullable), timestamps

Run: php artisan migrate

═══════════════════
STEP 3: MODELS
═══════════════════
Create these Eloquent models with full relationships:

User model:
- hasMany: posts, comments, bookmarks, follows
- role helpers: isAdmin(), isEditor(), isAuthor()
- fillable: name, email, password, role, avatar, bio, location

Category model:
- hasMany: posts
- belongsTo: parent (self)
- hasMany: children (self)
- use HasSlug trait from spatie/laravel-sluggable

Series model:
- belongsTo: user, category
- hasMany: posts (episodes), follows
- use HasSlug

Post model:
- belongsTo: user, category, series
- hasMany: comments, bookmarks, postViews, media
- belongsToMany: tags (through post_tag)
- use HasSlug
- boot method: auto-calculate read_time from content word count
  (avg 200 words per minute)
- scopePublished(): where status = published and published_at <= now
- scopeFeatured(): where is_featured = true
- scopeByType($type): where type = $type

Tag model:
- belongsToMany: posts
- use HasSlug

Comment model:
- belongsTo: post, user
- hasMany: replies (self-referential, parent_id)
- belongsTo: parent (self)

Bookmark model: belongsTo user, post
Follow model: belongsTo user, series
SiteSetting model: static get($key), static set($key, $value)

═══════════════════
STEP 4: FILAMENT ADMIN PANEL
═══════════════════
Create a Filament panel at /admin with these resources:

1. PostResource:
   - List with columns: title, type badge, status badge, author,
     views, published_at, is_featured toggle
   - Filters: by type, by status, by category, featured only
   - Form with tabs:
     Tab "Content": title (auto-generates slug), type select,
     category select, series select (shows when type=episode),
     episode_number (shows when type=episode),
     rich text editor for content, excerpt textarea
     Tab "Media": cover_image upload, audio_file upload
     Tab "Settings": status select, is_featured toggle,
     is_breaking toggle (shows when type=news),
     allow_tts toggle, year_era (shows when type=history),
     source, published_at datetime picker
     Tab "SEO": meta_title, meta_description
   - Tags relation manager
   - Comments relation manager (read-only list with approve/reject actions)

2. CategoryResource:
   - Simple form: name, type, description, icon, color, parent_id, sort_order

3. SeriesResource:
   - Form: title, description, cover_image, category_id,
     user_id, status, is_featured
   - Episodes relation manager (list of posts in this series)

4. UserResource:
   - List: name, email, role badge, is_active toggle, created_at
   - Form: name, email, role select, bio, location, is_active

5. TagResource:
   - Simple: name (auto-slug)

6. CommentResource:
   - List all comments with status filter
   - Bulk approve/reject actions

7. SiteSettingResource (or custom Settings page):
   - Key-value settings form with these fields:
     site_name, site_tagline, site_logo, social_facebook,
     social_twitter, social_instagram, social_youtube,
     breaking_news_text, footer_text

8. Dashboard widgets:
   - StatsOverviewWidget: Total Posts, Total Users, Total Views,
     Pending Approval count
   - Latest Posts table widget
   - Recent Users widget

Create a Filament admin user seeder.

═══════════════════
STEP 5: ROUTES & CONTROLLERS
═══════════════════
Create web routes and controllers:

routes/web.php:
  GET  /                          HomeController@index
  GET  /search                    SearchController@index
  GET  /category/{slug}           CategoryController@show
  GET  /series/{slug}             SeriesController@show
  GET  /series/{series}/{slug}    PostController@show (episode)
  GET  /{type}/{slug}             PostController@show (all types)
  GET  /author/{id}               AuthorController@show

  Auth routes (Breeze):
  GET  /login, POST /login
  GET  /register, POST /register
  GET  /dashboard                 UserDashboardController@index
  GET  /profile                   ProfileController@edit
  GET  /bookmarks                 BookmarkController@index
  POST /bookmarks/{post}          BookmarkController@toggle
  GET  /submit                    SubmissionController@create
  POST /submit                    SubmissionController@store
  POST /comments                  CommentController@store

routes/api.php:
  GET  /api/posts                 (paginated, filterable)
  GET  /api/posts/{slug}          (single post)
  GET  /api/search?q=             (search)
  POST /api/views/{post}          (increment view count)

HomeController:
  - Fetch: featured post (story or history, is_featured=true)
  - Fetch: featured stories (type=story, published, limit 3)
  - Fetch: episode series (with latest episode, limit 3)
  - Fetch: history articles (type=history, limit 4)
  - Fetch: folktale categories (all folktale categories)
  - Fetch: latest news (type=news, limit 3)
  - Fetch: latest blogs (type=blog, limit 3)
  - Fetch: breaking news posts (is_breaking=true, limit 5)
  - Pass all to home.blade.php view

PostController@show:
  - Find post by slug
  - Increment view_count
  - Log to post_views table
  - Get related posts (same category, limit 3)
  - Get comments (approved, with user and replies)
  - Return post.show view

═══════════════════
STEP 6: BLADE LAYOUTS
═══════════════════
Create resources/views/layouts/app.blade.php with:

HEAD:
  - Google Fonts: Cormorant Garamond (400,600,700 italic) + Nunito (400,500,600,700)
  - Tailwind CSS (CDN or compiled)
  - Alpine.js CDN
  - Custom CSS variables:
    --red: #c0392b
    --red-dark: #922b21
    --gold: #d4a843
    --dark: #1a0a08

TOPBAR:
  <div class="topbar"> dark bg #1a0a08, gold text
    Left: "📍 Northeast India's Premier Cultural Platform"
    Right: date | Login | Register | Admin (if admin user)
  </div>

HEADER: (sticky, white, 3px red bottom border, shadow)
  Logo: red square "K" mark + "KukiTales" in red Cormorant + tagline
  Desktop nav: Home | News▾ | Stories▾ | History▾ | Culture▾ | Blog
  Each nav item has dropdown menu on hover
  Right: "Submit Story" outline btn + "Login" dark btn
  Mobile: show only logo + hamburger button (3 lines)

  Mobile menu: full screen overlay, slides from right
  Contains all nav links with expandable sub-sections
  Alpine.js x-data for open/close state

SEARCH BAR: (below header, red-muted background)
  Pill-shaped search input + quick filter tags (hidden mobile)

FLASH MESSAGES: success/error alert banners

MAIN CONTENT: @yield('content')

FOOTER:
  4-col grid (mobile: 2-col → 1-col)
  Col 1: Logo + description + social icons
  Col 2: Content links
  Col 3: Community links
  Col 4: About links
  Bottom bar: copyright + regions text

Also create: layouts/admin.blade.php (Filament handles this)

═══════════════════
STEP 7: BLADE VIEWS
═══════════════════

1. home.blade.php
   Sections IN THIS ORDER:
   a. Hero (dark section) — featured story/history card
      Two columns: left=text/headline/buttons, right=featured card
   b. Breaking news ticker (red bar, auto-scrolling)
   c. Featured Stories (dark section, 2fr+1fr+1fr grid)
   d. Episode Stories (dark section, 3-col cards)
   e. Kuki History (light section, timeline + popular sidebar)
   f. Folktales (white, 4-col category grid, mobile 2-col)
   g. Latest News (light bg, filter pills + 3-col cards)
   h. Blog (white, stacked list items)
   i. Submit CTA (dark, 8 content type buttons)

2. posts/show.blade.php
   Sections:
   a. Reading progress bar (fixed top, red, JS scroll listener)
   b. Breadcrumb navigation
   c. Article header: cover image/gradient, category badge,
      title (Cormorant 36px), author+date+read time
   d. TTS Audio Player bar:
      Red bar with: ▶ Play button, article title, speed selector
      Uses window.speechSynthesis Web Speech API
      Alpine.js component handles play/pause/speed
   e. Article body (prose, max-width 680px, centered)
   f. Share buttons (WhatsApp, Facebook, Twitter, Copy Link)
   g. Tags list
   h. Author bio card (avatar, name, bio, article count)
   i. Related articles (3-col grid)
   j. Comments section (list + form if logged in)

3. categories/show.blade.php
   Category hero banner + filter bar + content grid + pagination

4. series/show.blade.php
   Series info + episode list (numbered) + follow button

5. auth/login.blade.php, register.blade.php (Breeze style, red theme)

6. user/dashboard.blade.php
   Tabs: My Articles | Bookmarks | Reading History

7. submissions/create.blade.php
   Multi-step form: Step 1 select type → Step 2 fill content
   Alpine.js for type selection and conditional fields

═══════════════════
STEP 8: TTS COMPONENT
═══════════════════
In posts/show.blade.php, build a TTS player using Alpine.js:

<div x-data="ttsPlayer()" class="tts-bar">
  <button @click="toggle()" — shows play or pause icon
  <span — shows "Playing..." or article title
  <select for speed: 0.75, 1, 1.25, 1.5, 2
  <button @click="stop()" — stop button
</div>

<script>
function ttsPlayer() {
  return {
    playing: false,
    utterance: null,
    speed: 1,
    // Get text from article body
    getText() {
      return document.getElementById('article-body').innerText;
    },
    toggle() {
      if (this.playing) {
        speechSynthesis.pause();
        this.playing = false;
      } else if (speechSynthesis.paused) {
        speechSynthesis.resume();
        this.playing = true;
      } else {
        this.utterance = new SpeechSynthesisUtterance(this.getText());
        this.utterance.rate = this.speed;
        this.utterance.onend = () => { this.playing = false; };
        speechSynthesis.speak(this.utterance);
        this.playing = true;
      }
    },
    stop() {
      speechSynthesis.cancel();
      this.playing = false;
    },
    changeSpeed() {
      if (this.utterance) this.utterance.rate = this.speed;
    }
  }
}
</script>

═══════════════════
STEP 9: SEEDERS
═══════════════════
Create DatabaseSeeder that runs:

1. AdminUserSeeder:
   Create admin user:
   name: KukiTales Admin
   email: admin@kukitales.com
   password: password (hashed)
   role: admin

2. CategorySeeder — create these categories:
   Stories: {name:"Short Stories", type:"story", icon:"📖"}
   {name:"Episode Stories", type:"episode", icon:"🎭"}
   {name:"Kuki History", type:"history", icon:"🏛️"}
   Folktales with sub-categories:
   {name:"Folktales", type:"folktale", icon:"🌿"} (parent)
   {name:"Animal Tales", type:"folktale", icon:"🐯", parent: Folktales}
   {name:"Moon & Stars", type:"folktale", icon:"🌕", parent: Folktales}
   {name:"Chief & Warrior Tales", type:"folktale", icon:"👑", parent: Folktales}
   {name:"Nature & Forest Spirits", type:"folktale", icon:"🌿", parent: Folktales}
   {name:"Love & Romance", type:"folktale", icon:"💘", parent: Folktales}
   {name:"Magic & Supernatural", type:"folktale", icon:"🔮", parent: Folktales}
   {name:"Battle & Bravery", type:"folktale", icon:"⚔️", parent: Folktales}
   {name:"Origin of the Hills", type:"folktale", icon:"🏔️", parent: Folktales}
   News categories:
   {name:"Community News", type:"news", icon:"📰"}
   {name:"Culture & Events", type:"news", icon:"🎭"}
   {name:"Education", type:"news", icon:"🏫"}
   {name:"Politics", type:"news", icon:"📌"}
   {name:"Sports", type:"news", icon:"🏆"}
   {name:"Blog", type:"blog", icon:"✍️"}
   {name:"Music & Songs", type:"music", icon:"🎵"}
   {name:"Gallery", type:"gallery", icon:"🖼️"}

3. SampleContentSeeder — create 3-5 sample posts for each type:
   Use realistic Kuki culture content for titles and excerpts
   Set status to published
   Set published_at to recent dates
   Mark 1 story and 1 history as is_featured=true
   Mark 1 news as is_breaking=true

4. SiteSettingSeeder:
   site_name: KukiTales
   site_tagline: Voices of the Hills
   breaking_news_text: "Kuki Cultural Festival 2026 announced | New folktale collection released | Episode 12 of Kuki Legends now live"
   footer_text: "Northeast India's Premier Cultural Platform"

Run: php artisan db:seed

═══════════════════
STEP 10: MIDDLEWARE & ROLES
═══════════════════
Create RoleMiddleware:
  php artisan make:middleware EnsureUserHasRole
  Check user->role against required roles
  Register in bootstrap/app.php

Protect routes:
  /admin/* — requires admin or editor role
  /submit — requires auth
  /bookmarks — requires auth
  /dashboard — requires auth

═══════════════════
STEP 11: FINAL CHECKS
═══════════════════
1. Run: php artisan migrate:fresh --seed
2. Run: php artisan storage:link
3. Run: npm run build (or npm run dev)
4. Test homepage loads with seeded data
5. Test admin panel at /admin with admin@kukitales.com / password
6. Test single article page with TTS player
7. Test mobile hamburger menu
8. Create .env.example file
9. Create README.md with:
   - Project description
   - Setup instructions (clone, composer install, .env setup, migrate, seed)
   - Railway deployment steps
   - Admin credentials
10. Create .gitignore (standard Laravel)

Report any errors and fix them before finishing.

---CLAUDE CODE PROMPT END---

═══════════════════════════════════════════════════════════════
  SECTION 12: GITHUB → RAILWAY DEPLOYMENT STEPS
═══════════════════════════════════════════════════════════════

After Claude Code finishes building locally:

1. git init
   git add .
   git commit -m "Initial KukiTales build"
   git remote add origin https://github.com/YOUR_USERNAME/kukitales.git
   git push -u origin main

2. Go to railway.app → Login → New Project
   → Deploy from GitHub repo → select kukitales

3. Add MySQL plugin to the project

4. Set these environment variables in Railway:
   APP_NAME=KukiTales
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=[generate with php artisan key:generate --show]
   APP_URL=https://[your-railway-domain].railway.app
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQL_HOST}}
   DB_PORT=${{MySQL.MYSQL_PORT}}
   DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
   DB_USERNAME=${{MySQL.MYSQL_USER}}
   DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
   FILESYSTEM_DISK=local

5. Set build command:
   composer install --no-dev --optimize-autoloader && npm run build

6. Set start command:
   php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT

7. Deploy → Railway builds and starts automatically
   → Visit your live URL

═══════════════════════════════════════════════════════════════
  END OF BRIEF
  Created for KukiTales by Claude (Anthropic)
  Version 1.0 — May 2026
═══════════════════════════════════════════════════════════════
