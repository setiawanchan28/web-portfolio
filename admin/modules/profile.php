<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();

$stmt = db()->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $headline = $_POST['headline'] ?? '';
    $about_text = $_POST['about_text'] ?? '';
    $years_experience = $_POST['years_experience'] ?? 0;
    $projects_completed = $_POST['projects_completed'] ?? 0;
    $websites_built = $_POST['websites_built'] ?? 0;
    $technologies_count = $_POST['technologies_count'] ?? 0;

    $profile_image = $profile['profile_image'] ?? null;
    $uploaded_img = upload_file('profile_image', 'profile');
    if ($uploaded_img) {
        $profile_image = $uploaded_img;
    }

    $resume_file = $profile['resume_file'] ?? null;
    $uploaded_pdf = upload_file('resume_file', 'profile', ['pdf', 'doc', 'docx']);
    if ($uploaded_pdf) {
        $resume_file = $uploaded_pdf;
    }

    if ($profile) {
        $update_stmt = db()->prepare("UPDATE profile SET full_name=?, headline=?, about_text=?, years_experience=?, projects_completed=?, websites_built=?, technologies_count=?, resume_file=?, profile_image=? WHERE id=?");
        $update_stmt->execute([$full_name, $headline, $about_text, $years_experience, $projects_completed, $websites_built, $technologies_count, $resume_file, $profile_image, $profile['id']]);
    } else {
        $insert_stmt = db()->prepare("INSERT INTO profile (full_name, headline, about_text, years_experience, projects_completed, websites_built, technologies_count, resume_file, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$full_name, $headline, $about_text, $years_experience, $projects_completed, $websites_built, $technologies_count, $resume_file, $profile_image]);
    }

    set_flash('success', 'Data Profil & About Me berhasil diperbarui!');
    header("Location: " . ADMIN_URL . "modules/profile.php");
    exit;
}

$page_title = 'Kelola Profil & About Me';
$active_menu = 'profile';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="glass-card p-4">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" value="<?= sanitize($profile['full_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Headline / Profesi Utama</label>
                <input type="text" name="headline" class="form-control" value="<?= sanitize($profile['headline'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Bio Lengkap (About Me)</label>
                <textarea name="about_text" class="form-control" rows="5" required><?= sanitize($profile['about_text'] ?? '') ?></textarea>
            </div>



            <h5 class="text-info mt-4 mb-2"><i class="fas fa-paperclip me-2"></i>File CV & Foto About</h5>
            <div class="col-md-6">
                <label class="form-label">Upload CV / Resume (PDF)</label>
                <?php if (!empty($profile['resume_file'])): ?>
                    <div class="mb-2">
                        <a href="<?= UPLOAD_URL . sanitize($profile['resume_file']) ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-file-pdf me-1"></i> Lihat CV Saat Ini</a>
                    </div>
                <?php endif; ?>
                <input type="file" name="resume_file" class="form-control" accept=".pdf">
            </div>
            <div class="col-md-6">
                <label class="form-label">Foto About Me</label>
                <?php if (!empty($profile['profile_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= UPLOAD_URL . sanitize($profile['profile_image']) ?>" style="max-height: 100px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="profile_image" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Profil</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
