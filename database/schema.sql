-- PORTOFOLIO DATABASE FULL EXPORT FROM LOCALHOST
-- Generated: 2026-09-02 15:07:23

SET FOREIGN_KEY_CHECKS=0;

-- Drop and create `article_categories` --
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Drop and create `articles` --
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `reading_time` int(11) DEFAULT 5,
  `status` enum('draft','published') DEFAULT 'published',
  `views_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Drop and create `certificates` --
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `issuer` varchar(150) NOT NULL,
  `issue_year` varchar(20) NOT NULL,
  `credential_id` varchar(100) DEFAULT NULL,
  `credential_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Drop and create `educations` --
DROP TABLE IF EXISTS `educations`;
CREATE TABLE `educations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution` varchar(150) NOT NULL,
  `degree_major` varchar(150) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `start_year` varchar(10) NOT NULL,
  `end_year` varchar(10) DEFAULT 'Present',
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Drop and create `experiences` --
DROP TABLE IF EXISTS `experiences`;
CREATE TABLE `experiences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(150) NOT NULL,
  `company` varchar(150) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `start_date` varchar(50) NOT NULL,
  `end_date` varchar(50) DEFAULT 'Present',
  `is_current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `tech_used` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `position_en` varchar(100) DEFAULT NULL,
  `company_en` varchar(100) DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `experiences` --
INSERT INTO `experiences` (`id`, `position`, `company`, `location`, `start_date`, `end_date`, `is_current`, `description`, `tech_used`, `display_order`, `position_en`, `company_en`, `description_en`) VALUES ('1', 'Teknisi Jaringan', 'PT Telkom Akses', 'Serang, Banten', '2013', '2024', '0', 'Berpengalaman dalam instalasi, pemeliharaan, troubleshooting, dan penanganan gangguan jaringan telekomunikasi. Terbiasa melakukan pekerjaan teknis di lapangan, melakukan analisis permasalahan, serta memastikan perangkat dan konektivitas jaringan dapat berfungsi dengan baik.', '', '0', 'Network Technician', 'PT Telkom Akses', 'Experienced in installation, maintenance, troubleshooting, and resolving telecommunication network disruptions. Accustomed to field technical operations, problem analysis, and ensuring optimal network connectivity and device functionality.');
INSERT INTO `experiences` (`id`, `position`, `company`, `location`, `start_date`, `end_date`, `is_current`, `description`, `tech_used`, `display_order`, `position_en`, `company_en`, `description_en`) VALUES ('2', 'Pranata Komputer Terampil', 'Badan Pusat Statistik Kabupaten Lebak', 'Lebak, Banten', '2024', '', '1', 'Mendukung pelaksanaan tugas di bidang teknologi informasi dan pengelolaan sistem komputer, termasuk dukungan teknis, pengembangan solusi digital, pengelolaan website, serta pemanfaatan teknologi untuk mendukung efektivitas pekerjaan dan penyajian informasi.', '', '1', 'Skilled Computer Specialist (Pranata Komputer)', 'BPS - Statistics Indonesia (Lebak Regency)', 'Supporting IT operations and computer system management, including technical support, digital solution development, website management, and leveraging technology to enhance workflow efficiency and data presentation.');

-- Drop and create `hero` --
DROP TABLE IF EXISTS `hero`;
CREATE TABLE `hero` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `greeting` varchar(100) DEFAULT 'Halo, Saya',
  `greeting_en` varchar(100) DEFAULT NULL,
  `headline` varchar(255) DEFAULT 'Pranata Komputer & Fullstack Developer',
  `headline_en` varchar(255) DEFAULT NULL,
  `subheadline` text DEFAULT NULL,
  `subheadline_en` text DEFAULT NULL,
  `cta_text_primary` varchar(50) DEFAULT 'Lihat Portfolio',
  `cta_url_primary` varchar(255) DEFAULT '#projects',
  `cta_text_secondary` varchar(50) DEFAULT 'Hubungi Saya',
  `cta_url_secondary` varchar(255) DEFAULT '#contact',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `hero` --
