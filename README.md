# Manami Education website

Server-rendered study-abroad consultancy website for Manami Education. The site includes public pages, an admin dashboard, a product catalogue and order flow, class-batch calendars, and an SSW application tracker.

This guide is the starting point for developers maintaining or deploying the project. Read the database and deployment warnings before running SQL on a populated site.

## Technology and runtime

- **PHP:** 8.0 or newer; PHP 8.2 is used in the local XAMPP environment.
- **Database:** MySQL or MariaDB, accessed through PDO with native prepared statements.
- **Web server:** Apache with `.htaccess`, `mod_rewrite`, `mod_headers`, and `mod_deflate` enabled.
- **Frontend:** Server-rendered PHP, custom CSS in `assets/css/`, Tailwind Play CDN utilities, Alpine.js, and a small site script.
- **Build/dependency manager:** None currently. There is no Composer, npm, or frontend build step.

The app is intentionally organized around small PHP entry points and `App\...` classes. The bootstrap autoloader maps `App\Foo\Bar` to `app/Foo/Bar.php`; add application classes under that namespace and follow the existing file naming convention.

### External frontend dependencies

Tailwind, Alpine.js, and Google Fonts are currently loaded from external CDNs. Tailwind Play CDN is convenient for the current no-build setup, but Tailwind recommends compiling CSS for production. A future local asset build should be planned and tested across all public and admin pages before removing the CDN; do not simply delete it.

## Repository map

```text
.
├── index.php                 # Homepage entry point and homepage data loading
├── *.php                     # Public pages: about, services, contact, legal, shop, etc.
├── admin/                    # Admin routes and dashboard
│   ├── _layout/              # Shared admin head, sidebar, topbar, and footer
│   ├── content/              # Editable site content
│   ├── leads/                # Lead inbox and lead details
│   ├── orders/               # Order list and order details
│   ├── products/             # Product CRUD
│   ├── ssw/                  # Skilled Worker application workflow
│   └── users/                # Admin user management
├── api/                      # Form, cart, order, payment-return, and file endpoints
├── app/                      # Application classes and shared bootstrap
│   ├── Payment/              # Payment gateway interface and adapters
│   ├── i18n/                 # English, Bengali, and Japanese translations
│   └── bootstrap.php         # Helpers, autoloader, session, and language startup
├── assets/
│   ├── css/                  # Global styles and country flags
│   ├── js/                   # Shared browser behavior
│   └── img/                  # Static branding and PWA images
├── blog/, books/, country/   # Slug-based detail-page controllers
├── courses/                  # Legacy course-route redirects
├── config/
│   ├── config.php            # Runtime configuration (environment-aware)
│   ├── schema.sql            # Fresh database schema and initial baseline rows
│   └── migration.sql         # Additive schema changes for existing databases
├── data/                     # Small PHP defaults/editor definitions still in use
├── partials/                 # Shared public layout and homepage sections
├── scripts/                  # CLI-only maintenance jobs
├── uploads/                  # Runtime user uploads; do not commit uploaded files
├── .htaccess                 # Apache routing, headers, compression, and access rules
├── .editorconfig              # Basic editor consistency
└── README.md                 # This guide
```

### Where to make common changes

| Change | Main location |
|---|---|
| Public page or homepage section | Root page controller and relevant `partials/*.php` |
| Shared navigation/footer | `partials/header.php`, `partials/topbar.php`, `partials/footer.php` |
| Admin feature | Matching route under `admin/`; reuse `admin/_layout/` |
| Database-backed content | `app/Content.php`, `app/DataAdapter.php`, and the corresponding admin route |
| Business logic | A focused class under `app/` |
| Request validation/security | `app/Validation.php`, `app/Csrf.php`, `app/Auth.php`, or the relevant endpoint |
| Database structure | `config/schema.sql` for new installs; append compatible changes to `config/migration.sql` |
| Translation | `app/i18n/{en-GB,bn,ja}.php` |
| Static visual assets | `assets/css/`, `assets/js/`, and `assets/img/` |
| Runtime secrets/settings | Server environment variables or protected admin settings; never commit secrets |

