-- Manami Education — Migration: New tables for leads, payments, SSW tracker, pixel events
-- Run this AFTER the base schema.sql has been imported.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- 1. LEADS TABLE (contact form + CTA form submissions)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS leads (
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

-- -----------------------------------------------------------
-- 2. PAYMENT_TRANSACTIONS TABLE (normalized payment tracking)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS payment_transactions (
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

-- -----------------------------------------------------------
-- 3. SSW_APPLICATIONS TABLE (SSW workflow tracker)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS ssw_applications (
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

-- -----------------------------------------------------------
-- 4. SSW_STAGE_HISTORY (audit trail for stage changes)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS ssw_stage_history (
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

-- -----------------------------------------------------------
-- 5. PIXEL_EVENTS_LOG (server-side event tracking)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pixel_events_log (
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

-- -----------------------------------------------------------
-- 6. ORDERS: payment method column
-- -----------------------------------------------------------
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS payment_method ENUM('bkash','cod') NOT NULL DEFAULT 'bkash' AFTER email;

SET FOREIGN_KEY_CHECKS = 1;
