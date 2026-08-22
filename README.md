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
- **Multi-language** support (English, Japanese, Bengali, Hindi)
- **Admin dashboard** for content, orders, leads, and settings
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

## Default admin access

- URL: `http://localhost/study-abroad-consultancy/admin/login.php`
- Email: `admin@manami.education`
- Password: `admin123`

Change the admin password immediately after the first login.

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