INSERT INTO `hero` (`id`, `greeting`, `greeting_en`, `headline`, `headline_en`, `subheadline`, `subheadline_en`, `cta_text_primary`, `cta_url_primary`, `cta_text_secondary`, `cta_url_secondary`, `profile_image`, `is_active`) VALUES ('1', 'Halo, Saya', 'Hello, I am', 'Building Practical Digital Solutions.', 'IT Specialist & Fullstack Developer', 'IT & Digital Solutions Professional yang berfokus pada web, jaringan, digitalisasi, dan teknologi modern.', 'Passionate about building scalable web solutions, network infrastructure, and digital media.', 'Lihat Portfolio', '#projects', 'Hubungi Saya', '#contact', 'profile/crop_6a8e91bb354ca1.71886511.png', '1');

-- Drop and create `messages` --
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `messages` --
INSERT INTO `messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES ('11', 'Dede', 'dede@gmail.com', '0877', 'dededede', 'dedededede', '0', '2026-09-02 13:24:34');

-- Drop and create `profile` --
DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT '[NAMA LENGKAP]',
  `headline` varchar(200) DEFAULT 'IT & Software Engineering Specialist',
  `headline_en` varchar(255) DEFAULT NULL,
  `about_text` longtext DEFAULT NULL,
  `about_text_en` text DEFAULT NULL,
  `years_experience` int(11) DEFAULT 5,
  `projects_completed` int(11) DEFAULT 30,
  `websites_built` int(11) DEFAULT 25,
  `technologies_count` int(11) DEFAULT 15,
  `resume_file` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `profile` --
INSERT INTO `profile` (`id`, `full_name`, `headline`, `headline_en`, `about_text`, `about_text_en`, `years_experience`, `projects_completed`, `websites_built`, `technologies_count`, `resume_file`, `profile_image`) VALUES ('1', 'Dede Setiawan', 'IT & Digital Solutions Professional', 'IT Specialist & Web Developer', 'Saya adalah IT Professional dengan pengalaman lebih dari 10 tahun di bidang jaringan, technical support, dan teknologi informasi. Saat ini berfokus pada digitalisasi, web, dan solusi teknologi, serta memanfaatkan AI untuk mengembangkan solusi yang praktis dan efektif.', 'Professional IT Specialist and Web Developer with strong expertise in IT technical support, network troubleshooting, web development using modern tools, and digital content creation. Dedicated to delivering efficient digital solutions and maintaining robust IT infrastructure.', '0', '0', '0', '0', 'profile/img_6a97b897142945.96764400.pdf', 'profile/img_6a97a4102f5a09.18489927.jpg');

-- Drop and create `project_images` --
DROP TABLE IF EXISTS `project_images`;
CREATE TABLE `project_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Drop and create `projects` --
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT 'Web',
  `short_description` text DEFAULT NULL,
  `full_description` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `project_year` varchar(10) DEFAULT '2026',
  `status` enum('Completed','In Progress','Maintained') DEFAULT 'Completed',
  `is_featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `title_en` varchar(150) DEFAULT NULL,
  `short_description_en` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `projects` --
INSERT INTO `projects` (`id`, `title`, `slug`, `category`, `short_description`, `full_description`, `thumbnail`, `demo_url`, `github_url`, `project_year`, `status`, `is_featured`, `display_order`, `created_at`, `title_en`, `short_description_en`, `description_en`) VALUES ('1', 'Sistem Laporan Harian Berbasis Web', 'sistem-laporan-harian-berbasis-web', 'Website', 'Sistem berbasis web untuk membantu proses pencatatan dan pelaporan kegiatan harian secara lebih terstruktur dan efisien. Dikembangkan dengan pendekatan AI-assisted development dan dirancang agar proses input, pengelolaan, serta penyajian laporan dapat dilakukan secara lebih praktis.', '', 'projects/img_6a97b97ae69cb7.05776646.png', 'https://laporan3602-app.vercel.app/', '', '2026', 'Completed', '1', '1', '2026-09-02 12:51:54', 'Web-Based Daily Reporting System', 'A web-based system designed for structured and efficient daily activity logging and reporting. Developed with AI-assisted tools to streamline data entry, management, and report generation.', NULL);

-- Drop and create `services` --
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `title_en` varchar(100) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-laptop-code',
  `short_description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `short_description_en` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `services` --
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('1', 'Website & Digital Solutions', 'Website & Digital Solutions', 'fas fa-laptop-code', 'Pembuatan dan pengembangan website sederhana untuk kebutuhan personal, organisasi, maupun usaha dengan pendekatan modern dan AI-assisted development.', '1', 'Development and building of web solutions for personal, organizational, and business needs using modern approaches and AI-assisted development.');
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('2', 'Website Maintenance', 'Website Maintenance', 'fas fa-globe', 'Membantu melakukan pemeliharaan, pembaruan konten, troubleshooting dasar, serta memastikan website tetap dapat digunakan dengan baik.', '2', 'Providing regular website maintenance, content updates, basic troubleshooting, and ensuring optimal website performance.');
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('3', 'IT & Network Support', 'IT & Network Support', 'fas fa-network-wired', 'Dukungan teknis untuk jaringan lokal, troubleshooting koneksi, perangkat komputer, serta kebutuhan infrastruktur IT dasar.', '3', 'Technical support for local area networks (LAN), connectivity troubleshooting, computer hardware maintenance, and basic IT infrastructure.');
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('4', 'CCTV Maintenance', 'CCTV Maintenance', 'fas fa-video', 'Instalasi, pengecekan, troubleshooting, dan pemeliharaan sistem CCTV.', '4', 'Installation, inspection, troubleshooting, and preventive maintenance of CCTV surveillance systems.');
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('5', 'Design & Social Media Content', 'Design & Social Media Content', 'fas fa-palette', 'Pembuatan desain visual untuk kebutuhan media sosial, publikasi, dokumentasi, dan komunikasi digital menggunakan Canva.', '5', 'Visual design creation for social media needs, digital publications, documentation, and communication graphics using Canva.');
INSERT INTO `services` (`id`, `title`, `title_en`, `icon_class`, `short_description`, `display_order`, `short_description_en`) VALUES ('6', 'Video Editing', 'Video Editing', 'fas fa-film', 'Editing video untuk kebutuhan dokumentasi, media sosial, publikasi kegiatan, dan konten digital menggunakan CapCut.', '6', 'Video editing for documentation, social media content, event coverage, and digital media production using CapCut.');

