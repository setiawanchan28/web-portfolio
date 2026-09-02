-- MySQL Database Schema for Portfolio Dynamic CMS
-- Database: portfolio_db

CREATE DATABASE IF NOT EXISTS `portfolio_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `site_title` VARCHAR(150) DEFAULT 'Developer Portfolio | IT Professional',
  `owner_name` VARCHAR(100) DEFAULT '[NAMA ANDA]',
  `headline` VARCHAR(255) DEFAULT 'Building Digital Experiences That Matter.',
  `bio_short` TEXT,
  `email` VARCHAR(100) DEFAULT 'admin@example.com',
  `phone_whatsapp` VARCHAR(20) DEFAULT '+6281234567890',
  `location` VARCHAR(100) DEFAULT 'Indonesia',
  `cv_file` VARCHAR(255) DEFAULT NULL,
  `favicon` VARCHAR(255) DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `primary_color` VARCHAR(20) DEFAULT '#38bdf8',
  `footer_text` VARCHAR(255) DEFAULT 'All Rights Reserved.',
  `telegram_bot_token` VARCHAR(255) DEFAULT NULL,
  `telegram_chat_id` VARCHAR(100) DEFAULT NULL,
  `meta_keywords` TEXT,
  `meta_description` TEXT,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Hero Table
CREATE TABLE IF NOT EXISTS `hero` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `greeting` VARCHAR(100) DEFAULT 'Halo, Saya',
  `greeting_en` VARCHAR(100) DEFAULT 'Hello, I am',
  `headline` VARCHAR(255) DEFAULT 'Pranata Komputer & Fullstack Developer',
  `headline_en` VARCHAR(255) DEFAULT 'IT Specialist & Fullstack Developer',
  `subheadline` TEXT,
  `subheadline_en` TEXT,
  `cta_text_primary` VARCHAR(50) DEFAULT 'Lihat Portfolio',
  `cta_url_primary` VARCHAR(255) DEFAULT '#projects',
  `cta_text_secondary` VARCHAR(50) DEFAULT 'Hubungi Saya',
  `cta_url_secondary` VARCHAR(255) DEFAULT '#contact',
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Profile Table (About Me)
CREATE TABLE IF NOT EXISTS `profile` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) DEFAULT '[NAMA LENGKAP]',
  `headline` VARCHAR(200) DEFAULT 'IT & Software Engineering Specialist',
  `headline_en` VARCHAR(200) DEFAULT NULL,
  `about_text` LONGTEXT,
  `about_text_en` LONGTEXT,
  `years_experience` INT DEFAULT 5,
  `projects_completed` INT DEFAULT 30,
  `websites_built` INT DEFAULT 25,
  `technologies_count` INT DEFAULT 15,
  `resume_file` VARCHAR(255) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Skill Categories
CREATE TABLE IF NOT EXISTS `skill_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL,
  `category_name_en` VARCHAR(100) DEFAULT NULL,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Skills
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `name_en` VARCHAR(100) DEFAULT NULL,
  `level_percentage` INT DEFAULT 85,
  `icon_class` VARCHAR(100) DEFAULT 'fas fa-code',
  `display_order` INT DEFAULT 0,
  FOREIGN KEY (`category_id`) REFERENCES `skill_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `title_en` VARCHAR(200) DEFAULT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `category` ENUM('Web', 'Application', 'UI/UX', 'Data', 'Other') DEFAULT 'Web',
  `short_description` TEXT,
  `short_description_en` TEXT,
  `full_description` LONGTEXT,
  `full_description_en` LONGTEXT,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `demo_url` VARCHAR(255) DEFAULT NULL,
  `github_url` VARCHAR(255) DEFAULT NULL,
  `project_year` VARCHAR(10) DEFAULT '2026',
  `status` ENUM('Completed', 'In Progress', 'Maintained') DEFAULT 'Completed',
  `is_featured` TINYINT(1) DEFAULT 0,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Project Images Gallery
CREATE TABLE IF NOT EXISTS `project_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  `display_order` INT DEFAULT 0,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Experiences
CREATE TABLE IF NOT EXISTS `experiences` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `position` VARCHAR(150) NOT NULL,
  `position_en` VARCHAR(150) DEFAULT NULL,
  `company` VARCHAR(150) NOT NULL,
  `company_en` VARCHAR(150) DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `start_date` VARCHAR(50) NOT NULL,
  `end_date` VARCHAR(50) DEFAULT 'Present',
  `is_current` TINYINT(1) DEFAULT 0,
  `description` TEXT,
  `description_en` TEXT,
  `tech_used` VARCHAR(255) DEFAULT NULL,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Educations
CREATE TABLE IF NOT EXISTS `educations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `institution` VARCHAR(150) NOT NULL,
  `degree_major` VARCHAR(150) NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `start_year` VARCHAR(10) NOT NULL,
  `end_year` VARCHAR(10) DEFAULT 'Present',
  `description` TEXT,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Services
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `title_en` VARCHAR(150) DEFAULT NULL,
  `icon_class` VARCHAR(100) DEFAULT 'fas fa-laptop-code',
  `short_description` TEXT,
  `short_description_en` TEXT,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Certificates
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `issuer` VARCHAR(150) NOT NULL,
  `issue_year` VARCHAR(20) NOT NULL,
  `credential_id` VARCHAR(100) DEFAULT NULL,
  `credential_url` VARCHAR(255) DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Article Categories
CREATE TABLE IF NOT EXISTS `article_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Articles / Blog
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT,
  `content` LONGTEXT,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `tags` VARCHAR(255) DEFAULT NULL,
  `reading_time` INT DEFAULT 5,
  `status` ENUM('draft', 'published') DEFAULT 'published',
  `views_count` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Messages (Contact Form)
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `subject` VARCHAR(200) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Social Links
CREATE TABLE IF NOT EXISTS `social_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `platform` VARCHAR(50) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon_class` VARCHAR(100) NOT NULL,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- SEED DEFAULT DATA --

