<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();

$stmt = db()->query("SELECT * FROM hero LIMIT 1");
$hero = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $greeting = $_POST['greeting'] ?? '';
    $headline = $_POST['headline'] ?? '';
    $subheadline = $_POST['subheadline'] ?? '';
    $cta_text_primary = $_POST['cta_text_primary'] ?? '';
    $cta_url_primary = $_POST['cta_url_primary'] ?? '';
    $cta_text_secondary = $_POST['cta_text_secondary'] ?? '';
    $cta_url_secondary = $_POST['cta_url_secondary'] ?? '';

    $profile_image = $hero['profile_image'] ?? null;

    // Check if base64 cropped image was submitted
    if (!empty($_POST['cropped_image'])) {
        $uploaded_crop = upload_base64_image($_POST['cropped_image'], 'profile');
        if ($uploaded_crop) {
            $profile_image = $uploaded_crop;
        }
    } else {
        $uploaded_img = upload_file('profile_image', 'profile');
        if ($uploaded_img) {
            $profile_image = $uploaded_img;
        }
    }

    if ($hero) {
        $update_stmt = db()->prepare("UPDATE hero SET greeting = ?, headline = ?, subheadline = ?, cta_text_primary = ?, cta_url_primary = ?, cta_text_secondary = ?, cta_url_secondary = ?, profile_image = ? WHERE id = ?");
        $update_stmt->execute([$greeting, $headline, $subheadline, $cta_text_primary, $cta_url_primary, $cta_text_secondary, $cta_url_secondary, $profile_image, $hero['id']]);
    } else {
        $insert_stmt = db()->prepare("INSERT INTO hero (greeting, headline, subheadline, cta_text_primary, cta_url_primary, cta_text_secondary, cta_url_secondary, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$greeting, $headline, $subheadline, $cta_text_primary, $cta_url_primary, $cta_text_secondary, $cta_url_secondary, $profile_image]);
    }

    set_flash('success', 'Hero section berhasil diperbarui!');
    header("Location: " . ADMIN_URL . "modules/hero.php");
    exit;
}

$page_title = 'Kelola Hero Section';
$active_menu = 'hero';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="glass-card p-4">
    <form action="" method="POST" enctype="multipart/form-data" id="hero-form">
        <input type="hidden" name="cropped_image" id="cropped_image">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Greeting / Salam</label>
                <input type="text" name="greeting" class="form-control" value="<?= sanitize($hero['greeting'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Main Headline</label>
                <input type="text" name="headline" class="form-control" value="<?= sanitize($hero['headline'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Sub-headline / Deskripsi Singkat</label>
                <textarea name="subheadline" class="form-control" rows="3" required><?= sanitize($hero['subheadline'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teks Button Utama (CTA Primary)</label>
                <input type="text" name="cta_text_primary" class="form-control" value="<?= sanitize($hero['cta_text_primary'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">URL Button Utama</label>
                <input type="text" name="cta_url_primary" class="form-control" value="<?= sanitize($hero['cta_url_primary'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Teks Button Sekunder (CTA Secondary)</label>
                <input type="text" name="cta_text_secondary" class="form-control" value="<?= sanitize($hero['cta_text_secondary'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">URL Button Sekunder</label>
                <input type="text" name="cta_url_secondary" class="form-control" value="<?= sanitize($hero['cta_url_secondary'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Foto Profile Hero</label>
                <?php if (!empty($hero['profile_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= UPLOAD_URL . sanitize($hero['profile_image']) ?>" id="current-img" style="max-height: 120px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="image-input" name="profile_image" class="form-control" accept="image/*">
                <small class="text-muted">Format: JPG, PNG, WEBP. Pilih gambar untuk membuka modal Crop & Resize.</small>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- Modal Interactive Image Cropper -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-crop-alt me-2 text-info"></i>Crop & Resize Foto Profil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <div style="max-height: 450px; overflow: hidden; background: #000;">
                    <img id="crop-image" src="" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer border-secondary justify-content-between">
                <div>
                    <button type="button" class="btn btn-sm btn-outline-light me-1" id="rotate-left"><i class="fas fa-undo"></i> Putar Kiri</button>
                    <button type="button" class="btn btn-sm btn-outline-light" id="rotate-right"><i class="fas fa-redo"></i> Putar Kanan</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info btn-sm fw-bold px-3" id="crop-submit"><i class="fas fa-check me-1"></i> Potong & Gunakan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image-input');
    const cropImage = document.getElementById('crop-image');
    const cropModalEl = document.getElementById('cropModal');
    const cropModal = new bootstrap.Modal(cropModalEl);
    const croppedInput = document.getElementById('cropped_image');
    const currentImg = document.getElementById('current-img');
    let cropper = null;

    imageInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function (event) {
                cropImage.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(file);
        }
    });

    cropModalEl.addEventListener('shown.bs.modal', function () {
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, {
            aspectRatio: 1, // 1:1 Square Ratio for Profile
            viewMode: 1,
            autoCropArea: 0.9,
            responsive: true,
        });
    });

    cropModalEl.addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    document.getElementById('rotate-left').addEventListener('click', function () {
        if (cropper) cropper.rotate(-90);
    });

    document.getElementById('rotate-right').addEventListener('click', function () {
        if (cropper) cropper.rotate(90);
    });

    document.getElementById('crop-submit').addEventListener('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 600,
                height: 600
            });
            const base64Image = canvas.toDataURL('image/png');
            croppedInput.value = base64Image;
            if (currentImg) {
                currentImg.src = base64Image;
            }
            cropModal.hide();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
