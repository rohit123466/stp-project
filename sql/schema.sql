-- ============================================================
-- Grovix Digital - Client Website Builder / CMS
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
('Search Engine Optimization', 'Our SEO service is built to get your business found by the customers already searching for what you offer. We start with in-depth keyword research to identify high-intent search terms in your industry, then optimize your website''s on-page elements (titles, meta descriptions, headings, and content) to match. We run technical audits to fix crawl errors, improve site speed, and ensure mobile-friendliness, and we build high-quality backlinks to strengthen your domain authority. You get monthly reporting so you can track rankings, organic traffic, and conversions in real numbers.\n\nWhat is included: keyword research and competitor analysis, on-page optimization, technical SEO audits, local SEO (Google Business Profile), link building, and a monthly performance report.\n\nWhy it matters: search is often the very first place a potential customer looks for a business like yours. Ranking on page one puts you in front of people who are already looking to buy, without paying for every click.', NULL, 'SEO Services | Grovix Digital', 'Boost your search rankings with Grovix Digital SEO services.'),
('Social Media Marketing', 'We manage your brand''s presence across Instagram, Facebook, and LinkedIn so you can stay focused on running your business. This includes a content calendar with scheduled posts, on-brand graphics and copywriting, community management (responding to comments and messages), and paid ad campaigns targeted at your ideal audience. We track engagement, follower growth, and click-throughs, and adjust your strategy every month based on what is actually working.\n\nWhat is included: content calendar and scheduling, custom graphics and captions, community management, influencer outreach, paid social ad campaigns, and monthly analytics reporting.\n\nWhy it matters: your customers are already spending hours every day on social media. A consistent, well-run presence builds trust and keeps your brand top of mind long before someone is ready to buy.', NULL, 'Social Media Marketing | Grovix Digital', 'Grow your brand on social media with our expert marketing team.'),
('Website Design & Development', 'We design and build custom, responsive websites that look great on every device and are built to convert visitors into customers. Every project starts with understanding your brand and your customers'' journey, followed by wireframes, a custom design, and hand-coded, clean development. We optimize for fast load times, accessibility, and basic on-page SEO from day one. Once live, we hand over a site that is easy for your team to update, with optional ongoing maintenance and support.\n\nWhat is included: custom UI/UX design, responsive front-end development, CMS integration where needed, basic on-page SEO setup, cross-browser testing, and 30 days of post-launch support.\n\nWhy it matters: your website is often the first real impression of your business. A fast, professional, mobile-friendly site builds credibility and turns casual visitors into paying customers.', NULL, 'Website Design & Development | Grovix Digital', 'Get a fast, responsive website designed to grow your business.'),
('Pay-Per-Click Advertising', 'We run data-driven Google Ads and Meta Ads campaigns designed to get you measurable results, not just clicks. This includes campaign strategy, keyword and audience research, ad copywriting, landing page recommendations, and continuous bid and budget optimization. We track cost-per-lead and return on ad spend closely, and provide transparent monthly reports so you always know exactly what your ad budget is achieving.\n\nWhat is included: campaign strategy and keyword research, ad copywriting and creative, landing page recommendations, ongoing bid and budget optimization, conversion tracking setup, and monthly ROI reporting.\n\nWhy it matters: paid ads put your business in front of the right people immediately, complementing the slower, long-term gains of SEO with fast, measurable traffic and leads.', NULL, 'PPC Advertising | Grovix Digital', 'Get measurable results with our PPC advertising services.');

-- ------------------------------------------------------------
-- Table: portfolio
-- ------------------------------------------------------------
CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    client_name VARCHAR(150) NOT NULL,
    project_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO portfolio (title, description, image, client_name, project_url) VALUES
('Moviefy - AI-Powered Movie Discovery', 'A movie discovery and streaming-guide platform built to help users find what to watch. Browse trending and top-rated titles, search with advanced filters, explore films by director, and describe a mood to get AI-suggested picks. Moviefy sources listings from third-party platforms rather than hosting media itself.', NULL, 'Personal Project', 'https://moviefy-dusky.vercel.app/'),
('IPL Auction Game', 'An interactive cricket player-auction web app inspired by the IPL Super Auction, where users can experience the bidding and team-building side of a franchise cricket auction in the browser.', NULL, 'Personal Project', 'https://iplauctiongame.vercel.app/');

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
('Ritika Sharma', 'Grovix Digital transformed our online presence completely. Our leads doubled within three months!', 5, NULL),
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
