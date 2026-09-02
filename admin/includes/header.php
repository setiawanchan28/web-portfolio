<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? sanitize($page_title) . ' - Admin Panel' : 'Admin Panel' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="<?= BASE_URL ?>assets/css/admin.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <i class="fas fa-terminal"></i> CMS ADMIN
            </div>
            <ul class="sidebar-menu">
                <li class="<?= ($active_menu ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'hero' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/hero.php"><i class="fas fa-home"></i> Hero Section</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'profile' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/profile.php"><i class="fas fa-user-tie"></i> Profile & About</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'skills' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/skills.php"><i class="fas fa-code"></i> Skills</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'projects' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'experiences' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/experiences.php"><i class="fas fa-briefcase"></i> Experience</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'educations' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/educations.php"><i class="fas fa-graduation-cap"></i> Education</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'services' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/services.php"><i class="fas fa-cogs"></i> Services</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'certificates' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/certificates.php"><i class="fas fa-certificate"></i> Certificates</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'articles' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/articles.php"><i class="fas fa-newspaper"></i> Articles / Blog</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'messages' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/messages.php"><i class="fas fa-envelope"></i> Messages</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'social' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/social.php"><i class="fas fa-share-alt"></i> Social Links</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'settings' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/settings.php"><i class="fas fa-sliders-h"></i> Site Settings</a>
                </li>
                <li class="<?= ($active_menu ?? '') === 'account' ? 'active' : '' ?>">
                    <a href="<?= ADMIN_URL ?>modules/account.php"><i class="fas fa-user-cog"></i> Admin Account</a>
                </li>
                <li class="mt-4">
                    <a href="<?= ADMIN_URL ?>logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-content">
            <div class="admin-topbar">
                <h2><?= isset($page_title) ? sanitize($page_title) : 'Dashboard' ?></h2>
                <div class="user-info d-flex align-items-center gap-3">
                    <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-external-link-alt me-1"></i> Preview Site</a>
                    <span class="badge bg-secondary"><i class="fas fa-user-circle me-1"></i> <?= sanitize($_SESSION['username'] ?? 'Admin') ?></span>
                </div>
            </div>
            <?php display_flash(); ?>
