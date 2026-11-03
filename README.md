# Manami Education — Study Abroad Consultancy Website

A production-ready study-abroad consultancy website for **Manami Education**, built with **PHP 8.x + MySQL/MariaDB + Tailwind CSS (CDN) + Alpine.js**. It includes a public-facing site, a student/order portal, and an admin dashboard.

## Features

- **Sticky header** with a compact 7-item desktop nav and mobile app-like drawer
- **Mega-dropdowns** for Services and Destinations
- **Shop section** combining Books and Courses
- **Hero** with event countdown, visa-success badge, and animated globe
- **Country grid** with hover image swap and flag chips
- **Services** (Student Visa, SSW, TITP, Japanese Language, IELTS)
- **Books & Courses** catalogue with cart and checkout
- **Success Stories** section with animated student counter and sliding testimonials
- **Blog / News** with article detail pages
- **Contact & CTA forms** that save leads to the database
- **FAQ accordion**, testimonials, stats counter, scroll reveal
- **Multi-language** support (English, Japanese, Bengali)
- **Admin dashboard** for content, orders, leads, and settings, including uploaded site logo and icons
- **Mobile bottom navigation** for quick access

## Tech stack

| Layer            | Tool                                           |
| ---------------- | ---------------------------------------------- |
| Server           | PHP 8.0+ (uses typed properties / match)       |
| Database         | MySQL / MariaDB                                |
| Styling          | Tailwind CSS via Play CDN + custom `style.css` |
| Interactivity    | Alpine.js via CDN                              |
| Fonts            | Google Fonts (Inter + Playfair Display)        |
| Build            | **None**                                       |

## Run locally with XAMPP

1. Place this folder inside your XAMPP `htdocs`:
   ```text
   /Applications/XAMPP/xamppfiles/htdocs/study-abroad-consultancy/
   ```
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Create the database and seed it:
   ```bash
   /Applications/XAMPP/xamppfiles/bin/mysql -u root -p manami_education < config/schema.sql
   /Applications/XAMPP/xamppfiles/bin/mysql -u root -p manami_education < config/migration.sql
   /Applications/XAMPP/xamppfiles/bin/mysql -u root -p manami_education < config/seed.sql
   ```
4. Open the site at:
   ```text
   http://localhost/study-abroad-consultancy/
   ```

> Do **not** open `http://localhost/` — that is the XAMPP dashboard. Always use the full path above.

## Admin access

Admin credentials are not displayed on the sign-in page. Change any initial password immediately after first login.

## Google Business Profile reviews

Manual testimonials remain available in **Admin → Content → Testimonials**. Google reviews are stored separately, shown with a Google review label, and synchronized from the verified Business Profile location.

To enable automatic sync:

1. Enable the Google Business Profile API for a Google Cloud project and authorize an OAuth refresh token with the `https://www.googleapis.com/auth/business.manage` scope. The Google account must have access to the verified business location.
2. Set these variables in the server environment (do not commit OAuth secrets):
   - `GBP_ACCOUNT_ID`
   - `GBP_LOCATION_ID`
   - `GOOGLE_OAUTH_CLIENT_ID`
   - `GOOGLE_OAUTH_CLIENT_SECRET`
   - `GOOGLE_OAUTH_REFRESH_TOKEN`
3. Apply `config/migration.sql` if the database has not received the `google_reviews` table yet.
4. Run an initial sync with `php scripts/sync-google-reviews.php`, then schedule that command hourly using the host's cron service. Each sync fetches every page of reviews, updates changed reviews, and hides reviews no longer returned by Google.

The sync command exits with a non-zero status and reports an error if credentials or the Google API are unavailable. The map and manual testimonials do not depend on Google review API credentials.

## Book sample PDFs and student stories