`data/site.php`, `data/homepage_editor.php`, and `data/success_stories.php` still supply specific defaults/editor metadata. Most live content is stored in MySQL and edited from the dashboard. Do not add a second static copy of content that is already managed in the database.

## Request and data flow

1. A page or endpoint includes `app/bootstrap.php`.
2. The bootstrap loads shared helpers, registers the `App\` class autoloader, starts the session, and boots language selection.
3. Page controllers load content through application classes such as `App\DataAdapter`, `App\Content`, and `App\Settings`.
4. Forms submit to a page controller or an endpoint in `api/`; state-changing requests should validate input and CSRF tokens before writes.
5. Public templates escape text and attribute values with `h()`. Rich HTML must be explicitly sanitized/handled; do not render untrusted database content as raw HTML.

Apache rewrite rules in `.htaccess` map `/blog/{slug}`, `/books/{slug}`, and `/country/{slug}` to their detail controllers. Legacy `/courses/{slug}` URLs redirect to the books catalogue; preserve that compatibility route unless external links are intentionally retired.

## Local development

### Requirements

- PHP 8.0+ with `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`, and `json`.
- `curl` for Google Business Profile synchronization and remote payment integrations.
- MySQL/MariaDB.
- Apache with the required modules above.
- GD is optional; image resizing is skipped when GD functions are unavailable.

### XAMPP setup

1. Put the project under XAMPP's `htdocs` directory and start Apache and MySQL.
2. Create an empty development database:

   ```sql
   CREATE DATABASE manami_education
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

3. Import the schema and then the additive migration:

   ```bash
   /Applications/XAMPP/xamppfiles/bin/mysql -u root -p manami_education < config/schema.sql
   /Applications/XAMPP/xamppfiles/bin/mysql -u root -p manami_education < config/migration.sql
   ```

   `schema.sql` includes baseline settings, catalog content, and an initial administrator account. It is for a **new, empty database only** and contains `DROP TABLE` statements. Never import it over a live database.

4. Configure local DB access in environment variables or `config/config.php`.
5. Open `http://localhost/study-abroad-consultancy/` (not the XAMPP dashboard at `/`).
6. Sign in to the dashboard and replace the initial administrator password before exposing any deployment.

There is no `seed.sql`; baseline rows are part of `config/schema.sql`.

### Configuration

`config/config.php` reads environment variables and provides development defaults. Set production values in the hosting control panel/environment rather than committing credentials:

| Variable | Purpose |
|---|---|
| `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_KEY` | Runtime environment, error display, canonical base URL, app key |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | MySQL connection |
| `MAIL_FROM`, `MAIL_DRIVER` | Outgoing email configuration |
| `BKASH_MODE`, `BKASH_MERCHANT` | bKash configuration |
| `SSLCOMMERZ_STORE_ID`, `SSLCOMMERZ_STORE_PASS`, `SSLCOMMERZ_SANDBOX` | SSLCommerz credentials and mode |
| `PRIVATE_BOOK_PREVIEW_PATH` | Absolute writable path outside the public web root for sample PDFs |
| `GBP_ACCOUNT_ID`, `GBP_LOCATION_ID`, `GOOGLE_OAUTH_CLIENT_ID`, `GOOGLE_OAUTH_CLIENT_SECRET`, `GOOGLE_OAUTH_REFRESH_TOKEN` | Optional Google Business Profile review sync |

For production, set `APP_ENV=production`, `APP_DEBUG=false`, use a unique database user with only the required database privileges, configure HTTPS, and keep secret values out of the repository and web root. The hosting provider must honor the Apache access rules, or equivalent restrictions must be configured at the server level.

## Database changes

- **Fresh install:** create an empty database, import `config/schema.sql`, then `config/migration.sql`.
- **Existing deployment:** take and verify a database backup, inspect the migration, then apply `config/migration.sql`. It is designed for additive schema updates; still test it on a copy of production data first.
- Never re-import `schema.sql` on an existing site: it drops tables and data.
- Keep schema changes backward-compatible where possible. Update this guide when adding required environment variables or operational steps.