-- Default Admin User (Password: admin123)
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`) VALUES
(1, 'admin', '$2y$10$FhaTljn7qc3ghCTISQaP7OVz92R98w.24gasD7AgBPBob6l.SjpwK', 'admin@example.com', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Default Site Settings
INSERT INTO `site_settings` (`id`, `site_title`, `owner_name`, `headline`, `bio_short`, `email`, `phone_whatsapp`, `location`) VALUES
(1, 'Developer Portfolio | IT & Software Professional', '[NAMA ANDA]', 'Building Dynamic & Futuristic Digital Experiences', 'Pranata Komputer / IT Specialist berdedikasi tinggi dalam transformasi digital.', 'admin@example.com', '+6281234567890', 'Jakarta, Indonesia')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Default Hero
INSERT INTO `hero` (`id`, `greeting`, `headline`, `subheadline`, `cta_text_primary`, `cta_url_primary`, `cta_text_secondary`, `cta_url_secondary`, `is_active`) VALUES
(1, 'Halo, Saya', 'Building Digital Experiences That Matter.', 'Pranata Komputer, Web Developer, dan Software Engineer berfokus pada digitalisasi & solusi teknologi modern.', 'Lihat Portfolio', '#projects', 'Hubungi Saya', '#contact', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Default Profile
INSERT INTO `profile` (`id`, `full_name`, `headline`, `about_text`, `years_experience`, `projects_completed`, `websites_built`, `technologies_count`) VALUES
(1, '[NAMA ANDA]', 'Pranata Komputer / Senior Developer', 'Saya adalah seorang praktisi IT yang berfokus pada pengembangan sistem informasi, website modern, serta digitalisasi proses bisnis.', 5, 25, 20, 12)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Default Categories
INSERT INTO `skill_categories` (`id`, `category_name`, `display_order`) VALUES
(1, 'IT & Technical Support', 1),
(2, 'Web & Digital Solutions', 2),
(3, 'Design & Multimedia', 3)
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `skills` (`category_id`, `name`, `level_percentage`, `icon_class`, `display_order`) VALUES
(1, 'Network Maintenance', 85, 'fas fa-network-wired', 1),
(1, 'Local Area Network (LAN)', 85, 'fas fa-sitemap', 2),
(1, 'Network Troubleshooting', 85, 'fas fa-wrench', 3),
(1, 'CCTV Installation & Maintenance', 85, 'fas fa-video', 4),
(1, 'Hardware & Computer Troubleshooting', 85, 'fas fa-desktop', 5),
(1, 'IT Technical Support', 85, 'fas fa-headset', 6),
(1, 'System & Device Maintenance', 85, 'fas fa-cogs', 7),
(2, 'Website Development dengan AI-assisted tools', 85, 'fas fa-laptop-code', 1),
(2, 'Web-based Reporting System', 85, 'fas fa-chart-bar', 2),
(2, 'Website Maintenance', 85, 'fas fa-globe', 3),
(2, 'Basic Database Management', 85, 'fas fa-database', 4),
(2, 'Digital Workflow & Automation', 85, 'fas fa-robot', 5),
(2, 'Basic HTML, CSS & JavaScript', 85, 'fas fa-code', 6),
(3, 'Canva Design', 85, 'fas fa-palette', 1),
(3, 'Social Media Content Design', 85, 'fas fa-hashtag', 2),
(3, 'Video Editing dengan CapCut', 85, 'fas fa-film', 3),
(3, 'Documentation & Digital Content', 85, 'fas fa-file-alt', 4),
(3, 'Visual Content Creation', 85, 'fas fa-photo-video', 5);

-- Default Services
INSERT INTO `services` (`title`, `icon_class`, `short_description`, `display_order`) VALUES
('Website & Digital Solutions', 'fas fa-laptop-code', 'Pembuatan dan pengembangan website sederhana untuk kebutuhan personal, organisasi, maupun usaha dengan pendekatan modern dan AI-assisted development.', 1),
('Website Maintenance', 'fas fa-globe', 'Membantu melakukan pemeliharaan, pembaruan konten, troubleshooting dasar, serta memastikan website tetap dapat digunakan dengan baik.', 2),
('IT & Network Support', 'fas fa-network-wired', 'Dukungan teknis untuk jaringan lokal, troubleshooting koneksi, perangkat komputer, serta kebutuhan infrastruktur IT dasar.', 3),
('CCTV Maintenance', 'fas fa-video', 'Instalasi, pengecekan, troubleshooting, dan pemeliharaan sistem CCTV.', 4),
('Design & Social Media Content', 'fas fa-palette', 'Pembuatan desain visual untuk kebutuhan media sosial, publikasi, dokumentasi, dan komunikasi digital menggunakan Canva.', 5),
('Video Editing', 'fas fa-film', 'Editing video untuk kebutuhan dokumentasi, media sosial, publikasi kegiatan, dan konten digital menggunakan CapCut.', 6);

-- Default Social Links
INSERT INTO `social_links` (`platform`, `url`, `icon_class`, `display_order`) VALUES
('Instagram', 'https://instagram.com/setiawanchan_', 'fab fa-instagram', 1);
