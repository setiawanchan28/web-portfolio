<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();

$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    $stmt = db()->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    set_flash('success', 'Project berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/projects.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $slug = create_slug($title);
    $category = $_POST['category'] ?? 'Web';
    $short_description = $_POST['short_description'] ?? '';
    $full_description = $_POST['full_description'] ?? '';
    $demo_url = $_POST['demo_url'] ?? '';
    $github_url = $_POST['github_url'] ?? '';
    $project_year = $_POST['project_year'] ?? date('Y');
    $status = $_POST['status'] ?? 'Completed';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $display_order = $_POST['display_order'] ?? 0;

    $thumbnail = $_POST['existing_thumbnail'] ?? null;
    $uploaded_thumb = upload_file('thumbnail', 'projects');
    if ($uploaded_thumb) {
        $thumbnail = $uploaded_thumb;
    }

    if ($id > 0) {
        $stmt = db()->prepare("UPDATE projects SET title=?, slug=?, category=?, short_description=?, full_description=?, thumbnail=?, demo_url=?, github_url=?, project_year=?, status=?, is_featured=?, display_order=? WHERE id=?");
        $stmt->execute([$title, $slug, $category, $short_description, $full_description, $thumbnail, $demo_url, $github_url, $project_year, $status, $is_featured, $display_order, $id]);
        $project_id = $id;
        set_flash('success', 'Project berhasil diperbarui!');
    } else {
        $stmt = db()->prepare("INSERT INTO projects (title, slug, category, short_description, full_description, thumbnail, demo_url, github_url, project_year, status, is_featured, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $category, $short_description, $full_description, $thumbnail, $demo_url, $github_url, $project_year, $status, $is_featured, $display_order]);
        $project_id = db()->lastInsertId();
        set_flash('success', 'Project baru berhasil ditambahkan!');
    }

    if (isset($_FILES['gallery_images'])) {
        $files = $_FILES['gallery_images'];
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $_FILES['single_gallery'] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                $gallery_path = upload_file('single_gallery', 'projects');
                if ($gallery_path) {
                    $img_stmt = db()->prepare("INSERT INTO project_images (project_id, image_path) VALUES (?, ?)");
                    $img_stmt->execute([$project_id, $gallery_path]);
                }
            }
        }
    }

    header("Location: " . ADMIN_URL . "modules/projects.php");
    exit;
}

$page_title = 'Kelola Projects Portfolio';
$active_menu = 'projects';
require_once __DIR__ . '/../includes/header.php';

if ($action === 'add' || $action === 'edit'):
    $project = null;
    $gallery = [];
    if ($action === 'edit') {
        $id = $_GET['id'] ?? 0;
        $stmt = db()->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        $gallery_stmt = db()->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY id ASC");
        $gallery_stmt->execute([$id]);
        $gallery = $gallery_stmt->fetchAll();
    }
?>
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 text-info"><?= $action === 'edit' ? 'Edit Project' : 'Tambah Project Baru' ?></h5>
            <a href="<?= ADMIN_URL ?>modules/projects.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $project['id'] ?? 0 ?>">
            <input type="hidden" name="existing_thumbnail" value="<?= sanitize($project['thumbnail'] ?? '') ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul Project</label>
                    <input type="text" name="title" class="form-control" value="<?= sanitize($project['title'] ?? '') ?>" required placeholder="Contoh: Website Sistem Informasi Desa">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select" required>
                        <?php $cats = ['Web', 'Application', 'UI/UX', 'Data', 'Other']; ?>
                        <?php foreach ($cats as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($project['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Singkat (Short Description)</label>
                    <textarea name="short_description" class="form-control" rows="2" placeholder="Ringkasan singkat project untuk card landing page..."><?= sanitize($project['short_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Lengkap & Detail Fitur</label>
                    <textarea name="full_description" class="form-control" rows="5" placeholder="Penjelasan teknis, masalah yang diselesaikan, arsitektur..."><?= sanitize($project['full_description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Demo / Live Preview</label>
                    <input type="url" name="demo_url" class="form-control" value="<?= sanitize($project['demo_url'] ?? '') ?>" placeholder="https://example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Repository GitHub</label>
                    <input type="url" name="github_url" class="form-control" value="<?= sanitize($project['github_url'] ?? '') ?>" placeholder="https://github.com/username/repo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun Project</label>
                    <input type="text" name="project_year" class="form-control" value="<?= sanitize($project['project_year'] ?? date('Y')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status Project</label>
                    <select name="status" class="form-select">
                        <option value="Completed" <?= ($project['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="In Progress" <?= ($project['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Maintained" <?= ($project['status'] ?? '') === 'Maintained' ? 'selected' : '' ?>>Maintained</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="display_order" class="form-control" value="<?= $project['display_order'] ?? 0 ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thumbnail Utama Project</label>
                    <?php if (!empty($project['thumbnail'])): ?>
                        <div class="mb-2">
                            <img src="<?= UPLOAD_URL . sanitize($project['thumbnail']) ?>" style="max-height: 120px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload Gallery Screenshot (Multiple)</label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Dapat memilih lebih dari 1 file gambar.</small>
                </div>

                <?php if (!empty($gallery)): ?>
                    <div class="col-12 mt-3">
                        <label class="form-label">Gallery Screenshot Ter-upload:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($gallery as $img): ?>
                                <div class="position-relative border rounded p-1">
                                    <img src="<?= UPLOAD_URL . sanitize($img['image_path']) ?>" style="height: 70px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-12 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?= !empty($project['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-white" for="is_featured">Tampilkan sebagai Featured Project di Landing Page</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Project</button>
            </div>
        </form>
    </div>
<?php else: 
    $projects = db()->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC")->fetchAll();
?>
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 text-info"><i class="fas fa-project-diagram me-2"></i>Daftar Portfolio Projects</h5>
            <a href="<?= ADMIN_URL ?>modules/projects.php?action=add" class="btn btn-info btn-sm fw-semibold"><i class="fas fa-plus me-1"></i> Tambah Project Baru</a>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th>Featured</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada project yang ditambahkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['thumbnail'])): ?>
                                        <img src="<?= UPLOAD_URL . sanitize($p['thumbnail']) ?>" style="width: 50px; height: 40px; object-fit: cover; border-radius: 6px;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= sanitize($p['title']) ?></strong></td>
                                <td><span class="badge bg-dark border border-info text-info"><?= sanitize($p['category']) ?></span></td>
                                <td><?= sanitize($p['project_year']) ?></td>
                                <td>
                                    <?php if ($p['is_featured']): ?>
                                        <span class="badge bg-success">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Biasa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $p['display_order'] ?></td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>modules/projects.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a>
                                    <a href="<?= ADMIN_URL ?>modules/projects.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus project ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