-- Drop and create `site_settings` --
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(150) DEFAULT 'Developer Portfolio | IT Professional',
  `owner_name` varchar(100) DEFAULT '[NAMA ANDA]',
  `headline` varchar(255) DEFAULT 'Building Digital Experiences That Matter.',
  `bio_short` text DEFAULT NULL,
  `email` varchar(100) DEFAULT 'admin@example.com',
  `phone_whatsapp` varchar(20) DEFAULT '+6281234567890',
  `location` varchar(100) DEFAULT 'Indonesia',
  `cv_file` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `primary_color` varchar(20) DEFAULT '#38bdf8',
  `footer_text` varchar(255) DEFAULT 'All Rights Reserved.',
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telegram_bot_token` varchar(255) DEFAULT NULL,
  `telegram_chat_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `site_settings` --
INSERT INTO `site_settings` (`id`, `site_title`, `owner_name`, `headline`, `bio_short`, `email`, `phone_whatsapp`, `location`, `cv_file`, `favicon`, `logo`, `primary_color`, `footer_text`, `meta_keywords`, `meta_description`, `og_image`, `updated_at`, `telegram_bot_token`, `telegram_chat_id`) VALUES ('1', 'Developer Portfolio | IT & Software Professional', 'Dede Setiawan', '', '', 'ddsetiawan28@gmail.com', '+6281288884078', 'Lebak, Banten, Indonesia', NULL, NULL, NULL, '#38bdf8', 'All Rights Reserved.', '', '', NULL, '2026-09-02 13:02:06', '8984230442:AAHUXo18wdbZaxSfIAnqXIHjBPwe_3xT3K0', '56126303');