## Current application areas

- **Homepage:** managed copy and visual features live under Admin → Homepage / Homepage Features; section markup is in `partials/`.
- **Student success and testimonials:** dashboard-managed media/content; obtain permission before publishing identifiable student images.
- **Books, courses, and orders:** the catalogue is in `books.php`; legacy `courses.php` and `/courses/{slug}` URLs redirect there. Product detail routes are in `books/`; order actions are handled in `api/` and business logic in `app/Order.php`.
- **Order tracking:** public tracking uses order code plus phone/email contact checks and an IP-based failed-attempt limit. The limit requires the `order_tracking_attempts` table from the migration.
- **Book previews:** private PDFs are streamed through `api/book-preview.php` after order and payment checks; do not place these PDFs under public `uploads/`.
- **Class schedule:** staff manage batches and holidays in `admin/class-batches.php`; the public calendar is `batch-schedule.php`.
- **SSW pipeline:** `app/SSW.php` contains workflow operations; admin pages are under `admin/ssw/`.
- **Google reviews:** `scripts/sync-google-reviews.php` is CLI-only. Configure the GBP variables above and schedule it with the hosting cron service only after validating credentials and API access.
- **Legacy quick-order URL:** `quick-order.php` redirects to `books.php`; keep this redirect unless external links and search indexing are intentionally retired.
- **Legacy course URLs:** `courses.php` and `/courses/{slug}` redirect to `books.php`; keep these redirects unless external links and search indexing are intentionally retired.
- **Browser/offline cache:** application responses use no-store headers and the service worker removes old Manami registrations/caches. Static asset caching is also disabled by the current Apache rules.

## Coding conventions

- Keep request/response orchestration in page controllers; put reusable business rules in `App\...` classes.
- Use one class per matching file under `app/`; namespace it consistently so the bootstrap autoloader can find it.
- Use PDO prepared statements for variable SQL values. Do not concatenate request data into SQL.
- Validate inputs server-side, check authorization at the mutation handler (not only in the UI), and verify CSRF tokens on state-changing browser requests.
- Escape output at the template boundary. Treat database values as untrusted.
- Keep public routes, rewrite targets, admin role checks, and translations consistent when changing a feature.
- Avoid adding duplicated static content, unused compatibility code, or new runtime dependencies without a concrete need.
- Update this README and relevant translation strings when a feature or deployment requirement changes.

## Validation

There is currently no PHPUnit/Pest suite, static analyzer, formatter, Composer manifest, or npm build configuration. Run the PHP parser across the project after PHP changes:

```bash
find . -type f -name '*.php' -not -path './uploads/*' -print0 | xargs -0 -n1 php -l
```

For route smoke tests with the local Apache server running:

```bash
curl -sS -o /dev/null -w '%{http_code} %{url_effective}\n' \
  http://localhost/study-abroad-consultancy/
```

Also manually exercise the affected public page, dashboard workflow, and form submission against a disposable development database. Syntax checks and HTTP status codes do not replace functional, accessibility, browser, or payment-provider tests.

## Deployment checklist

- [ ] Back up the current database and uploaded files.
- [ ] Deploy the PHP files and apply the reviewed migration to the target database.
- [ ] Set production environment variables and remove development credentials.
- [ ] Confirm PHP version/extensions and writable upload/private-PDF directories.
- [ ] Confirm HTTPS, Apache rewrites, headers, and protected paths work on the actual host.
- [ ] Change/verify administrator credentials and remove unused accounts.
- [ ] Verify mail delivery and any payment gateway in sandbox before enabling live payments.
- [ ] Verify a public order lookup, a paid-order preview authorization, batch calendar, and admin edit flows.
- [ ] Review logs, upload permissions, backups, and hosting cron configuration.
- [ ] Test the site from outside the hosting account; local XAMPP checks cannot validate Hostinger behavior.
