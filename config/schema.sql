-- Manami Education — full schema
-- Import this once into an empty MySQL/MariaDB database.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_log;
DROP TABLE IF EXISTS ssw_document_checklist;
DROP TABLE IF EXISTS ssw_stage_history;
DROP TABLE IF EXISTS ssw_applications;
DROP TABLE IF EXISTS class_holidays;
DROP TABLE IF EXISTS class_batches;
DROP TABLE IF EXISTS pixel_events_log;
DROP TABLE IF EXISTS payment_transactions;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS countries;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS settings;

CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(190) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin','editor','viewer') NOT NULL DEFAULT 'editor',
    status          ENUM('active','disabled') NOT NULL DEFAULT 'active',
    must_change_pw  TINYINT(1) NOT NULL DEFAULT 0,
    last_login_at   DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE login_attempts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip            VARCHAR(64) NOT NULL,
    email         VARCHAR(190) NOT NULL,
    succeeded     TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip, attempted_at),
    INDEX idx_email_time (email, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
    `key`        VARCHAR(80) NOT NULL PRIMARY KEY,
    `value`      TEXT NOT NULL,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE banners (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot        VARCHAR(40) NOT NULL DEFAULT 'hero',
    image_id    INT UNSIGNED NULL,
    heading     VARCHAR(255) NULL,
    subheading  VARCHAR(255) NULL,
    cta_text    VARCHAR(80) NULL,
    cta_href    VARCHAR(255) NULL,
    sort        INT NOT NULL DEFAULT 0,
    active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE media (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type          ENUM('image','video','file') NOT NULL DEFAULT 'image',
    path          VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime          VARCHAR(120) NOT NULL,
    size_bytes    INT UNSIGNED NOT NULL,
    alt           VARCHAR(255) NULL,
    uploaded_by   INT UNSIGNED NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE countries (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(8) NOT NULL UNIQUE,
    name        VARCHAR(80) NOT NULL,
    flag_css    VARCHAR(40) NULL,
    image_id    INT UNSIGNED NULL,
    description VARCHAR(255) NULL,
    sort        INT NOT NULL DEFAULT 0,
    active      TINYINT(1) NOT NULL DEFAULT 1,
    featured    TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE services (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(80) NOT NULL UNIQUE,
    title       VARCHAR(120) NOT NULL,
    icon        VARCHAR(40) NULL,
    summary     VARCHAR(255) NULL,
    body        MEDIUMTEXT NULL,
    cta_text    VARCHAR(80) NULL,
    cta_href    VARCHAR(255) NULL,
    sort        INT NOT NULL DEFAULT 0,
    active      TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE testimonials (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    country    VARCHAR(80) NULL,
    photo_id   INT UNSIGNED NULL,
    rating     TINYINT NOT NULL DEFAULT 5,
    body       TEXT NOT NULL,
    sort       INT NOT NULL DEFAULT 0,
    active     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE success_stories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    photo_id    INT UNSIGNED NULL,
    destination VARCHAR(80) NOT NULL,
    story_year  SMALLINT UNSIGNED NOT NULL,
    program     VARCHAR(160) NOT NULL,
    quote       TEXT NOT NULL,
    sort        INT NOT NULL DEFAULT 0,
    active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_success_stories_active_sort (active, sort, id),
    CONSTRAINT fk_success_story_photo FOREIGN KEY (photo_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE faqs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question   VARCHAR(255) NOT NULL,
    answer     TEXT NOT NULL,
    sort       INT NOT NULL DEFAULT 0,
    active     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE posts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug          VARCHAR(160) NOT NULL UNIQUE,
    type          ENUM('blog','news') NOT NULL DEFAULT 'blog',
    title         VARCHAR(255) NOT NULL,
    excerpt       VARCHAR(500) NULL,
    body          MEDIUMTEXT NULL,
    cover_id      INT UNSIGNED NULL,
    author_id     INT UNSIGNED NULL,
    status        ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at  DATETIME NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type_status (type, status, published_at),
    CONSTRAINT fk_post_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug          VARCHAR(160) NOT NULL UNIQUE,
    type          ENUM('physical','digital','course') NOT NULL DEFAULT 'physical',
    title         VARCHAR(255) NOT NULL,
    summary       VARCHAR(500) NULL,
    description   MEDIUMTEXT NULL,
    price_bdt     DECIMAL(10,2) NOT NULL DEFAULT 0,
    cover_id      INT UNSIGNED NULL,
    gallery_json  JSON NULL,
    file_path     VARCHAR(255) NULL,
    preview_pdf_path VARCHAR(255) NULL,
    stock         INT NOT NULL DEFAULT 0,
    sold          INT NOT NULL DEFAULT 0,
    sort          INT NOT NULL DEFAULT 0,
    active        TINYINT(1) NOT NULL DEFAULT 1,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code              VARCHAR(40) NOT NULL UNIQUE,
    name              VARCHAR(120) NOT NULL,
    address           VARCHAR(255) NOT NULL,
    thana             VARCHAR(80) NOT NULL,
    phone             VARCHAR(40) NOT NULL,
    email             VARCHAR(190) NULL,
    payment_method    ENUM('bkash','cod') NOT NULL DEFAULT 'bkash',
    bkash_txn         VARCHAR(80) NULL,
    bkash_screenshot  VARCHAR(255) NULL,
    subtotal          DECIMAL(10,2) NOT NULL DEFAULT 0,
    shipping          DECIMAL(10,2) NOT NULL DEFAULT 0,
    total             DECIMAL(10,2) NOT NULL DEFAULT 0,
    status            ENUM('placed','confirmed','paid','processing','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'placed',
    notes             TEXT NULL,
    ip                VARCHAR(64) NULL,
    user_agent        VARCHAR(255) NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    title        VARCHAR(255) NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    qty          INT NOT NULL DEFAULT 1,
    line_total   DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE activity_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(60) NOT NULL,
    entity      VARCHAR(60) NULL,
    entity_id   INT UNSIGNED NULL,
    meta        JSON NULL,
    ip          VARCHAR(64) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entity (entity, entity_id),
    INDEX idx_user_time (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE leads (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(120) NOT NULL,
    phone            VARCHAR(40) NOT NULL,
    email            VARCHAR(190) NULL,
    destination      VARCHAR(80) NULL,
    study_level      VARCHAR(80) NULL,
    service_interest ENUM('student-visa','ssw','titp','jlpt','ielts','career-counselling','other') NULL,
    message          TEXT NULL,
    source           ENUM('contact_form','cta_form','book_page','course_page','facebook_ad','organic') NOT NULL DEFAULT 'contact_form',
    status           ENUM('new','contacted','qualified','converted','lost') NOT NULL DEFAULT 'new',
    assigned_to      INT UNSIGNED NULL,
    fbclid           VARCHAR(120) NULL,
    gclid            VARCHAR(120) NULL,
    ip               VARCHAR(64) NULL,
    user_agent       VARCHAR(255) NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status, created_at),
    INDEX idx_source (source, created_at),
    INDEX idx_email (email),
    CONSTRAINT fk_lead_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payment_transactions (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id         INT UNSIGNED NOT NULL,
    method           ENUM('bkash_manual','bkash_api','sslcommerz','paypal','stripe','cod','bank_transfer') NOT NULL,
    amount           DECIMAL(10,2) NOT NULL,
    currency         VARCHAR(3) NOT NULL DEFAULT 'BDT',
    txn_reference    VARCHAR(120) NULL,
    gateway_response JSON NULL,
    screenshot_path  VARCHAR(255) NULL,
    status           ENUM('pending','verified','failed','refunded') NOT NULL DEFAULT 'pending',
    verified_by      INT UNSIGNED NULL,
    verified_at      DATETIME NULL,
    notes            TEXT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_method_status (method, status),
    INDEX idx_reference (txn_reference),
    CONSTRAINT fk_pt_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_pt_verified FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ssw_applications (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id          INT UNSIGNED NULL,
    order_id         INT UNSIGNED NULL,
    applicant_name   VARCHAR(120) NOT NULL,
    applicant_phone  VARCHAR(40) NOT NULL,
    applicant_email  VARCHAR(190) NULL,
    passport_number  VARCHAR(40) NULL,
    jlpt_level       ENUM('none','n5','n4','n3','n2','n1') NOT NULL DEFAULT 'none',
    industry         VARCHAR(80) NULL,
    preferred_prefecture VARCHAR(80) NULL,
    current_stage    ENUM(
        'assessment',
        'jlpt_prep',
        'jlpt_registered',
        'jlpt_passed',
        'skills_test_prep',
        'skills_test_passed',
        'employer_matching',
        'employer_matched',
        'coa_application',
        'coa_approved',
        'visa_application',
        'visa_approved',
        'departure_prep',
        'deployed',
        'onboarded'
    ) NOT NULL DEFAULT 'assessment',
    jlpt_test_date   DATE NULL,
    skills_test_date DATE NULL,
    employer_name    VARCHAR(200) NULL,
    coa_reference    VARCHAR(80) NULL,
    visa_reference   VARCHAR(80) NULL,
    departure_date   DATE NULL,
    notes            TEXT NULL,
    assigned_to      INT UNSIGNED NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_stage (current_stage, created_at),
    INDEX idx_jlpt (jlpt_level),
    INDEX idx_lead (lead_id),
    INDEX idx_order (order_id),
    CONSTRAINT fk_ssw_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL,
    CONSTRAINT fk_ssw_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    CONSTRAINT fk_ssw_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ssw_stage_history (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id   INT UNSIGNED NOT NULL,
    from_stage       VARCHAR(40) NULL,
    to_stage         VARCHAR(40) NOT NULL,
    changed_by       INT UNSIGNED NULL,
    notes            TEXT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_application (application_id),
    CONSTRAINT fk_ssh_app FOREIGN KEY (application_id) REFERENCES ssw_applications(id) ON DELETE CASCADE,
    CONSTRAINT fk_ssh_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ssw_document_checklist (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id   INT UNSIGNED NOT NULL,
    document_key     VARCHAR(40) NOT NULL,
    status           ENUM('not_started','received','verified','not_applicable') NOT NULL DEFAULT 'not_started',
    notes            VARCHAR(500) NULL,
    updated_by       INT UNSIGNED NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ssw_document (application_id, document_key),
    INDEX idx_ssw_document_status (status),
    CONSTRAINT fk_ssw_document_app FOREIGN KEY (application_id) REFERENCES ssw_applications(id) ON DELETE CASCADE,
    CONSTRAINT fk_ssw_document_user FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pixel_events_log (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name       VARCHAR(60) NOT NULL,
    page_url         VARCHAR(500) NOT NULL,
    referrer         VARCHAR(500) NULL,
    fb_browser_id    VARCHAR(120) NULL,
    fb_pixel_id      VARCHAR(120) NULL,
    meta_json        JSON NULL,
    ip               VARCHAR(64) NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event (event_name, created_at),
    INDEX idx_page (page_url(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE class_batches (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_code       VARCHAR(40) NOT NULL UNIQUE,
    course_name      VARCHAR(120) NOT NULL,
    start_date       DATE NOT NULL,
    duration_months  TINYINT UNSIGNED NOT NULL DEFAULT 4,
    class_weekdays   VARCHAR(20) NOT NULL,
    class_time       VARCHAR(80) NULL,
    class_location   VARCHAR(120) NULL,
    active           TINYINT(1) NOT NULL DEFAULT 1,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_class_batches_active_start (active, start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE class_holidays (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    holiday_date DATE NOT NULL UNIQUE,
    title        VARCHAR(120) NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO users (name, email, password_hash, role, must_change_pw) VALUES
('Administrator', 'admin@manami.education', '$2y$12$9neMuXcFUIBf4VSXRUuKeOjALfZDN0x3O0yAnOOz9JMBimDj.UYL.', 'admin', 1);

INSERT INTO settings (`key`,`value`) VALUES
('brand_name',        'Manami'),
('brand_tagline',     'EDUCATION'),
('brand_slogan',      'Manami Leads, You Succeed...'),
('contact_phone',     '+880 1817-890995'),
('contact_phone_tel', '+8801817890995'),
('contact_email',     'manamieducationbd@gmail.com'),
('contact_address',   '20/A, B, Block No-2, Gate No-3, Monipuripara, Farmgate, Dhaka, 1215, Bangladesh'),
('contact_address_link', 'https://maps.google.com/?q=20%2FA+B+Block+No-2+Monipuripara+Farmgate+Dhaka+1215'),
('domain',            'https://manamieducation.com.bd'),
('og_image',          'https://manamieducation.com.bd/assets/img/og-default.jpg'),
('event_title',       'Manami Study Japan Expo 2026'),
('event_date',        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 21 DAY), '%Y-%m-%d')),
('social_json',       '[{"label":"Facebook","href":"https://facebook.com/","icon":"facebook"},{"label":"Instagram","href":"https://instagram.com/","icon":"instagram"},{"label":"YouTube","href":"https://youtube.com/","icon":"youtube"},{"label":"TikTok","href":"https://tiktok.com/","icon":"tiktok"},{"label":"X","href":"https://x.com/","icon":"x"}]'),
('nav_json',          '[{"key":"home","href":"index.php"},{"key":"quick_order","href":"quick-order.php"},{"key":"study_in_japan","children":[{"key":"student_visa","href":"services.php#student-visa"},{"key":"ssw","href":"services.php#ssw"},{"key":"titp","href":"services.php#titp"},{"key":"japanese_lang","href":"services.php#japanese"},{"key":"ielts","href":"services.php#ielts"}]},{"key":"destinations","children":[{"key":"destinations_page","href":"destinations.php"},{"key":"japan","href":"country/japan"},{"key":"australia","href":"country/australia"},{"key":"canada","href":"country/canada"},{"key":"united_kingdom","href":"country/united-kingdom"},{"key":"united_states","href":"country/united-states"},{"key":"europe","href":"country/europe"},{"key":"malaysia","href":"country/malaysia"},{"key":"south_korea","href":"country/south-korea"}]},{"key":"courses","href":"courses.php"},{"key":"books_courses","href":"books.php"},{"key":"events","href":"events.php"}]'),
('stats_json',        '[{"label":"Students Placed","value":300},{"label":"Partner Institutions","value":85},{"label":"Courses Available","value":240},{"label":"Visa Success Rate","value":"99%"}]'),
('logo_image_id',     ''),
('favicon_image_id',     ''),
('apple_touch_icon_image_id', ''),
('og_image_id',       '');

INSERT INTO countries (code,name,flag_css,description,sort,active,featured) VALUES
('jp','Japan','flag-jp','Featured destination — student visa, SSW, TITP',1,1,1),
('au','Australia','flag-au','Future destination',2,1,0),
('ca','Canada','flag-ca','Future destination',3,1,0),
('gb','United Kingdom','flag-gb','Future destination',4,1,0),
('us','United States','flag-us','Future destination',5,1,0),
('eu','Europe','flag-eu','Future destination',6,1,0),
('my','Malaysia','flag-my','Future destination',7,1,0),
('kr','South Korea','flag-kr','Future destination',8,1,0);

INSERT INTO services (slug,title,icon,summary,body,cta_text,cta_href,sort,active) VALUES
('student-visa','Student Visa Support','book','Enrol at a Japanese language school, university, or college.','We help you shortlist JLPT-friendly schools, prepare documents, and file your COE + visa application end to end. Typical timeline: 4–6 months.','Start Student Visa','#contact',1,1),
('ssw','Skilled Worker Programme (SSW)','route','Work in Japan under the Specified Skilled Worker visa.','SSW lets you work in 16 designated industries (care, construction, food service, etc.). We match you with vetted employers, prepare JLPT N4 / skills-test evidence, and file your visa.','Start SSW','#contact',2,1),
('titp','Technical Intern Training (TITP)','plane','Train and work in Japan for 1–5 years.','TITP places you with a sending organisation and Japanese host company in industries like agriculture, manufacturing, and textiles. Includes JLPT N4 / N3 prep.','Start TITP','#contact',3,1),
('ielts','IELTS Preparation','award','Band 6.5+ for English-medium study.','A 6-week intensive covering all four modules with mock tests and personalised feedback.','Enrol Now','#contact',4,1),
('jlpt','Japanese Language Courses (JLPT N5–N1)','globe','From zero to JLPT N1 with native-trained teachers.','Levels N5 → N1, weekend and weekday batches, in-person (Dhanmondi) and online.','Enrol Now','#contact',5,1),
('career-counselling','Career Counselling','compass','Personalised pathway to Japan.','Free 30-minute assessment with a registered counsellor: pick the right visa, the right school, and the right budget.','Book Free Session','#contact',6,1);

INSERT INTO testimonials (name,country,rating,body,sort,active) VALUES
('Rahim Ahmed','Japan',5,'Manami Education handled my student visa from Dhaka to Osaka. Got into a top language school in 5 months.',1,1),
('Sumaiya Khan','Japan',5,'SSW pathway was confusing until Manami explained it step by step. Now working in Nagoya food service.',2,1),
('Tanvir Hossain','Japan',5,'TITP placed me with a great host company. JLPT N4 prep was excellent.',3,1),
('Nusrat Jahan','Japan',5,'Free assessment was honest — they even told me to wait a year to save money. Trusted them with my Student Visa.',4,1);

INSERT INTO faqs (question,answer,sort,active) VALUES
('What visa should I apply for to study in Japan?','For full-time study at a Japanese university, college, or language school, the Student Visa (Ryugaku) is the right fit. We help with both the school admission and the visa application.',1,1),
('What is SSW and how is it different from TITP?','SSW (Specified Skilled Worker) is a work visa in 16 industries, valid for up to 5 years and renewable. TITP is a training program (1–3 years, extendable to 5) that can convert into SSW. Both require JLPT N4 or an industry skills test.',2,1),
('How long does the whole process take?','Student visa: 4–6 months. SSW: 3–5 months. TITP: 2–4 months. We start with a free assessment so you know the realistic timeline for your case.',3,1),
('Do I need to know Japanese before applying?','For Student Visa, basic Japanese is helpful but many schools start from zero. For SSW and TITP, JLPT N4 (or an industry skills test) is required — we run JLPT N4 prep in-house.',4,1),
('What documents do I need?','Typical set: passport, academic transcripts & certificates, financial proof (bank statement / sponsor letter), JLPT certificate (if any), photos, and CV. We give you a personalised checklist after the free assessment.',5,1),
('How much does it cost?','Manami Education charges a transparent consultancy fee; school tuition and living costs are paid directly to the institution. You will see a full cost breakdown within 48 hours of your free assessment.',6,1),
('Can I work part-time on a Student Visa?','Yes — up to 28 hours/week with school permission. We brief all our students on the rules before they fly.',7,1);

INSERT INTO posts (slug,type,title,excerpt,body,author_id,status,published_at) VALUES
('japan-student-visa-checklist-2026','blog','Japan Student Visa Checklist for 2026 (Bangladeshi Applicants)','A complete document and timeline checklist for Bangladesh → Japan Student Visa in 2026.','<p>Applying for a Japanese Student Visa from Bangladesh in 2026? Here is the exact checklist and timeline we use at Manami Education…</p><p><strong>Step 1:</strong> Free assessment with our counsellor…</p>',1,'published',NOW()),
('ssw-vs-titp-which-is-right-for-me','blog','SSW vs TITP — Which Japan Work Pathway Is Right for You?','Both lead to working in Japan, but the requirements, pay, and timeline differ. Here is how to choose.','<p>SSW and TITP are the two main pathways for Bangladeshis who want to work in Japan. Here is how they compare…</p>',1,'published',NOW()),
('jlpt-n4-6-month-plan','blog','JLPT N4 in 6 Months — A Realistic Study Plan','JLPT N4 in 6 months is doable if you follow this plan from ex-N4 examiners.','<p>If you can dedicate 1.5 hours a day, this is the plan we give our SSW and TITP candidates…</p>',1,'published',NOW()),
('manami-study-japan-expo-2026','news','Manami Study Japan Expo 2026 — Save the Date','Our biggest Japan-focused event of the year, free entry, in Dhaka.','<p>We are back with our biggest Japan-focused event. Meet Japanese language school representatives, employers, and past students…</p>',1,'published',NOW());

INSERT INTO products (slug,type,title,summary,description,price_bdt,stock,sort,active) VALUES
('manami-japan-handbook','physical','Manami Japan Handbook (2026 Edition)','The complete Bangladesh → Japan handbook. 240 pages, full colour.','A 240-page full-colour handbook covering Student Visa, SSW, TITP, JLPT prep, packing, and life in Japan. Written by Manami counsellors.',1200.00,200,1,1),
('jlpt-n4-vocabulary-deck','digital','JLPT N4 Vocabulary Deck (PDF + Anki)','1,500 essential N4 words with example sentences.','1,500 essential N4 vocabulary items with English and Bangla meanings, example sentences, and an Anki deck. Instant download after payment.',500.00,9999,2,1),
('ssw-starter-pack','digital','SSW Starter Pack (Templates + Checklist)','All the templates you need for the SSW application.','Document templates (CV Japanese style, sponsor letter, financial proof) plus a 40-page step-by-step checklist for the SSW pathway.',1500.00,9999,3,1),
('manami-japan-orientation-course','course','Manami Japan Orientation Course (4 Weeks, Live)','4-week live online orientation before you fly.','A 4-week live online course covering: banking, SIM, housing, transport, emergency contacts, JLPT roadmap, and 1:1 Q&A with a Japan-based mentor.',8000.00,50,4,1);