- Add or edit a book from **Admin → Products** and upload a short excerpt in **Book sample PDF**. Only PDFs up to 25 MB are accepted; upload sample pages, not the complete book.
- Preview files are stored outside the public web directory. The web server/PHP user must be able to write to the configured path. Set `PRIVATE_BOOK_PREVIEW_PATH` to an absolute writable directory outside the web root if the default parent directory is not writable. PHP's `upload_max_filesize` and `post_max_size` must also allow uploads of up to 25 MB.
- A customer can download a book's preview from **Track Order** only after the order is marked **Paid** or **Completed**. They verify with the order code and phone used at checkout. The PDF itself is streamed through the access-checking endpoint and is not linked as a public upload.
- Add real student photos and story details from **Admin → Content → Student Success**. Active stories appear in the homepage gallery after saving. Obtain permission before publishing students' photos and quotes.
- The homepage student count is set to **300+**. The migration updates the saved stats and success-section count so older saved values do not continue to show 1,200+.

## Homepage features, class schedules, and SSW

- Manage the chairman profile and the Student Insights Facebook video at **Admin → Homepage Features**. The chairman section stays hidden until a name and portrait are configured and visibility is enabled. Use a public Facebook video permalink; arbitrary iframe URLs are rejected.
- Publish class schedules at **Admin → Class Batches**. Add the batch ID, course, start date, duration, weekly class days, time, and location. The public calendar is available at `/batch-schedule.php`. Add government and campus holidays from the same dashboard; they are excluded from scheduled class days. Holiday dates are maintained manually, so check official announcements and update them for each batch.
- Manage the SSW pipeline at **Admin → SSW Pipeline**. Staff can create applications directly or convert an existing SSW lead without re-entering the applicant's details. Application records support assignment, JLPT/skills test dates, employer and COA/visa references, departure date, notes, a document-status checklist, and an audit history of forward stage changes. The checklist tracks status only; it does not store passport or other document files.
- On a new deployment, import `config/schema.sql`, then `config/migration.sql`, then `config/seed.sql`. For an existing deployment, back up the database and run `config/migration.sql` once; it creates the class schedule tables along with other additive application tables.
- Browser caching and offline service-worker caching are disabled so content updates appear on refresh. Existing Manami service-worker registrations and their caches are removed automatically on the next visit.

## File structure

```
study-abroad-consultancy/
├── index.php                 # Home page
├── about.php
├── services.php
├── destinations.php
├── books.php                 # Shop — books
├── courses.php               # Shop — courses
├── blog.php
├── faq.php
├── contact.php
├── login.php                 # Student/order tracking login
├── admin/                    # Admin dashboard
├── api/                      # Cart, checkout, leads, etc.
├── app/                      # Core classes (DataAdapter, I18n, DB, etc.)
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/                   # schema.sql, migration.sql, seed.sql
├── data/                     # Default arrays for site, nav, countries, etc.
├── partials/                 # Header, footer, topbar, lang switcher
├── uploads/                  # User-uploaded images
└── README.md
```

## Customising content

- **Brand & nav** → `data/site.php` and the `settings` table (`nav_json`)
- **Translations** → `app/i18n/{en-GB,bn,hi,ja}.php`
- **Countries** → `data/countries.php` + `countries` table
- **Services** → `data/services.php`
- **Books/Courses** → `books` and `courses` tables
- **Posts** → `posts` table

## Deployment to a live host

1. **Upload files** to the host's public directory (often `public_html/`).
2. **Create a MySQL/MariaDB database** on the host and import:
   - `config/schema.sql`
   - `config/migration.sql`
   - `config/seed.sql`
3. **Update database credentials** in `config/config.php` or set environment variables:
   ```php
   'db' => [
       'host'    => 'localhost',
       'port'    => '3306',
       'name'    => 'your_db_name',
       'user'    => 'your_db_user',
       'pass'    => 'your_db_password',
       'charset' => 'utf8mb4',
   ],
   ```
   Environment variables are also supported: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
4. **Update site settings** in the database (`settings` table) or edit `data/site.php` for brand/contact details.
5. **Ensure `.htaccess`** is uploaded and Apache `mod_rewrite` is enabled.
6. **Change the default admin password** after first login.

### Required PHP extensions

- `pdo`
- `pdo_mysql`
- `mbstring`
- `json`

## Notes

- The project no longer uses the old static-data-only architecture; it is now database-driven.
- Country flag CSS classes are approximations — replace with SVG flags if needed.
- Hot-linked Unsplash images are for demo only; upload production images to `uploads/`.
- For production, consider switching from the Tailwind Play CDN to a built CSS file.
# study-abroad-consultancy