-- Drop and create `skill_categories` --
DROP TABLE IF EXISTS `skill_categories`;
CREATE TABLE `skill_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `category_name_en` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `skill_categories` --
INSERT INTO `skill_categories` (`id`, `category_name`, `category_name_en`, `display_order`) VALUES ('1', 'IT & Technical Support', 'IT & Technical Support', '1');
INSERT INTO `skill_categories` (`id`, `category_name`, `category_name_en`, `display_order`) VALUES ('2', 'Web & Digital Solutions', 'Web & Digital Solutions', '2');
INSERT INTO `skill_categories` (`id`, `category_name`, `category_name_en`, `display_order`) VALUES ('3', 'Design & Multimedia', 'Design & Multimedia', '3');

-- Drop and create `skills` --
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `level_percentage` int(11) DEFAULT 85,
  `icon_class` varchar(100) DEFAULT 'fas fa-code',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `skills` --
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('1', '1', 'Network Maintenance & Troubleshooting', 'Network Maintenance & Troubleshooting', '85', 'fas fa-network-wired', '1');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('4', '1', 'CCTV Installation & Maintenance', 'CCTV Installation & Maintenance', '85', 'fas fa-video', '2');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('5', '1', 'Hardware & Computer Troubleshooting', 'Hardware & Computer Troubleshooting', '85', 'fas fa-desktop', '3');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('6', '1', 'IT Technical Support', 'IT Technical Support', '85', 'fas fa-headset', '4');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('8', '2', 'Website Development dengan AI-assisted tools', 'Website Development with AI-assisted tools', '85', 'fas fa-laptop-code', '1');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('9', '2', 'Web-based Reporting System', 'Web-based Reporting System', '85', 'fas fa-chart-bar', '2');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('10', '2', 'Website Maintenance', 'Website Maintenance', '85', 'fas fa-globe', '3');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('11', '2', 'Basic Database Management', 'Basic Database Management', '85', 'fas fa-database', '4');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('13', '2', 'Basic HTML, CSS & JavaScript', 'Basic HTML, CSS & JavaScript', '85', 'fas fa-code', '6');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('14', '3', 'Canva Design', 'Canva Design', '85', 'fas fa-palette', '1');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('15', '3', 'Social Media Content Design', 'Social Media Content Design', '85', 'fas fa-hashtag', '2');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('16', '3', 'Video Editing', 'Video Editing', '85', 'fas fa-film', '3');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('17', '3', 'Documentation & Digital Content', 'Documentation & Digital Content', '85', 'fas fa-file-alt', '4');
INSERT INTO `skills` (`id`, `category_id`, `name`, `name_en`, `level_percentage`, `icon_class`, `display_order`) VALUES ('18', '3', 'Visual Content Creation', 'Visual Content Creation', '85', 'fas fa-photo-video', '5');

-- Drop and create `social_links` --
DROP TABLE IF EXISTS `social_links`;
CREATE TABLE `social_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon_class` varchar(100) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `social_links` --
INSERT INTO `social_links` (`id`, `platform`, `url`, `icon_class`, `display_order`) VALUES ('1', 'Instagram', 'https://instagram.com/setiawanchan_', 'fab fa-instagram', '1');

-- Drop and create `users` --
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `users` --
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`, `updated_at`) VALUES ('1', 'admin', '$2y$10$LnSUJdu92NFvbsD7tW1lrOfDFiiFye8VhmKm19T5PCqiDYcjVMTFC', 'ddsetiawan28@gmail.com', 'Dede Setiawan', 'admin', '2026-08-26 13:41:44', '2026-08-26 13:58:46');

SET FOREIGN_KEY_CHECKS=1;
