<?php
$page_title = 'Dashboard Overview';
$active_menu = 'dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/database.php';

// Fetch stats counts
$total_projects = db()->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_skills = db()->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$total_articles = db()->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_messages = db()->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages = db()->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
$total_experiences = db()->query("SELECT COUNT(*) FROM experiences")->fetchColumn();
$total_certificates = db()->query("SELECT COUNT(*) FROM certificates")->fetchColumn();

// Fetch recent messages
$recent_messages = db()->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!-- Stat Cards Grid -->
<div class="row g-4 mb-4">
    <div class="col-md-4 col-lg-3">
        <div class="glass-card stat-card">
            <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
            <div>
                <div class="stat-number"><?= $total_projects ?></div>
                <div class="stat-label">Total Projects</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="glass-card stat-card">
            <div class="stat-icon"><i class="fas fa-code"></i></div>
            <div>
                <div class="stat-number"><?= $total_skills ?></div>
                <div class="stat-label">Total Skills</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="glass-card stat-card">
            <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
            <div>
                <div class="stat-number"><?= $total_articles ?></div>
                <div class="stat-label">Articles / Blog</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="glass-card stat-card">
            <div class="stat-icon position-relative">
                <i class="fas fa-envelope"></i>
                <?php if ($unread_messages > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                <?php endif; ?>
            </div>
            <div>
                <div class="stat-number"><?= $total_messages ?></div>
                <div class="stat-label">Messages (<?= $unread_messages ?> Unread)</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Overview Table -->
    <div class="col-lg-8">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 font-weight-bold text-info"><i class="fas fa-inbox me-2"></i>Pesan Terbaru Masuk</h5>
                <a href="<?= ADMIN_URL ?>modules/messages.php" class="btn btn-sm btn-outline-info">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Subject</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_messages)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada pesan masuk.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_messages as $msg): ?>
                                <tr>
                                    <td><strong><?= sanitize($msg['name']) ?></strong><br><small class="text-muted"><?= sanitize($msg['email']) ?></small></td>
                                    <td><?= sanitize($msg['subject']) ?></td>
                                    <td><small class="text-muted"><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></small></td>
                                    <td>
                                        <?php if ($msg['is_read']): ?>
                                            <span class="badge bg-secondary">Dibaca</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Baru</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Links Widget -->
    <div class="col-lg-4">
        <div class="glass-card p-4">
            <h5 class="mb-3 text-info"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
            <div class="d-grid gap-2">
                <a href="<?= ADMIN_URL ?>modules/projects.php?action=add" class="btn btn-outline-light text-start py-2"><i class="fas fa-plus-circle me-2 text-info"></i> Tambah Project Baru</a>
                <a href="<?= ADMIN_URL ?>modules/articles.php?action=add" class="btn btn-outline-light text-start py-2"><i class="fas fa-pen me-2 text-info"></i> Tulis Artikel Baru</a>
                <a href="<?= ADMIN_URL ?>modules/skills.php" class="btn btn-outline-light text-start py-2"><i class="fas fa-plus me-2 text-info"></i> Kelola Skills & Icon</a>
                <a href="<?= ADMIN_URL ?>modules/settings.php" class="btn btn-outline-light text-start py-2"><i class="fas fa-sliders-h me-2 text-info"></i> Pengaturan SEO & Profil</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
