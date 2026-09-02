<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();

$stmt = db()->query("SELECT * FROM site_settings LIMIT 1");
$settings = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = $_POST['site_title'] ?? '';
    $owner_name = $_POST['owner_name'] ?? '';
    $headline = $_POST['headline'] ?? '';
    $bio_short = $_POST['bio_short'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_whatsapp = $_POST['phone_whatsapp'] ?? '';
    $location = $_POST['location'] ?? '';
    $primary_color = $_POST['primary_color'] ?? '#38bdf8';
    $footer_text = $_POST['footer_text'] ?? '';
    $telegram_bot_token = $_POST['telegram_bot_token'] ?? '';
    $telegram_chat_id = $_POST['telegram_chat_id'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';

    $favicon = $settings['favicon'] ?? null;
    $uploaded_fav = upload_file('favicon', 'settings');
    if ($uploaded_fav) {
        $favicon = $uploaded_fav;
    }

    $logo = $settings['logo'] ?? null;
    $uploaded_logo = upload_file('logo', 'settings');
    if ($uploaded_logo) {
        $logo = $uploaded_logo;
    }

    if ($settings) {
        $update_stmt = db()->prepare("UPDATE site_settings SET site_title=?, owner_name=?, headline=?, bio_short=?, email=?, phone_whatsapp=?, location=?, favicon=?, logo=?, primary_color=?, footer_text=?, telegram_bot_token=?, telegram_chat_id=?, meta_keywords=?, meta_description=? WHERE id=?");
        $update_stmt->execute([$site_title, $owner_name, $headline, $bio_short, $email, $phone_whatsapp, $location, $favicon, $logo, $primary_color, $footer_text, $telegram_bot_token, $telegram_chat_id, $meta_keywords, $meta_description, $settings['id']]);
    } else {
        $insert_stmt = db()->prepare("INSERT INTO site_settings (site_title, owner_name, headline, bio_short, email, phone_whatsapp, location, favicon, logo, primary_color, footer_text, telegram_bot_token, telegram_chat_id, meta_keywords, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$site_title, $owner_name, $headline, $bio_short, $email, $phone_whatsapp, $location, $favicon, $logo, $primary_color, $footer_text, $telegram_bot_token, $telegram_chat_id, $meta_keywords, $meta_description]);
    }

    set_flash('success', 'Pengaturan Website & SEO berhasil disimpan!');
    header("Location: " . ADMIN_URL . "modules/settings.php");
    exit;
}

$page_title = 'Kelola Site Settings & SEO';
$active_menu = 'settings';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="glass-card p-4">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <h5 class="text-info mb-2"><i class="fas fa-globe me-2"></i>Identitas Website</h5>
            <div class="col-md-6">
                <label class="form-label">Judul Website (SEO Site Title)</label>
                <input type="text" name="site_title" class="form-control" value="<?= sanitize($settings['site_title'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Pemilik Website</label>
                <input type="text" name="owner_name" class="form-control" value="<?= sanitize($settings['owner_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Kontak Utama</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($settings['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor WhatsApp / HP</label>
                <input type="text" name="phone_whatsapp" class="form-control" value="<?= sanitize($settings['phone_whatsapp'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Lokasi Domisili</label>
                <input type="text" name="location" class="form-control" value="<?= sanitize($settings['location'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Warna Aksen Utama (Primary Color Code)</label>
                <input type="color" name="primary_color" class="form-control form-control-color w-100" value="<?= sanitize($settings['primary_color'] ?? '#38bdf8') ?>">
            </div>

            <h5 class="text-info mt-4 mb-2"><i class="fab fa-telegram me-2"></i>Notifikasi Telegram Bot</h5>
            <div class="col-md-6">
                <label class="form-label">Telegram Bot Token</label>
                <input type="text" name="telegram_bot_token" class="form-control" value="<?= sanitize($settings['telegram_bot_token'] ?? '') ?>" placeholder="Contoh: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                <small class="text-muted d-block mt-1">Dapatkan dari @BotFather di Telegram</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Telegram Chat ID / User ID</label>
                <input type="text" name="telegram_chat_id" class="form-control" value="<?= sanitize($settings['telegram_chat_id'] ?? '') ?>" placeholder="Contoh: 987654321">
                <small class="text-muted d-block mt-1">Dapatkan ID Anda dari @userinfobot di Telegram</small>
            </div>

            <h5 class="text-info mt-4 mb-2"><i class="fas fa-search me-2"></i>SEO & Meta Data</h5>
            <div class="col-12">
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="3"><?= sanitize($settings['meta_description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Meta Keywords (Pisahkan dengan koma)</label>
                <input type="text" name="meta_keywords" class="form-control" value="<?= sanitize($settings['meta_keywords'] ?? '') ?>" placeholder="portfolio, developer, php, mysql, pranata komputer">
            </div>

            <h5 class="text-info mt-4 mb-2"><i class="fas fa-icons me-2"></i>Logo & Favicon</h5>
            <div class="col-md-6">
                <label class="form-label">Favicon (.ico, .png)</label>
                <?php if (!empty($settings['favicon'])): ?>
                    <div class="mb-2"><img src="<?= UPLOAD_URL . sanitize($settings['favicon']) ?>" style="max-height: 32px;"></div>
                <?php endif; ?>
                <input type="file" name="favicon" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Logo Custom</label>
                <?php if (!empty($settings['logo'])): ?>
                    <div class="mb-2"><img src="<?= UPLOAD_URL . sanitize($settings['logo']) ?>" style="max-height: 50px;"></div>
                <?php endif; ?>
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>

            <div class="col-12 mt-3">
                <label class="form-label">Teks Footer Copyright</label>
                <input type="text" name="footer_text" class="form-control" value="<?= sanitize($settings['footer_text'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Pengaturan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
