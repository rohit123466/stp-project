-- ============================================================
-- OpportuneX Digital - Client Website Builder / CMS
-- Database Schema + Seed Data
-- Import this file in phpMyAdmin (XAMPP) to set up the project
-- ============================================================

CREATE DATABASE IF NOT EXISTS opportunex_cms;
USE opportunex_cms;

-- ------------------------------------------------------------
-- Table: admins
-- Stores admin panel login accounts. Password is hashed using
-- PHP's password_hash() function, never stored in plain text.
-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin account row. The password column below is a placeholder,
-- NOT a valid hash. After importing this file, run /sql/seed_admin.php
-- once in your browser -- it uses PHP's own password_hash() to set the
-- real password (admin / admin123) so the hash is guaranteed to match
-- your PHP version. Delete seed_admin.php afterwards (see README).
INSERT INTO admins (username, password) VALUES
('admin', 'RUN_SEED_ADMIN_PHP_SCRIPT_TO_SET_PASSWORD');

-- ------------------------------------------------------------
-- Table: services
-- ------------------------------------------------------------
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    meta_title VARCHAR(160) DEFAULT NULL,
    meta_description VARCHAR(300) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO services (title, description, image, meta_title, meta_description) VALUES
('Search Engine Optimization', 'We help your business rank higher on Google with keyword research, on-page SEO, and technical audits that drive organic traffic.', NULL, 'SEO Services | OpportuneX Digital', 'Boost your search rankings with OpportuneX Digital SEO services.'),
('Social Media Marketing', 'From content calendars to paid ad campaigns, we manage your brand presence across Instagram, Facebook, and LinkedIn.', NULL, 'Social Media Marketing | OpportuneX Digital', 'Grow your brand on social media with our expert marketing team.'),
('Website Design & Development', 'Custom, responsive websites built to convert visitors into customers, backed by clean code and modern design.', NULL, 'Website Design & Development | OpportuneX Digital', 'Get a fast, responsive website designed to grow your business.'),
('Pay-Per-Click Advertising', 'We run data-driven Google Ads and Meta Ads campaigns that maximize ROI and minimize wasted ad spend.', NULL, 'PPC Advertising | OpportuneX Digital', 'Get measurable results with our PPC advertising services.');

-- ------------------------------------------------------------
-- Table: portfolio
-- ------------------------------------------------------------
CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    client_name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO portfolio (title, description, image, client_name) VALUES
('E-commerce Rebrand', 'Complete rebrand and Shopify storefront redesign that increased conversion rate by 32%.', NULL, 'Urban Threads Clothing'),
('Local SEO Campaign', 'Ranked client in the top 3 Google Map Pack results for 15 local search terms within 4 months.', NULL, 'GreenLeaf Cafe'),
('Lead Generation Funnel', 'Built a landing page + PPC funnel that reduced cost-per-lead by 40%.', NULL, 'Apex Fitness Studio');

-- ------------------------------------------------------------
-- Table: testimonials
-- ------------------------------------------------------------
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO testimonials (client_name, message, rating, image) VALUES
('Ritika Sharma', 'OpportuneX Digital transformed our online presence completely. Our leads doubled within three months!', 5, NULL),
('Aman Verma', 'Professional, responsive, and results-driven. The SEO work they did put us on the map — literally.', 5, NULL),
('Priya Nair', 'Great team to work with. They understood our brand voice and delivered a website we are proud of.', 4, NULL);

-- ------------------------------------------------------------
-- Table: leads
-- Stores contact form submissions from the public site.
-- status tracks the sales pipeline stage for each lead.
-- ------------------------------------------------------------
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('New','Contacted','Interested','Converted','Lost') NOT NULL DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO leads (name, email, phone, message, status) VALUES
('Rahul Mehta', 'rahul.mehta@example.com', '9876543210', 'Interested in a new website for my restaurant business.', 'New'),
('Sneha Kapoor', 'sneha.kapoor@example.com', '9123456780', 'Can you help improve our Instagram engagement?', 'Contacted'),
('Vikram Singh', 'vikram.singh@example.com', '9988776655', 'Looking for SEO services for our e-commerce store.', 'Interested'),
('Anjali Gupta', 'anjali.gupta@example.com', '9090909090', 'We signed up last week, following up on the contract.', 'Converted'),
('Karan Malhotra', 'karan.malhotra@example.com', '9001122334', 'Requested a quote but went with another agency.', 'Lost'),
('Neha Joshi', 'neha.joshi@example.com', '9871234560', 'Need a landing page for an upcoming product launch.', 'New');
