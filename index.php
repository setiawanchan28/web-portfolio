<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helper.php';
require_once __DIR__ . '/config/lang.php';

// Fetch all dynamic data from MySQL
$settings = db()->query("SELECT * FROM site_settings LIMIT 1")->fetch();
$hero = db()->query("SELECT * FROM hero LIMIT 1")->fetch();
$profile = db()->query("SELECT * FROM profile LIMIT 1")->fetch();

$skill_categories = db()->query("SELECT * FROM skill_categories ORDER BY display_order ASC")->fetchAll();
$projects = db()->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC")->fetchAll();
$experiences = db()->query("SELECT * FROM experiences ORDER BY display_order ASC, id DESC")->fetchAll();
$educations = db()->query("SELECT * FROM educations ORDER BY display_order ASC, id DESC")->fetchAll();
$services = db()->query("SELECT * FROM services ORDER BY display_order ASC, id DESC")->fetchAll();
$certificates = db()->query("SELECT * FROM certificates ORDER BY display_order ASC, id DESC")->fetchAll();
$articles = db()->query("SELECT * FROM articles WHERE status='published' ORDER BY id DESC LIMIT 6")->fetchAll();
$social_links = db()->query("SELECT * FROM social_links ORDER BY display_order ASC")->fetchAll();

// Handle Contact Form Submission
$contact_flash = $_SESSION['contact_flash'] ?? '';
unset($_SESSION['contact_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = db()->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);

        // Send instant notification to Telegram Bot if configured
        send_telegram_notification($name, $email, $phone, $subject, $message, $settings);

        $_SESSION['contact_flash'] = 'success';
    } else {
        $_SESSION['contact_flash'] = 'error';
    }

    header("Location: " . BASE_URL . "#contact");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($settings['site_title'] ?? 'Developer Portfolio') ?></title>
    <meta name="description" content="<?= sanitize($settings['meta_description'] ?? '') ?>">
    <meta name="keywords" content="<?= sanitize($settings['meta_keywords'] ?? '') ?>">
    
    <?php if (!empty($settings['favicon'])): ?>
        <link rel="shortcut icon" href="<?= UPLOAD_URL . sanitize($settings['favicon']) ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Futuristic Style -->
    <link href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

    <!-- Ambient Glowing Background Orbs -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#hero">
                <i class="fas fa-terminal me-2"></i><?= sanitize($settings['owner_name'] ?? 'DEV.PORTFOLIO') ?>
            </a>
            <button class="navbar-toggler border-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link" href="#hero"><?= $t['home'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#about"><?= $t['about'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills"><?= $t['skills'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#projects"><?= $t['projects'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#experience"><?= $t['experience'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#services"><?= $t['services'] ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact"><?= $t['contact'] ?></a></li>

                    <!-- Language Switcher Panel -->
                    <li class="nav-item ms-lg-2 dropdown">
                        <a class="btn btn-sm btn-outline-futuristic dropdown-toggle py-1 px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i> <?= strtoupper($current_lang) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                            <li><a class="dropdown-item <?= $current_lang === 'id' ? 'active' : '' ?>" href="?lang=id">🇲🇨 Indonesia (ID)</a></li>
                            <li><a class="dropdown-item <?= $current_lang === 'en' ? 'active' : '' ?>" href="?lang=en">🇬🇧 English (EN)</a></li>
                        </ul>
                    </li>

                    <!-- Theme Toggle -->
                    <li class="nav-item ms-lg-2">
                        <button id="theme-toggle" class="btn btn-sm btn-outline-futuristic py-1 px-3"><i class="fas fa-sun"></i></button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7" data-aos="fade-right">
                    <span class="badge bg-dark border border-info text-info px-3 py-2 rounded-pill mb-3 fs-6">
                        <i class="fas fa-sparkles me-2"></i><?= sanitize($hero['greeting'] ?? 'Halo, Saya') ?>
                    </span>
                    <h1 class="hero-title"><?= sanitize($hero['headline'] ?? 'Building Digital Experiences') ?></h1>
                    <p class="lead text-muted mb-4 fs-5" style="max-width: 600px;"><?= sanitize($hero['subheadline'] ?? '') ?></p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="<?= sanitize($hero['cta_url_primary'] ?? '#projects') ?>" class="btn btn-futuristic">
                            <?= sanitize($hero['cta_text_primary'] ?? $t['see_portfolio']) ?> <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="<?= sanitize($hero['cta_url_secondary'] ?? '#contact') ?>" class="btn btn-outline-futuristic">
                            <?= sanitize($hero['cta_text_secondary'] ?? $t['contact_me']) ?>
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 text-center" data-aos="fade-left">
                    <div class="hero-profile-wrapper">
                        <?php if (!empty($hero['profile_image'])): ?>
                            <img src="<?= UPLOAD_URL . sanitize($hero['profile_image']) ?>" alt="Profile" class="hero-profile-img">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80" alt="Profile" class="hero-profile-img">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="glass-box p-4 p-md-5" data-aos="fade-up">
                <div class="row align-items-center g-4">
                    <div class="col-lg-4 text-center">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="<?= UPLOAD_URL . sanitize($profile['profile_image']) ?>" class="img-fluid rounded-4 border border-info shadow-lg" style="max-height: 350px; object-fit: cover;">
                        <?php else: ?>
                            <div class="p-5 text-center bg-dark rounded-4 text-muted"><i class="fas fa-user-tie fs-1"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-8">
                        <h2 class="text-info fw-bold mb-2"><?= $t['about_me'] ?></h2>
                        <h4 class="text-white mb-3"><?= sanitize($profile['headline'] ?? '') ?></h4>
                        <p class="text-muted lead fs-6 mb-4" style="white-space: pre-wrap;"><?= sanitize($profile['about_text'] ?? '') ?></p>



                        <?php if (!empty($profile['resume_file'])): ?>
                            <a href="<?= UPLOAD_URL . sanitize($profile['resume_file']) ?>" target="_blank" class="btn btn-futuristic">
                                <i class="fas fa-download me-2"></i> <?= $t['download_cv'] ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SKILLS SECTION -->
    <section id="skills" class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-info font-monospace"><?= $t['tech_competencies'] ?></span>
                <h2 class="fw-bold text-white fs-1"><?= $t['skills_title'] ?></h2>
            </div>

            <div class="row g-4">
                <?php foreach ($skill_categories as $cat): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="glass-box p-4 h-100">
                            <h4 class="text-info mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="fas fa-layer-group me-2"></i><?= sanitize($cat['category_name']) ?>
                            </h4>

                            <?php
                            $s_stmt = db()->prepare("SELECT * FROM skills WHERE category_id = ? ORDER BY display_order ASC");
                            $s_stmt->execute([$cat['id']]);
                            $cat_skills = $s_stmt->fetchAll();
                            ?>

                            <ul class="list-unstyled mb-0">
                                <?php foreach ($cat_skills as $sk): ?>
                                    <li class="d-flex align-items-center mb-3 text-light">
                                        <i class="<?= sanitize(!empty($sk['icon_class']) ? $sk['icon_class'] : 'fas fa-check-circle') ?> text-info me-3 fs-5" style="width: 24px; text-align: center;"></i>
                                        <span class="fw-medium"><?= sanitize($sk['name']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- PROJECTS SECTION -->
    <section id="projects" class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-info font-monospace"><?= $t['portfolio_showcase'] ?></span>
                <h2 class="fw-bold text-white fs-1"><?= $t['recent_projects'] ?></h2>

                <!-- Category Filters -->
                <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">
                    <button class="btn btn-futuristic project-filter-btn active" data-filter="all"><?= $t['all_projects'] ?></button>
                    <button class="btn btn-outline-futuristic project-filter-btn" data-filter="Web">Web</button>
                    <button class="btn btn-outline-futuristic project-filter-btn" data-filter="Application">Application</button>
                    <button class="btn btn-outline-futuristic project-filter-btn" data-filter="UI/UX">UI/UX</button>
                    <button class="btn btn-outline-futuristic project-filter-btn" data-filter="Data">Data</button>
                </div>
            </div>

            <div class="row g-4 justify-content-center" id="projects-grid">
                <?php foreach ($projects as $pj): ?>
                    <div class="col-lg-4 col-md-6 project-item" data-category="<?= sanitize($pj['category']) ?>" data-aos="fade-up">
                        <div class="glass-box project-card">
                            <?php if (!empty($pj['thumbnail'])): ?>
                                <img src="<?= UPLOAD_URL . sanitize($pj['thumbnail']) ?>" alt="Project" class="project-thumb">
                            <?php else: ?>
                                <div class="bg-dark text-muted d-flex align-items-center justify-content-center" style="height: 220px;"><i class="fas fa-image fs-1"></i></div>
                            <?php endif; ?>

                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-dark border border-info text-info"><?= sanitize($pj['category']) ?></span>
                                    <small class="text-muted"><?= sanitize($pj['project_year']) ?></small>
                                </div>
                                <h4 class="text-white fw-bold mb-2"><?= sanitize($pj['title']) ?></h4>
                                <p class="text-muted small mb-4 flex-grow-1"><?= sanitize($pj['short_description']) ?></p>

                                <div class="d-flex gap-2">
                                    <?php if (!empty($pj['demo_url'])): ?>
                                        <a href="<?= sanitize($pj['demo_url']) ?>" target="_blank" class="btn btn-sm btn-futuristic w-100"><i class="fas fa-external-link-alt me-1"></i> <?= $t['live_demo'] ?></a>
                                    <?php endif; ?>
                                    <?php if (!empty($pj['github_url'])): ?>
                                        <a href="<?= sanitize($pj['github_url']) ?>" target="_blank" class="btn btn-sm btn-outline-futuristic w-100"><i class="fab fa-github me-1"></i> <?= $t['github_repo'] ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- EXPERIENCE SECTION -->
    <section id="experience" class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-info font-monospace"><?= $t['career_path'] ?></span>
                <h2 class="fw-bold text-white fs-1"><?= $t['work_experience'] ?></h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="timeline" data-aos="fade-up">
                        <?php foreach ($experiences as $ex): ?>
                            <div class="timeline-item">
                                <div class="glass-box p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h4 class="text-info fw-bold mb-0"><?= sanitize($ex['position']) ?></h4>
                                            <h6 class="text-white"><?= sanitize($ex['company']) ?> <small class="text-muted">| <?= sanitize($ex['location']) ?></small></h6>
                                        </div>
                                        <span class="badge bg-dark border border-secondary text-light">
                                            <?= sanitize($ex['start_date']) ?> - <?= $ex['is_current'] ? $t['present'] : sanitize($ex['end_date']) ?>
                                        </span>
                                    </div>
                                    <p class="text-muted mb-3" style="white-space: pre-wrap;"><?= sanitize($ex['description']) ?></p>
                                    <?php if (!empty($ex['tech_used'])): ?>
                                        <div class="text-info small"><i class="fas fa-tools me-2"></i>Tech: <?= sanitize($ex['tech_used']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-info font-monospace"><?= $t['what_i_offer'] ?></span>
                <h2 class="fw-bold text-white fs-1"><?= $t['my_services'] ?></h2>
            </div>

            <div class="row g-4">
                <?php foreach ($services as $srv): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="glass-box p-4 text-center h-100">
                            <div class="mb-3 text-info fs-1">
                                <i class="<?= sanitize($srv['icon_class']) ?>"></i>
                            </div>
                            <h4 class="text-white fw-bold mb-3"><?= sanitize($srv['title']) ?></h4>
                            <p class="text-muted"><?= sanitize($srv['short_description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="glass-box p-4 p-md-5" data-aos="fade-up">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <span class="text-info font-monospace"><?= $t['get_in_touch'] ?></span>
                        <h2 class="fw-bold text-white fs-1 mb-4"><?= $t['contact_title'] ?></h2>
                        <p class="text-muted mb-4"><?= $t['contact_desc'] ?></p>

                        <?php
                        // Format WhatsApp link automatically
                        $raw_phone = $settings['phone_whatsapp'] ?? '';
                        $clean_wa = preg_replace('/[^0-9]/', '', $raw_phone);
                        if (str_starts_with($clean_wa, '0')) {
                            $clean_wa = '62' . substr($clean_wa, 1);
                        }
                        $wa_url = !empty($clean_wa) ? "https://wa.me/" . $clean_wa : "#";
                        $email_url = !empty($settings['email']) ? "mailto:" . sanitize($settings['email']) : "#";
                        ?>

                        <div class="d-flex align-items-center mb-3 text-light">
                            <i class="fas fa-envelope text-info fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <a href="<?= $email_url ?>" class="text-white fw-bold text-decoration-none hover-info"><?= sanitize($settings['email'] ?? '') ?></a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3 text-light">
                            <i class="fab fa-whatsapp text-info fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block">WhatsApp</small>
                                <a href="<?= $wa_url ?>" target="_blank" class="text-white fw-bold text-decoration-none hover-info"><?= sanitize($settings['phone_whatsapp'] ?? '') ?></a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center text-light">
                            <i class="fas fa-map-marker-alt text-info fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block"><?= $t['location'] ?></small>
                                <strong><?= sanitize($settings['location'] ?? '') ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <?php if ($contact_flash === 'success'): ?>
                            <div class="alert alert-success"><?= $t['msg_success'] ?></div>
                        <?php elseif ($contact_flash === 'error'): ?>
                            <div class="alert alert-danger"><?= $t['msg_error'] ?></div>
                        <?php endif; ?>

                        <form action="#contact" method="POST">
                            <input type="hidden" name="send_message" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted"><?= $t['your_name'] ?></label>
                                    <input type="text" name="name" class="form-control" required placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted"><?= $t['your_email'] ?></label>
                                    <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">No. HP / WhatsApp</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+62 812-3456-7890">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted"><?= $t['subject'] ?></label>
                                    <input type="text" name="subject" class="form-control" placeholder="Project Inquiry / Discussion">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted"><?= $t['your_message'] ?></label>
                                    <textarea name="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-futuristic w-100 py-3 fw-bold"><i class="fas fa-paper-plane me-2"></i> <?= $t['send_message'] ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-custom py-4 border-top border-secondary border-opacity-25">
        <div class="container text-center">
            <?php if (!empty($social_links)): ?>
                <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                    <?php foreach ($social_links as $soc): ?>
                        <a href="<?= sanitize($soc['url']) ?>" target="_blank" class="text-info fs-4 me-2 hover-glow" title="<?= sanitize($soc['platform']) ?>">
                            <i class="<?= sanitize($soc['icon_class']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="mb-0 text-muted small">&copy; <?= date('Y') ?> <?= sanitize($settings['owner_name'] ?? 'Developer') ?>. <?= sanitize($settings['footer_text'] ?? $t['all_rights']) ?></p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>
